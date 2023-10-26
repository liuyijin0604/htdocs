<?php
/* @var $this ErpProducts */

$this->breadcrumbs=array(
    'Erp Products'=>array('/erpProductRoute'),
    'List',
);
?>
<h1><?php echo $this->t('ERP Product Routes'); ?></h1>

<?php echo '<div class="controller-top-bar">';?>
<?php echo '<a class="jqm_link" href="ErpProductRoute/input"><div class="icon"></div>'.$this->t('INPUT').'</a>'; ?>
<?php echo '<a class="jqm_link" href="ErpProductRoute/output"><div class="icon"></div>'.$this->t('OUTPUT(to driver)').'</a>'; ?>
<?php echo '<a class="jqm_link" href="ErpProductRoute/outputInside"><div class="icon"></div>'.$this->t('OUTPUT(internal use)').'</a>'; ?>
<?php echo '<a class="jqm_link" href="ErpProductRoute/outputAgent"><div class="icon"></div>'.$this->t('OUTPUT(to agent)').'</a>'; ?>
<?php echo '<a class="jqm_link" href="ErpProductRoute/return"><div class="icon"></div>'.$this->t('RETURN').'</a>'; ?>
<?php echo '<a class="jqm_link" href="ErpProductRoute/virtual"><div class="icon"></div>'.$this->t('VIRTUAL').'</a>'; ?>
<?php echo '</div>';?>


<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'erp-product-route-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        array(
            'name' =>  'product_id',
            'value' => '$data->getProductName()'
        ),
        array(
            'name' =>  'warehouse_id',
            'value' => '$data->getWarehouseName()'
        ),
        array(
            'name' =>  'from_id',
            'value' => '$data->getFromName()'
        ),
        array(
            'name' =>  'to_id',
            'value' => '$data->getToName()'
        ),
        'quantity',
        'price',
        'cost',
        'notes',
        array(
            'name' => 'type',
            'value' => '$data->getType()'
        ),
        'added_time',
        array(
            'class'=>'CButtonColumn',
            'template'=>'{update}{revert}',
            'buttons'=>array
            (
                'update' => array(
                    'imageUrl'=>false,
                    'visible'=>'$data->showUpdateButton()',
                    'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'),'data-win-class' => 'L'),
                ),

                'revert' => array(
                    'imageUrl'=>false,
                    'visible'=>'$data->showRevertButton()',
                    'options' => array('class' => 'grid_edit_btn grid-revert-btn', 'label'=>$this->t('Revert')),
                    'url' => 'Yii::app()->createUrl("erpProductRoute/revert", ["id" => $data->id])'
                ),

            ),
        ),
    ),
)); ?>
<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        $('.search-button', tab.data('panel')).click(function(){
            $('.search-form', tab.data('panel')).toggle();
            return false;
        });
        $('.search-form form', tab.data('panel')).submit(function(){
            $('#erp-products-grid', tab.data('panel')).yiiGridView('update', {
                data: $(this).serialize()
            });
            return false;
        });
        tab.bind('onOpen', function(){
            $('#erp-product-route-grid', tab.data('panel')).yiiGridView('update');
        });

        $('.grid_edit_btn.grid-revert-btn').click(function(e){
           if ( !confirm('Are your sure revert this route?') ) {
               e.preventDefault();
               e.stopPropagation();
           }
        });
    });
</script>
