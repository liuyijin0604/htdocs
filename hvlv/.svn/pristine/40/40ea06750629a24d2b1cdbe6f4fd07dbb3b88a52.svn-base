<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--dpi 100 -T 20 -R 20 -B 20 -L 20 -O Landscape" win-only="--disable-smart-shrinking" />
<title>Pallet Mark</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; box-sizing: border-box; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 48px; text-rendering: optimize-speed; width: 2120px; }
p.logo { text-align: center; padding-bottom: 30px; }
.barcode{ font-family: IDAutomationHC39M; font-size: 36px; padding: 30px; text-align: center; }
h1.dest { padding: 30px; font-size: 60px; text-align: center; }
h4 { font-size: 26px; }
small { font-size: 16px; }
div.page-break { clear:both; display: none; page-break-after: always; }
h2.connote { text-align: center; padding: 30px;}
table { border-collapse: collapse; border: 4px solid #000; }
table td{ padding: 0.5em 0.5em; border: 2px solid #000;}
table td table td { padding: 10px; }
</style>
</head>

<body width="2120">
<?php
$tpl = 'pltmrk_def';
$burl = Yii::app()->request->hostInfo.Yii::app()->baseUrl;
switch($model->poc){
	case 'CNXMN':
	case 'CNXM2':
		$tpl = 'pltmrk_xmn';
	break;
	case 'CNJJI':
		$tpl = 'pltmrk_jji';
	break;
}

$excl = empty($_GET['excl'])? [] : preg_split('/[, ;]+/', $_GET['excl']);

foreach($plts as $pc=>$m){
	if(empty($m->lines) || in_array($m->ref, $excl)) unset($plts[$pc]);
}

if(!is_array($plts)) $plts = [];
$tplt = sizeof($plts);
$pc = 1;
foreach($plts as $m){
	$w = 0;
	$ids = empty($m)? [] : $m->getFids();
	if(!empty($ids)){
		$sql = 'SELECT SUM(weight) FROM shipment WHERE id IN('.implode(',', $ids).')';
		$w = Yii::app()->db->createCommand($sql)->queryScalar();
	}
	$c = sizeof($ids);
	if(empty($c)) continue;
	include($tpl.'.php');
	echo '<div class="page-break"></div>';
	$pc++;
}
?>
</body>
</html>
