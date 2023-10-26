<div class="container">
<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'wms-prod-form',
	'enableAjaxValidation' => false,
));?>

	<div class="row">
		<div class="col col-md-3 col-sm-4">
			<div class="form-group">
			<?php echo $form->labelEx($model, 'type'); ?>
			<?php
			if ($model->name) {
				echo $form->dropDownList($model, 'type', $this->t(WmsProd::$types), array('empty' => $this->t('Select One'), 'class' => 'form-control', 'disabled' => 'disabled'));
			} else {
				echo $form->dropDownList($model, 'type', $this->t(WmsProd::$types), array('empty' => $this->t('Select One'), 'class' => 'form-control'));
			}
			?>
			</div>
		</div>
		<div class="col col-md-3 col-sm-4">
			<div class="form-group">
			<?php echo $form->labelEx($model, 'name'); ?>
			<?php
			if ($model->name) {
				echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 100, 'class' => 'form-control', 'disabled' => 'disabled'));
			} else {
				echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 100, 'class' => 'form-control'));
			}
			?>
			</div>
		</div>
		<div class="col col-md-3 col-sm-4">
			<div class="form-group">
			<?php echo $form->labelEx($model, 'name_zh'); ?>
			<?php
			if ($model->isNewRecord) {
				echo $form->textField($model, 'name_zh', array('size' => 60, 'maxlength' => 100, 'class' => 'form-control'));
			} else {
				echo $form->textField($model, 'name_zh', array('size' => 60, 'maxlength' => 100, 'class' => 'form-control', 'readOnly' => true));
			}
			?>
		 </div>
		</div>
	</div>

	<div class="row">
		<div class="col col-md-3 col-sm-4">
			<div class="form-group">
			<?php echo $form->labelEx($model, 'ean'); ?><span><b> / Barcode</b></span>
			<?php
			if ($model->isNewRecord) {
				echo $form->textField($model, 'ean', array('size' => 50, 'maxlength' => 50, 'class' => 'form-control'));
			} else {
				echo $form->textField($model, 'ean', array('size' => 50, 'maxlength' => 50, 'class' => 'form-control', 'readOnly' => true));
			}
			?>
			</div>
		</div>
		<div class="col col-md-3 col-sm-4">
			<div class="form-group">
			<?php echo CHtml::label('Sku', 'prod_sku'); ?><span class="required">*</span>
			<?php echo CHtml::textField('prod_sku', $model->getSku(Yii::app()->user->org), array('size' => 50, 'maxlength' => 50, 'class' => 'form-control')); ?>
			</div>
		</div>
		<div class="col col-md-3 col-sm-4">
			<div class="form-group">
			<?php echo $form->labelEx($model, 'brand'); ?>
			<?php echo $form->textField($model, 'brand', array('size' => 60, 'maxlength' => 100, 'class' => 'form-control')); ?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col col-md-2 col-sm-2">
			<div class="form-group">
			<?php echo CHtml::label('Dim (cm) W', 'dim') ?>
			<?php echo CHtml::textField('dim[w]', @$model->dims['w'], array('size' => 10, 'maxlength' => 10, 'class' => 'form-control')); ?> &nbsp;
			</div>
		</div>
		<div class="col col-md-2 col-sm-2">
			<div class="form-group">
			<?php echo CHtml::label('H', 'dim') ?>
			<?php echo CHtml::textField('dim[h]', @$model->dims['h'], array('size' => 10, 'maxlength' => 10, 'class' => 'form-control')); ?> &nbsp;
			</div>
		</div>
		<div class="col col-md-2 col-sm-2">
			<div class="form-group">
			<?php echo CHtml::label('D', 'dim') ?>
			<?php echo CHtml::textField('dim[d]', @$model->dims['d'], array('size' => 10, 'maxlength' => 10, 'class' => 'form-control')); ?> &nbsp;
			</div>
		</div>
		<div class="col col-md-2 col-sm-2">
			<div class="form-group">
			<?php echo CHtml::label('Volume (cm<sup>3</sup>)', 'cbm') ?>
			<?php
			if ($model->isNewRecord) {
				echo $form->textField($model, 'cbm', array('size' => 15, 'class' => 'form-control'));
			} else {
				echo $form->textField($model, 'cbm', array('size' => 15, 'class' => 'form-control', 'readOnly' => true));
			}
			?> &nbsp;
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col col-md-2 col-sm-2">
			<div class="form-group">
			<?php echo CHtml::label('Weight(g)', 'weight'); ?>
			<?php echo $form->textField($model, 'weight', array('size' => 15, 'class' => 'form-control')); ?>
			</div>
		</div>
		<div class="col col-md-2 col-sm-2">
			<div class="form-group">
			<?php echo CHtml::label('Expiry Span (years)', 'expiry_span'); ?>
			<?php echo $form->textField($model, 'mdata[expiry_span]', array('size' => 15, 'class' => 'form-control')); ?>
			</div>
		</div>
		<div class="col col-md-2 col-sm-2">
			<div class="form-group">
			<?php echo CHtml::label('Minimum Stock Alert', 'min_stock_alert'); ?>
			<?php echo CHtml::textField('min_stock_alert', $model->getMinStockAlert(Yii::app()->user->org), array('size' => 15, 'class' => 'form-control')); ?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col col-md-6 col-sm-2">
			<div class="form-group">
			<?php echo $form->labelEx($model, 'status'); ?>
			<?php echo $form->radioButtonList($model, 'status', $this->t(array(1 => 'Active', 0 => 'Inactive')), array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp')); ?>
			<?php echo CHtml::hiddenField('WmsProd[org_id]', Yii::app()->user->org) ?>
			</div>
		</div>
	</div>

	<div class="form-group button">
	<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save'), array('class' => 'btn btn-primary')); ?>
	</div>
<?php $this->endWidget();?>
</div><!-- form -->
</div>
<script type="text/javascript">
$(function () {
	$('#WmsProd_name').on('focus', function() {
		var me = $(this);
		var ac_select = function(me, ui){
		$('#WmsProd_name').val(ui.label);
		$('#WmsProd_name_zh').val(ui.name_zh);
		$('#WmsProd_ean').val(ui.ean);
		$('#WmsProd_brand').val(ui.brand);
		if (ui.dim) {
			$('#dim_w').val(ui.dim.w);
			$('#dim_h').val(ui.dim.h);
			$('#dim_d').val(ui.dim.d);
		}
		$('#WmsProd_cbm').val(ui.cbm);
		$('#WmsProd_weight').val(ui.weight);
		};
		me.autocomplete({
		'source': "<?=$this->createUrl('product/suggest2', array('oid' => Yii::app()->user->org))?>" + "/type/" + $('#WmsProd_type').val(),
		'showAnim': 'fold',
		'minLength': 2,
		'delay': 200,
		select: function(event, ui){
			ac_select($(this), ui.item);
			return false;
		},
		response: function(evt, ui){
			if(ui.content.length == 1){
			ui.item = ui.content[0];
			ac_select($(this), ui.item);
			}else if(ui.content.length == 0){
			}
			return false;
		},
		}).data('acinit', 1);
	});

	$('input[name*="dim"]').on('change', function() {
		var dim = $('#dim_w').val() * $('#dim_h').val() * $('#dim_d').val();
		$('#WmsProd_cbm').val(dim);
	});
});
</script>