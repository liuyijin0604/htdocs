<h1><?=$this->t('Send Messages');?></h1>
<div style="width:95%;overflow:auto">
<div style="">
<div class="form" >
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'send-message-form',
	'action'=>$this->createUrl('message/create'),
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
	<div class="row" style="margin-left:1%;">
		<?php echo $form->labelEx($model,'to_id'); ?>
		<?php echo $form->hiddenField($model,'to_id');
			$acname = empty($_GET["tabid"])? 'owner_ac' : $_GET["tabid"].'_owner_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('message/opSuggest'),
				'value' => '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val(""); return false; }',
				),
				'htmlOptions' => array(
					'size' => '50',
					'class' =>'form-control'
				),
		));
		?>

		<?php echo $form->error($model,'to_id'); ?>
	</div>

	<div class="row" style="margin-left:1%;">
		<?php echo $form->labelEx($model,'msg'); ?>
		<?php echo $form->textArea($model,'msg',array('rows'=>6, 'cols'=>50,'class' =>'form-control')); ?>
		<?php echo $form->error($model,'msg'); ?>
	</div>

	<div class="row buttons" style="margin-left:1%;">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
<?php
$model->from_id = Yii::app()->user->id;
$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'id'=>$_GET["tabid"].'-message-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 5),
	'filter'=>$model,
	'columns'=>array(
		'time',
		array('name' => 'to_id', 'value' => '$data->getTo()'),
		array('name' => 'status', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('Message[status]', $model->status, $this->t(Message::$states), array('prompt'=>$this->t('All'))),),
		'msg',
	),
)); ?>
</div>
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('form#send-message-form').on('success', function(){
		$('#notifc').notify({message: {html: 'Send Success'}}).show();
		
		$('#<?=$_GET["tabid"]?>-message-grid').yiiGridView("update");
		return true;
	});
});
</script>