<div style="position:absolute">
<p>Parcels: <b><?=$model->totShipments();?></b> &nbsp; Total Weight: <b><?=$model->totWeight();?>KG</b></p>
</div>
<div style="text-align:right">
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-96px -768px" class="icon"></div> Download</a> &nbsp;
<a href="<?=$this->createUrl('locoConsol/export', array('id'=>$model->id));?>" target="_blank"><div style="background-position:-48px -688px" class="icon"></div> Export Manifest</a>
</div>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createUrl('locoConsol/download', array('id'=>$model->id, 'type'=> 'pdf'));?>" target="_blank">PDF Labels</a></li>
		<li><a href="<?=$this->createUrl('locoConsol/download', array('id'=>$model->id, 'type'=> 'pod'));?>" target="_blank">Manual PODs</a></li>
	</ul>
</div>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'loco-consol-form',
	'enableAjaxValidation'=>false,
));
?>
	<?php echo $form->errorSummary($model); ?>
	
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php echo $form->dropDownList($model,'dpt_id',Org::dptList()); ?>
		<?php echo $form->error($model,'dpt_id'); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'etd'); ?>
		<?php echo $form->textField($model,'etd', array('size' => 12, 'id' => 'etd_'.$_GET["tabid"],'class' => 'date_input')); ?>
		<?php echo $form->error($model,'etd'); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'eta'); ?>
		<?php echo $form->textField($model,'eta', array('size' => 12, 'id' => 'eta_'.$_GET["tabid"],'class' => 'date_input')); ?>
		<?php echo $form->error($model,'eta'); ?>
	</div>

	<div class="row">
		<?php echo $form->radioButtonList($model,'service', Consol::$services, array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp')); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');

});
</script>