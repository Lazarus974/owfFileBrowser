<?php

class wfr_owfFileBrowser_owfFileBrowser_home extends wf_route_request {
	
	public function __construct($wf) {
		$this->wf				= $wf;
		$this->renderer			= $wf->owfFileBrowser_renderer();
		$this->scanner			= $wf->owfFileBrowser_scanner();
		
		$this->core_request		= $wf->core_request();
		$this->session			= $wf->session();
		
		//$this->me = intval($this->session->session_me["id"]);
		$this->lang = $this->wf->core_lang()->get_context("owfFileBrowser");
		//$this->dateformat = $this->bank_utils->getDateFormat();
		
		/* retrieve directories */
		$this->directories = array();
		$this->a_directories = $this->wf->owfFileBrowser_directories();
		$ret = $this->a_directories->dao->get();
		foreach($ret as $dir) {
			$dir["active"] = false;
			$this->directories[$dir["id"]] = $dir;
		}
	}
	
	public function main() {
		
		/* block if no directory */
		if(empty($this->directories))
			$this->wf->display_error(500, "No directory configured yet", true);
		
		/* set current page */
		$page = intval(substr($this->core_request->get_ghost(), 1));
		if(!$page || !isset($this->directories[$page]))
			$page = key($this->directories);
		$this->directories[$page]["active"] = true;
		$directory = $this->directories[$page];
		
		/* determine if json request */
		$json = false;
		$headers = apache_request_headers();
		if(isset($headers["Accept"]))
			if(strstr($headers["Accept"], "json") || strstr($headers["Accept"], "javascript"))
				$json = true;
		
		/* get content */
		$scan = $this->scanner->scan($directory["path"]);
		if($scan) {
			$tpl = new core_tpl($this->wf);
			$tpl->set('body', $this->scanner->render($scan));
			$tpl->set('directory', $directory);
			$body = $tpl->fetch('owfFileBrowser/content');
		}
		else
			$body = "<p class='text-center text-danger'>Directory error</p>";
		
		/* return content in json if requested */
		if($json) {
			echo json_encode($body);
			exit(0);
		}
		
		/* otherwise simple plain html */
		$tpl = new core_tpl($this->wf);
		$this->renderer->rendering($body);
	}
	
	public function download() {
		
		$latency = $this->wf->latency_checker()->get_bandwidth();
		
		$file = $this->wf->get_var('file');
		$did = intval($this->wf->get_var('directory'));
		$directory = current($this->a_directories->dao->get(array("id" => $did)));
		
		$json = $this->wf->get_var('json');
		$pages = array(
			"pictures" => "",
			"games" => "",
			"softwares" => "",
			"music" => "",
			"videos" => ""
		);
		
		if(!$latency)
			$this->wf->display_error(403, "Your latency has not been checked yet", true);
		
		if(!$directory)
			$this->wf->display_error(404, "Page not found", true);
		
		$filename = $directory["path"].'/'.$file;
		if(!file_exists($filename))
			$this->wf->display_error(404, "File not found", true);
		
		$dl_time = $this->scanner->get_download_time($filename);
		
		if($dl_time > 1200)
			$this->wf->display_error(403, "This file is too big for you", true);
		
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $filename);
		finfo_close($finfo);
		
		if(!$json) {
			header("Content-Description: File Transfer");
			header("Content-Type: application/octet-stream");
#			header("Content-Type: $mime");
			header("Content-Disposition: attachment; filename=".basename($filename));
			header("Content-Transfer-Encoding: binary");
			header("Expires: 0");
			header("Cache-Control: must-revalidate");
			header("Pragma: public");
			header("Content-Length: ".filesize($filename));
			ob_clean();
			readfile($filename);
		}
		else {
			echo json_encode(true);
		}
		exit(0);
	}
}
