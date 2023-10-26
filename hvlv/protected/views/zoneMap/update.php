<h1><?=$model->org->name.':'.$model->zone_id.' Update';?></h1>

<p>Caution, this updates the whole zonemap for <b><?=$model->org->name?></b> with zone id: <b><?=$model->zone_id;?></b>. <a href="<?=$this->createUrl('zoneMap/download', ['org_id' => $model->org_id, 'zone_id' => $model->zone_id]);?>" target="_blank">Please download</a> the current data file for use as cross check and template.</p>
<p>To post postcode list, you may leave "Postcode To" empty, system will group postcodes together to create a postcode range.</p>
<br />
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'zonemap-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
		<label>Excel file</label>
		<input type="file" name="zonemap" />
	</div>
<div class="row buttons">
	<?php echo CHtml::submitButton($this->t('Submit')); ?>
</div>
<?php $this->endWidget(); ?>
</div><!-- form -->
