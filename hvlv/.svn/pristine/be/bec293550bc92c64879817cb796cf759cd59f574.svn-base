<style>
    .display_none {
		display: none;
	}
</style>
<div class="form" style="padding-left: 20px;">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'action' => Yii::app()->createUrl($this->route),
		'method' => 'get','id'=>'bidding_list'
	)); ?>
	<div class="row">
		<div class="col col-md-6 col-sm-12">
			<div class="row">
				<?php echo $form->label($model, 'Delivery Date:') . "&nbsp;&nbsp;&nbsp;&nbsp;" . $form->textField($model, 'deliverydate', ['class' => 'date_input', 'id' => $_GET['tabid'] . '_deliverydate']); ?>
			</div>
			<br />
			<div class="row">
				<?php echo $form->label($model, 'Address Type:') . "&nbsp;&nbsp;&nbsp;&nbsp;" . $form->dropDownList($model, 'address_type', CargoProcessBidding::$address_Types, ['empty' => 'Select One','style' => 'width:180px;height:25px;']); ?>
			</div>
		</div>
		<div class="col col-md-6 col-sm-12">
			<div class="row">
				<?php echo $form->label($model, 'Unloading Type:') . "&nbsp;" . $form->dropDownList($model, 'unload_type', CargoProcessBidding::$unloading_Types, ['empty' => 'Select One','style' => 'width:180px;height:25px;']); ?>
			</div>
			<br />
			<div class="row">
				<?php echo $form->label($model, 'Delivery Range:') . "&nbsp;&nbsp;" . $form->dropDownList($model, 'delivery_range', CargoProcessBidding::$delivery_Ranges, ['empty' => 'Select One','style' => 'width:180px;height:25px;']); ?>
			</div>
		</div>
	</div>
	<div class="display_none">
		<?php echo CHtml::telField('mybidding',0,['id'=>'mybidding'])?>
	</div>
	<br />
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Search')); //array('style'=>'margin-left:10px;')
		?>
		<?php echo CHtml::button('My Bidding', array(
			"id" => 'my_bidding',
		)); ?>
	</div>

	<?php $this->endWidget(); ?>
</div><!-- search-form -->
<script type="text/javascript">
	$(function() {
		var tab = $('#<?= $_GET["tabid"]; ?>');
		var panel = tab.data('panel');

		$('#my_bidding', panel).click(function() {
			$('#mybidding', panel).val(1);
			$('#bidding_list',panel).submit();
			$('#mybidding', panel).val(0);
		});
	});
</script>