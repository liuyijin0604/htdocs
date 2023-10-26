<?php

class ediCONTROL extends ediCustomsMsg {

	function __construct($msg, $time) {
		parent::__construct($msg, $time);
		$this->Name = 'CONTRL';
		$this->Type = 'CONTRL';
		$this->parseJobIDX();
		$this->parseStatus();
	}

	private function parseJobIDX() {
		$pattern = "/UCI\+(\w+)\+\w+::\w+\+\w+\+\d+/";
		if (preg_match($pattern, $this->msgTxt, $m)) {
			$record = $this->dbsc->read($m[1]);
			$this->jobIDX = empty($record)? 0 : $record['jbIDX'];
		}
	}

	private function parseStatus() {
		$ctrlMsg = array('4'=>'Rejected', '7'=>'Acknowledged', '8'=>'Received');
		$resend  = array('4'=>true, '7'=>false, '8'=>false);
		$control = "/UCI\+\w+\+\w+::\w+\+\w+\+(\d+)/";
		echo 'Control Message '.$ctrlMsg[$m[1]]."\n";
		if (preg_match($control, $this->msgTxt, $m)) {
			$this->Status = $ctrlMsg[$m[1]];
			$this->Resendable = $resend[$m[1]];
		}
	}
}