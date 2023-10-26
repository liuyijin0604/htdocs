<?php
include_once('ediParser.php');
$ep = new ediParser;
foreach(glob('D:\Working\denai_order\resource\HN_EDI\*.txt') as $f){
	echo $f."\n";
	$ep->load($f);
	var_dump($ep->raw);
	break;
}