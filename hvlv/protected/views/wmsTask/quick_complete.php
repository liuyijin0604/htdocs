<h1><?=$this->t('Quick Complete');?> <?php echo $model->id; ?></h1>

<div class="form">
	<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'wmstask-quick-complete-form',
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('wmsTask/quickComplete', array('id' => $model->id, 'confirm' => true)),
	));
	?>
	<p><input id="wmstask_quick_complete_btn" type="submit" value="Complete" /></p>
</div>
<?php $this->endWidget(); ?>

<script type="text/javascript">
$(function() {
  var tab = $("#<?=$_GET['tabid'];?>");
  var panel = tab.data('panel');
  var win = $("#jqmw_<?=$_GET['tabid'];?>");

  $('form#wmstask-quick-complete-form', win).on('success', function(e, r) {
		$('.popCancel').trigger('click');
    $('#cg-task-grid', panel).yiiGridView('update');
  });
});
</script>