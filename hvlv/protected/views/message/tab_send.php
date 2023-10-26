<div style="height:800px;overflow:auto">
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'send-message-form',
	'action'=>$this->createUrl('message/create'),
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
	<div class="row">
		<?php echo $form->labelEx($model,'to_id'); ?>
		<?php echo $form->hiddenField($model,'to_id');
			$acname = empty($_GET["tabid"])? 'owner_ac' : $_GET["tabid"].'_owner_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('org/opSuggest'),
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
				),
		));
		?>

		<?php echo $form->error($model,'to_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'msg'); ?>
		<?php echo $form->textArea($model,'msg',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'msg'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
<?php
$model->from_id = Yii::app()->user->id;
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'-message-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 10),
	'filter'=>$model,
	'columns'=>array(
		'time',
		array('name' => 'to_id', 'value' => '$data->getTo()'),
		array('name' => 'status', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('Message[status]', $model->status, $this->t(Message::$states), array('prompt'=>$this->t('All'))),),
		array('name'=>'msg','type'=>'raw','value'=>'"<p style=\"word-wrap:break-word;width:200px;\">".$data->msg."</p>"'),
		['class'=>'oButtonColumn',
            'template'=>'{Open Task}',
            'buttons'=>[
                'Open Task' => [
                    'url'=>'Yii::app()->createURL("tlaTask/operation")."?taskNo=".$data->getTaskNo()',
                    'imageUrl'=>false,
                    'visible'=>'$data->isTask()',
                    'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Open Task'), 'title' => 'Open Task'],
                ]
            ],
        ]
	),
)); ?>
</div>
<script type="text/javascript">
$(function(){
	var win = $('#jqmw2_<?=$_GET["tabid"];?>');
	$('form#send-message-form', win).on('success', function(){
		var t = $('.ui-tabs', win);
		t.tabs('load', t.tabs('option','active'));
	});
});
</script>