<?php

abstract class files_info {
	
	public function __construct($wf) {
		$this->wf = $wf;
	}
	
	protected function ts($text) {
		return $text;
	}
	
	abstract public function get_info($filename);
}
