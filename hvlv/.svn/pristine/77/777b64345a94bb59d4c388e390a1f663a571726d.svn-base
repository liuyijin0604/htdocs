<?php
class eAdaptorServer extends \SoapServer
{
	private $options = [];
	public $trackingId;
	public $logging = true;

	public function __construct($wsdl, $options=[]){
		$this->options = array_merge($options, [
			'uri'      => 'http://CargoWise.com/eHub/2010/06',
			'trace'    => 1,
		]);
		parent::__construct($wsdl, $this->options);
	}

	public function __call($method_name, $arguments){
		if(!method_exists($this, $method_name))
			throw new Exception('Method '.$method_name.' not found');

		return call_user_func_array(array($this, $method_name), $arguments);

	}

	public function SendStreamRequestTrackingID($r){
		$this->trackingId = $r;
		//$this->log("trackingId\t".$r);
	}

	public function SendStream($r){
		$this->securityHeader();
		file_put_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'xml_queue/'.$r->Payload->Message->TrackingID.'.xml', gzdecode(base64_decode($r->Payload->Message->_)));
		$this->log("Stream\t".$r->Payload->Message->TrackingID.'.xml saved');
		return true;
	}

	public function Ping(){
		$this->securityHeader();
		$pr = new StdClass;
		$pr->PingResult = true;
		return $pr;
	}

	public function Security($r){
		$r->UsernameToken->Username;
		$r->UsernameToken->Password;
	}

	public function securityHeader(){
		$hns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';
		$o = new StdClass;
		$o->Created = new SoapVar(gmdate('Y-m-d\TH:i:s\Z'), XSD_STRING, NULL, $hns, NULL, $hns);
		$o->Expires = new SoapVar(gmdate('Y-m-d\TH:i:s\Z', strtotime('+5 minute')), XSD_STRING, NULL, $hns, NULL, $hns);
		$t = new StdClass;
		$t->Timestamp = new SoapVar($o, SOAP_ENC_OBJECT, NULL, $hns, 'Timestamp', $hns);
		$hdr = new SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'Security', $t, true);
		$this->addSoapHeader($hdr);
	}

	public function log($msg){
		if(!$this->logging) return;
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		file_put_contents($tmp.'eAdaptorServer.log', date('Y-m-d H:i:s')."\t".$msg."\n", FILE_APPEND);
	}
}

class EAdaptorController extends CController{

	public function actionServer(){
		$server = new eAdaptorServer(Yii::app()->basePath.DIRECTORY_SEPARATOR.'data/eAdapterOutboundWebService.singleWsdl.xml');
		$server->setObject($server);
		$server->handle();
	}
}