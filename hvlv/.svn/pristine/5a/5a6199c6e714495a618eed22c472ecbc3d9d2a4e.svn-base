<h1><?=$this->t('Change Password');?> <?php echo $model->id; ?></h1>

<br>
<div class="form">
	<?php
	$form=$this->beginWidget('CActiveForm', array(
	    'id'=>'org-change-password',
	    'htmlOptions' => ['class'=>'org-change-password'],
	    'enableAjaxValidation'=>false,
	    'action' => $this->createUrl('org/changePassword', array('id' => $model->id, 'confirm' => true)),
	));
	?>
	<p><input id="wmstask_import_btn" type="submit" value="Confirm" /></p>
</div>
<?php $this->endWidget(); ?>

<script type="text/javascript">
$(function() {
  var tab = $("#<?=$_GET['tabid'];?>");
  var panel = tab.data('panel');
  var win = $("#jqmw_<?=$_GET['tabid'];?>");

  $('form#org-change-password', win).on('success', function(e, r) {
		$('.popCancel').trigger('click');
    $('#org-grid', panel).yiiGridView('update');
  });
});
</script>
