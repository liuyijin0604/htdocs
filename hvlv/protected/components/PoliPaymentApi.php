<?php

class PoliPaymentApi{

	private static $accounts = [
		'pcae' => [
			'POLI_MERCHCODE' => 'S6102066',
			'POLI_AUTHCODE' => 'IYPTMKBBHM',
		],
		'tla' => [
			'POLI_MERCHCODE' => 'S6105076',
			'POLI_AUTHCODE' => 'Lr4$7!qSE8^t',
		],
	];

	const PAYLINK_KEY = 'fhwerweo42ADEEFF389kdkjhvne33nwr';
	const TRN_URL = 'https://poliapi.apac.paywithpoli.com/api/v2/Transaction/GetTransaction?token=';
	const REQUEST_URL = 'https://poliapi.apac.paywithpoli.com/api/Transaction/Initiate';

	public static function request($url, $data = false, $acc='pcae'){

		$auth = base64_encode(self::$accounts[$acc]['POLI_MERCHCODE'] . ':' . self::$accounts[$acc]['POLI_AUTHCODE']);
		$header = ['Content-Type: application/json', 'Authorization: Basic '.$auth];
		$ch = curl_init($url);
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		if(!empty($data)){
			curl_setopt( $ch, CURLOPT_POST, 1);
			curl_setopt( $ch, CURLOPT_POSTFIELDS, is_string($data)? $data : json_encode($data));
		}
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt( $ch, CURLOPT_REFERER, '');
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec( $ch );

		curl_close ($ch);
		return json_decode($response, true);
	}

	public static function initiate($data, $acc='pcae'){
		/*$data = [
			'Amount' => $amt,
			'CurrencyCode' => 'AUD',
			'MerchantData' => $order_status,
			'MerchantReference' => $ref,
			'MerchantHomepageURL' => $baselink,
			'SuccessURL' => $success,
			'FailureURL' => $cancel,
			'CancellationURL' => $cancel,
			'NotificationURL' => $nudge,
		];*/

		$response_json = self::request("https://poliapi.apac.paywithpoli.com/api/Transaction/Initiate", $data, $acc);

		$redirect_url = '';
		$error_message=null;
		if( isset( $response_json['TransactionToken'] ) and $response_json['TransactionToken'] != "" ){
			$transactionToken=$response_json['TransactionToken'];
		}
		if( isset( $response_json['TransactionRefNo'] ) and $response_json['TransactionRefNo'] != "" ){
			$transactionRefNo=$response_json['TransactionRefNo'];
		}
		if( isset( $response_json['NavigateURL'] ) and $response_json['NavigateURL'] != "" ){
			$redirect_url=$response_json['NavigateURL'];
		}
		if( isset( $response_json['ErrorMessage'] ) and $response_json['ErrorMessage'] != "" ){
			$error_message=$response_json['ErrorMessage'];
		}
		if( isset( $response_json['Message'] ) and $response_json['Message'] != "" ){
			$error_message=$response_json['Message'];
		}
		
		if( $error_message != "" ){
			// Error
			echo "<strong>Sorry your payment cannot be processed - ".$error_message."</strong>";
			
		} else {
			// Redirect
			header( 'Location: '.$redirect_url );
		}
	}

	public static function notify() {
		$ipaddress = $_SERVER["REMOTE_ADDR"];
		$token = null;//Make token accessible as null to the whole document.
		if(isset($_POST["Token"])){//If it's in get
			$token = $_POST["Token"];//Set it
		}
		if(isset($_GET["token"])&&!isset($_POST["Token"])){//If it's in get
			$token = $_GET["token"];//Set it
		}

		return self::request("https://poliapi.apac.paywithpoli.com/api/Transaction/GetTransaction?token=".urlencode($token));
	}

	public static function getPoliTrn($token, $acc = 'pcae') {
		$trn = '';

		$auth = base64_encode(self::$accounts[$acc]['POLI_MERCHCODE'] . ':' . self::$accounts[$acc]['POLI_AUTHCODE']);
		$header = array();
		$header[] = 'Content-Type: application/json';
		$header[] = 'Authorization: Basic ' . $auth;

		// https://poliapi.apac.paywithpoli.com/api/v2/Transaction/GetTransaction?token={transactionToken}
		$requst_url = self::TRN_URL.$token;
		try {
			$ch = curl_init($requst_url);

			//See the cURL documentation for more information: http://curl.haxx.se/docs/sslcerts.html
			//We recommend using this bundle: https://raw.githubusercontent.com/bagder/ca-bundle/master/ca-bundle.crt
			//curl_setopt($ch, CURLOPT_CAINFO, "/ca-bundle.crt");
			// curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);

			// currently just disable ssl
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);

			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($ch);

			if ($response == false) {
				throw new Exception(curl_error($ch), curl_errno($ch));
			}

			curl_close($ch);
		} catch( Exception $e) {
			trigger_error( sprintf(
				'Curl failed with error #%d: %s',
				$e->getCode(), $e->getMessage()),
				E_USER_ERROR);

			return false;
		}

		$json = json_decode($response, true);

		// get url token save them for query soon
		if ( is_array($json) ) {
			$trn = $json;
		} else {
		}

		return $trn;
	}

	public static function payPoli($data, $acc='pcae') {
		return self::request(self::REQUEST_URL, $data, $acc);
	}
}
