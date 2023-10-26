<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'ex-prodb-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->dropDownList($model, 'type', $this->t(ExProdb::$types), array('empty' => $this->t('Select One'))); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>200)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'name_zh'); ?>
		<?php echo $form->textField($model,'name_zh',array('size'=>20,'maxlength'=>200)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'brand'); ?>
		<?php echo $form->textField($model,'brand',array('size'=>15,'maxlength'=>200)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'model'); ?>
		<?php echo $form->textField($model,'model',array('size'=>15,'maxlength'=>200)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'code'); ?>
		<?php echo $form->textField($model,'code',array('size'=>15,'maxlength'=>30)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'weight'); ?>
		<?php echo $form->textField($model,'weight',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'unit'); ?>
		<?php echo $form->textField($model,'unit',array('size'=>4,'maxlength'=>20)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'price'); ?>
		<?php echo $form->textField($model,'price',array('size'=>6,'maxlength'=>10)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'hs'); ?>
		<?php echo $form->textField($model,'hs',array('size'=>12,'maxlength'=>30)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'hs2'); ?>
		<?php echo $form->textField($model,'hs2',array('size'=>12,'maxlength'=>30)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'tax'); ?>
		<?php echo $form->textField($model,'tax',array('size'=>6,'maxlength'=>10)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'sku'); ?>
		<?php echo $form->textField($model,'sku',array('size'=>12,'maxlength'=>30)); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('面单品名', 'labelname');?>
		<?php echo CHtml::textField('meta[labelname]', @$model->mdata['labelname'], array('size'=>25)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'tag'); ?>
		<?php echo $form->textArea($model,'tag',array('rows'=>3, 'cols'=>50)); ?>
		<p><small>Please use , to separate tags</small></p>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'note'); ?>
		<?php echo $form->textArea($model,'note',array('rows'=>3, 'cols'=>50)); ?>
	</div>
	
	<?php if(!$model->isNewRecord): ?>
	<div class="row rowcol">
		<label>POC Prices</label>
		<?php
		$pps = new ExProdbPrice('search');
		$pps->pid = $model->id;
		$pps->type = 10;
		$pps->status = 1;
		$cs = ExChannel::model()->findAll('status = 50');
		$pocs = [];
		foreach($cs as $c){
			$pocs[] = $c->code;
		}
		$ec=new CDbCriteria;
		$ec->addInCondition("poc", $pocs);

		$this->widget('application.extensions.editablegrid.CEditableGridView', array(
			'id'=>$_GET["tabid"].'_prod_poc-grid',
			'cssFile' => false,
			'dataProvider'=>$pps->search(true, 30, $ec),
			'formUrl' => $this->createUrl('exprod/pocPriceGrid', array('id'=>$model->id)),
			'filter'=>null,
			'summaryText' => '',
			'editable' => true,
			'showQuickBar' => false,
			'afterSave' => "function(r){
				if(r.done == true){
					myApp.notice(r.msg, 5000);
				}else{
					myApp.alert(r.msg, false);
				}
				return r.done;
			}",
			'columns'=>array(
				array('name' => 'poc', 'class' => 'CEditableColumn', 'type' => 'list', 'filter' => ExChannel::getPocs(true)),
				array('name' => 'price', 'class' => 'CEditableColumn'),
				array('name' => 'name', 'class' => 'CEditableColumn'),
				array('name' => 'sn', 'class' => 'CEditableColumn'),
				array('name' => 'hs', 'class' => 'CEditableColumn'),
				array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save}'),
			),
		));
		?>
	</div>
	<?php endif; ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?> &nbsp;
		<?php if(!$model->isNewRecord): ?>
		<a class="jqm_link dupli" href="<?=$this->createUrl('exprod/copy', array('id'=>$model->id));?>">Copy</a>
		<?php endif; ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->