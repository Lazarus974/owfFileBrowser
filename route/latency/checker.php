<?php

class wfr_owfFileBrowser_latency_checker extends wf_route_request {
	
	public function __construct($wf) {
		$this->wf = $wf;
		$this->checker = $wf->latency_checker();
		$this->latency = current($this->checker->dao->get());
	}
	
	public function check_latency() {
		
		$start = intval($this->wf->get_var("start"));
		$lag = intval($this->wf->get_var("lag"));
		$now = intval(microtime(true) * 1000);
		
		if($start) {
			//$lag = $now - $start;
			if($this->latency) {
				$this->checker->dao->modify(array(), array('latency' => -$now));
			}
			else
				$this->checker->dao->add(array('latency' => -$now));
			
			$a = $this->checker->dao->get();
			
			echo json_encode(array(
				"status" => true,
				"message" => "Next"
			));
		}
		elseif($lag) {
			$delay_client = $lag / 2;
			$delay_server = ($now + $this->latency['latency']) / 2;
			
			$error_ratio = $delay_server > 100 ? 0.1 : 0.25;
			
			if(abs($delay_server - $delay_client) > $delay_server * $error_ratio) {
				$this->checker->dao->modify(array(), array('latency' => 0));
				echo json_encode(array(
					"status" => false,
					"message" => "Inconsistent data, try again"
				));
			}
			else {
				$latency = intval(($delay_server + $delay_client) / 2);
				$this->checker->dao->modify(array(), array('latency' => $latency));
				echo json_encode(array(
					"status" => true,
					"message" => "Latency is $latency ms"
				));
			}
		}
		else {
			echo json_encode(array(
				"status" => false,
				"message" => "Protocol violation"
			));
		}
	}
	
	// this function does not handle network errors, it should stop when they occurs
	public function check_bandwidth() {
		
		if($this->latency && $this->latency['bandwidth'] < 0)
			$this->wf->display_error(403, 'Another bandwidth test is running', true);
		
		$action = $this->wf->get_var('action');
		
		if($action == "get") {
			$bw = $this->checker->get_bandwidth();
			echo json_encode(array(
				"status" => $bw > 0,
				"bandwidth" => $bw,
				"message" => $this->checker->format_bandwidth($bw)
			));
			exit(0);
		}
		
		if($this->latency)
			$this->checker->dao->modify(array(), array('bandwidth' => -1));
		else
			$this->checker->dao->add(array('bandwidth' => -1));
		
		/* generate random data */
		$data = "";
		do {
			$data .= chr(rand(65, 91));
		} while(strlen($data) < 65535);
		
		/* send data for 10 seconds */
		$sent = 0;
		$out = fopen("php://output", "w");
		$now = intval(microtime(true) * 1000);
		do {
			$sent += fwrite($out, $data);
		} while(intval(microtime(true) * 1000) - $now < 10000);
		
		$bandwidth = intval($sent / 10);
		
		if(!$bandwidth)
			$bandwidth = 0;
		
		$this->checker->dao->modify(array(), array('bandwidth' => $bandwidth));
	}
}
