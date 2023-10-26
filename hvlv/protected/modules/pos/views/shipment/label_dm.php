<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PCA Express DotMatrix Label</title>

<style type="text/css">
@media print {  
	@page {
		size: 210mm 140mm;
		margin: 5mm;
	}
	body{ transform: none; width: 210mm; height: 140mm; }
}
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: "Microsoft YaHei", Verdana, Geneva, sans-serif; font-size: 16px; text-rendering: optimize-speed;  background: #fff; }
small{ font-size: 12px;}
td.p10 { padding: 10px;}
td.p5 { padding: 5px;}
</style>
</head>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="37%" valign="top" style="padding-top: 80px">
			<p style="height: 40px"><?=$org->id;?></p>
			<p style="height: 60px;"><?=$model->cnor->name.' &nbsp; &nbsp; &nbsp; '.$model->cnor->tel;?></p>
			<p style="height: 140px;"><?=$model->cnor->fullAddress();?></p>
			
			<p style="height: 80px;"><?=$model->cnee->name.' &nbsp; &nbsp; &nbsp; '.$model->cnee->tel;?></p>
			<p><?=$model->cnee->getCnFullAddress();?></p>
		</td>
		<td width="30%" valign="top" style="padding: 150px 15px 0 15px">
			<p style="text-align:right;height:70px;"><?=empty($model->weight)? '' : $model->weight;?></p>
			<p style="font-size: 1.2em">
		<?php
			if(!empty($model->eitems['g'])){
				$gs = [];
				foreach($model->eitems['g'] as $i => $g){
					if(empty($model->eitems['q'][$i])) continue;
					$gs[] = $g.' &times; '.@$model->eitems['q'][$i];
				}
				echo implode("<br />\n", $gs);
			}
		?></p>
			<p><?=$model->note;?></p>
		</td>
		<td valign="top" style="padding-top: 10px">
			<p style="height: 320px"><?=$model->cref;?></p>
			<p><?=$model->cnor->name.' &nbsp; &nbsp; &nbsp; '.date("Y-m-d");?></p>
		</td>
	</tr>
</table>
<script type="text/javascript">
	window.print();
	setTimeout(function(){ window.close(); }, 1e3);
</script>
