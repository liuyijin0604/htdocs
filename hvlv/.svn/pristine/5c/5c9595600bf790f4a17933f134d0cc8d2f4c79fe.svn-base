<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'amazon_plan_form',
	'enableAjaxValidation'=>false,
)); ?>
	<div class="row">
	<div class='rowcol .col-block'>
		<div class="row">
			<?php echo $form->labelEx($model,'amazon_booking_time'); ?>
			<?php echo CHtml::textField('amazon_booking_time','',array('size' => 20,'class'=>"date_input",'id'=>'amazon_plan_time1'.$_GET['tabid'])); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model,'pallet'); ?>
			<?php echo CHtml::numberField('pallet',0); ?>
			<?php echo CHtml::hiddenField('dpt_id',$dptId); ?>
		</div>
	</div>

	
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php
	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
	'id'=>$_GET["tabid"].'_cargo_process_grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>[		
		array('name' => 'amazon_booking_time'),
		array('name' => 'pallet'),
		array('name' => 'user_id'),
		array('name' => 'create'),

		['class'=>'oButtonColumn',
			'template'=>'{delete}',
			'buttons'=>[
				'delete' => [
					'url'=>' Yii::app()->createURL("importsAmazon/deleteAmazonPlan")."?id=".$data->id',
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
				]
			],
		]
	],
]);
?>