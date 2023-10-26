<style type="text/css">
div.form input[type="submit"].msg_sent, div.form input[type="button"].msg_sent { background: #cfc; }
div.form input[type="submit"].msg_sent:hover, div.form input[type="button"].msg_sent:hover {
	color: #0c0;
	border-color: #0c0;
}
</style>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'cnl-booking-form',
	'enableAjaxValidation'=>false,
));
if(empty($model->mdata['bk'])){
	$model->mdata['bk'] = [
		'pol' => $model->pol,
		'pod' => $model->pod,
		'eta' => substr($model->targetEta, 0, 10),
		'etd' => '',
	];
}
?>

	<div class="row rowcol rowleft">
		<?php echo CHTML::label('POL','pol'),
		CHtml::textField('meta[bk][pol]', @$model->mdata['bk']['pol'], array('size'=>6,'maxlength'=>5, 'class' => 'required')); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHTML::label('POD','pod'),
		CHtml::textField('meta[bk][pod]', @$model->mdata['bk']['pod'], array('size'=>6,'maxlength'=>5, 'class' => 'required')); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHTML::label('ETD','etd'),
		CHtml::textField('meta[bk][etd]', @$model->mdata['bk']['etd'], array('size'=>10,'maxlength'=>10, 'id' => 'etd_'.$_GET["tabid"], 'class' => 'required date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHTML::label('ETA','eta'),
		CHtml::textField('meta[bk][eta]', @$model->mdata['bk']['eta'], array('size'=>10,'maxlength'=>10, 'id' => 'eta_'.$_GET["tabid"], 'class' => 'required date_input')); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHTML::label($model->transportMode == 20? 'AWB No.' : 'B/L No.','bln'),
		CHtml::textField('meta[bk][bln]', @$model->mdata['bk']['bln'], array('size'=>15,'maxlength'=>20, 'class' => 'required')); ?>
		<?php echo $model->AwbTracking('🔍'); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHTML::label('House Bill No.','hbn'),
		CHtml::textField('meta[bk][hbn]', @$model->mdata['bk']['hbn'], array('size'=>15,'maxlength'=>20, 'class' => 'required')); ?>
	</div>

	<?php if($model->transportMode == 10): ?>

	<div class="row rowcol">
		<?php echo CHTML::label('Vessel Name','vsl'),
		CHtml::textField('meta[bk][vsl]', @$model->mdata['bk']['vsl'], array('size'=>15,'maxlength'=>20, 'class' => 'required')); ?>
	</div>

	<?php endif; ?>

	<div class="row rowcol">
		<?php echo CHTML::label($model->transportMode == 20? 'Flight No.' : 'Voyage No.','voy'),
		CHtml::textField('meta[bk][voy]', @$model->mdata['bk']['voy'], array('size'=>15,'maxlength'=>20, 'class' => 'required')); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Save')); ?>
		<?php echo CHtml::submitButton((empty($model->mdata['msg'][131])? '' : '✔ ').$this->t('Confirm'), ['name' => 'confirm', 'class' => 'btn_send'.(empty($model->mdata['msg'][131])? '' : ' msg_sent')]); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<div class="form">
	<div class="row rowcol rowleft">
		<?php echo CHTML::label('ATD','atd'),
		CHtml::textField('meta[bk][atd]', @$model->mdata['bk']['atd'], array('size'=>10,'maxlength'=>10, 'id' => 'atd_'.$_GET["tabid"], 'class' => 'required date_input')); ?> <?php echo CHtml::button((empty($model->mdata['msg'][136])? '' : '✔ ').$this->t('Send'), ['class' => 'btn_send send_atd'.(empty($model->mdata['msg'][136])? '' : ' msg_sent')]); ?> <?php echo CHtml::button((empty($model->mdata['msg'][140])? '' : '✔ ').$this->t('Pre-Alert'), ['class' => 'btn_send send_pra'.(empty($model->mdata['msg'][140])? '' : ' msg_sent')]); ?>
	</div>

	<div class="row rowcol" style="margin-left: 20px;">
		<?php echo CHTML::label('ATA','ata'),
		CHtml::textField('meta[bk][ata]', @$model->mdata['bk']['ata'], array('size'=>10,'maxlength'=>10, 'id' => 'ata_'.$_GET["tabid"], 'class' => 'required date_input')); ?> <?php echo CHtml::button((empty($model->mdata['msg'][137])? '' : '✔ ').$this->t('Send'), ['class' => 'btn_send send_ata'.(empty($model->mdata['msg'][137])? '' : ' msg_sent')]); ?>
	</div>
</div>

<div class="form" style="clear:both; padding-top: 20px">
<h3>Cargo</h3>
<?php
$p = $model->bCargos[0];
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'cnl-cargo-form',
	'enableAjaxValidation'=>false,
	'action' => $this->createUrl('cnlOrder/cargoUpdate', ['id' => $p->id]),
));
foreach($p->attributes as $k => $v){
	if(in_array($k, ['id', 'type', 'order_id', 'containerList', 'packageList', 'meta'])) continue;
?>
	<div class="row rowcol">
		<?php echo CHTML::label($p->getAttributeLabel($k), 'cargo_'.$k),
		CHtml::textField('cargo['.$k.']', @$v, array('size'=>15,'maxlength'=>20, 'class' => in_array($k, ['chargeWeight', 'chargeVolume'])? 'required':'')); ?>
	</div>
<?php
}
echo '<div style="max-width: 280px; clear:both;"><h4>Containers</h4>';
	$ctn = new CnlCargoContainer;
	$this->widget('application.extensions.editablegrid.CEditableGridView', array(
		'id'=>$_GET["tabid"].'_ctn-grid',
		'cssFile' => false,
		'dataProvider' => $p->conatinerDP(),
		'modelClass' => $ctn,
		'formUrl' => $this->createUrl('cnlOrder/cargoGrid', array('id'=>$p->id)),
		'filter'=>null,
		'summaryText' => '',
		'afterSave' => "function(r){
			if(r.done == true){
				myApp.notice(r.msg, 5000);
			}else{
				myApp.alert(r.msg, false);
			}
			return r.done;
		}",
		'columns'=>array(
			array('header' => 'Size', 'name' => 'size', 'class' => 'CEditableColumn', 'type' => 'list', 'filter' => [20=>20, 40=>40, 45 => 45]),
			array('header' => 'Type', 'name' => 'type', 'type' => 'list', 'filter' => ['GP' => 'GP', 'HQ' => 'HQ', 'RF' => 'RF'], 'class' => 'CEditableColumn'),
			array('header' => 'No', 'name' => 'no', 'class' => 'CEditableColumn'),
			array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save}',),
		),
	));
echo '</div>';
echo '<div style="max-width: 500px; clear:both;"><h4>Packages</h4>';
	$ctn = new CnlCargoPackage;
	$this->widget('application.extensions.editablegrid.CEditableGridView', array(
		'id'=>$_GET["tabid"].'_pkg-grid',
		'cssFile' => false,
		'dataProvider' => $p->packageDP(),
		'modelClass' => $ctn,
		'formUrl' => $this->createUrl('cnlOrder/cargoGrid', array('id'=>$p->id)),
		'filter'=>null,
		'summaryText' => '',
		'afterSave' => "function(r){
			if(r.done == true){
				myApp.notice(r.msg, 5000);
			}else{
				myApp.alert(r.msg, false);
			}
			return r.done;
		}",
		'columns'=>array(
			array('header' => 'UOM', 'name' => 'packageUom', 'class' => 'CEditableColumn', 'type' => 'list', 'filter' => ['PLT'=>'PLT', 'CTN'=>'CTN']),
			array('header' => 'Qty', 'name' => 'num', 'class' => 'CEditableColumn'),
			array('header' => 'L(mm)', 'name' => 'length', 'class' => 'CEditableColumn'),
			array('header' => 'W(mm)', 'name' => 'width', 'class' => 'CEditableColumn'),
			array('header' => 'H(mm)', 'name' => 'height', 'class' => 'CEditableColumn'),
			array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save}',),
		),
	));
?>

<div class="row buttons">
	<?php echo CHtml::submitButton($this->t('Save')); ?>
	<?php echo CHtml::submitButton((empty($model->mdata['msg'][134])? '' : '✔ ').$this->t('Send'), ['name' => 'send', 'class' => 'btn_send'.(empty($model->mdata['msg'][134])? '' : ' msg_sent')]); ?>
</div>
<?php $this->endWidget(); ?>
</div><!-- form -->
<div style="clear:both;margin-bottom:1em;"></div>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	var t = $('#cnlorder-tabs', panel);

	$('input.btn_send', panel).on('click', function(e){
		if(!window.confirm('Ready to send?')){
			e.stopPropagation();
			return false;
		}
	});

	$('#cnl-order-form', panel).on('success', function(){
		t.tabs('load', t.tabs('option','active'));
	});

	var sendMsg = function(id, mt, em){
		var f = $('#'+id+'_<?=$_GET["tabid"];?>', panel);
		var v = f.val();
		if(v == '' || (f.hasClass('date_input') && !v.match(/\d{4}\-\d{2}\-\d{2}/))) {
			myApp.alert(em);
			return false;
		}
		$.get('<?=$this->createUrl('cnlOrder/sendMsg', ['id' => $model->id]);?>?t='+mt+'&meta[bk]['+id+']='+v, function(){
			t.tabs('load', t.tabs('option','active'));
		});
	};

	$('input.send_atd', panel).on('click', function(){
		sendMsg('atd', 136, 'Please enter ATD');
	});

	$('input.send_pra', panel).on('click', function(){
		sendMsg('atd', 140, 'Please enter ATD');
	});

	$('input.send_ata').on('click', function(){
		sendMsg('ata', 137, 'Please enter ATA');
	});
});
</script>