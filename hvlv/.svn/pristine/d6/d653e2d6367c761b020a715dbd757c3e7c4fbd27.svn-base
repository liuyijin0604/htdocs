<h1><?php echo $this->t('Consumable Goods Inventory'); ?></h1>
<?php
echo '<div style="margin-top: -20px; margin-left: 450px;"><a class="tab_link" title="Consumables" href="cgoods/goodsList"><div style="background-position:-16px 0" class="icon"></div>'.$this->t('Manage Consumable Goods').'</a></div>';
?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'cg-inventory-grid',
    'cssFile' => false,
    'dataProvider'=>$model,
    'columns'=>array(
        'name',
        array('header' => 'Inventory','name' => 'total'),
        array(
            'class'=>'CButtonColumn',
            'template'=>'{add}{minus}{history}',
            'buttons'=>array
            (
                'add' => array(
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'url' => 'Yii::app()->createURL("cgoods/addCgInventory", array("id" => $data->id))',
                    'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Add')),
                ),
                'minus' => array(
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'url' => 'Yii::app()->createURL("cgoods/minusCgInventory", array("id" => $data->id))',
                    'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Minus')),
                ),
                'history' => array(
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'url' => 'Yii::app()->createURL("cgoods/historyCgInventory", array("id" => $data->id))',
                    'options' => array('class' => 'jqm_link grid_gallery_btn', 'label'=>$this->t('Minus')),
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
            $('#cg-inventory-grid', panel).yiiGridView('update');
        });
    });
</script>
