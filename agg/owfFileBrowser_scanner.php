<?php

function remove_hidden_files($f) {return $f[0] != '.';}

class owfFileBrowser_scanner extends wf_agg {
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * 
	 *		DIRECTORY SCANNING
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function scan($directory) {
		if(!is_dir($directory))
			return false;
		
		$cache_file = $this->wf->get_last_filename("var/data/files/$directory");
		$cache_dir = dirname($cache_file);
		
		if(!file_exists($cache_dir))
			@mkdir($cache_dir, 0755, true);
		
		if(file_exists($cache_file))
			return unserialize(file_get_contents($cache_file));
		
		$scanned = $this->__scan($directory, 1);
		file_put_contents($cache_file, serialize($scanned));
		return $scanned;
	}
	
	private function __scan($directory, $level, $parent = '/') {
		$ret = array();
		
		$scan_res = @scandir($directory);
		if(!is_array($scan_res))
			return false;
		
		$scan = array_filter($scan_res, "remove_hidden_files");
		foreach($scan as $filename) {
			$entry = array('name' => $filename, 'level' => $level, 'parent' => $parent);
			
			$fullname = $directory."/".$filename;
			
			if(is_dir($fullname)) {
				$entry['type'] = 'd';
				$entry['childs'] = $this->__scan($fullname, $level + 1, $parent.$filename.'/');
			}
			else {
				$entry['type'] = 'f';
			}
			
			$ret[$fullname] = $entry;
		}
		return $ret;
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * 
	 *		TREE RENDERING
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function render($scan) {
		if(!is_array($scan))
			return "";
		
		foreach($scan as $k => $s) {
			$scan[$k] = $this->__render($s);
			if($s['type'] == 'd')
				$scan[$k] .= $this->render($s['childs']);
		}
		return implode($scan);
	}
	
	public function __render($tree) {
		$tpl = new core_tpl($this->wf);
		$tpl->set('tree', $tree);
		return $tpl->fetch('owfFileBrowser/files');
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * 
	 *		FILE INFO IN JSON
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function json_info() {
		
		$did = intval($this->wf->get_var('directory'));
		$file = $this->wf->get_var('file');
		
		$directory = current($this->wf->owfFileBrowser_directories()->dao->get(array("id" => $did)));
		$dir = $directory["path"];
		if(!$dir)
			return "Page error";
		
		$filename = $dir.$file;
		if(!file_exists($filename))
			return "File not found";
		
		$size = filesize($filename);
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = @finfo_file($finfo, $filename);
		if(!$mime)
			return "Permission issue";
		
		finfo_close($finfo);
		
		$download_time = $this->get_download_time($filename);
		
		$tpl = new core_tpl($this->wf);
		$tpl->set('filename', $file);
		$tpl->set('file', basename($file));
		$tpl->set('size', $this->format_size($size));
		$tpl->set('directory', $directory);
		$tpl->set('mime', $mime);
		$tpl->set('content', $this->get_file_content($filename, $mime, $size));
		$tpl->set('download_time', $this->format_time($download_time));
		$tpl->set('downloadable', $download_time ? $download_time < 1200 : false);
		return $tpl->fetch('owfFileBrowser/file_info');
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * 
	 *		UTILITIES
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function format_size($bytes, $full = false) {
		if($bytes > 1073741824) {
			return number_format($bytes/1073741824, 2, ',', ' ').' Go '.($full ? $this->format_size(intval($bytes%1073741824)) : '');
		}
		elseif($bytes > 1048576) {
			return number_format($bytes/1048576, 2, ',', ' ').' Mo '.($full ? $this->format_size(intval($bytes%1048576)) : '');
		}
		elseif($bytes > 1024) {
			return number_format($bytes/1024, 2, ',', ' ').' Ko '.($full ? $this->format_size(intval($bytes%1024)) : '');
		}
		else
			return $bytes.' o';
	}
	
	public function format_time($time, $full = true) {
		$time = intval($time);
		if($time > 86400) {
			return intval($time/86400).' J '.($full ? $this->format_time(intval($time%86400)) : '');
		}
		elseif($time > 3600) {
			return intval($time/3600).' h '.($full ? $this->format_time(intval($time%3600)) : '');
		}
		elseif($time > 60) {
			return intval($time/60).' min '.($full ? $this->format_time(intval($time%60)) : '');
		}
		elseif($time > 0)
			return intval($time).' sec';
		else
			return 'instant';
	}
	
	public function get_file_content($filename, $mime, $size) {
		list($type, $extension) = explode('/', $mime);
		if($type == "text")
			return array(array("html" => true, "name" => "Contenu", 'value' => nl2br(htmlentities(file_get_contents($filename, false, NULL, 0, 1024).($size > 1024 ? ' [...]' : '')))));
		elseif($type == "audio" || $type == "video")
			return (new files_info_mp3($this->wf))->get_info($filename);
		elseif($type == "image")
			return (new files_info_pictures($this->wf))->set_mime($extension)->get_info($filename);
	}
	
	public function get_download_time($filename, $bandwidth = 0) {
		if(!$bandwidth)
			$bandwidth = $this->wf->latency_checker()->get_bandwidth();
		
		if(!$bandwidth || !file_exists($filename))
			return false;
		
		$size = filesize($filename);
		
		return $size / $bandwidth;
	}
}
