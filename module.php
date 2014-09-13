<?php

// @todo
// In the renderer : lang and title support

class wfm_owfFileBrowser extends wf_module {
	public function __construct($wf) { $this->wf = $wf; }
	public function get_name() { return("owfFileBrowser"); }
	public function get_description()  { return("OpenWF files browser"); }
	public function get_banner()  { return("owfFileBrowser/1.0.0-HEAD"); }
	public function get_version() { return("1.0.0-HEAD"); }
	public function get_authors() { return(array("Olivier Leclercq")); }
	public function get_depends() { return(array("core", "admin", "session", "ppjQuery", "belboudou")); }
	
	public function get_actions() {
		return array(
			/* main routes */
			"/files" => array(
				WF_ROUTE_ACTION,
				"owfFileBrowser/home",
				"main",
				"",
				WF_ROUTE_HIDE,
				array("session:simple")
			),
			"/files/download" => array(
				WF_ROUTE_ACTION,
				"owfFileBrowser/home",
				"download",
				"",
				WF_ROUTE_HIDE,
				array("session:simple")
			),
			
			"/admin/files" => array(
				WF_ROUTE_ACTION,
				"admin/owfFileBrowser/directories",
				"main",
				"FileViewer directories",
				WF_ROUTE_SHOW,
				array("session:admin")
			),
			"/latency/checker" => array(
				WF_ROUTE_ACTION,
				"latency/checker",
				"check_latency",
				"",
				WF_ROUTE_HIDE,
				array("session:simple")
			),
			"/bandwidth/checker" => array(
				WF_ROUTE_ACTION,
				"latency/checker",
				"check_bandwidth",
				"",
				WF_ROUTE_HIDE,
				array("session:simple")
			),
		);
	}
	
	public function json_module() {
		$return = array();
		
		$return[] = array(
			"agg" => "owfFileBrowser_scanner",
			"method" => "json_info",
			"perm" => array("session:simple")
		);
		
		return $return;
	}
	
	public function belboudou_mainpage() {
		return array(
			"/files" => array(
				"title" => $this->ts("Files"),
				"img" => $this->wf->linker("/picture/ftp.png"),
				"alt" => $this->ts("Files"),
				"icon" => "briefcase"
			)
		);
	}
	
}
