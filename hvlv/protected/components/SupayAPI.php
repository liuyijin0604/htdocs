<?php
class SupayAPI {
	private $merchant_id = '72795';
	private $authentication_code = 'lkjh678jGQDJokl9XQHaHIyfuytttqqq';
	private $ep_alipay = 'https://api.superpayglobal.com/payment/bridge/merchant_request';
	private $ep_wechatpay = 'https://api.superpayglobal.com/payment/wxpayproxy/merchant_request';
	private $ep_notify_verify = 'https://api.superpayglobal.com/payment/bridge/notification_verification';
	private $ep_result = 'https://api.superpayglobal.com/payment/bridge/get_payment_detail';

	public $err, $return_url, $notification_url;

	public function __construct($notification_url = null, $return_url = null, $merchant_id = null, $authentication_code = null) {
		if (!empty($notification_url)) {
			$notification_url = str_replace('ep', 'os', $notification_url);
			$notification_url = str_replace('www', 'os', $notification_url);
			$this->notification_url = $notification_url;
		}
		if (!empty($merchant_id)) $this->merchant_id = $merchant_id;
		if (!empty($authentication_code)) $this->authentication_code = $authentication_code;
		if (!empty($return_url)) $this->return_url = $return_url;
	}

	public function checkoutByAliPay($product_title, $task_id, $currency, $amount) {
		$nvpStr_ = '&product_title=' . rawurlencode($product_title);
		$nvpStr_ .= '&merchant_trade_no=' . $task_id;
		$nvpStr_ .= '&currency=' . $currency;
		$nvpStr_ .= '&total_amount=' . $amount;

		$token = 'merchant_id=' . $this->merchant_id . '&authentication_code=' . $this->authentication_code;
		$token .= '&merchant_trade_no=' . $task_id . '&total_amount=' . $amount;
		$token = md5($token);
	
		// Set the API operation, version, and API signature in the request.
		$nvpreq = 'merchant_id=' . $this->merchant_id . '&authentication_code=' . $this->authentication_code;
		$nvpreq .= $nvpStr_;
		$time = gmdate('Y-m-d H:i:s');
		$nvpreq .= '&create_time=' . rawurlencode($time) . '&notification_url=' . rawurlencode($this->notification_url);
		if (!empty($this->return_url)) $nvpreq .= '&return_url=' . $this->return_url;
		$nvpreq .= '&token=' . $token;

		return $this->ep_alipay . '?' . $nvpreq;
	}

	public function checkoutByWechatPay($product_title, $task_id, $currency, $amount) {
		$nvpStr_ = '&product_title=' . rawurlencode($product_title);
		$nvpStr_ .= '&merchant_trade_no=' . $task_id;
		$nvpStr_ .= '&currency=' . $currency;
		$nvpStr_ .= '&total_amount=' . $amount;

		$token = 'merchant_id=' . $this->merchant_id . '&authentication_code=' . $this->authentication_code;
		$token .= '&merchant_trade_no=' . $task_id . '&total_amount=' . $amount;
		$token = md5($token);
	
		// Set the API operation, version, and API signature in the request.
		$nvpreq = 'merchant_id=' . $this->merchant_id . '&authentication_code=' . $this->authentication_code;
		$nvpreq .= $nvpStr_;
		$time = gmdate('Y-m-d H:i:s');
		$nvpreq .= '&create_time=' . rawurlencode($time) . '&notification_url=' . $this->notification_url;
		if (!empty($this->return_url)) $nvpreq .= '&return_url=' . $this->return_url;
		$nvpreq .= '&token=' . $token;

		$httpResponse = $this->PPHttpPost('checkout', $this->ep_wechatpay, $nvpreq, $token);
		if (!$httpResponse) {
			echo 'Checkout by Wechat failed';
		} else {
			$data = json_decode($httpResponse, true);
			if (!$data['result'] == 'SUCCESS') {
				echo 'Checkout by Wechat failed';
			} else {
				return $data['supayCashierURL'];
			}
		}
	}

	public function verifyNotification() {
		if (!empty($_GET['notice_id']) && !empty($_GET['merchant_trade_no']) && !empty($_GET['token'])) {
			if (md5('notice_id='.$_GET['notice_id'].'&merchant_trade_no='.$_GET['merchant_trade_no'].'&authentication_code='.$this->authentication_code) == $_GET['token']) {
				// Set the API operation, version, and API signature in the request.
				$nvpreq = 'notice_id=' . $_GET['notice_id'] . '&merchant_trade_no=' . $_GET['merchant_trade_no'];
				$token = md5($nvpreq);
				$nvpreq .= '&token=' . $token;

				$httpResponse = $this->PPHttpPost('notify_verify', $this->ep_notify_verify . '?' . $nvpreq, $nvpreq, $token, false);
				if (!$httpResponse) {
					echo 'failed';
				} else {
					return $httpResponse == 'SUCCESS';
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function checkoutResult($task_id) {
		$nvpStr_ = '&merchant_trade_no=' . $task_id;
	
		// Set the API operation, version, and API signature in the request.
		$nvpreq = 'merchant_id=' . $this->merchant_id . '&authentication_code=' . $this->authentication_code;
		$nvpreq .= $nvpStr_;
		$token = md5($nvpreq);
		$nvpreq .= '&token=' . $token;

		$httpResponse = $this->PPHttpPost('result', $this->ep_result . '?' . $nvpreq, $nvpreq, $token, false);
		if (!$httpResponse) {
			echo 'failed';
		} else {
			$data = json_decode($httpResponse, true);
			return $data['query_success'] == 'T';
		}
	}

	public function PPHttpPost($methodName_, $ep, $nvpreq, $token, $post = true, $redirect = false) {
		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ep);
		if ($redirect) {
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		}
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, $post);
	
		// Set the request as a POST FIELD for curl.
		if ($post) curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
	
		// Get response from the server.
		$httpResponse = curl_exec($ch);
		if ($redirect) {
			return curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		} else {
			return $httpResponse;
		}
	}

}