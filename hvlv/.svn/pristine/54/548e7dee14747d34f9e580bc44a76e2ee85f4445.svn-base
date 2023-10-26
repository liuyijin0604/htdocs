<h1><?=$model->getType();?></h1>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'wms-task-form',
	'enableAjaxValidation' => false,
));?>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Cust', 'org_id'); ?>
		<?php echo CHtml::hiddenField('org_id', '');
			$acname = empty($_GET["tabid"]) ? 'org_ac' : $_GET["tabid"] . '_org_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('org/ownerSuggest'),
				'value' => '',
				'options' => array(
					'showAnim' => 'fold',
					'minLength' => 2,
					'delay' => 200,
					'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
					'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'class' => 'required',
					'size' => '30',
				),
		)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'ref'); ?>
		<?php echo $form->textField($model, 'ref', ['size' => 25]); ?>
	</div>

	<div class="row rowcol rowleft">
	<?php echo $form->labelEx($model, 'status'); ?>
		<?php echo $form->dropDownList($model, 'status', $this->t(WmsTask::$states_adhoc), array('empty' => $this->t('Select One'))); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'op_id'); ?>
		<?php if ($model->type == WmsTask::TYPE_ADHOC_TASK) {
			$model->op_id = $model->op_id ? $model->op_id : Yii::app()->user->id;
			$op_name = User::model()->findByPk(Yii::app()->user->id)->getName();
		} else {
			$op_name = '';
		} ?>
		<?php echo $form->hiddenField($model, 'op_id');
		$acname = empty($_GET["tabid"]) ? 'op_ac' : $_GET["tabid"] . '_op_ac';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name' => $acname,
			'sourceUrl' => array('user/suggest'),
			'value' => empty($model->op) ? $op_name : $model->op->getName(),
			'options' => array(
				'showAnim' => 'fold',
				'minLength' => 2,
				'delay' => 200,
				'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
				'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
			),
			'htmlOptions' => array(
				'size' => '20',
			),
		));
		?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Process', 'mdata[proc_id]'); ?>
		<?php echo $form->hiddenField($model, 'mdata[proc_id]');
		$acname = empty($_GET["tabid"]) ? 'proc_ac' : $_GET["tabid"] . '_proc_ac';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name' => $acname,
			'sourceUrl' => array('user/suggest'),
			'value' => empty($model->mdata['proc_id']) ? '' : User::model()->findByPk($model->mdata['proc_id'])->getName(),
			'options' => array(
				'showAnim' => 'fold',
				'minLength' => 2,
				'delay' => 200,
				'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
				'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
			),
			'htmlOptions' => array(
				'size' => '20',
			),
		));
		?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('QA', 'mdata[qa_id]'); ?>
		<?php echo $form->hiddenField($model, 'mdata[qa_id]');
		$acname = empty($_GET["tabid"]) ? 'qa_ac' : $_GET["tabid"] . '_qa_ac';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name' => $acname,
			'sourceUrl' => array('user/suggest'),
			'value' => empty($model->mdata['qa_id']) ? '' : User::model()->findByPk($model->mdata['qa_id'])->getName(),
			'options' => array(
				'showAnim' => 'fold',
				'minLength' => 2,
				'delay' => 200,
				'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
				'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
			),
			'htmlOptions' => array(
				'size' => '20',
			),
		));
		?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'due_time'); ?>
		<?php echo $form->textField($model, 'due_time', ['class' => 'datetime_input', 'id' => $_GET['tabid'] . '_dt_due']); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'schd_time'); ?>
		<?php echo $form->textField($model, 'schd_time', ['class' => 'datetime_input', 'id' => $_GET['tabid'] . '_dt_schd']); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'start_time'); ?>
		<?php echo $form->textField($model, 'start_time', ['class' => 'datetime_input', 'id' => $_GET['tabid'] . '_dt_start']); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'compl_time'); ?>
		<?php echo $form->textField($model, 'compl_time', ['class' => 'datetime_input', 'id' => $_GET['tabid'] . '_dt_comp']); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save'), ['name' => 'act_btn']); ?>
	</div>

<?php $this->endWidget();?>

</div><!-- form -->

<script type="text/javascript">
$(function() {
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	$('#wms-task-form', panel).on({
		'success': function(e, r){
			<?php if ($model->isNewRecord) {?>
			myApp.tabs.CreateTab({
				title: 'T-'+r.id,
				url: tab.data('url').replace(/\/create.+/, '/update/'+r.id),
			});
			tab.trigger('close');
			<?php } else {?>
			tab.trigger('reload_tab');
			<?php }?>
			$('tbody input', t).prop('disabled', false);
		}
	});
});
</script>