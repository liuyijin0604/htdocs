<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink' => CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
		'Setting',
	),
));
?>

<div class="container">
	<div class="form">
		<?php
		$form = $this->beginWidget('CActiveForm', array(
			'id'=>'setting-form',
			'enableAjaxValidation' => false,
		)); ?>
		<div class="row">
			<div class="col-xs-3" style="padding-right: 3%">
				<h3 style="margin-left: -15px">Consumer Backorder Report</h3>
				<div class="row">
					<div class="form-group">
						<label for="consumer-backorder-feq">Frequency</label>
						<?php echo CHtml::dropDownList('consumer-backorder-feq', @$org->extra['consumer-backorder-feq'], Pcaw::$feqs, ['class' => 'form-control', 'empty' => 'Select One']); ?>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label for="consumer-backorder-rec">Recipient (<span class="required">Separate by ;</span>)</label>
						<?php echo CHtml::textArea('consumer-backorder-rec', @$org->extra['consumer-backorder-rec'], ['class' => 'form-control', 'rows' => 8]); ?>
					</div>
				</div>
			</div>
			<div class="col-xs-3" style="padding-right: 3%">
				<h3 style="margin-left: -15px">Retailer Backorder Report</h3>
				<div class="row">
					<div class="form-group">
						<label for="retailer-backorder-feq">Frequency</label>
						<?php echo CHtml::dropDownList('retailer-backorder-feq', @$org->extra['retailer-backorder-feq'], Pcaw::$feqs, ['class' => 'form-control', 'empty' => 'Select One']); ?>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label for="retailer-backorder-rec">Recipient (<span class="required">Separate by ;</span>)</label>
						<?php echo CHtml::textArea('retailer-backorder-rec', @$org->extra['retailer-backorder-rec'], ['class' => 'form-control', 'rows' => 8]); ?>
					</div>
				</div>
			</div>
			<div class="col-xs-3" style="padding-right: 3%">
				<h3 style="margin-left: -15px">Delivery Despatch Consumer</h3>
				<div class="row">
					<div class="col-xs-6" style="padding-left: 0">
						<div class="form-group">
							<label for="delivery-despatch-consumer-feq">Frequency</label>
							<?php echo CHtml::dropDownList('delivery-despatch-consumer-feq', @$org->extra['delivery-despatch-consumer-feq'], Pcaw::$feqs, ['class' => 'form-control', 'empty' => 'Select One']); ?>
						</div>
					</div>
					<div class="col-xs-6" style="padding-right: 0">
						<div class="form-group">
							<label for="delivery-despatch-consumer-range">Range</label>
							<?php echo CHtml::dropDownList('delivery-despatch-consumer-range', @$org->extra['delivery-despatch-consumer-range'], Pcaw::$ranges, ['class' => 'form-control', 'empty' => 'Select One']); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label for="delivery-despatch-consumer-rec">Recipient (<span class="required">Separate by ;</span>)</label>
						<?php echo CHtml::textArea('delivery-despatch-consumer-rec', @$org->extra['delivery-despatch-consumer-rec'], ['class' => 'form-control', 'rows' => 8]); ?>
					</div>
				</div>
			</div>
			<div class="col-xs-3" style="padding-right: 3%">
				<h3 style="margin-left: -15px">Delivery Despatch Retailer</h3>
				<div class="row">
					<div class="col-xs-6" style="padding-left: 0">
						<div class="form-group">
							<label for="delivery-despatch-retailer-feq">Frequency</label>
							<?php echo CHtml::dropDownList('delivery-despatch-retailer-feq', @$org->extra['delivery-despatch-retailer-feq'], Pcaw::$feqs, ['class' => 'form-control', 'empty' => 'Select One']); ?>
						</div>
					</div>
					<div class="col-xs-6" style="padding-right: 0">
						<div class="form-group">
							<label for="delivery-despatch-retailer-range">Range</label>
							<?php echo CHtml::dropDownList('delivery-despatch-retailer-range', @$org->extra['delivery-despatch-retailer-range'], Pcaw::$ranges, ['class' => 'form-control', 'empty' => 'Select One']); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label for="delivery-despatch-retailer-rec">Recipient (<span class="required">Separate by ;</span>)</label>
						<?php echo CHtml::textArea('delivery-despatch-retailer-rec', @$org->extra['delivery-despatch-retailer-rec'], ['class' => 'form-control', 'rows' => 8]); ?>
					</div>
				</div>
			</div>

			<div class="col-xs-3" style="padding-right: 3%">
				<h3 style="margin-left: -15px">Inbound Delivery Advice</h3>
				<div class="row">
					<div class="form-group">
						<label for="inbound-delivery-advice-feq">Frequency</label>
						<?php echo CHtml::dropDownList('inbound-delivery-advice-feq', @$org->extra['inbound-delivery-advice-feq'], Pcaw::$feqs, ['class' => 'form-control', 'empty' => 'Select One']); ?>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label for="inbound-delivery-advice-rec">Recipient (<span class="required">Separate by ;</span>)</label>
						<?php echo CHtml::textArea('inbound-delivery-advice-rec', @$org->extra['inbound-delivery-advice-rec'], ['class' => 'form-control', 'rows' => 8]); ?>
					</div>
				</div>
			</div>
			<div class="col-xs-3" style="padding-right: 3%">
				<h3 style="margin-left: -15px">Inbound Delivery Report</h3>
				<div class="row">
					<div class="col-xs-6" style="padding-left: 0">
						<div class="form-group">
							<label for="inbound-delivery-report-feq">Frequency</label>
							<?php echo CHtml::dropDownList('inbound-delivery-report-feq', @$org->extra['inbound-delivery-report-feq'], Pcaw::$feqs, ['class' => 'form-control', 'empty' => 'Select One']); ?>
						</div>
					</div>
					<div class="col-xs-6" style="padding-right: 0">
						<div class="form-group">
							<label for="inbound-delivery-report-range">Range</label>
							<?php echo CHtml::dropDownList('inbound-delivery-report-range', @$org->extra['inbound-delivery-report-range'], Pcaw::$ranges, ['class' => 'form-control', 'empty' => 'Select One']); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label for="inbound-delivery-report-rec">Recipient (<span class="required">Separate by ;</span>)</label>
						<?php echo CHtml::textArea('inbound-delivery-report-rec', @$org->extra['inbound-delivery-report-rec'], ['class' => 'form-control', 'rows' => 8]); ?>
					</div>
				</div>
			</div>
			<div class="col-xs-3" style="padding-right: 3%">
				<h3 style="margin-left: -15px">Goods Return</h3>
				<div class="row">
					<div class="form-group">
						<label for="goods-return-feq">Frequency</label>
						<?php echo CHtml::dropDownList('goods-return-feq', @$org->extra['goods-return-feq'], Pcaw::$feqs, ['class' => 'form-control', 'empty' => 'Select One']); ?>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label for="goods-return-rec">Recipient (<span class="required">Separate by ;</span>)</label>
						<?php echo CHtml::textArea('goods-return-rec', @$org->extra['goods-return-rec'], ['class' => 'form-control', 'rows' => 8]); ?>
					</div>
				</div>
			</div>
			<div class="col-xs-3" style="padding-right: 3%">
				<h3 style="margin-left: -15px">Minimum Stock Alert</h3>
				<div class="row">
					<div class="form-group">
						<label for="minimum-stock-alert-feq">Frequency</label>
						<?php echo CHtml::dropDownList('minimum-stock-alert-feq', @$org->extra['minimum-stock-alert-feq'], Pcaw::$feqs, ['class' => 'form-control', 'empty' => 'Select One']); ?>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label for="minimum-stock-alert-rec">Recipient (<span class="required">Separate by ;</span>)</label>
						<?php echo CHtml::textArea('minimum-stock-alert-rec', @$org->extra['minimum-stock-alert-rec'], ['class' => 'form-control', 'rows' => 8]); ?>
					</div>
				</div>
			</div>

			<div class="col-xs-3" style="padding-right: 3%">
				<h3 style="margin-left: -15px">Stock On Hand Report</h3>
				<div class="row">
					<div class="form-group">
						<label for="stock-on-hand-feq">Frequency</label>
						<?php echo CHtml::dropDownList('stock-on-hand-feq', @$org->extra['stock-on-hand-feq'], Pcaw::$feqs, ['class' => 'form-control', 'empty' => 'Select One']); ?>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label for="stock-on-hand-rec">Recipient (<span class="required">Separate by ;</span>)</label>
						<?php echo CHtml::textArea('stock-on-hand-rec', @$org->extra['stock-on-hand-rec'], ['class' => 'form-control', 'rows' => 8]); ?>
					</div>
				</div>
			</div>
			<div class="col-xs-3" style="padding-right: 3%">
				<h3 style="margin-left: -15px">Invoice</h3>
				<div class="row">
					<div class="form-group">
						<label for="invoice-feq">Send Or Not</label>
						<?php echo CHtml::dropDownList('invoice-feq', @$org->extra['invoice-feq'], ['yes' => 'Yes', 'no' => 'No'], ['class' => 'form-control', 'empty' => 'Select One']); ?>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label for="invoice-rec">Recipient (<span class="required">Separate by ;</span>)</label>
						<?php echo CHtml::textArea('invoice-rec', @$org->extra['invoice-rec'], ['class' => 'form-control', 'rows' => 8]); ?>
					</div>
				</div>
			</div>

			<div class="col-xs-3" style="padding-right: 3%">
				<h3 style="margin-left: -15px">Hold Task Report</h3>
				<div class="row">
					<div class="form-group">
						<label for="hold-task-feq">Frequency</label>
						<?php echo CHtml::dropDownList('hold-task-feq', @$org->extra['hold-task-feq'], Pcaw::$feqs, ['class' => 'form-control', 'empty' => 'Select One']); ?>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label for="hold-task-rec">Recipient (<span class="required">Separate by ;</span>)</label>
						<?php echo CHtml::textArea('hold-task-rec', @$org->extra['hold-task-rec'], ['class' => 'form-control', 'rows' => 8]); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<?php echo CHtml::submitButton('Save', array('class'=>'btn btn-primary ajax-link')); ?>
		</div>
		<?php $this->endWidget(); ?>
	</div>
</div>