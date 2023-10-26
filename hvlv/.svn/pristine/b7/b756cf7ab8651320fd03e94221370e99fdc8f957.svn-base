^XA
^PW812
^LL1218
~SD07
^PR4
<?php
$endTrans = end($label->trans);
?>

^LRY
^FO28,28^GB450,80,80^FS
^FO468,28^GB320,80,80^FS
^FO28,60^AQN,38,38^FB450,1,0,C^FH^FD<?=empty($endTrans->mdata['fw_satchelSize'])? $label->weight.'Kg' : 'LETTER BOX';?>^FS
^FO478,60^AQN,38,38^FB310,1,0,C^FH^FD<?php
if (isset($endTrans->mdata['fw_dest_code'])) {
	echo $endTrans->mdata['fw_dest_code'];
}elseif(isset($endTrans->mdata['fw_toRf'])){
	echo trim($endTrans->mdata['fw_toRf'].' '.$endTrans->mdata['fw_subDepotCode']);
	if(isset($endTrans->mdata['fw_toCf'])) echo ' '.$endTrans->mdata['fw_toCf'];
} else {
	echo 'SYD';
}?>^FS

^FO28,360^GB740,720,1^FS
^FO440,360^GB1,660,1^FS
^FO46,380^A0N,32,32^TBN,390,300^FH^FD<?php echo ucwords(strtolower($label->cnee->name)) . (empty($label->cnee->company) || $label->cnee->name == $label->cnee->company ? '' : ', ' . substr(ucwords(strtolower($label->cnee->company)), 0, 35)); ?>_0D_0A<?php echo str_replace("\n", "_0D_0A", $label->cnee->apAddress()); ?>_0D_0A<?php echo $label->cnee->tel; ?>^FS

^FO46,700^AQN,30,30^TBN,570,140^FH^FDSpecial Instructions:^FS
^FO46,738^AQN,30,30^TBN,570,140^FH^FD<?=empty($endTrans->mdata['fw_satchelSize'])? 'Signature On Delivery Required' : '';?>^FS
^BY2
^FO46,840^AQN,28,24^TBN,570,120^FH^FDInternl use only^FS
^FO46,880^BCN,80,Y,N,N^FD<?=$label->hbn;?>^FS


^FO455,548^AQN,28,28^TBN,570,140^FH^FDReference:^FS
^FO455,576^AQN,28,28^TBN,300,140^FH^FD<?=$label->hbn?>^FS
^FO455,606^AQN,28,28^TBN,300,140^FH^FD<?=$label->cref?>^FS
<?php
	$name=$label->cnor->name;
	$org=$label->agent;
	if (!empty($org->extra['delivery_label_name'])) {
		$name=$org->extra['delivery_label_name'];
	}
	$warehouseAddress = $name.'_0D_0A6C The Crescent, Kingsgrove NSW 2208_0D_0ABuyer is Not to return in person';
$sort = FastwayAPI::rf2sort($endTrans->mdata['fw_toRf'], $endTrans->mdata['fw_origin']);
$fw_sort_map = ['METRO' => 1, 'NSW' => 2, 'VIC' => 3, 'QLD' => 4, 'SA/WA' =>5, 'TAS' => 6];
$sort .= isset($fw_sort_map[$sort])? '   '.$fw_sort_map[$sort] : '';
?>
^FO455,684^AQN,26,26^TBN,300,180^FH^FD<?php echo $warehouseAddress; ?>^FS
^FO535,380
^BXN,4,200
<?php
$addr = str_split($label->cnee->address, 20);
$dm_data = [substr(empty($label->cnee->company)? $label->cnee->name : $label->cnee->company, 0, 35), '', $addr[0], empty($addr[1])? '' : $addr[1], $label->cnee->suburb, '', $label->cnee->postcode, $label->cnee->tel, '', substr($label->cnee->name, 0, 25)];
?>
^FD<?=implode('|', $dm_data);?>^FS

^FO28,1020^GB740,60,60^FS
^FO60,1033^AQN,23,23^TBN,570,140^FH^FD<?=empty($endTrans->mdata['fw_satchelSize'])? $label->weight.'Kg' : 'LBX';?>^FS
^FO330,1033^AQN,23,23^TBN,570,140^FH^FD<?php echo $label->created; ?>^FS
^FO630,1033^A0N,32,32^TBN,570,140^FH^FD<?=empty($endTrans->mdata['fw_toRf'])? '1 / 1' : $sort; ?>^FS

^BY3
^FO180,150^BCN,150,Y,N,N^FD<?php echo $label->ref;?>^FS

^XZ