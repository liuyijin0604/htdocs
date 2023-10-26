#!/usr/bin/env php
<?php
//hvlv@toplogistics.com.au: "|/home/pcaexpre/ics2api/pipe.php"
$fd = fopen('php://stdin', 'r');
$data = '';
while (!feof($fd)) {
	$data .= fread($fd, 1024);
}
fclose($fd);

include_once(dirname(__DIR__).'/os/protected/components/curl.php');

$eml2api = function($data){
	if(empty($data)) return false;
	$c = new curl('https://api.pcaex.com/ics');
	$c->setopt(CURLOPT_POSTFIELDS, $data);
	$c->setopt(CURLOPT_CUSTOMREQUEST, 'POST');
	$c->setopt(CURLOPT_RETURNTRANSFER, true);
	$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
	$c->setopt(CURLOPT_TIMEOUT, 20);
	$c->setopt(CURLOPT_HTTPHEADER, ['Content-Type: message/rfc822', 'Content-Length: ' . strlen($data), 'Authorization: Basic ' . base64_encode('ics:'.md5($data.'|'.'80add605974139c76e4932fde1efc76338f2a888'))]);
	$dir = false;
	if ($c->exec() !== true) {
		$dir = 'resend';
	}elseif($c->result != '100'){
		$dir = 'problem';
	}
	$tf = __DIR__.'/'.$dir.'/'.sha1($data).'.eml';
	if($dir !== false && !is_file($tf)) file_put_contents($tf, $data);
	return $dir == false;
};

//send eml
$eml2api($data);

//resend
$pid = __DIR__.'/resend_api.pid';
if (!is_file($pid) || filectime($pid) < (time() - 120)) {
	file_put_contents($pid, '1');
	foreach(glob(__DIR__.'/resend/*.eml') as $f){
		if($eml2api(file_get_contents($f))){
	 		unlink($f);
		}
	}
	unlink($pid);
}

exit(0);
