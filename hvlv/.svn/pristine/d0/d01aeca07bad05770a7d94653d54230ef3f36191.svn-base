
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'erp-whreport-form',
    'enableAjaxValidation'=>false,
)); ?>

<div class="row">
    <div class="rowcol">
    <?php echo CHtml::label('Warehouse','warehouse_id');; ?>
    <?php echo CHtml::dropDownList('warehouse_id', $model['whid'],ErpProductRoutes::warehouseList());?>
    </div>

    <div class="rowcol">
        <?php echo CHtml::label('To','forwarehouse'); ?>
        <?php echo CHtml::textField('wh_to_date',$model['end_time'],['class' => 'date_input']); ?>
        <?php echo CHtml::button($this->t('Refresh'),array('onclick' => 'whsend();')); ?>
    </div>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'erp-whreport-grid',
    'cssFile' => false,
    'dataProvider'=>$model['whdata_provider'],
    'columns'=>array(
        'id',
        'name',
        'total',
        'consumed',
        'left'
    ),
)); ?>

<?php $this->endWidget(); ?>
