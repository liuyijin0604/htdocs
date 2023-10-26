<?php

class ediUserMsg {

	protected $RespABN = RESPONSI_ABN;
	private $test = SCA_TESTING;

	protected $dbjb, $dbcp, $dbct, $dbsc;
	protected $job, $msg, $msgTxt, $Type, $Name, $idx, $icr, $ver, $err;

	function __construct() {
		$this->dbjb = new dbmJob();
		$this->dbcp = new dbmCompany();
		$this->dbct = new dbmJobcntr();
		$this->dbsc = new dbmSea_cargo();
	}

	//escape reserved char
	public function strEscape($str){
		$str = trim($str);
		//remove all chars not supported by ics
		$str = preg_replace('/[^ -Z\^\-~\\\n]/','',$str);
		$str = preg_replace('/([\+\:\'\?])/','?\\1',$str);
		$str = preg_replace('/[\n\r\t]+/',' ',$str);
		return $str;
	}

	//split char
	public function smartSplit($str, $size, $m){
		$str = substr($str, 0, $size * $m);
		$sl = strlen($str);
		$o = 0;
		while($sp = strpos($str,"?",$o)){
			if(($sp+1)%$size == 0){
				$str = substr($str,0,$sp)." ".substr($str,$sp,$sl);
				$sl++;
			}
			$o=$sp+1;
		}
		$p = $m - floor($sl/$size);
		$stra = str_split($str, $size);
		$str = implode(":", $stra);
		return $str;
	}

	public function getError() {
		return implode("\n", $this->err);
	}

	public function prepare($jid) {
		$data['jbIDX']   = $jid;
		$data['scaName'] = $this->Name;
		$data['scaType'] = empty($this->Type)? 'N/A' : $this->Type;
		$data['scaMemo'] = 'Sent By '.(empty($_SESSION['name'])? 'UNKNOWN' : $_SESSION['name']);
		$this->idx = $this->dbsc->create($data);
		$this->ver = $this->dbsc->getVer($jid, $this->Name);
	}


	public function formatMessage() {
		$date = date('ymd');
		$time = date('Hi');
		$this->icr  = str_pad($this->idx, 12, '0', STR_PAD_LEFT);

		$this->msgTxt = "UNB+UNOC:3+".CREATOR_ID."::".CREATOR_ID."+".RECIPIENT_ID."+$date:$time+".$this->icr."++++1";
		$this->msgTxt .= ($this->test)? "++1'\n" : "'\n";
		$this->msgTxt .= implode("'\n", $this->msg);
		$this->msgTxt .= "'\nUNZ+1+".$this->icr."'";

		return $this->msgTxt;
	}


	public function sendToCustoms() {
		if (empty($this->msgTxt) || !empty($this->err)){
			$this->dbsc->del($this->idx);
			return false;
		}

		$mailer = new icsMailer(SENDER_EMAIL, PEM_SIG, PEM_PASSWORD, CREATOR_ID, RECIPIENT_ID);
		$mailer->addEDI($this->icr, $this->msgTxt);
		$sent = $mailer->Send();
		if ($sent === true) {
			$data['scaVer']  = $this->ver;
			$data['scaMsg']  = $this->msgTxt;
			$data['scaTime'] = date('Y-m-d H:i:s');
			$this->dbsc->update($this->idx, $data);
			return true;
		} else {
			$this->msg = implode('<br />', $sent);
			$this->dbsc->del($this->idx);
			return false;
		}
	}
} // end of class
