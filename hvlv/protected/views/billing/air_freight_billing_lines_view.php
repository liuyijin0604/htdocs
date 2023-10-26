
<h1> <?php echo  $pmodel->invoice_no; ?>  - Billing Lines</h1>

<?php
    $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'af-billing-import-list-lines-grid',
        'cssFile' => false,
        'dataProvider'=>$model->search(),
      //  'filter' => $model,
        'columns'=>array(
            'glcode',
            ['name' => 'desc', 'type' => 'raw', 'value' => 'nl2br($data->desc)'],
            'gst',
            'amount'
        ))
    ); ?>

