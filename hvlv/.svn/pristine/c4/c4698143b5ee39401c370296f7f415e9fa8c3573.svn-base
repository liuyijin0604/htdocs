<?php

class ediCustomsMsg {

	protected $Time, $jobIDX, $Name, $Type, $ver, $Status, $msgTxt, $Resendable, $Done;
	protected $dbjb, $dbsc;

	public static $_included = false;

	// do not use new() to create object, use createMsgObject() instead
	function __construct($msg, $time) {
		$this->msgTxt = preg_replace("/([^?])'/", "$1'\n", $msg);
		$this->Time = date('Y-m-d H:i:s', $time);
		$this->Status = 'UNKNOWN STATUS, PLEASE CONTACT SYSTEM DEVELOPER';
		$this->Done = false;
		$this->jobIDX = 0;
		$this->dbjb = new dbmJob();
		$this->dbsc = new dbmSea_cargo();
		$this->parseVer();
	}

	function includeAll() {
		if (ediCustomsMsg::$_included) return;
		foreach (glob(dirname(__FILE__).'/edi*.php') as $file) {
			include_once($file);
		}
		ediCustomsMsg::$_included = true;
	}

	public function createMsgObject($msg, $time) {
		ediCustomsMsg::includeAll();
		$pattern = '/BGM\+961:::(\w+)\+/';
		$className = 'edi'.(preg_match($pattern, $msg, $m)? $m[1] : 'CONTROL');
		if (class_exists($className))
			return new $className($msg, $time);
		return null;
	}

	protected function parseVer() {
		$pattern = '/UNB\+UNOC:3\+\w+::\w+\+\w+\+\d+:\d+\+(\d+)\+/';
		$this->ver = (preg_match($pattern, $this->msgTxt, $m))? (int)$m[1] : 0;
	}

	public function updateDb() {
		if (!empty($this->msgTxt)) {
			if (! $this->dbsc->isOldResponse($this->ver)) {
				$sca['jbIDX']   = $this->jobIDX;
				$sca['scaName'] = $this->Name;
				$sca['scaType'] = $this->Type;
				$sca['scaVer']  = $this->ver;
				$sca['scaMsg']  = $this->msgTxt;
				$sca['scaTime'] = $this->Time;
				$sca['scaRcvd'] = 1;
				$sca['scaMemo'] = 'Received From Customs';
				$this->dbsc->create($sca);
			}
		}
		//var_dump($this->jobIDX);
		if (!empty($this->jobIDX)) {
			$job['jbScaResend'] = $this->Resendable;
			$job['jbScaStatus'] = $this->Status;
			$job['jbScaDone'] = $this->Done;
			$this->dbjb->update($this->jobIDX, $job);
		}
	}

}