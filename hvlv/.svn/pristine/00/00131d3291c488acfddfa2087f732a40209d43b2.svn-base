<h1><?=$this->t('Create Import Consol');?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-form',
	'enableAjaxValidation'=>false,
));
$model->pol = 'CNSHA';
$model->pod = 'AUSYD';
?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<?php echo $form->errorSummary($model); ?>
	
	<div class="row rowcol">
		<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php echo $form->dropDownList($model,'dpt_id',Org::dptList(), array('empty' => 'Select One')); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'awb'); ?>
		<?php echo $form->textField($model,'awb',array('size'=>15,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'airline'); ?>
		<?php echo $form->textField($model,'airline',array('size'=>15,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'flight'); ?>
		<?php echo $form->textField($model,'flight',array('size'=>15,'maxlength'=>50)); ?>
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
	$m->type = 20;
	$m->status = 10;
	$m->dpt_id = empty($_GET['ImcoConsol']['dpt_id'])? -1 : $_GET['ImcoConsol']['dpt_id'];
	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'con-man-grid',
	'cssFile' => false,
	'dataProvider' => $m->search(),
	'filter' => null,
	'summaryText' => false,
	'columns'=>array(
		array('header' => '<input type="checkbox" id="chkbox_all" name="recs_all" checked="checked" />', 'type' => 'raw', 'value' => '$data->owner->overCreditLimit()? "<input type=\"checkbox\" disabled />" : "<input class=\"chkbox\" type=\"checkbox\" name=\"recs[]\" value=\"".$data->id."\" checked />"', 'htmlOptions' => array('align' => 'center')),
		array('name' => 'fwd_id', 'type' => 'raw', 'value' => '$data->owner->name.($data->owner->overCreditLimit()? " <span style=\"color:#c00\">(Over Limit)</span>" : "")'),
		array('header' => 'File', 'type' => 'raw', 'value' => '"<a href=\"".$data->getFileLink()."\" target=\"_blank\">".$data->getFileName()."</a>"',),
		array('header' => 'Packs', 'type' => 'raw', 'value' => '$data->totPacks()'),
		array('header' => 'Weight', 'type' => 'raw', 'value' => '$data->totWeight()'),
		array('header' => 'CBM', 'type' => 'raw', 'value' => '$data->TotCBM()'),
		'created',
		array(
			'class'=>'oButtonColumn',
			'template'=>'{delmani}',
			'buttons'=>array
			(
				'delmani' => array(
					'imageUrl'=>false,
					'visible'=>'true',
                    'url' => 'Yii::app()->createUrl("imcoConsol/delmani", ["id" => $data->id])',
                    'label' => 'Delete',
					'options' => array('class' => 'tab_link grid_edit_btn delete_mani_btn', 'title' => '"MAN ".$data->id'),
				),
			),
		)
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
		var url = tab.data('url').replace('imcoConsol/createManif','imcoConsol/update/'+r.id);
		tab.data('url', url).trigger('load');
	});
	
	tab.data('panel').off('click').on('click', 'input#chkbox_all', function(r){
		$('input.chkbox', tab.data('panel')).attr('checked', this.checked);
	});

    tab.data('panel').off('click').on('click','.delete_mani_btn',function(r){
       if ( !confirm('Are you sure delete the manifest ?') ) {
           r.preventDefault();
           r.stopPropagation();
       }
    });

	$('#ImcoConsol_dpt_id', tab.data('panel')).off('change').on('change', function(){
		$('#con-man-grid', tab.data('panel')).yiiGridView('update', {
			data: { 'ImcoConsol[dpt_id]': $(this).val() }
		});
	});
});
</script>