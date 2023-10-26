<h1><?=$this->t('Update Manifest');?> - <?php echo $model->awb; ?></h1>

<div style="text-align:right;"><a class="export" href="<?=$this->createUrl('manifest/export', array('id'=>$model->id));?>" target="_blank"><div style="background-position:-48px -688px" class="icon"></div> <?=$this->t('Delivery Manifest');?></a> &nbsp; <a class="print" href="<?=$this->createUrl('manifest/sac', array('id'=>$model->id));?>" target="_blank"><div style="background-position:-48px -688px" class="icon"></div> <?=$this->t('SAC Manifest');?></a> &nbsp; <a class="print" href="<?=$this->createUrl('manifest/msac', array('id'=>$model->id));?>" target="_blank"><div style="background-position:-48px -688px" class="icon"></div> <?=$this->t('SAC Manifest(Multi)');?></a></div>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'manifest-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<div class="row">
		<?php echo $form->labelEx($model,'fwd_id'); ?>
		<?php echo $model->agent->name; ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'awb'); ?>
		<?php echo $form->textField($model,'awb',array('size'=>25,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'flight'); ?>
		<?php echo $form->textField($model,'flight',array('size'=>25,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'pol'); ?>
		<?php echo $form->dropDownList($model,'pol', AppHelper::setting2List('pols')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'pod'); ?>
		<?php echo $form->dropDownList($model,'pod', AppHelper::setting2List('pods')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'eta'); ?>
		<?php echo $form->textField($model,'eta', array('size' => 12, 'id' => 'eta_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row">
		<label for="manifest">Tracking Manifest - <small>.csv/.xls/.xlsx File</small></label>
		<input type="file" name="tm" id="manifest" />
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Update')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->