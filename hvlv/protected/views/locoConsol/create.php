<h1><?=$this->t('Create Consol');?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-form',
	'enableAjaxValidation'=>false,
));
?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<?php echo $form->errorSummary($model); ?>
	
	<div class="row rowcol">
		<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php echo $form->dropDownList($model,'dpt_id',Org::dptList(), array('empty' => 'Select One')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'etd'); ?>
		<?php echo $form->textField($model,'etd', array('size' => 12, 'id' => 'etd_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'eta'); ?>
		<?php echo $form->textField($model,'eta', array('size' => 12, 'id' => 'eta_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>
	
	<div class="row">
	<?php
	echo CHtml::label('Receipts','recs');
	$m = new Manifest('search');
	$m->type = 220;
	$m->status = 10;
	$m->dpt_id = empty($_GET['LocoConsol']['dpt_id'])? -1 : $_GET['LocoConsol']['dpt_id'];
	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'con-man-grid',
	'cssFile' => false,
	'dataProvider' => $m->search(),
	'filter' => null,
	'summaryText' => false,
	'columns'=>array(
		array('header' => '<input type="checkbox" id="chkbox_all" name="recs_all" checked="checked" />', 'type' => 'raw', 'value' => '"<input class=\"chkbox\" type=\"checkbox\" name=\"recs[]\" value=\"".$data->id."\" checked />"', 'htmlOptions' => array('align' => 'center')),
		array('name' => 'fwd_id', 'value' => '$data->owner->name'),
		array('header' => 'File', 'type' => 'raw', 'value' => '"<a href=\"".$data->getFileLink()."\" target=\"_blank\">".$data->getFileName()."</a>"',),
		array('header' => 'Packs', 'type' => 'raw', 'value' => '$data->totPacks()'),
		array('header' => 'Weight', 'type' => 'raw', 'value' => '$data->totWeight()'),
		array('header' => 'CBM', 'type' => 'raw', 'value' => '$data->TotCBM()'),
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
		var url = tab.data('url').replace('locoConsol/create','locoConsol/update/'+r.id);
		tab.data('url', url).trigger('load');
	});
	
	tab.data('panel').off('click').on('click', 'input#chkbox_all', function(r){
		$('input.chkbox', tab.data('panel')).attr('checked', this.checked);
	});
	
	$('#LocoConsol_dpt_id', tab.data('panel')).off('change').on('change', function(){
		$('#con-man-grid', tab.data('panel')).yiiGridView('update', {
			data: { 'LocoConsol[dpt_id]': $(this).val() }
		});
	});
});
</script>