<h1><?php echo $this->t('Consumable Goods'); ?></h1>
<?php
echo '<div style="margin-top: -20px; margin-left: 250px;"><a class="jqm_link" href="cgoods/createConsumableGoods"><div style="background-position:-16px 0" class="icon"></div>'.$this->t('Add New Item').'</a></div>';
?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'cg-products-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        'name',
       // array(
         //   'name' =>  'vendor_id',
          //  'value' => '$data->getVendorName()'
        //),
        'buy_price',
      //  'sale_price',
        'barcode',
        'added_time',
        'order_cycle',
        'notes',
        array(
            'class'=>'CButtonColumn',
            'template'=>'{update}',
            'buttons'=>array
            (
                'update' => array(
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'url' => 'Yii::app()->createURL("cgoods/updateCgoods", array("id" => $data->id))',
                    'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update')),
                ),
            ),
        ),
    ),
)); ?>
<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        tab.bind('onOpen', function(){
            $('#cg-products-grid', panel).yiiGridView('update');
        });
    });
</script>
