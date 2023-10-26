<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'wms-prod-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
	<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->dropDownList($model, 'type', $this->t(WmsProd::$types), array('empty' => $this->t('Select One'))); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'name'); ?>
<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>100)); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'name_zh'); ?>
<?php echo $form->textField($model,'name_zh',array('size'=>60,'maxlength'=>100)); ?>
	</div>

	<div class="rowcol rowleft">
	<?php echo $form->labelEx($model,'ean'); ?>
<?php echo $form->textField($model,'ean',array('size'=>30,'maxlength'=>50)); 
if(!empty($model->ean)):
?>
<br /><img class="barcode" src="<?=$this->createUrl('barcode/draw', array('code' => preg_match('/^\d{13}$/', $model->ean)? 'EAN13' : 'C128', 'text' => $model->ean, 'height' => 60));?>" height="60" />
<?php endif;?>
	</div>

	<div class="rowcol">
		<?php echo CHtml::button('Gen EAN (12 digits)', ['style' => 'margin-top: 13px', 'id' => 'gen_ean']); ?>
	</div>

	<div id="prod_other">
		<div class="row">
		<?php echo $form->labelEx($model,'brand'); ?>
		<?php echo $form->textField($model,'brand',array('size'=>60,'maxlength'=>100)); ?>
		</div>

		<div class="row">
		<?php echo $form->labelEx($model,'model'); ?>
		<?php echo $form->textField($model,'model',array('size'=>60,'maxlength'=>100)); ?>
		</div>

		<div class="row">
		<?php echo CHtml::label('Country Of Origin', 'mdata[coo]'); ?>
		<?php echo CHtml::textField('mdata[coo]', @$model->mdata['coo'], array('size' => 60, 'maxlength' => 100)); ?>
		</div>

		<div class="row">
		<?php echo $form->labelEx($model,'dim'); ?>
		W<?php echo CHtml::textField('dim[w]', @$model->dims['w'], array('size'=>10,'maxlength'=>10)); ?> &nbsp; 
		H<?php echo CHtml::textField('dim[h]', @$model->dims['h'], array('size'=>10,'maxlength'=>10)); ?> &nbsp; 
		D<?php echo CHtml::textField('dim[d]', @$model->dims['d'], array('size'=>10,'maxlength'=>10)); ?>
		</div>

		<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'cbm'); ?>
		<?php echo $form->textField($model,'cbm', array('size'=>15)); ?>cm<sup>3</sup>
		</div>

		<div class="row rowcol">
		<?php echo $form->labelEx($model,'weight'); ?>
		<?php echo $form->textField($model,'weight', array('size'=>15)); ?>g
		</div>

		<div class="row rowcol">
		<?php echo $form->labelEx($model, 'Expiry Span'); ?>
		<?php echo CHtml::textField('mdata[expiry_span]', @$model->mdata['expiry_span'], array('size' => 15)); ?> year(s)
		</div>
	</div>

	<div id="prod_material">
		<div class="row">
			<div class="row rowcol">
			<?php echo $form->labelEx($model,'price',array('label' => 'Sale Price', 'required' => 'required')); ?>
			$ <?php echo CHtml::textField('mdata[price]', @$model->mdata['price'], array('size'=>10,'maxlength'=>10, 'required' => 'required')); ?>
			</div>
			<div id="prod_moq">
				<div class="row rowcol">
				<?php echo $form->labelEx($model,'moq',array('label' => 'Mutli Order Qty', 'required' => 'required')); ?>
				<?php echo CHtml::textField('mdata[moq]', @$model->mdata['moq'], array('size'=>10,'maxlength'=>10, 'required' => 'required')); ?> / Unit
				</div>
			</div>
			<div id="prod_bqtf">
				<div class="row rowcol">
				<?php echo $form->labelEx($model,'bqtf',array('label' => 'Box Qty To Free', 'required' => 'required')); ?>
				<?php echo CHtml::textField('mdata[bqtf]', @$model->mdata['bqtf'], array('size'=>10,'maxlength'=>10, 'required' => 'required')); ?> / Box
				</div>
				<div class="row rowcol">
				<?php echo $form->labelEx($model,'price2',array('label' => 'Price If Charge', 'required' => 'required')); ?>
				$ <?php echo CHtml::textField('mdata[price2]', @$model->mdata['price2'], array('size'=>10,'maxlength'=>10, 'required' => 'required')); ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="row rowcol">
			<?php echo $form->labelEx($model,'line',array('label' => 'Warning Line Qty', 'required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[line]', @$model->mdata['line'], array('size'=>10,'maxlength'=>10, 'required' => 'required')); ?> / Unit
			</div>
			<div class="row rowcol">
			<?php echo $form->labelEx($model,'order',array('label' => 'Display Order', 'required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[order]', @$model->mdata['order'], array('size'=>10,'maxlength'=>10, 'required' => 'required')); ?>
			</div>
			<div class="row rowcol">
			<?php echo $form->labelEx($model,'img',array('label' => 'Image')); ?>
			<?php echo CHtml::textField('mdata[img]', @$model->mdata['img'], array('size'=>20,'maxlength'=>20)); ?>
			</div>
		</div>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'status'); ?>
	<?php echo $form->radioButtonList($model,'status', $this->t(array(1=>'Active', 0=>'Inactive')), array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp')); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script>
$(function() {
	var flag = '<?=$model->isNewRecord?>';
	if (!flag) {
		var tab = $("#<?=$_GET['tabid'];?>");
		var win = tab.data('panel');
	} else {
		var win = $("#jqmw_<?=$_GET['tabid'];?>");
	}

	change(win);
	$('select#WmsProd_type', win).on('change', function() {
		change(win);
	});

	function change(parent) {
		var type = $('#wms-prod-form select#WmsProd_type', parent).val();
		if (type == 20) {
			$('#prod_other', parent).hide();
			$('#prod_material', parent).show();

			var price = $('#wms-prod-form #mdata_price', parent).val();
			if (price > 0) {
				$('#prod_bqtf', parent).hide();
			} else {
				$('#prod_bqtf', parent).show();
			}
		} else {
			$('#prod_other', parent).show();
			$('#prod_material', parent).hide();
		}
	}

	$('#gen_ean', win).on('click', function() {
		$.ajax({
			type: 'POST',
			url: '<?=!empty($model->id) ? Yii::app()->createUrl("wmsProd/genBarcode", ["id" => $model->id]) : Yii::app()->createUrl("wmsProd/genBarcode")?>',
			dataType: 'json',
			success: function(resp) {
				if (resp.done) {
					$('#WmsProd_ean', win).val(resp.ean);
				}
			}
		});
	});
});
</script>