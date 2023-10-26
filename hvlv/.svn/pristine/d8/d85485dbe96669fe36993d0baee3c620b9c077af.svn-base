^XA
^PW812
^LL1218
~SD07
^PR4


^LRY
^FO28,28^GB500,80,80^FS
^FO500,28^GB270,80,80^FS
^FO210,60^AQN,38,38^TBN,570,140^FH^FD<?php echo $model['weight']; ?>Kg^FS
^FO610,60^AQN,38,38^TBN,570,140^FH^FD<?php if ( isset($model['shipment']->trans[0]->mdata['fw_dest_code']) ) {
                    echo $model['shipment']->trans[0]->mdata['fw_dest_code'];
                   } else {
                    echo 'SYD';
                  }?>^FS

^FO28,360^GB740,720,1^FS
^FO440,360^GB1,660,1^FS
<?php $lines= floor(strlen($model['cnee_address'])/26);?>

^FO46,380^AQN,28,28TBN,570,140^FH^FD<?php echo ucwords(strtolower($model['cnee_name'])) . (empty($model['cnee_company']) || $model['cnee_name'] == $model['cnee_company'] ? '' : ', ' . substr(ucwords(strtolower($model['cnee_company'])), 0, 33)); ?>^FS
^FO46,405^AQN,28,28^TBN,350,140^FH^FD<?php echo $model['cnee_address']; ?>^FS
^FO46,<?=433+28*$lines?>^AQN,28,28^TBN,570,140^FH^FD<?php echo $model['cnee_state_postcode']; ?>^FS
^FO46,<?=461+28*$lines?>^AQN,28,28^TBN,570,140^FH^FD<?php echo $model['cnee_phone']; ?>^FS

^FO46,700^AQN,30,30^TBN,570,140^FH^FDSpecial Instructions:^FS
^FO46,738^AQN,30,30^TBN,570,140^FH^FDSignature On Delivery Required^FS
^BY2
^FO46,840^AQN,28,24^TBN,570,120^FH^FDInternl use only^FS
^FO46,880^BCN,80,Y,N,N^FD<?=$model["parcel_uni_no"]?>^FS


^FO455,548^AQN,28,28^TBN,570,140^FH^FDReference:^FS
^FO455,576^AQN,28,28^TBN,300,140^FH^FD<?=$model["parcel_cref"]?>^FS
^FO455,606^AQN,28,28^TBN,300,140^FH^FD<?=$model['parcel_task_ref']?>^FS
<?php $name_line= floor(strlen($model['cnor_name'])/25);?>
^FO455,684^AQN,26,26^TBN,300,140^FH^FD<?php echo $model['cnor_name']; ?>^FS
^FO455,<?=714+26*$name_line?>^AQN,26,26^TBN,470,140^FH^FD<?php echo $model['cnor_address']; ?>^FS
^FO455,<?=740+26*$name_line?>^AQN,26,26^TBN,570,140^FH^FD<?php echo $model['cnor_state_postcode']; ?>^FS
^FO455,<?=770+26*$name_line?>^AQN,26,26^TBN,570,140^FH^FD04-16843474^FS
<?php if($model['agent_id']==1253):?>
^FO455,<?=830+26*$name_line?>^AQN,26,26^TBN,570,140^FH^FDQTY:<?php echo $model['parcel_qty']; ?>^FS
<?php endif;?>
^FO535,380
^BXN,4,200
^FDPCAE Express||6C The Crescent Kingsgrove|SYDNEY|2208|||^FS

^FO28,1020^GB740,60,60^FS
^FO60,1033^AQN,23,23^TBN,570,140^FH^FD<?php echo $model['weight']; ?>Kg^FS
^FO330,1033^AQN,23,23^TBN,570,140^FH^FD<?php echo $model['created']; ?>^FS
^FO680,1033^AQN,23,23^TBN,570,140^FH^FD<?php echo $model['parcel_index']; ?>^FS


^BY3
^FO226,150^BCN,150,Y,N,N^FD<?php echo $model['parcel_ref'];?>^FS


^XZ