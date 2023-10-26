
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'erp-driverreport-form',
    'enableAjaxValidation'=>false,
)); ?>

<div class="row" style="margin-top: 50px;">
    <div class="rowcol">
        <?php echo CHtml::label('Driver','to_driver_id');; ?>
        <?php echo CHtml::dropDownList('did', $model['did'],ErpProductRoutes::driverList());?>
    </div>

    <div class="rowcol">
        <?php echo CHtml::label('To','fordriver'); ?>
        <?php echo CHtml::textField('driver_to_date',$model['driver_end_time'],['class' => 'date_input']); ?>
        <?php echo CHtml::button($this->t('Refresh'),array('onclick' => 'driversend();')); ?>
    </div>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'erp-driverreport-grid',
    'cssFile' => false,
    'dataProvider'=>$model['driverdata_provider'],
    'columns'=>array(
        'id',
        'name',
        'total',
        'consumed',
        'left'
    ),
)); ?>

<?php $this->endWidget(); ?>
