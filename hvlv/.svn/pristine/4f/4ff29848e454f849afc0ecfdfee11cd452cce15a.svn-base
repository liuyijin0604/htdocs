<!-- <?php
class PayPalApi {
	private $ver = '109.0';
	private $usr = 'billing_api1.pcaexpress.com.au';
	private $pwd = 'HPESD3EPH4Y2KEDS';
	private $sig = 'Arqi6jlzkENWCfG5UD-8HvbPiahoAjBOkk0713.3S7WTsgtLfNDC5L0O';
	private $ep = 'https://api-3t.paypal.com/nvp';

	public $logo_url = 'https://www.pcaexpress.com.au/wp-content/uploads/2014/04/PCAE_Logo.png';
	public $err, $return_url, $cancel_url;

	public function __construct($return_url = null, $cancel_url = null, $logo_url = null){
		if(!empty($return_url)) $this->return_url = $return_url;
		if(!empty($cancel_url)) $this->cancel_url = $cancel_url;
		if(!empty($logo_url)) $this->logo_url = $logo_url;
	}

	public function ExpressCheckout($items){
		$ds = '';
		$tot = 0;
		foreach($items as $i=>$r){
			if($r['qty'] == 0) continue;
			$ds .= '&L_PAYMENTREQUEST_0_NAME'.$i.'='.urlencode($r['name']).
					'&L_PAYMENTREQUEST_0_AMT'.$i.'='.$r['price'].
					'&L_PAYMENTREQUEST_0_QTY'.$i.'='. urlencode($r['qty']);
			$tot += $r['price'] * $r['qty'];
		}
		//Parameters for SetExpressCheckout, which will be sent to PayPal
		$padata = 	'&METHOD=SetExpressCheckout'.
					'&RETURNURL='.urlencode($this->return_url).
					'&CANCELURL='.urlencode($this->cancel_url).
					'&BRANDNAME=PCA+Express'.
					'&PAYMENTREQUEST_0_PAYMENTACTION=SALE'.
					$ds.
					'&NOSHIPPING=1'. //set 1 to hide buyer's shipping address, in-case products that does not require shipping
					'&PAYMENTREQUEST_0_ITEMAMT='.urlencode($tot).
					'&PAYMENTREQUEST_0_SHIPPINGAMT=0'.
					'&PAYMENTREQUEST_0_AMT='.urlencode($tot).
					'&PAYMENTREQUEST_0_CURRENCYCODE=AUD'.
					'&LOCALECODE=GB'. //PayPal pages to match the language on your website.
					'&LOGOIMG='.$this->logo_url. //site logo
					'&CARTBORDERCOLOR=FFFFFF'. //border color of cart
					'&ALLOWNOTE=1';
		$res = $this->PPHttpPost('SetExpressCheckout', $padata);
		if("SUCCESS" == strtoupper($res["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($res["ACK"])){
			return 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$res["TOKEN"];
		}else{
			$this->err = urldecode($res["L_LONGMESSAGE0"]);
			return false;
		}
	}

	public function GetExpressCheckoutDetails($token){
		$padata = '&TOKEN='.urlencode($token);
		return $this->PPHttpPost('GetExpressCheckoutDetails', $padata);
	}

	public function DoExpressCheckoutPayment($tot, $token, $payerid){
		$padata = '&TOKEN='.urlencode($token).'&PAYERID='.$payerid.
			'&PAYMENTREQUEST_0_PAYMENTACTION=SALE'.
			'&PAYMENTREQUEST_0_AMT='.urlencode($tot).
			'&PAYMENTREQUEST_0_CURRENCYCODE=AUD';

		$res = $this->PPHttpPost('DoExpressCheckoutPayment', $padata);
		if("SUCCESS" == strtoupper($res["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($res["ACK"])){
			return true;
		}else{
			$this->err = urldecode($res["L_LONGMESSAGE0"]);
			return false;
		}
	}

	public function PPHttpPost($methodName_, $nvpStr_) {
			// Set the curl parameters.
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->ep);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
		
			// Turn off the server and peer verification (TrustManager Concept).
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
		
			// Set the API operation, version, and API signature in the request.
			$nvpreq = 'METHOD='.$methodName_.'&VERSION='.$this->ver.'&USER='.$this->usr.'&PWD='.$this->pwd.'&SIGNATURE='.$this->sig.$nvpStr_;
		
			// Set the request as a POST FIELD for curl.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
		
			// Get response from the server.
			$httpResponse = curl_exec($ch);
		
			if(!$httpResponse) {
				exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
			}
		
			// Extract the response details.
			$httpResponseAr = explode("&", $httpResponse);
		
			$httpParsedResponseAr = array();
			foreach ($httpResponseAr as $i => $value) {
				$tmpAr = explode("=", $value);
				if(sizeof($tmpAr) > 1) {
					$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
				}
			}
		
			if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
				exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
			}
		
		return $httpParsedResponseAr;
	}	
} -->