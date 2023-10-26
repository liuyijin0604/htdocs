<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'links' => array(
        'Manage Manifest',
    ),
));
?>

<h1><?=$this->t('Manage Manifest');?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'manifest-form',
	'enableAjaxValidation'=>false,
));
$o = Org::model()->findByPk(Yii::app()->user->org);
?>

<div class="row">
    <div class="col col-sm-4">
    <div class="form-group">
	<?php if( !isset($o->extra['warehouse']) || (isset($o->extra['warehouse']) && sizeof($o->extra['warehouse']) > 1 ) ): ?>

		<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php echo $form->dropDownList($model, 'dpt_id', $o->getWarehouseList(), array('empty' => 'Select One')); ?>
		<?php echo $form->error($model,'dpt_id'); ?>

	<?php else:
	$model->dpt_id = $o->extra['warehouse'][0];
	echo $form->hiddenField($model, 'dpt_id');
	endif; ?>
    </div>
    </div>
    </div>


 <div class="form-group">
            <?php
            echo CHtml::label('Manifests','manis');
            $m = new Manifest('search');
            if(isset($_GET['Manifest'])) $m->attributes=$_GET['Manifest'];
            $m->type = 10;  // Import lodgement
            $m->consol_id = 0; // only for not consoled
            $m->dpt_id = ( !isset($o->extra['warehouse']) || ( isset($o->extra['warehouse']) && sizeof($o->extra['warehouse']) > 1 ) ) ? (empty($_GET['ImcoConsol']['dpt_id'])? -1 : $_GET['ImcoConsol']['dpt_id']) : $o->extra['warehouse'][0];

            // check for paid manifest only
            $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'con-man-grid',
            'cssFile' => false,
            'dataProvider' => $m->search(true,15,false),
            'filter' => $m,
            'summaryText' => false,
            'columns'=>array(
                array('header' => 'ID', 'name' => 'id'),
                array('header' => 'Client','name' => 'fwd_id' ,'type'=>'raw','value' => 'empty($data->owner)? "" : $data->owner->name'),
                array('header' => 'Status','name' => 'status', 'type' => 'raw', 'value' => '$data->getStatus()',
                    'filter'=>CHtml::dropDownList(get_class($m).'[status]', $m->status, $this->t($m->statusList()), array('prompt' => $this->t('All'), 'class' => 'form-control'))),
                array('header' => 'Packages', 'name' => 'allshipments','type' => 'raw','value' => '$data->totPacks()'),
                array('header' => 'Weight', 'name' => 'totalWeight','type' => 'raw','value' => '$data->totWeight()'),
                array('header' => 'CBM', 'name' => 'totalCBM','type' => 'raw','value' => '$data->totCBM()'),
                // array('header' => 'Value', 'name' => 'total'),
                array('header' => 'Created', 'name' => 'created'),
                array(
                    'class'=>'oButtonColumn',
                    'template'=>'{mupdate}',
                    'buttons'=>array
                    (
                        'mupdate' => array(
                            'imageUrl'=>false,
                           // 'visible'=> '',
                            'label' => 'Update',
                            'url' => 'Yii::app()->createUrl("consol", ["mupdate" => $data->id])',
                            'options' => array('class' => 'tab_link grid_edit_btn','target' => '_blank'),
                        ),
                    ),
                ),
            ),
        )); ?>

    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php ob_start(); ?>

<script type="text/javascript">
$(function(){
	$('#ImConsol_dpt_id').off('change').on('change', function(){
		$('#con-man-grid').yiiGridView('update', {
			data: { 'ImcoConsol[dpt_id]': $(this).val() }
		});
	});
});
</script>

<?php $this->registerJS(ob_get_clean(),8); ?>