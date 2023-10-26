<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'links' => array(
        'Update Consol',
    ),
));
?>


<h1><?=$this->t('Update Consol');?> <?php echo $model->no; ?> - <i><?=$model->getStatus();?></i></h1>
<div style="text-align:right;">
<a href="#" data-dropdown="#dropdown-1"><div style="background-position:-48px -688px" ></div> <a class="export" href="<?=$this->createUrl('consol/export', array('id'=>$model->id));?>" target="_blank"><div style="background-position:-48px -688px" class="icon"></div> <?=$this->t('Export');?></a></a>
</div>

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

    <div class="row">
        <div class="col col-sm-4">
            <div class="form-group">
		<?php echo CHtml::label('Depot *','consol'); ?>
        <?php echo CHtml::textField('dpt_name',$model->depot->name,array('size'=>40,'maxlength'=>80,'disabled' => 'disabled')); ?>
		<?php echo $form->error($model,'dpt_id'); ?>
	</div></div></div>

    <div class="row">
        <div class="col col-sm-3">
            <div class="form-group">
		<?php echo $form->labelEx($model,'awb'); ?>
		<?php echo $form->textField($model,'awb',array('size'=>15,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'awb'); ?>
	</div></div>

            <div class="col col-sm-3">
                <div class="form-group">
		<?php echo $form->labelEx($model,'airline'); ?>
		<?php echo $form->textField($model,'airline',array('size'=>15,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'airline'); ?>
	</div></div>

                <div class="col col-sm-3">
                    <div class="form-group">
		<?php echo $form->labelEx($model,'flight'); ?>
		<?php echo $form->textField($model,'flight',array('size'=>15,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'flight'); ?>
	</div></div>
</div>

    <div class="row">
        <div class="col col-sm-8">
            <div class="row">
                <div class="col col-sm-3">
                    <div class="form-group">
		<?php echo $form->labelEx($model,'pol'); ?>
		<?php echo $form->dropDownList($model,'pol', AppHelper::setting2List('pols')); ?>
		<?php echo $form->error($model,'pol'); ?>
	</div></div>

                    <div class="col col-sm-3">
                        <div class="form-group">
		<?php echo $form->labelEx($model,'pod'); ?>
		<?php echo $form->dropDownList($model,'pod', AppHelper::setting2List('pods')); ?>
		<?php echo $form->error($model,'pod'); ?>
	</div></div>

                        <div class="col col-sm-3">
                            <div class="form-group">
		<?php echo $form->labelEx($model,'etd'); ?>
		<?php echo $form->textField($model,'etd', array('size' => 12, 'id' => 'etd','class' => 'date_input')); ?>
		<?php echo $form->error($model,'etd'); ?>
	</div></div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'eta'); ?>
		<?php echo $form->textField($model,'eta', array('size' => 12, 'id' => 'eta','class' => 'date_input')); ?>
		<?php echo $form->error($model,'eta'); ?>
	</div></div>
	</div></div></div>

<div class="form-group">
	<?php if($model->status < 20): ?>
	<!--
		<div style="position: absolute; right: 190px; margin-top:-5px;">
			<a href="#" data-dropdown="#dropdown-3"><div class="icon" style="background-position:-192px -80px"></div><?=$this->t('Add/Remove shipments');?></a>
<div id="dropdown-3" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-left">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createURL('consol/addParcel', array('id' => $model->id));?>" class="jqm_link" target="_blank"><?=$this->t('Add Shipments');?></a></li>
		<li><a href="<?=$this->createUrl('consol/bulkParcels', array('id' => $model->id));?>" class="jqm_link" target="_blank"><?=$this->t('Bulk Actions');?></a></li>
	</ul>
</div>
		</div> -->

	<?php
	 endif;
	echo CHtml::label('Shipments','recs');
	$m = new ImParcel('search');
	$m->consol_id = $model->id;
	//$m->dpt_id = empty($_GET['Console']['dpt_id'])? -1 : $_GET['Console']['dpt_id'];
	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'con-par-grid',
	'cssFile' => false,
	'dataProvider' => $m->search(),
	'filter' => null,
	'columns'=>array(
		array('header' => 'AWB', 'name' => 'hbn'),
     //   array('header' => 'Client','name' => 'agent_id' ,'type'=>'raw','value' => '$data->agent->name'),
		array('header' => 'Goods', 'name' => 'goods'),
		array('header' => 'Packages', 'name' => 'pkg'),
		array('header' => 'Value', 'name' => 'dvalue'),
		array('header' => 'Weight', 'name' => 'weight'),
		array('header' => 'CBM', 'name' => 'cbm'),
		array('header' => 'State', 'name' => 'state'),
		array('header' => 'Post Code', 'name' => 'postcode')
		/*array(
			'class'=>'oButtonColumn',
			'deleteConfirmation'=>'Are you sure to remove this parcel from consol.?',
			'template'=>'{delete} {label}',
			'buttons'=>array(
				'delete' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'grid_delete_btn', 'label'=>$this->t('Remove from Consol.')),
					'visible'=> $m->status < 20,
					'url' => 'Yii::app()->createURL("consol/removeParcel", array("id" => $data->id))',
				),
				'label' => array(
					'imageUrl' => false,
					'label' => $this->t('Label'),
					'options' => array('class' => 'grid_file_btn export_btn', 'target' => '_blank'),
					'url' => 'substr(Yii::app()->createURL("parcel/print", array("hbn" => $data->id, "size"=> "A6")), 0, -5)',					
				),
			),
		),*/
	),
)); ?>
</div>

<p>Total Weight: <b><?=$model->totWeight();?>KG</b></p>

<p>Total CBM: <b><?=$model->totCBM();?>M<sup>3</sup></b></p>


<?php if($model->status < 20): ?>

<div class="form-group">

		<?php echo CHtml::checkbox('confirm-consol'); ?>
		<?php echo CHtml::label('Confirm Consol','confirm-consol', array('class' => 'radio_label')); ?>
	</div>

<div class="form-group">
    <div class="buttons">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>
	</div>
<?php endif; ?>

<?php $this->endWidget(); ?>

</div><!-- form -->


<?php ob_start(); ?>

<script type="text/javascript">
$(function(){
	$('#con-par-grid').yiiGridView('update');
	$(document).off('click','#con-par-grid a.grid_delete_btn');
});
</script>
<?php $this->registerJS(ob_get_clean(),8); ?>