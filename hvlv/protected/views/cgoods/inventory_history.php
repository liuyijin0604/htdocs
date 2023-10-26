<h1><?php echo $this->t('Inventory History'); ?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'cg-inventory-history-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(),
    'columns'=>array(
        'added_time',
        'qty',
        array('header' => 'Operator','type' => 'raw','value' => '!empty($data->user) ? $data->user->name : ""'),
        'note'
    ),
)); ?>
