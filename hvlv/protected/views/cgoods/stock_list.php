<div class="container">
<div style="right: 20px;position: absolute;">
<!-- <a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-48px -688px" class="icon"></div> Export</a>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
  <ul class="dropdown-menu">
    <li><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('wmsStock/export');?>" target="_blank" >Current Search</a></li>
    <li><a href="<?=$this->createUrl('wmsStock/report');?>" class="jqm_link">Stock Report</a></li>
                <li><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('wmsStock/exportLedger');?>" target="_blank" >Export Ledger</a></li>
  </ul>
</div> -->
  <a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1" class="grid_edit_btn">Edit Stock</a>
  <div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
    <ul class="dropdown-menu">
      <li><a class="jqm_link" href="<?=$this->createUrl('cgoods/editStock', array('type' => 'in'))?>">Stock In</a></li>
      <li><a class="jqm_link" href="<?=$this->createUrl('cgoods/editStock', array('type' => 'out'))?>">Stock Out</a></li>
    </ul>
  </div>
</div>
<h1><?=$this->t('CG Stocks');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php
  $criteria = new CDbCriteria;
  $criteria->with = ['prod'];
  $criteria->compare('prod.type', '20');
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
  'id'=>'cg-stock-grid',
  'cssFile' => false,
  'dataProvider'=>$model->search(true, 30, $criteria),
  'filter'=>$model,
  'columns'=>array(
    array('name' => 'prod_name', 'value' => 'empty($data->prod)? "" : $data->prod->name'),
    array('name' => 'prod_ean', 'type' => 'raw', 'value' => 'empty($data->prod)? "" : "<a href=\"".Yii::app()->createUrl("wmsProd/update", ["id" => $data->prod_id])."\" class=\"tab_link\" title=\"Product ".$data->prod->name."\">".$data->prod->ean."</a>"'),
    array('name' => 'cust_name', 'value' => 'empty($data->customer)? "" : $data->customer->shortName(2)'),
    'qty',
    'qty_res',
    'expiry',
    'batch',
    'updated',
    array(
      'class'=>'oButtonColumn',
      'template'=>'{view}',
      'buttons'=>array
      (
        'view' => array(
          'imageUrl'=>false,
          'url' => 'Yii::app()->createUrl("WmsStock/" . $data->id)',
          'options' => array('class' => 'jqm_link grid_view_btn', 'data-win-class' => 'L'),
        ),
      ),
    ),
  ),
)); ?>
</div>

<script type="text/javascript">
$(function(){
  var tab = $("#<?=$_GET['tabid'];?>");
  var panel = tab.data('panel');

  tab.bind('onOpen', function(){
    $('#cg-stock-grid', panel).yiiGridView('update');
  });

  $('a.export_search', panel).on('mousedown', function(){
    var q = $('.filters input, .filters select', panel).serialize()+'&'+$('.search-form form', panel).serialize();
    $(this).attr('href', $(this).data('baseurl') + '?' + q);
  });
});
</script>