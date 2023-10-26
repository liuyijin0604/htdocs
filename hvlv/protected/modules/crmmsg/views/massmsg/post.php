<h1><?=$this->t('Post CRM Mass Message');?> - <?php echo $model->id; ?></h1>

<div style="border: 1px solid black; padding: 20px;">
	<b><?php echo $model->mdata['title'];?></b>
	<br>
	<br>
	<?php echo $model->mdata['content'];?>
</div>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'crm-mass-msg-post-form',
	'enableAjaxValidation'=>false,
)); ?>
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Confirm')); ?>
	</div>
<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	$('#crm-mass-msg-post-form', panel).on({
		'success': function(e, r) {
		},
		'error': function(e, r) {
		}
	});
});
</script>