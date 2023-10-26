<div class="container">
<div style="right: 20px;position: absolute;">
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-48px -688px" class="icon"></div> Export</a>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
  <ul class="dropdown-menu">
    <li><a href="<?=$this->createUrl('cgoods/print');?>" target="_blank">Print Orders</a></li>
  </ul>
</div>
</div>
<h1><?=$this->t('CG Orders');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>


<?php
  $criteria = new CDbCriteria;
  $criteria->compare('status', array(20, 30, 99));
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
  'id'=>'cg-task-grid',
  'cssFile' => false,
  'dataProvider'=>$model->search(true, 30, $criteria),
  'filter'=>$model,
  'columns'=>array(
    array('name' => 'job_no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("wmsJob/update", ["id" => $data->job_id])."\" class=\"tab_link\" title=\"".$data->job->no."\">".$data->job->no."</a>"'),
    array('name' => 'id', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("wmsTask/update", ["id" => $data->id])."\" class=\"tab_link\" title=\"".$data->getNo()."\">".$data->getNo()."</a>"'),
    'ref',
    array('name' => 'status', 'value' => '$data->getStatus()', 
      'filter'=>CHtml::dropDownList('WmsTask[status]', $model->status, array(
        array_search('Scheduled', WmsTask::$states) => 'Scheduled',
        array_search('WIP', WmsTask::$states) => 'WIP',
        array_search('Completed', WmsTask::$states) => 'Completed',
      ), array('prompt'=>$this->t('All'))),),
    'schd_time',
    'compl_time',
    array(
      'class'=>'oButtonColumn',
      'template'=>'{complete}{view}{update}',
      'buttons'=>array
      (
        'complete' => array(
          'imageUrl' => false,
          'label' => 'Complete',
          'url' => 'Yii::app()->createUrl("cgoods/completetask", array("id" => $data->id))',
          'options' => array('class' => 'jqm_link grid_edit_btn'),
          'click' => 'refresh'
        ),
        'view' => array(
          'imageUrl'=>false,
          'url' => 'Yii::app()->createUrl("WmsTask/" . $data->id)',
          'options' => array('class' => 'jqm_link grid_view_btn'),
        ),
        'update' => array(
          'imageUrl'=>false,
          'url' => 'Yii::app()->createUrl("WmsTask/update", array("id" => $data->id))',
          'visible'=>'true',
          'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->getNo()'),
        ),
      ),
    ),
  ),
)); ?>
</div>

<script type="text/javascript">
function refresh() {
  var tab = $("#<?=$_GET['tabid'];?>");
  var panel = tab.data('panel');
  $('#cg-task-grid', panel).yiiGridView('update');
}

$(function(){
  var tab = $("#<?=$_GET['tabid'];?>");
  var panel = tab.data('panel');

  tab.bind('onOpen', function(){
    $('#cg-task-grid', panel).yiiGridView('update');
  });
});
</script>
