<div class="container">
<div style="right: 20px;position: absolute;">
<a class="jqm_link" href="<?=$this->createUrl('wmsProd/create');?>"><div class="icon" style="background-position:-16px 0"></div> New Product</a>
</div>
<h1><?=$this->t('Manage Products');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
  'id'=>'cg-prod-grid',
  'cssFile' => false,
  'dataProvider'=>$model->search(),
  'filter'=>$model,
  'columns'=>array(
    array('name' => 'type', 'value' => '$data->getType()', 
      'filter'=>CHtml::dropDownList('WmsProd[type]', $model->type, array(array_search('Material', WmsProd::$types) => 'Material')),),
    'ean',
    'name',
    'name_zh',
    'brand',
    'model',
    array('name' => 'status', 'value' => '$data->getStatus()', 
      'filter'=>CHtml::dropDownList('WmsProd[status]', $model->status, $this->t(WmsProd::$states), array('prompt'=>$this->t('All'))),),
    array('name' => 'order', 'value' => '$data->mdata["order"]',),
    array(
      'class'=>'oButtonColumn',
      'template'=>'{view}{update}',
      'buttons'=>array
      (
        'view' => array(
          'imageUrl'=>false,
          'url' => 'Yii::app()->createUrl("WmsProd/" . $data->id)',
          'options' => array('class' => 'jqm_link grid_view_btn'),
        ),
        'update' => array(
          'imageUrl'=>false,
          'url' => 'Yii::app()->createUrl("WmsProd/update", array("id" => $data->id))',
          'visible'=>'true',
          'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '"Product ".$data->name'),
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
    $('#cg-prod-grid', panel).yiiGridView('update');
  });
});
</script>
