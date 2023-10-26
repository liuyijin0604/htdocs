#!/usr/bin/env php
<?php
include_once(dirname(__DIR__).'/components/curl.php');
$mail_dir = '/home/pcaexpre/mail/pcaexpress.com.au/hvlv/';

$batch2api = function($files) use ($mail_dir){
	$d = [];
	foreach($files as $f){
		if(!is_file($f) || preg_match('/^dovecot/', basename($f))) continue;
		$d[] = [basename($f), file_get_contents($f)];
	}
	$data = gzencode(json_encode($d));
	$c = new curl('https://api.pcaex.com/ics');
	$c->setopt(CURLOPT_POSTFIELDS, $data);
	$c->setopt(CURLOPT_CUSTOMREQUEST, 'POST');
	$c->setopt(CURLOPT_RETURNTRANSFER, true);
	$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
	$c->setopt(CURLOPT_TIMEOUT, 20);
	$c->setopt(CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($data), 'Authorization: Basic ' . base64_encode('ics:'.md5($data.'|'.'80add605974139c76e4932fde1efc76338f2a888'))]);

	if ($c->exec()) {
		$res = json_decode($c->result, true);
		if(empty($res['errors'])){
			foreach($res as $fs){ //move queue to cur
				if($fs[1] > 200) continue;
				if(is_file($mail_dir.'.queue/'.$fs[0])) {
					rename($mail_dir.'.queue/'.$fs[0], $mail_dir.'cur/'.$fs[0]);
				}
			}
		}
	}
};

//move new to queue
foreach(glob($mail_dir.'new/*') as $f){
	if(is_file($f) && filectime($f) < (time() - 5)){
		rename($f, $mail_dir.'.queue/'.basename($f));
	}
}

//process queue
$pid = __DIR__.'/ics_batch.pid';
if (!is_file($pid) || filectime($pid) < (time() - 300)) {
	file_put_contents($pid, '1');
	$fs = glob($mail_dir.'.queue/*');
	$pgs = ceil(count($fs) / 100);
	for($i = 0; $i < $pgs; $i++){
		$batch2api(array_slice($fs, $i * 100, 100));
	}
	unlink($pid);
}

exit(0);
