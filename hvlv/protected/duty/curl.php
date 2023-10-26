<?php
class curl {
	var $caseless, $handle, $header, $result, $rcode, $options, $status, $rawResponse,$err;
	
	public function __construct($theURL=null){
		if (!function_exists('curl_init')){
			trigger_error('PHP was not built with --with-curl, rebuild PHP to use the curl class.', E_USER_ERROR);
		}
		$this->handle = curl_init();
		
		if (!empty($theURL)){
			$this->setopt(CURLOPT_URL, $theURL); 
		}
		$this->setopt(CURLOPT_HEADER, true);
		$this->setopt(CURLOPT_RETURNTRANSFER, true);
	}

	public function close(){
		curl_close($this->handle);
		$this->handle = null ;
	}

	public function exec(){
		$response = curl_exec($this->handle);
        $this->rawResponse = $response;
		if (curl_errno($this->handle) != 0){
			$this->err = curl_error($this->handle);
			return false;
		}
		$this->status = curl_getinfo($this->handle);
		$this->status['errno'] = curl_errno($this->handle);
		$this->status['error'] = curl_error($this->handle);
		$delimiter = "\r\n\r\n";
		
		while(preg_match('#^HTTP/[0-9\\.]+\s+100\s+Continue#i',$response)){ //remove 100 Continue header
    		$tmp = explode($delimiter, $response, 2);
    		$response = $tmp[1];
		}

		do{ //split header/body
			list($r_headers, $response) = explode($delimiter,$response,2);
			$h_lines = explode("\r\n",$r_headers);
			$r_lines = array_shift($h_lines);
			if (preg_match('@^HTTP/[0-9]\.[0-9] ([0-9]{3})@',$r_lines, $matches)) {
				$this->rcode = $matches[1];
			}else{
				$this->rcode = "Error";
				break;
			}
		}while ($this->rcode == "302");
		
		$this->result = $response;
		$this->header = array();
		foreach ($h_lines as $hl){
			if(preg_match('/^([^:]+):\s*(.*)$/', $hl, $m)){
				$header = $m[1];
				$value = $m[2];
				if(empty($this->header[$header])) $this->header[$header] = $value;
				else $this->header[$header] .= '; '.$value;
			}
		}

		return true;
	}

	public function setopt($theOption, $theValue){
		curl_setopt($this->handle, $theOption, $theValue);
		$this->options[$theOption] = $theValue ;
	}
	
	public function getOption($theOption){
		if (isset($this->options[$theOption])){
			return $this->options[$theOption];
		}
		return null ;
	}
	
	public function getHeader(){
		return $this->header;	
	}

    public function getRawResponse(){
        return $this->rawResponse;
    }

	public function hasError(){
		if (isset($this->status['error'])){
			return (empty($this->status['error']) ? false : $this->status['error']);
		}else{
			return false ;
		}
	}

	public function getStatus($theField=null){
		if (empty($theField)){
			return $this->status ;
		}else{
			if (isset($this->status[$theField])){
				return $this->status[$theField] ;
			}else{
				return false ;
			}
		}
	}

	public function asPostString($theData, $theName = NULL){
		$thePostString = '' ;
		$thePrefix = $theName ;
		
		if (is_array($theData)){
			foreach ($theData as $theKey => $theValue){
				if ($thePrefix === NULL){
					$thePostString .= '&' . $this->asPostString($theValue, $theKey);
				}else{
					$thePostString .= '&' . $this->asPostString($theValue, $thePrefix . '[' . $theKey . ']');
				}
			}
		}else{
			$thePostString .= '&' . urlencode((string)$thePrefix) . '=' . urlencode($theData);
		}
		return substr($thePostString, 1);
	}

//end class
}
