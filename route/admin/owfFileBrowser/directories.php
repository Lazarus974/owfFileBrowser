<?php

class wfr_owfFileBrowser_admin_owfFileBrowser_directories extends wf_route_request {
	
	public function __construct($wf) {
		$this->wf = $wf;
		$this->directories = $this->wf->owfFileBrowser_directories();
		$this->renderer = $this->wf->admin_html();
		$this->lang = $this->wf->core_lang()->get_context("owfFileBrowser");
	}
	
	public function main() {
		$dsrc = new core_datasource_db($this->wf, "owfFileBrowser_directories");
		$dset = new core_dataset($this->wf, $dsrc);
		
		$filters = array();
		$cols = array(
			//'name' => array(
				//'name'      => $this->tournaments->lang->ts('Name'),
				//'orderable' => true,
			//),
		);
		
		$dset->set_cols($cols);
		$dset->set_filters($filters);
		
		$dset->set_row_callback(array($this, 'callback_row'));

		/* template utilisateur */
		$tplset = array();
		$dview = new core_dataview($this->wf, $dset);
		$tpl = new core_tpl($this->wf);

		$add_link = $this->directories->dao->add_link();
		
		$in = array(
			"dao_link_add" => $add_link,
			"dataset" => $dview->render(NULL, $tplset)
		);	 
		$tpl->set_vars($in);
		
		$this->renderer->set_backlink($this->wf->linker('/admin'), "Home", "home");
		$this->renderer->set_title($this->lang->ts("Directories"));
		$this->renderer->rendering($tpl->fetch('admin/magic/default'));
	}

	public function callback_row($row, $datum) {
		$link = $this->directories->dao->mod_link($datum['id']);
		$r = "<li>".
				"<a href='".$link."'>".
				"<h3>".htmlspecialchars($datum["name"])."</h3>".
				"<p>".
					htmlspecialchars($datum["description"])."<br/>".
					htmlspecialchars($datum["path"])."".
				"</p>".
			"</a></li>";
		return($r);
	}
	
}
