
<style type="text/css">
	p {
		font-size: 16px;
		color: black;
	}

	.display_none {
		display: none;
	}

	.btn_next {
		color: #fff;
		background-color: #007aff;
		border: 1px solid #007aff;
		width: 200px;
		height: 40px;
		float: right;
	}
</style>


<?php
$objShipment = $model->shipment;
$strHbn = $objShipment->hbn;
$numWeight = $objShipment->weight;
$numPkg = $objShipment->pkg;
$numCBM = $model->getTotalCBM();
$strFullAddress = $objShipment->cnee->fullAddress();
$strName = $objShipment->cnee->name;
$strTel = $objShipment->cnee->tel;
$strEmail = $objShipment->cnee->email;
// $numUnloadingFee = 20*$numCBM; // $20/m3
$strInvoiceId = $model->mdata['customer_confirmed']['surcharge_invoice'];

// Yii::app()->name = 'TLA';
$objInvoice = Invoice::model()->findByPk($strInvoiceId);
$numTotalFee = $objInvoice->total;
?>

<div class="content-padded">
	<h3>PAYMENT:</h3>
	<div class="form">


		<?php
		$form = $this->beginWidget('CActiveForm', array(
			'id' => 'cargo-confirm-form',
			'enableAjaxValidation' => false,
			'htmlOptions' => ['enctype' => 'multipart/form-data'],
		)); ?>

		<div class="row">

			<div class="col-12" style="padding-left: 5px;">
				</br>
				</br>
				<h4>Your shipment Information</h4>
				<p>Ref No.<?= $strHbn ?></p>
				<div>
					<?= $numWeight ?> kg<br />
					<?= $numPkg ?> pcs<br />
					<?= $numCBM ?> cbm<br />
				</div>
				<br />
				<?php
				$listLine = InvLine::model()->findAll('inv_id = :inv_id', [':inv_id' => $strInvoiceId]);
				foreach ($listLine as $objLine) {
					echo '<p>' . $objLine->det . ' $' . $objLine->amount . ' Inc GST </p>';
				}
				?>
				<br />
				<p>Total= $<?= $numTotalFee ?> Inc GST </p>
			</div>

			<div class="col-12" style="padding-left: 5px;">
				</br>
				</br>
				<h4>Select Payment</h4>
				<?php echo CHtml::RadioButtonList(
					'select_payment',
					'Paypal',
					// ['paypal'=>'Paypal','card_paypal'=>'Credit Card via Paypal','supay'=>'Wechat/Alipay','poli'=>'Bank Transfer via POLI'],
					['Paypal' => 'Paypal/Credit Card', 'Wechat' => 'Wechat', 'Alipay' => 'Alipay'],
					['labelOptions' => ['class' => 'radio_label'], 'separator' => '<br/>']
				) ?>
				<br />
				<button type="button" onclick="funcNextShipment()" class="btn_next">Next</button>
			</div>

		</div>

		<?php $this->endWidget(); ?>

	</div>
</div>

<script>
	function funcNextShipment() {
		strSelectPayment = $("input[name='select_payment']:checked").val();
		<?php
			$strUrlPrefix = 'https://' . $_SERVER['HTTP_HOST'];
			if ($strUrlPrefix == 'https://localhost:82') {
				$strUrlPrefix = 'http://localhost:82/wma/booking';
			}
			$strUrl = $strUrlPrefix.'/makeSupay?id='.$strInvoiceId.'&act=' ;
		?>
		$.get('<?= $strUrl?>' + strSelectPayment, function(e) {
			e = JSON.parse(e);
			window.open(e.url, target = "_self");
		});
	}
</script>