<?php
/* @var $this ErpProducts */

$this->breadcrumbs=array(
    'Erp Products'=>array('/erpProducts'),
    'List',
);
?>
<h1><?php echo $this->t('ERP Products'); ?></h1>

<?php echo '<div style="margin-top: -20px; margin-left: 250px;"><a class="jqm_link" href="ErpProducts/create"><div style="background-position:-16px 0" class="icon"></div>'.$this->t('Add New Product').'</a></div>';?>

<p>
    <?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>

<div class="search-form" style="display:none">
    <?php $this->renderPartial('_search',array(
        'model'=>$model,
    )); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'erp-products-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        'name',
        array(
            'name' =>  'vendor_id',
            'value' => '$data->getVendorName()'
        ),
        'buy_price',
        'sale_price',
        'barcode',
        'added_time',
        'notes',
        array(
            'class'=>'CButtonColumn',
            'template'=>'{update}',
            'buttons'=>array
            (
                'update' => array(
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update')),
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
            $('#erp-products-grid', tab.data('panel')).yiiGridView('update');
        });
    });
</script>
