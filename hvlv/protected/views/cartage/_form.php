<?php
/* @var $this CartageController */
/* @var $model Cartage */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'cartage-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation' => false,
));?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
	<fieldset style="width: 30%;display: inline">
		<legend>From:</legend>
		<div class="row">

			<?php echo $form->labelEx($model, 'from_id'); ?>
			<?php echo $form->hiddenField($model, 'from_id', array('size' => 11, 'maxlength' => 11)); ?>
			<?php $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $_GET["tabid"] . '_from1_org_name',
				'source' => $this->createUrl('cart/cartage/suggest_agent'),
				'value' => empty($model->from_id) ? '' : $model->from_org->name,
				'options' => array(
					'minLength' => '1',
					'select' => "js:function(e,u) {
						$('#Cartage_from_id',$('#" . $_GET["tabid"] . "').data('panel')).val(u.item.id);
						$('#Cartage_from_addr',$('#" . $_GET["tabid"] . "').data('panel')).val(u.item.addr);
						$('#Cartage_from_contact',$('#" . $_GET["tabid"] . "').data('panel')).val(u.item.contact);

						$('#Cartage_from_id',$('#jqmw_" . $_GET["tabid"] . "')).val(u.item.id);
						$('#Cartage_from_addr',$('#jqmw_" . $_GET["tabid"] . "')).val(u.item.addr);
						$('#Cartage_from_contact',$('#jqmw_" . $_GET["tabid"] . "')).val(u.item.contact);
					}",
				),
				'htmlOptions' => array('size' => 60, 'maxlength' => 200),
			));?>
			<?php echo $form->error($model, 'from_id'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model, 'from_addr'); ?>
			<?php echo $form->textField($model, 'from_addr', array('size' => 60, 'maxlength' => 200)); ?>
			<?php echo $form->error($model, 'from_addr'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model, 'from_contact'); ?>
			<?php echo $form->textField($model, 'from_contact', array('size' => 60, 'maxlength' => 100)); ?>
			<?php echo $form->error($model, 'from_contact'); ?>
		</div>
   </fieldset>

	<fieldset style="width: 30%; display: inline; margin-left: 50px;">
		<legend>To:</legend>
		<div class="row">
			<?php echo $form->labelEx($model, 'to_id'); ?>
			<?php echo $form->hiddenField($model, 'to_id', array('size' => 11, 'maxlength' => 11)); ?>
			<?php $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $_GET["tabid"] . '_to1_org_name',
				'source' => $this->createUrl('cart/cartage/suggest_agent'),
				'value' => empty($model->to_id) ? '' : Org::getName($model->to_id),
				'options' => array(
					'minLength' => '1',
					'select' => "js:function(e,u) {
						$('#Cartage_to_id',$('#" . $_GET["tabid"] . "').data('panel')).val(u.item.id);
						$('#Cartage_to_addr',$('#" . $_GET["tabid"] . "').data('panel')).val(u.item.addr);
						$('#Cartage_to_contact',$('#" . $_GET["tabid"] . "').data('panel')).val(u.item.contact);

						$('#Cartage_to_id',$('#jqmw_" . $_GET["tabid"] . "')).val(u.item.id);
						$('#Cartage_to_addr',$('#jqmw_" . $_GET["tabid"] . "')).val(u.item.addr);
						$('#Cartage_to_contact',$('#jqmw_" . $_GET["tabid"] . "')).val(u.item.contact);
					}",
				),
				'htmlOptions' => array('size' => 60, 'maxlength' => 200),
			));?>
			<?php echo $form->error($model, 'to_id'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model, 'to_addr'); ?>
			<?php echo $form->textField($model, 'to_addr', array('size' => 60, 'maxlength' => 200)); ?>
			<?php echo $form->error($model, 'to_addr'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model, 'to_contact'); ?>
			<?php echo $form->textField($model, 'to_contact', array('size' => 60, 'maxlength' => 100)); ?>
			<?php echo $form->error($model, 'to_contact'); ?>
		</div>
	</fieldset>

	<div class="row">
		<?php echo $form->labelEx($model, 'type'); ?>
		<?php echo $form->dropDownList($model, 'type', array(10 => 'Customer to Warehouse', 20 => 'Warehouse to Customer', 30 => 'Warehouse to Terminal', 40 => 'Terminal to Warehouse ', 50 => 'Customer to Customer'), array('prompt' => $this->t('All'))) ?>
		<?php echo $form->error($model, 'type'); ?>
	</div>
	<?php if (!empty($model->job_id)) { ?>
		<div class="row">
			<?php echo CHtml::label('Job No', 'job_id'); ?>
			<?php $job = EdiJob::model()->findByPk($model->job_id); ?>
			<?php echo CHtml::textField('job_no', $job->no, array('size' => 60, 'maxlength' => 200)); ?>
			<?php echo $form->hiddenField($model, 'job_id', array('size' => 11, 'maxlength' => 11)); ?>
		</div>
	<?php } ?>
	<div class="row">
		<?php echo $form->labelEx($model, 'ref'); ?>
		<?php echo $form->textField($model, 'ref', array('size' => 60, 'maxlength' => 200)); ?>
		<?php echo $form->error($model, 'ref'); ?>
	</div>
	<div class="row">
		<?php echo CHtml::label('Assign To Org', 'org_id'); ?>
		<?php echo $form->hiddenField($model, 'org_id', array('size' => 11, 'maxlength' => 11)); ?>
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name' => $_GET["tabid"] . '_org1_org_name',
			'source' => $this->createUrl('cart/cartage/suggest_agent'),
			'value' => empty($model->org_id) ? '' : $model->org->name,
			'options' => array(
				'minLength' => '1',
				'select' => "js:function(e,u) {
					$('#Cartage_org_id',$('#" . $_GET["tabid"] . "').data('panel')).val(u.item.id);

					$('#Cartage_org_id',$('#jqmw_" . $_GET["tabid"] . "').data('panel')).val(u.item.id);
				}",
			),
			'htmlOptions' => array('size' => 60, 'maxlength' => 200),
		));?>
		<?php echo $form->error($model, 'org_id'); ?>
	</div>


	<div class="row">
		<?php echo $form->labelEx($model, 'scd_time'); ?>
		<?php echo $form->textField($model, 'scd_time', array('size' => 25, 'id' => 'scd_time_create', 'class' => 'datetime_input')); ?>
		<?php echo $form->error($model, 'scd_time'); ?>
	</div>




	<div class="row">
		<?php echo $form->labelEx($model, 'plt'); ?>
		<?php echo $form->textField($model, 'plt', array('size' => 50, 'maxlength' => 50)); ?>
		<?php echo $form->error($model, 'plt'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'mdata[pmc]'); ?>
		<?php echo $form->textField($model, 'mdata[pmc]', array('size' => 50, 'maxlength' => 50)); ?>
		<?php echo $form->error($model, 'mdata[pmc]'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'cbm'); ?>
		<?php echo $form->textField($model, 'cbm', array('size' => 50, 'maxlength' => 50)); ?>
		<?php echo $form->error($model, 'cbm'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'kg'); ?>
		<?php echo $form->textField($model, 'kg', array('size' => 50, 'maxlength' => 50)); ?>
		<?php echo $form->error($model, 'kg'); ?>
	</div>



	<div class="row">
		<?php echo CHtml::label('Note', 'cartage_note'); ?>
		<?php echo CHtml::textArea('cartage_note', empty($model->mdata['notes']['op']) ? '' : $model->mdata['notes']['op'], array('rows' => 6, 'cols' => 50)); ?>
	</div>

	<div class="row">
		<?php echo $form->radioButtonList($model, 'mdata[proc_type]', Cartage::$proc_types, array('separator' => '&nbsp;&nbsp;', 'labelOptions' => array('class' => 'radio_label', 'style' => 'display: inline; font-weight: bold'))); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget();?>

</div><!-- form -->
<script>
	$(function() {
		var tab = $("#<?=$_GET['tabid']?>");
		var panel = tab.data('panel');
		var win = $("#jqmw_<?=$_GET['tabid']?>");

		$('form#cartage-form', win).on('success', function() {
			setTimeout(function() {
				$('.popCancel', win).trigger('click');
				$.fn.yiiGridView.update('cartage-arrange-grid', {
					data: { 'Cartage[status]': 10 }
				});
			}, 1e3);
		});

	});
</script>