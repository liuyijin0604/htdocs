<?php
//opcache_invalidate(__FILE__);
class ApiIcsAction extends CAction
{
	public $ctlr, $settings, $data, $parser, $errors = [], $out, $debug = false;
	private $key = '80add605974139c76e4932fde1efc76338f2a888';

	public function run()
	{
		$this->data = file_get_contents('php://input');
		$this->ctlr = $this->getController();
		$this->settings = Yii::app()->params['otherICS']['FJR636T'];
		if (empty($_SERVER['HTTP_AUTHORIZATION']) || !$this->auth()) {
			throw new CHttpException(401, 'Authentication error, access denied.');
		}

		if ($this->debug) {
			$this->log($this->data);
		}

		if($_SERVER["CONTENT_TYPE"] == 'message/rfc822'){
			$this->out = $this->save($this->data);
		}elseif($_SERVER["CONTENT_TYPE"] == 'application/json'){
			$data = json_decode(gzdecode($this->data), true);
			$this->out = [];
			$trans = Yii::app()->db->beginTransaction();
			$c = 0;
			try{
				foreach($data as $msg){
					$this->out[] = [$msg[0], $this->save($msg[1])];
				}
				$trans->commit();
			} catch (Exception $ex) {
				$trans->rollback();
				$this->errors[] = $ex->getMessage();
			}
		}else{
			throw new CHttpException(401, 'Authentication error, access denied.');
		}
		
		empty($this->errors) ? $this->response($this->out) : $this->errorResponse();
	}

	protected function auth()
	{
		list($user, $hash) = explode(':', base64_decode(preg_replace('/^Basic\s+/i', '', trim($_SERVER['HTTP_AUTHORIZATION']))));
		
		if($user != 'ics' || $hash != md5($this->data.'|'.$this->key)){
			return false;
		}

		return true;
	}

	public function save($msg){
		return $this->_decryptEmail($msg);
	}

	private function _decryptEmail($msg){
		$msg_enc = $this->tempFile($msg);
		$msg_sig = $this->tempFile();
		$tmp_sig = $this->tempFile();
		$msg_plain = $this->tempFile();
		if(openssl_pkcs7_decrypt($msg_enc, $msg_sig, $this->settings['enc_pem'], array($this->settings['enc_pem'], $this->settings['pem_pwd']))){
			echo "111";
			if(openssl_pkcs7_verify($msg_sig, PKCS7_DETACHED | PKCS7_NOVERIFY | PKCS7_NOCHAIN, $tmp_sig, array(), substr($this->settings['enc_pem'], 7), $msg_plain)){
				list($header, $body) = explode("\r\n\r\n", file_get_contents($msg_plain), 2);
								print_r([$header, $body]);
				Yii::app()->end();
				preg_match("/filename\=([^\.]+)\.edi/", $header, $m);
				$ns = explode('_', empty($m[1])? '' : trim($m[1]));
				if(empty($ns[1]) && empty($body)){
					return $this->_forwardEmail();
				}
				$ns[1] = ltrim($ns[1], '0');
				if(Edimsg::model()->count('mid = :mid AND sender = :sndr AND  dt > DATE_SUB(NOW(), INTERVAL 5 DAY)', [':mid' => $ns[1], ':sndr' => $ns[0]]) > 0) return;
				$em = new Edimsg;
				$em->dt = date('Y-m-d H:i:s');
				$em->type = '';
				$em->sender = $ns[0];
				$em->receiver = $ns[2];
				$em->status = 10;
				$em->mid = $ns[1];
				$em->msg = quoted_printable_decode($body);
				$em->save();
				if ($this->debug) {
					$this->log('msg saved: '.$this->parser->getHeader('subject'));
				}
				$this->cleanTempFile([$msg_enc, $msg_sig, $tmp_sig, $msg_plain]);
				return 100;
			}else{
				$this->cleanTempFile([$msg_enc, $msg_sig, $tmp_sig, $msg_plain]);
				return $this->_forwardEmail($msg);
			}
		}else{
			$this->cleanTempFile([$msg_enc, $msg_sig, $tmp_sig, $msg_plain]);
			return $this->_forwardEmail($msg);
		}
	}

	private function _forwardEmail($msg){
		if ($this->debug) {
			$this->log('email forward: '.$this->parser->getHeader('subject'));
		}
		include_once('PHPMailer/class.phpmailer.php');
		$mail = new PHPMailer();
		$mail->IsHTML(false);
		$mail->From     = "hvlv@toplogistics.com.au";
		$mail->FromName = "HVLV";
		$mail->Subject  = "HVLV unknown message: ".$this->parser->getHeader('subject');
		$mail->Body = 'message attached';
		$mail->AddStringAttachment($msg, 'forwared_message.eml', '8bit', 'message/rfc822');
		$mail->AddAddress('gero@toplogistics.com.au');
		if(!$mail->send()){
			return 201;
		}

		return 200;
	}

	protected function tempFile($data=false) {
		$tmpfname = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime'. DIRECTORY_SEPARATOR . 'ics', "ICS");
		if(!empty($data)) file_put_contents($tmpfname, $data);
		return $tmpfname;
	}

	protected function cleanTempFile($fs){
		foreach($fs as $f){
			@unlink($f);
		}
	}

	public function errorResponse()
	{
		$this->response(['errors' => $this->errors]);
	}

	public function response($o)
	{
		echo json_encode($o);
		Yii::app()->end();
	}

	public function log($l)
	{
		$tmp = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;
		file_put_contents($tmp . 'ics_api.log', date('Y-m-d H:i:s') . ' ' . $l . "\n", FILE_APPEND);
	}

}
