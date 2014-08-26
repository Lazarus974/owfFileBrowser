<?php

class latency_checker_dao extends core_dao_form_db {
	
	public function add($data) {
		$data['update_time'] = time();
		$data['user_id'] = $this->wf->session()->session_me['id'];
		return parent::add($data);
	}
	
	public function modify(array $where = array(), $data) {
		$data['update_time'] = time();
		$where['user_id'] = $this->wf->session()->session_me['id'];
		return parent::modify($where, $data);
	}
	
	public function get(array $where = array()) {
		$where['user_id'] = $this->wf->session()->session_me['id'];
		return parent::get($where);
	}
	
}

class latency_checker extends wf_agg {
	
	public function loader() {
		$this->wf->core_dao();
		
		$this->struct = array(
			"form" => array(
				"perm" => array("core:smtp"),
				"add_title" => "",
				"mod_title" => "",
			),
			"data" => array(
				"id" => array(
					"type" => WF_PRI,
					"perm" => array("session:admin"),
				),
				"user_id" => array(
					"type" => WF_INT,
					"perm" => array("session:admin"),
					"name" => "User",
					"kind" => OWF_DAO_LINK_MANY_TO_ONE,
					"dao" => array($this->wf->session()->user, "get"),
					"field-id" => "id",
					"field-name" => "email",
				),
				"latency" => array(
					"type" => WF_BIGINT,
					"perm" => array("session:admin"),
					"name" => "User latency",
					"kind" => OWF_DAO_NUMBER,
					"value" => 0,
				),
				"bandwidth" => array(
					"type" => WF_BIGINT,
					"perm" => array("session:admin"),
					"name" => "User bandwidth",
					"kind" => OWF_DAO_NUMBER,
					"value" => 0,
				),
				"update_time" => array(
					"type" => WF_BIGINT,
					"perm" => array("session:admin"),
					"name" => "Last check time",
					"kind" => OWF_DAO_DATETIME,
				),
			),
		);
		
		$this->dao = new latency_checker_dao(
			$this->wf,
			"user_latency",
			OWF_DAO_ALL,
			$this->struct,
			"user_latency",
			"User Latency"
		);
	}
	
	public function get_latency() {
		$latency = current($this->dao->get());
		return $latency['latency'] > 0 ? $latency['latency'] : 0;
	}
	
	public function get_bandwidth() {
		$latency = current($this->dao->get());
		return $latency['bandwidth'] > 0 ? $latency['bandwidth'] : 0;
	}
	
	public function format_bandwidth($bytes) {
		if($bytes > 1073741824) {
			return number_format($bytes/1073741824, 2, ',', ' ').' Go/s';
		}
		elseif($bytes > 1048576) {
			return number_format($bytes/1048576, 2, ',', ' ').' Mo/s';
		}
		elseif($bytes > 1024) {
			return number_format($bytes/1024, 2, ',', ' ').' Ko/s';
		}
		else
			return $bytes.' o';
	}
}
