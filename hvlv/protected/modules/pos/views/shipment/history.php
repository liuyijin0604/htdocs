<?php foreach ($batches as $batch) { ?>
	<p><a href="<?=$this->createUrl('shipment/downloadManifest', ['date' => $batch['batch']])?>" target="_blank"><?php echo $batch['batch']; ?>.xlsx</a> <?=count(ExParcel::model()->findAll('batch = :batch AND agent_id = :o', array(':batch' => $batch['batch'], ':o' => Yii::app()->user->org))) ?> shipments</p>
<?php } ?>