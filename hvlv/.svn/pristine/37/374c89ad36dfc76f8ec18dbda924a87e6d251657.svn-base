<?php

class ediSEACRR extends ediCustomsMsg {

	function __construct($msg, $time) {
		parent::__construct($msg, $time);
		$this->Name = 'SEACRR';
		$this->Type = 'CUSRES';
		$this->parseJobIDX();
		$this->parseStatus();
	}

	private function parseJobIDX() {
		$pattern = '/RFF\+ABO:DSJ(\d+):/';
		$this->jobIDX = preg_match($pattern, $this->msgTxt, $m)? (int)$m[1] : 0;
	}

	private function parseStatus() {
		$allok = "/FTX\+AAO\+\+\+THIS TRANSACTION WAS ACCEPTED/";
		$error = "/FTX\+AAO\+\+\+THIS TRANSACTION WAS REJECTED/";
		$this->Status = '';
		if (preg_match($allok, $this->msgTxt)) {
			$this->Status = 'THIS TRANSACTION WAS ACCEPTED';
			$this->Resendable = false;
			$this->Done = true;
		}
		elseif (preg_match($error, $this->msgTxt)) {
			preg_match_all("/FTX\+AAO\+\+\+(.*)'/", $this->msgTxt, $m);
			foreach($m[1] as $t) {
				$this->Status .= $t."<br />\n";
			}
			$this->Resendable = true;
		}
	}
}