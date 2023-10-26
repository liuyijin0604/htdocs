<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--dpi 150 -T 10 -R 10 -B 10 -L 10 -O Landscape --page-size A5" />
<?php //--disable-smart-shrinking ?>
<title>授权信</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 20px; text-rendering: optimize-speed; width: 1120px; }
table.chart td, table.chart th{ border: 1px #999 solid; padding: 5px; border-right:none; border-bottom: none; }
table.chart{ border: none; border: 1px #999 solid; border-top: none; border-left: none; border-collapse: collapse; width: 800px; }
tr.even td, tr.even th{ background: rgba(200, 200, 200, 0.6); }
p { line-height: 1.5em; }
.sig { font-family: "Heiti SC", "签名连笔字", "Microsoft YaHei", "微软雅黑"; font-size: 3em;}
</style>
</head>

<body width="1120">
<p style="font-size: 1.5em;">*代理报关委托书</p>
<br />
<p style="font-size: 1.2em;">被委托方(签章): 昆明普斯特速递货运有限责任公司<p>

<table class="chart">
<tr><td width="300">委托人(即收件人)</td><td><?=$p->cnee->name;?></td></tr>
<tr><td>委托人(即收件人)身份证号码<br />(或其他有效证件号码)</td><td><?=empty($p->cnee->cnid)? $p->cnee->cnid_no : $p->cnee->cnid->no;?></td></tr>
<tr><td>委托人(即收件人)地址</td><td><?=$p->cnee->getCnFullAddress();?></td></tr>
<tr><td>EMS快递单号</td><td><?=$p->ref;?></td></tr>
<tr><td>内件品名及数量	</td><td><?php
$gd = array();
foreach($p->eitems['g'] as $i => $g){
    $gd[] = $g.' &times; '.intval($p->eitems['q'][$i]);
}
echo implode(', ', $gd);
?></td></tr>
</table><br />

<p>本人保证遵守中国《海关法》及国家相关法规，承诺所需办理报关的快件系个人合理自用。现全权委托贵公司代理报关及相关事宜，愿意接受海关及其它监管部门的监管并承担法律责任。</p>
<br />
<br />
<p>委托人(即收件人)签字：<div class="sig"><?=$p->cnee->name;?></div></p>
<br />
<br />
<p>委托日期： <?=$p->created;?></p>
</body>
</html>
