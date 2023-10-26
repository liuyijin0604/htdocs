<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PCA Express 2 Secs Label</title>

<style type="text/css">
@media print {  
	@page {
		size: 100mm 150mm;
		margin: 0;
		padding: 0;
	}
	body{ transform: none; width: 100mm; }
}
*{ margin: 0; padding: 0; letter-spacing: normal !important; box-sizing: border-box; }
body{ font-family: "Microsoft YaHei", Verdana, Geneva, sans-serif; font-size: 10px; text-rendering: optimize-speed;  background: #fff; margin: 0; padding: 0;}
small{ font-size: 8px;}
table td { padding: 5px; }
</style>
</head>

<body>
<?php
foreach($rs as $label):
?>
<div style="padding:2mm; page-break-after: always; page-break-inside: avoid">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="60%" valign="middle" class="dto" height="50">
			<img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/PCAE_Logo_large.png" alt="PCA Express Logo" width="160" align="left" />
		</td>
		<td align="center" valign="bottom">
			<?php
			$bc = new TCPDFBarcode($label->hbn, 'C128');
	echo '<img src="data:image/png;base64,'.base64_encode($bc->getBarcodePngData(1, 25)).'" alt="barcode" />';
			?>
			<span><?=$label->hbn;?></span>
		</td>
	</tr>
</table>
<div style="border: 1px #000 solid;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="2" valign="top" height="60">
			<h4>收件人/To: <span><?=$label->cnee->name;?></span></h4>
		<?php
		echo '<p style="font-size:1.2em;">',$label->cnee->getCnFullAddress(), ' &nbsp; ', $label->cnee->tel, '</p>';
		?>
		</td>
	</tr>
	<tr>
		<td colspan="2" valign="top" class="dto" style="border-top: 1px #000 solid;">
		<div style="height: 85px; overflow:hidden;"><p>货物描述/Goods Detail:</p><p style="font-size: 1.4em">
		<?php
			if(!empty($label->eitems['g'])){
				$gs = [];
				foreach($label->eitems['g'] as $i => $g){
					$gs[] = $g.' &times; '.@$label->eitems['q'][$i];
				}
				echo implode(", ", $gs);
			}
		?></p>
		</div>
		</td>
	</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top: 1px #000 solid;">
	<tr>
		<td width="60%" valign="top" class="di" style="min-height: 30px;">
			<p>备注/Notes:</p>
		<div style="height: 25px; overflow:hidden;">
				<?=nl2br(trim($label->note));?>
				<?=empty($label->cref)? '' : '<br />Ref: '.$label->cref;?>
		</div>
		</td>
		<td style="border-left: 1px #000 solid; padding: 0;" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="wci">
			<tr>
				<td>日期/Date</td>
				<td><?=$label->created;?></td>
			</tr>
	<?php if($label->weight > 0): ?>
			<tr>
				<td width="50%">重量/Weight</td>
				<td><?=$label->weight;?> kg</td>
			</tr>
	<?php endif; ?>
		</table></td>
	</tr>
</table>
<div style="border-top: 1px #000 solid; border-bottom: 1px #000 solid; padding: 5px; text-align:center"><div><?php
	echo '<img src="data:image/png;base64,'.base64_encode($bc->getBarcodePngData(2, 50)).'" alt="barcode" />';
	?></div><span style="font-size:1.4em"><?=$label->hbn;?></span>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="45%" valign="top" height="60">寄件人/Sender:
			<p>
	  <?php
      echo $label->cnor->name, '<br />', $label->cnor->fullAddress(array('suburb', 'state', 'postcode')), $label->cnor->tel;
    ?>
			</p></td>
		<td valign="top" style="border-left: 1px #000 solid;">收件人/To: <span><?=$label->cnee->name;?></span>
		<?php
		echo '<p>',$label->cnee->getCnFullAddress(), ' &nbsp; ', $label->cnee->tel, '</p>';
		?></td>
	</tr>
	<tr>
		<td colspan="2" valign="top" class="dto" style="border-top: 1px #000 solid;border-bottom: 1px #000 solid;">
		<div style="height: 50px; overflow:hidden;"><p>货物描述/Goods Detail: <span style="padding-left: 100px">日期/Date &nbsp; <?=$label->created;?></span></p><p style="font-size: 0.8em">
		<?php
			if(!empty($label->eitems['g'])){
				$gs = [];
				foreach($label->eitems['g'] as $i => $g){
					$gs[] = $g.' &times; '.@$label->eitems['q'][$i];
				}
				echo implode(", ", $gs);
			}
		?></p>
		</div>
		</td>
	</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="60%">
	<div style="padding: 5px; text-align:center"><div><?php
	echo '<img src="data:image/png;base64,'.base64_encode($bc->getBarcodePngData(2, 30)).'" alt="barcode" width="200" />';
	?></div><span style="font-size:1.2em"><?=$label->hbn;?></span>
</div>
</td>
<td align="right" valign="middle" style="padding-right: 10px;"><img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/PCAE_Logo_large.png" alt="PCA Express Logo" width="100" />
</td>
</tr>
</table>
</div>
</div>
<?php
endforeach;
?>
</body>
<script type="text/javascript">
	window.print();
	setTimeout(function(){ window.close(); }, 1e3);
</script>
