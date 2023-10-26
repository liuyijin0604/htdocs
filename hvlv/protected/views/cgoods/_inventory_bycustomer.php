<br>
<h1> Customer: <?php echo $org->name . '('. $org->id .')' ;?> </h1>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'cg-customer-inventory-grid',
    'cssFile' => false,
    'htmlOptions'=>array('style'=>'width: 70%'),
    'dataProvider'=>$cinventory,
    'columns'=>array(
        array('header' => 'Month','name' => 'month'),
        array('header' => 'Name','name' => 'name'),
        array('header' => 'Consumed','name' => 'total','type' => 'raw','value' => 'abs($data->total)')
    ),
)); ?>
