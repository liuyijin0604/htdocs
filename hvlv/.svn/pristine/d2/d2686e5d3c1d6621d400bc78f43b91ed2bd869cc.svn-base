<div class="container">

<h1><?=$this->t('Batch Orders');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>


<?php
  $criteria = new CDbCriteria;
  $criteria->compare('status', array(30, 99));
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
  'id'=>'cg-job-grid',
  'cssFile' => false,
  'dataProvider'=>$model->search(true, 30, $criteria),
  'filter'=>$model,
  'columns'=>array(
    array('name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("wmsJob/update", ["id" => $data->id])."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"'),
    array('name' => 'status', 'value' => '$data->getStatus()', 
      'filter'=>CHtml::dropDownList('WmsJob[status]', $model->status, array(
        array_search('Processing', WmsJob::$states) => 'Processing',
        array_search('Completed', WmsJob::$states) => 'Completed'
      ), array('prompt'=>$this->t('All'))),),
    'created',
    array(
      'class'=>'oButtonColumn',
      'template'=>'{print}{view}{update}',
      'buttons'=>array
      (
        'print' => array(
          'url' => 'Yii::app()->createUrl("cgoods/printbatched", array("id" => $data->id))',
          'imageUrl' => false,
          'options' => array('class' => 'grid_print_btn', 'target' => '_blank'),
          'label' => 'Print'
        ),
        'view' => array(
          'imageUrl'=>false,
          'url' => 'Yii::app()->createUrl("WmsJob/" . $data->id)',
          'options' => array('class' => 'jqm_link grid_view_btn'),
        ),
        'update' => array(
          'imageUrl'=>false,
          'url' => 'Yii::app()->createUrl("WmsJob/update", array("id" => $data->id))',
          'visible'=>'true',
          'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->no'),
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
    $('#cg-job-grid', panel).yiiGridView('update');
  });
});
</script>
