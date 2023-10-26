<?php
#############################
#Programmer: Frank Liu		#
#E-Mail: me@lele.info		#
#HTTP: lele.info			#
#DATE: 12/12/2008
#############################

//Get edi message from Customs response emails
class icsFetcher {
	private $_pop, $_pem, $_key;
	protected $edis = Array();

	function __construct($user, $passwd, $server="localhost") {
		if (!extension_loaded('imap')) {
			if (!dl('imap.so')) {
				die("Fetcher Error: This class requires imap extension to run!<br/>");
			}
		}

		//Open stream connection to the POP server
		$this->_pop= imap_open("{".$server.":110/pop3/notls}INBOX",$user,$passwd) or die ("Connection Error: " . imap_last_error()."<br/>");
		if (($num_msgs = @imap_num_msg($this->_pop)) == 0){
			imap_close($this->_pop);
			echo("No Message found!<br/>");
			exit(0);
		}
	}

	public function fetchMsgFor($siteid, $pem, $key, $fwdfrom = false){
		$this->_pem = $pem;
		$this->_key = $key;

		foreach(imap_sort($this->_pop,SORTARRIVAL,0) as $mid) {
			$header = imap_headerinfo($this->_pop, $mid);

			//encrypted message from customs
			if(preg_match("/^AAA336C_\d{14}_".$siteid."$/", trim($header->subject)) && trim($header->fromaddress) == "cargo@ccf.customs.gov.au"){
				$this->fetchEncryptedMsg($mid, $header);
				continue;
			}

			//forwarded message not encrypted
			if(preg_match("/AAA336C_\d{14}_".$siteid."/", trim($header->subject)) && ($fwdfrom == $header->from[0]->mailbox."@".$header->from[0]->host || (!$fwdfrom === true))){
				$this->fetchForwardedMsg($mid, $header);
				continue;
			}
		}

		return $this->edis;
	}

	private function fetchEncryptedMsg($mid, $header){
		$msg_enc = $this->saveTmpFile(imap_fetchheader($this->_pop, $mid)."\r\n\r\n".imap_body($this->_pop, $mid));
		$msg_sig = $this->saveTmpFile("");
		openssl_pkcs7_decrypt($msg_enc, $msg_sig, "file://".dirname(__FILE__)."/".$this->_pem, array("file://".dirname(__FILE__)."/".$this->_pem, $this->_key));
		unlink($msg_enc);
		$this->getAttachedEDI($mid, $header, $msg_sig);
	}

	private function fetchForwardedMsg($mid, $header){
		$struct = imap_fetchstructure($this->_pop, $mid);
		if ($struct->type === 1 && ($struct->subtype != 'ALTERNATIVE' && $struct->subtype != 'RELATED')){
			for($i=0; $i<count($struct->parts); $i++) {
				if(isset($struct->parts[$i]->disposition) && (strtoupper($struct->parts[$i]->disposition) === "ATTACHMENT" || strtoupper($struct->parts[$i]->disposition) === "INLINE")) {
					foreach($struct->parts[$i]->dparameters as $key=>$val) {
						if(strtoupper($val->attribute) === "FILENAME" && !empty($val->value) && $val->value == "smime.p7m"){
							$body = "Content-Type: application/pkcs7-mime; name=\"smime.p7m\"\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: inline;filename=\"smime.p7m\"\r\n\r\n".imap_fetchbody($this->_pop, $mid, $i + 1);
							$msg_sig = $this->saveTmpFile($body);
							$this->getAttachedEDI($mid, $header, $msg_sig);
							break;
						}
					}
				}
			}
		}
	}

	private function getAttachedEDI($mid, $msgheader, $msg_sig){
		$tmp_sig = $this->saveTmpFile("");
		$msg_plain = $this->saveTmpFile("");
		openssl_pkcs7_verify($msg_sig, PKCS7_DETACHED | PKCS7_NOVERIFY | PKCS7_NOCHAIN, $tmp_sig, array(), dirname(__FILE__)."/".$this->_pem, $msg_plain);
		list($header, $body) = explode("\r\n\r\n", file_get_contents($msg_plain), 2);
		preg_match("/name\=\"([^\"]+)\"/",$header,$m);
		$edi->name = $m[1];
		$edi->data = quoted_printable_decode($body);
		$edi->utime = $msgheader->udate;
		$edi->from = $msgheader->fromaddress;
		array_push($this->edis, $edi);
		unlink($tmp_sig);
		unlink($msg_sig);
		unlink($msg_plain);
		imap_delete($this->_pop, $mid);
	}

	protected function saveTmpFile($data) {
		$tmpfname = tempnam(sys_get_temp_dir(), "ICS");
		file_put_contents($tmpfname, $data);
		return $tmpfname;
	}

	//Delete used mails and end session
	public function finish() {
		imap_expunge($this->_pop);
		imap_close($this->_pop);
	}

//end of class
}

//test class
/*$ef = new ediFetcher('cmr@dslogistics.com.au', 'qPxX1Dqng5', 'mail.dslogistics.com.au');
$edis = $ef->fetchMsgFor("AAK343M", "orite_ics2.pem", "n3v3R0uT", "frank@orite.com");
var_dump($edis);
$ef->finish();*/
?>
