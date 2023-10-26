<div style="right: 40px;position: absolute;">
<?php
$valid = $model->isCurrent();
if($valid && empty($model->vto)): ?>
<a class="ajax_link copy_rate" href="<?=$this->createUrl('exChannel/copyRate', ['id' => $model->id]);?>" title="Copy Rate"><div class="icon" style="background-position:-16px 0"></div> Copy Rate</a>
<?php endif; ?>
</div>
<h1><?php echo $model->name; ?> <?=$this->t('Rates');?></h1>
<div class="check_result" style="position: absolute; right: 20px; padding: 10px; border: 1px solid #AED0EA; max-height: 130px; overflow: auto;">checking...</div>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'ex-channel-rate-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Courier','courier');?>
		<?php echo $form->dropdownList($model, 'org_id', Org::tList(10), ['empty' => 'Select One']); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'currency'); ?>
		<?php echo $form->dropdownList($model, 'currency', Invoice::$currencies, ['empty' => 'Select One']); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'vfrom'); ?>
		<?php echo $form->textField($model, 'vfrom', ['class' => 'date_input']); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'vto'); ?>
		<?php echo $form->textField($model, 'vto', ['class' => 'date_input']); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Charge/Pack','ppk');?>
		<?php echo CHtml::textField('mdata[ppk]', @$model->mdata['ppk'], ['size' => '10']); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Charge/KG','pkg');?>
		<?php echo CHtml::textField('mdata[pkg]', @$model->mdata['pkg'], ['size' => '10']); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('~Duty/Pack','duty_ppk');?>
		<?php echo CHtml::textField('mdata[duty_ppk]', @$model->mdata['duty_ppk'], ['size' => '10']); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('~Duty/KG','duty_pkg');?>
		<?php echo CHtml::textField('mdata[duty_pkg]', @$model->mdata['duty_pkg'], ['size' => '10']); ?>
	</div>

	<div class="row buttons">
		<?php echo $valid? CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')) : ''; ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
<?php
if(empty($model->id)) $model->id = 0;
if($model->id > 0):
$ss = new ZoneRate('search');
$ss->unsetAttributes();
$ss->rate_id = $model->id;

$this->widget('application.extensions.editablegrid.CEditableGridView', [
	'id' => $_GET['tabid'] . '_ccrates-grid',
	'cssFile' => false,
	'dataProvider' => $ss->search(),
	'formUrl' => $this->createUrl('exChannel/ratesGrid', array('id' => $model->id)),
	'summaryText' => '',
	'editable' => $valid,
	'showQuickBar' => $valid,
	'afterSave' => "function(r){
	if(r.done == true){
		myApp.notice(r.msg, 5000);
		$('#jqmw_".$_GET["tabid"]."').trigger('checkRate');
	}else{
		myApp.alert(r.msg, false);
	}
	return r.done;
}",
	'columns' => [
		['name' => 'zone', 'class' => 'CEditableColumn'],
		['name' => 'zone_name', 'class' => 'CEditableColumn'],
		['name' => 'weight_lo', 'class' => 'CEditableColumn'],
		['name' => 'weight_hi', 'class' => 'CEditableColumn'],
		['name' => 'base', 'class' => 'CEditableColumn'],
		['name' => 'item', 'class' => 'CEditableColumn'],
		['name' => 'perkg', 'class' => 'CEditableColumn'],
		['name' => 'nkg', 'class' => 'CEditableColumn'],
		['name' => 'minimum', 'class' => 'CEditableColumn'],
		['class' => 'CEditableButtonColumn', 'template' => '{edit} {cancel} {save}'],
	],
]);
endif;
?>

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	win.on('checkRate', function(){
		var rid = <?=$model->id;?>;
		if(rid > 0) $('div.check_result', win).load('exChannel/rateCheck/'+rid);
	}).trigger('checkRate');
	
	$('a.copy_rate', win).on('click', function(){
		var vt = window.prompt('This will expire the current rate and start a new rate, please enter cutoff date in "yyyy-mm-dd" to continue.');
		if(vt.match(/^\d{4}-\d{2}-\d{2}$/) !== null){
			$.get($(this).attr('href')+'?cutoff='+vt, function(r){
				if(r.done){
					win.data('opener').trigger('refresh');
					win.jqmHide();
				}else{
					myApp.alert(r.msg);
				}
			}, 'json');
		}
		return false;
	});
});
</script>