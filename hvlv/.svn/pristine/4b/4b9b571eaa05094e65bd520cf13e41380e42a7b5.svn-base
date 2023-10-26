<h1><?php echo $this->t('Consumables Vendors'); ?></h1>

<?php echo '<div style="margin-top: -20px; margin-left: 250px;"><a class="jqm_link" href="cgoods/createVendor"><div style="background-position:-16px 0" class="icon"></div>'.$this->t('Add New Vendor').'</a></div>';?>


<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'cg-vendors-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        'name',
        'product_name',
        'product_code',
        'min_qty',
        'price',
        'valid_from_time',
        'valid_to_time',
        'notes',
        array(
            'class'=>'CButtonColumn',
            'template'=>'{update}',
            'buttons'=>array
            (
                'update' => array(
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'url' => 'Yii::app()->createURL("cgoods/updateVendor", array("id" => $data->id))',
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
            $('#cg-vendors-grid', panel).yiiGridView('update');
        });
    });
</script>
