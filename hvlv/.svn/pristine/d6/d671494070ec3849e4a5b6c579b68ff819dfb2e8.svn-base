<h1><?=$this->t('Batch Tag Update');?></h1>
<div class="form" style="display:none">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'ex-prodb-tag-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'tag'); ?>
		<?php echo CHtml::textArea('tags', '', array('rows'=>3, 'cols'=>50)); ?>
		<?php echo CHtml::hiddenField('otags', ''); ?>
		<p><small>Please use , to separate tags</small></p>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'ex-prodb-tag-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'afterAjaxUpdate' => 'function(id, data){ $("#'.$_GET["tabid"].'").trigger("gridUpdated");}',
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'type', 'value' => '$data->getType()', 
			'filter'=>CHtml::dropDownList('ExProdb[type]', $model->type, $this->t(ExProdb::$types), array('prompt'=>$this->t('All'))),),
		'name_zh',
		'brand',
		'code',
		'price',
		'weight',
		'tag',
		/*
		'unit',
		'tag',
		'note',
		*/
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>array
			(
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '"Product ".$data->name'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	var upform = $('#ex-prodb-tag-form', panel);
	tab.on('onOpen', function(){
		$('#ex-prodb-tag-grid', panel).yiiGridView('update');
	});
	upform.data('action', upform.attr('action'));
	tab.on('gridUpdated', function(evt){
		var q = $('.filters input, .filters select', panel).serialize();
		upform.attr('action', upform.data('action') + '&' + q);
		$.post(upform.attr('action'), {'get' : 'tags'}, function(r){
			$('textarea#tags', panel).val(r);
			$('input#otags', panel).val(r);
		});
		$('div.form', panel).slideDown();
	});

	$('form#ex-prodb-tag-form', panel).on('success', function(e, r){
		tab.trigger('onOpen');
	});
});
</script>