<h1><?=$this->t('Complete Order');?> <?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		array('name' => 'taskid', 'value' => $model->getNo()),
		array('name' => 'type', 'value' => WmsTask::$types[$model->type]),
		array('name' => 'status', 'value' => WmsTask::$states[$model->status]),
		'schd_time',
	),
)); ?>
<br>
<div class="form">
	<?php
	$form=$this->beginWidget('CActiveForm', array(
	    'id'=>'cgoods-complete-order',
	    'htmlOptions' => ['class'=>'cgoods-complete-order'],
	    'enableAjaxValidation'=>false,
	    'action' => $this->createUrl('cgoods/completetask', array('id' => $model->id, 'confirm' => true)),
	));
	?>
	<p><input id="wmstask_import_btn" type="submit" value="Complete" /></p>
</div>
<?php $this->endWidget(); ?>

<script type="text/javascript">
$(function() {
  var tab = $("#<?=$_GET['tabid'];?>");
  var panel = tab.data('panel');
  var win = $("#jqmw_<?=$_GET['tabid'];?>");

  $('form#cgoods-complete-order', win).on('success', function(e, r) {
		$('.popCancel').trigger('click');
    $('#cg-task-grid', panel).yiiGridView('update');
  });
});
</script>
