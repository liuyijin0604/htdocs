<h1><?=$this->t('Create Consol');?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-form',
	'enableAjaxValidation'=>false,
));
?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<?php echo $form->errorSummary($model); ?>
	
	<div class="row">
	<?php
	echo CHtml::label('Receipts','recs');
	$m = new ExDirect('search');
	$m->status = 20;
	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'con-man-grid',
	'cssFile' => false,
	'dataProvider' => $m->search(),
	'filter' => null,
	'summaryText' => false,
	'columns'=>array(
		array('header' => '<input type="checkbox" id="chkbox_all" name="recs_all" checked="checked" />', 'type' => 'raw', 'value' => '"<input class=\"chkbox\" type=\"checkbox\" name=\"recs[]\" value=\"".$data->id."\" checked />"', 'htmlOptions' => array('align' => 'center')),
		'hbn',
		array('name' => 'agent_name', 'value' => 'empty($data->agent)? "" : $data->agent->shortName(2)',),
		array('name' => 'cnor_name', 'value' => '$data->cnor->name',),
		array('name' => 'cnee_name', 'value' => '$data->cnee->name',),
		'state',
		'value',
		'created',
		/*array(
			'class'=>'oButtonColumn',
			'template'=>'{view} {update}',
			'buttons'=>array
			(
				'view' => array(
					'url' => '$data->getFileLink()',
					'imageUrl'=>false,
					'options' => array('class' => 'grid_view_btn', 'target' => '_blank'),
				),
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '"MAN ".$data->id'),
				),
			),
		),*/
	),
)); ?>
</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Create'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('form#consol-form', tab.data('panel')).on('success', function(e, r){
		var url = tab.data('url').replace('ExdirConsol/create','ExdirConsol/update/'+r.id);
		tab.data('url', url).trigger('load');
	});
	
	tab.data('panel').off('click').on('click', 'input#chkbox_all', function(r){
		$('input.chkbox', tab.data('panel')).attr('checked', this.checked);
	});
	
	$('#ExdirConsol_dpt_id', tab.data('panel')).off('change').on('change', function(){
		$('#con-man-grid', tab.data('panel')).yiiGridView('update', {
			data: { 'ExdirConsol[dpt_id]': $(this).val() }
		});
	});
});
</script>