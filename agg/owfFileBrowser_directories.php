<?php

class owfFileBrowser_directories extends wf_agg {
	
	public function loader() {
		$this->wf->core_dao();
		
		$this->lang = $this->wf->core_lang()->get_context("owfFileBrowser");
		
		$this->struct = array(
			"form" => array(
				"perm" => array("session:admin"),
				"add_title" => $this->lang->ts("Adding a directory"),
				"mod_title" => $this->lang->ts("Editing a directory"),
			),
			"data" => array(
				"id" => array(
					"type" => WF_INT | WF_PRIMARY | WF_AUTOINC,
					"perm" => array("session:admin"),
					"name" => $this->lang->ts("ID"),
					"kind" => OWF_DAO_INPUT_READON,
				),
				"name" => array(
					"type" => WF_VARCHAR,
					"perm" => array("session:admin"),
					"name" => $this->lang->ts("Name"),
					"kind" => OWF_DAO_INPUT,
				),
				"description" => array(
					"type" => WF_VARCHAR,
					"perm" => array("session:admin"),
					"name" => $this->lang->ts("Description"),
					"kind" => OWF_DAO_INPUT,
				),
				"path" => array(
					"type" => WF_VARCHAR,
					"perm" => array("session:admin"),
					"name" => $this->lang->ts("Path"),
					"kind" => OWF_DAO_INPUT,
				),
			),
		);
		
		$this->dao = new core_dao_form_db(
			$this->wf,
			"owfFileBrowser_directories",
			OWF_DAO_ALL,
			$this->struct,
			"owfFileBrowser_directories",
			"owfFileBrowser Directories"
		);
	}
	
	private $dirs = null;
	public function get_directories($safe = true) {
		if(!isset($this->dirs)) {
			$ret = $this->dao->get(array());
			$current = $this->get_current_directory(false);
			if(!$current)
				$current = current($ret);
			foreach($ret as $dir) {
				$dir["active"] = false;
				if($current["id"] == $dir["id"])
					$dir["active"] = true;
				$this->dirs[$dir["id"]] = $dir;
			}
		}
		return $this->dirs;
	}
	
	public function get_current_directory($safe = true) {
		$id = intval($this->wf->get_var("directory"));
		$ret = current($this->dao->get(array("id" => $id)));
		if(!$ret && $safe)
			$ret = current($this->get_directories());
		return $ret;
	}
	
}
