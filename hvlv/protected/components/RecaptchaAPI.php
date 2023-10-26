<?php
class RecaptchaAPI
{
	const RECAPTCHA_V3_SITE_KEY = "6Ldtmg4aAAAAAL-XiCePLTVZJrWDs8VmpqSRnVjZ";
	const RECAPTCHA_V3_SECRET_KEY = "6Ldtmg4aAAAAAIADlHb-_fqm_wTTnNaXlg2NIeLy";

	public static function sendRecaptcha($post)
	{
		if(!empty($post))
		{
			$token = $post['token'];
			$action = $post['action'];
			 
			// call curl to POST request
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => self::RECAPTCHA_V3_SECRET_KEY, 'response' => $token)));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close($ch);
			$arrResponse = json_decode($response, true);
			// verify the response
			if($arrResponse["success"] == '1' && $arrResponse["action"] == $action && $arrResponse["score"] >= 0.5) {
			    return true;
			} else {
			   return false;
			}
		}
	}
}
?>