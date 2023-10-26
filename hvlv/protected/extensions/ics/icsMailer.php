<?php
class icsMailer{
	public $err = array();
	public $testing = false;
	protected $Subject, $Body, $CreatorID;
	
	public function __construct($test=false){
		$this->testing = $test;
		$this->CreatorID = $test? Yii::app()->params['ics']['test_site'] : Yii::app()->params['ics']['prod_site'];
	}

	public function addEDI($id, $edi){
		$this->Subject = $this->CreatorID."_{$id}_".Yii::app()->params['ics']['customs_id'];

	//	$this->boundary = md5(uniqid(time()));

		$this->Body = "MIME-Version: 1.0\n";
		/*$this->Body .= "Content-Type: multipart/mixed; boundary=\"".$this->boundary."\"\n";
		$this->Body .= "Content-Transfer-Encoding: quoted-printable\n\n";
		$this->Body .= "This is a multi-part message in MIME format.\n\n";
		$this->Body .= "--".$this->boundary."\n";
		$this->Body .= "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
		$this->Body .= "Content-Transfer-Encoding: quoted-printable\n\n";
		$this->Body .= "--".$this->boundary."\n";*/
		$this->Body .= "Content-Type: application/octet-stream; name=\"".$this->Subject.".edi\"\n";
		$this->Body .= "Content-Transfer-Encoding: base64\n";
		$this->Body .= "Content-Disposition: attachment; filename=\"".$this->Subject.".edi\"\n\n";
		$this->Body .= chunk_split(base64_encode($edi))."\n\n";
		//$this->Body .= "--".$this->boundary."--\n";
	}

	private function signEncrypt(){
		$headers = array("From" => Yii::app()->params['ics']['email'], "X-Priority" => 1, "Importance" => "HIGH");
		$msgraw = tempnam("/tmp", "msgraw");
		file_put_contents($msgraw, $this->Body);
		$msgsign = tempnam("/tmp", "msgsign");
		if(openssl_pkcs7_sign($msgraw, $msgsign, Yii::app()->params['ics']['sig_pem'] ,array(Yii::app()->params['ics']['sig_pem'], Yii::app()->params['ics']['pem_pwd']), array(), 0)){
			$msgenc = tempnam("/tmp", "msgenc");
			if(openssl_pkcs7_encrypt($msgsign, $msgenc, Yii::app()->params['ics']['customs_pk'], $headers, 0, 1)){
				list($this->Header, $this->Body) = explode("\n\n", file_get_contents($msgenc), 2);
			}else{
				$this->addError("Encrypt Error!");
			}
		}else{
			$this->addError("Sign Error!");
		}

		while ($err = openssl_error_string()){
			$this->addError($err);
		}

		unlink($msgraw);
		unlink($msgsign);
		unlink($msgenc);

		return empty($this->Error);
	}

	public function Send(){
		if($this->signEncrypt()){
			return @mail(Yii::app()->paramss['ics']['customs_email'], $this->Subject, $this->Body, $this->Header, '-f'.Yii::app()->params['ics']['email']);
		}else{
			return $this->Error;
		}
	}

	private function addError($e){
		if(is_array($e)){
			$this->Error = array_merge($this->Error, $e);
		}else{
			array_push($this->Error, $e);
		}
	}
//end of class
}
