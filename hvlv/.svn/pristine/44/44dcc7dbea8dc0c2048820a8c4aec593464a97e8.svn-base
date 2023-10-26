<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'refmap-form',
	'enableAjaxValidation'=>false,
	'action' => $this->createUrl('exParcel/refMap'),
));
$map = [];
foreach($rmap as $k=>$v){
	$map[] = $k."\t".$v;
}
?>
	<div class="row">
		<textarea name="map" style="width:100%;min-height:400px;"><?=implode("\n", $map);?></textarea>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->