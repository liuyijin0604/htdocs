<h1><?=$this->t('Consumables Order Details');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'cg-order-details-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(),
    'columns'=>array(
        'cg_id',
       	array('header' => 'Name' ,'name' => 'cg_id', 'value' => 'empty($data->cg) ? "" : $data->cg->name'),
        'qty',
    ),
));
?>
