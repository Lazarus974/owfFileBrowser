<?php

class owfFileBrowser_renderer extends wf_agg {
	
	public $title = 'Files viewer';
	
	public function loader() {
		$this->session		= $this->wf->session();
		$this->cipher		= $this->wf->core_cipher();
		
		$this->directories	= $this->wf->owfFileBrowser_directories();
	}
	
	public function rendering($body) {
		
		// deprecated
		$bandwidth = $this->wf->latency_checker()->get_bandwidth();
		
		$tpl = new core_tpl($this->wf);
		
		$in = array(
			"lang"			=> 'en', //change
			"title"			=> $this->title,
			"directories"	=> $this->directories->dao->get(),
			//"page"			=> $this->page,
			"body"			=> json_encode($body),
			'here'			=> $this->cipher->encode($this->wf->linker('/admin/files', true)),
			'me'			=> $this->session->session_me,
			'perms'			=> $this->session->session_my_perms,
			"bandwidth"		=> $bandwidth,
			//'dateFormat'	=> strtolower($this->bank_utils->getFullDateFormat())
		);
		
		$tpl->set_vars($in);
		
		echo $tpl->fetch('owfFileBrowser/main');
	}
	
	public function set_title($title) { $this->title = $title; }
	
}
