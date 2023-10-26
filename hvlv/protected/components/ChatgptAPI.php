<?php
class ChatgptAPI
{
	// private const api_key = "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";
	private const api_key = "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";
	private const url = 'https://api.openai.com/v1/engines/davinci/completions'; // ChatGPT API 端点

	public static function requestResidentialAddressAPI($address)
	{
		$question = " Can you please check if it is residential address? And can you please just tell me yes or no?";
		// 设置 HTTP 请求标头
		$headers = array(
		    'Content-Type: application/json',
		    'Authorization: Bearer ' . ChatgptAPI::api_key,
		);

		// 准备要发送的数据
		$data = array(
			// 'model' => "gpt-3.5-turbo",
			// 'temperature' => 0.8,
		    'prompt' => $address." ".$question,
		    'max_tokens' => 200,
		);

		// 发送 HTTP POST 请求到 ChatGPT API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, ChatgptAPI::url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($ch);
		curl_close($ch);

		// 解析 API 返回的 JSON 数据
		$json = json_decode($response, true);

		// 输出生成的文本
		// print_r($json);
		print_r($address." ".$question);
		echo $json['choices'][0]['text'];
	}
	
	public static function requestResidentialAddress35API($address,$output=false)
	{
		$url = 'https://api.openai.com/v1/chat/completions';
		$question = " Can you tell me if this address is residential or commercial? And can you please tell me only one word?";
		// 设置 HTTP 请求标头
		$headers = array(
		    'Content-Type: application/json',
		    'Authorization: Bearer ' . ChatgptAPI::api_key,
		);

		// 准备要发送的数据
		$data = array(
			'model' => "gpt-3.5-turbo",
			'temperature' => 1.0,
		    // 'prompt' => $address." ".$question,
		    'max_tokens' => 200,
		    "messages" => [
				["role"=> "user", "content"=> $address." ".$question,]
			],
		);

		// 发送 HTTP POST 请求到 ChatGPT API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($ch);
		curl_close($ch);

		// 解析 API 返回的 JSON 数据
		$json = json_decode($response, true);

		// 输出生成的文本
		print_r($address." ".$question);
		if($output){
			if (isset($json['choices'][0]['message']['content'])) {
				return $json['choices'][0]['message']['content'];
			}
			else{
				return $json['error']['message'];
			}
		}
		else{
			if (isset($json['choices'][0]['message']['content'])) {
				echo $json['choices'][0]['message']['content'];
			}
			else{
				print_r($json);
			}
		}
		return ;		
	}

	public static function checkShipmentResidAddreAPI($shipment,$printout=false)
	{
		$url = 'https://api.openai.com/v1/chat/completions';
		$question = " Can you tell me if this address is residential or commercial? And can you please tell me only one word?";
		$address = $shipment->cnee->getCnFullAddress();
		// 设置 HTTP 请求标头
		$headers = array(
		    'Content-Type: application/json',
		    'Authorization: Bearer ' . ChatgptAPI::api_key,
		);

		// 准备要发送的数据
		$data = array(
			'model' => "gpt-3.5-turbo",
			'temperature' => 1.0,
		    'max_tokens' => 200,
		    'messages' => [
				["role"=> "user", "content"=> $address." ".$question,]
			],
		);

		// 发送 HTTP POST 请求到 ChatGPT API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($ch);
		curl_close($ch);

		// 解析 API 返回的 JSON 数据
		$json = json_decode($response, true);

		// 输出生成的文本
		print_r($address." ".$question);
		if($printout){
			if (isset($json['choices'][0]['message']['content'])) {
				echo $json['choices'][0]['message']['content'];
			}
			else{
				print_r($json);
			}
		}
		else{			
			if (isset($json['choices'][0]['message']['content'])) {
				return $json['choices'][0]['message']['content'];
			}
			else{
				return $json['error']['message'];
			}
		}
		return ;		
	}

	public static function validateShipmentAddreAPI($address,$printout=false)
	{
		$url = 'https://api.openai.com/v1/chat/completions';
		// $question = " Can you please check if this is a correct address? Can you please tell me yes or no? and can you return the valid address to me if this is not valid? The format just like (address, suburb, state, postcode)";
		$question = "Please valid the following address and tell me yes or no. And then return to me as the format just like (address, suburb, state, postcode): ";
		// $address = $shipment->cnee->getCnFullAddress();
		// 设置 HTTP 请求标头
		$headers = array(
		    'Content-Type: application/json',
		    'Authorization: Bearer ' . ChatgptAPI::api_key,
		);

		// 准备要发送的数据
		$data = array(
			'model' => "gpt-3.5-turbo",
			// 'model' => "babbage-002",
			'temperature' => 1.0,
		    'max_tokens' => 200,
		    'messages' => [
				// ["role"=> "user", "content"=> $address." ".$question],
				["role"=> "system", "content"=> "You are a helpful assistant that valids the address."],
				["role"=> "user", "content"=> $question.$address],
			],
		);

		// 发送 HTTP POST 请求到 ChatGPT API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($ch);
		curl_close($ch);

		// 解析 API 返回的 JSON 数据
		$json = json_decode($response, true);

		// 输出生成的文本
		print_r($question.$address);
		if($printout){
			if (isset($json['choices'][0]['message']['content'])) {
				echo $json['choices'][0]['message']['content'];
			}
			else{
				print_r($json['error']['message']);
			}
		}
		else{			
			if (isset($json['choices'][0]['message']['content'])) {
				return $json['choices'][0]['message']['content'];
			}
			else{
				return $json['error']['message'];
			}
		}
		return ;		
	}

	public static function correctAddreAPI($address,$printout=false)
	{
		$url = 'https://api.openai.com/v1/chat/completions';
		// $question = " Can you please check if this is a correct address? Can you please tell me yes or no? and can you return the valid address to me if this is not valid? The format just like (address, suburb, state, postcode)";
		$question = "Please validate the following address and correct it in format(address,suburb,state,postcode): ";
		// $address = $shipment->cnee->getCnFullAddress();
		// 设置 HTTP 请求标头
		$headers = array(
		    'Content-Type: application/json',
		    'Authorization: Bearer ' . ChatgptAPI::api_key,
		);

		// 准备要发送的数据
		$data = array(
			'model' => "gpt-3.5-turbo",
			'temperature' => 1.0,
		    'max_tokens' => 200,
		    'messages' => [
				// ["role"=> "user", "content"=> $address." ".$question],
				["role"=> "system", "content"=> "You are a helpful assistant that valids the address."],
				["role"=> "user", "content"=> $question.$address],
			],
		);

		// 发送 HTTP POST 请求到 ChatGPT API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($ch);
		curl_close($ch);

		// 解析 API 返回的 JSON 数据
		$json = json_decode($response, true);

		// 输出生成的文本
		// print_r($question.$address);
		if($printout){
			if (isset($json['choices'][0]['message']['content'])) {
				echo $json['choices'][0]['message']['content'];
			}
			else{
				print_r($json['error']['message']);
			}
		}
		else{			
			if (isset($json['choices'][0]['message']['content'])) {
				return $json['choices'][0]['message']['content'];
			}
			else{
				return $json['error']['message'];
			}
		}
		return ;		
	}

	public static function getCorrectAddress($address,$printout=false){
		$arr = explode(":",ChatgptAPI::correctAddreAPI($address,$printout));
		print_r($arr);
		$address = [];
		$length = count($arr);
		if($length>1){
			$a = str_replace(array("\r","\n",'.'), '', $arr[$length-1]);
			$address = explode(",",$a);
		}
		return $address;
	}

	public static function generateEssayToolAPI($question,$printout=false)
	{
		$url = 'https://api.openai.com/v1/chat/completions';
		
		// 设置 HTTP 请求标头
		$headers = array(
		    'Content-Type: application/json',
		    'Authorization: Bearer ' . ChatgptAPI::api_key,
		);

		// 准备要发送的数据
		$data = array(
			'model' => "gpt-3.5-turbo",
			// 'model' => "babbage-002",
			'temperature' => 1.0,
		    'max_tokens' => 2000,
		    'messages' => [
				// ["role"=> "user", "content"=> $address." ".$question],
				["role"=> "system", "content"=> "You are a helpful assistant."],
				["role"=> "user", "content"=> $question],
			],
		);

		// 发送 HTTP POST 请求到 ChatGPT API
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		// Execute the request
		$response = curl_exec($ch);

		// Check for cURL errors
		if (curl_errno($ch)) {
		    echo 'Error: ' . curl_error($ch);
		    exit;
		}

		// Close cURL session
		curl_close($ch);
		// 解析 API 返回的 JSON 数据
		$json = json_decode($response, true);

		// 输出生成的文本
		if($printout){
			if (isset($json['choices'][0]['message']['content'])) {
				echo $json['choices'][0]['message']['content'];
			}
			else{
				print_r($json['error']['message']);
			}
		}
		else{			
			if (isset($json['choices'][0]['message']['content'])) {
				return $json['choices'][0]['message']['content'];
			}
			else{
				return $json['error']['message'];
			}
		}
		return ;		
	}
	
}
?>