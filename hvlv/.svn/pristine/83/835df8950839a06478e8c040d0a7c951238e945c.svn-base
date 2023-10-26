<style type="text/css">
	table.chart1 {
		margin: auto;
	}
</style>
<div class="form">
	<?php
	$id = $job->id;
	$models[] = $job;
	$url = $this->createUrl('job/signErrorPage');
	if (isset($ids) && $ids != "") {
		$id = $ids;
	}

	$form = $this->beginWidget(
		'CActiveForm',
		array(
			'id' => 'job-acr_form',
			'enableAjaxValidation' => false,
			'action' => $url . "?id=" . $id,
			'htmlOptions' => ["class" => "ifrm-form", "enctype" => "multipart/form-data"]
		),
	);
	?>


	<div class="row">
		<div class="form-group">
			<table width="100%" cellspacing="0" cellpadding="0" style="height: 50px; ">
				<tr>
					<td style="text-align: center; font-size: 45px;font-weight: bold;padding-bottom:10px;" width="100%">Cargo Receipt</td>
				</tr>
			</table>
			<?php
			if ($job->type == CargoProcess::NORMAL_CARGO) {
				foreach ($models as $key => $model) {
					$boolIsOverWeight = ($model->getWeight(true) / $model->getPackages() > 25) ? true : false;
					$boolIsOverSize = $model->getIsOverSize();
					$boolHasForklift = $model->getHasForklift();
					echo '<table  width="80%"cellspacing="0" cellpadding="0"  class="chart1">';
					echo '       <tr ><th class="even1" width="50%" align="left">HOUSE BILL:</th><td width="50%" style="font-weight:bold;">' . @$model->getRef() . '</td></tr>';
					echo '       <tr ><th class="even1" width="50%" align="left">QTY</th><td width="50%">' . @$model->getPackages() . '&nbsp;Pcs</td></tr>';
					if ($boolIsOverWeight) {
						echo '     <tr ><th class="even1" width="50%" align="left">Weight</th><td width="50%" style="color:red">' . @$model->getWeight(true) . 'Kg <img src=' . Yii::app()->request->baseUrl . '/images/up.png height="15" width="15"/></td></tr>';
					} else {
						echo '     <tr ><th class="even1" width="50%" align="left">Weight</th><td width="50%">' . @$model->getWeight(true) . 'Kg</td></tr>';
					}
					if ($boolIsOverSize) {
						echo '       <tr ><th class="even1" width="50%" align="left">CBM</th><td width="50%">' . @$model->getTotalCBM() . ' <img src=' . Yii::app()->request->baseUrl . '/images/up.png height="15" width="15"/></td></tr>';
					} else {
						echo '       <tr ><th class="even1" width="50%" align="left">CBM</th><td width="50%">' . @$model->getTotalCBM() . '</td></tr>';
					}
					if (!$boolHasForklift) {
						echo '       <tr ><th class="even1" width="50%" align="left"></th><td width="50%"><img src=' . Yii::app()->request->baseUrl . '/images/folklift.png height="50" width="50"/></td></tr>';
					}

					echo '</table>';
					echo '</br>';
				}
			} else {
				foreach ($models as $key => $model) {
					echo '<table  width="80%"cellspacing="0" cellpadding="0"  class="chart1">';
					echo '       <tr ><th class="even1" width="50%" align="left">HOUSE BILL:</th><td width="50%" style="font-weight:bold;">' . $job->getFBAConnote(true) . '</td></tr>';
					echo '</table>';
					echo '</br>';
				}
			}
			?>
			<div style="font-size:1em;line-height: 2em;margin:auto;width: 80%">
				<div>
					Delivery to:<br /><i>
						<p style="font-size:1em;">
							<?php
							$address = '';
							if (!empty($models[0]->shipment->mdata['cargo_receipt_addr']) && ($models[0]->shipment->mdata['cargo_receipt_addr'] != "")) {
								$address = $models[0]->shipment->mdata['cargo_receipt_addr'];
							} else {
								$address = @$models[0]->shipment->cnee->name . "," . @$models[0]->shipment->cnee->address . ",<br/>" . @$models[0]->shipment->cnee->suburb . "," . @$models[0]->shipment->cnee->state . ",<br/>" . @$models[0]->shipment->cnee->postcode . ";    Tel:<a href=tel://" . @$models[0]->shipment->cnee->modifyToAustralianPhoneNumber() . ">" . @$models[0]->shipment->cnee->modifyToAustralianPhoneNumber() . "</a>";
								if (!empty($models[0]->cnee->company)) {
									$address .= ";    Company:" . @$models[0]->shipment->cnee->company;
								}
							}
							echo $address;
							?>

						</p>
					</i>
				</div>


				<?php
				if ($job->type == CargoProcess::NORMAL_CARGO) {
					if (!empty($job->note)) {
				?>
						<div>
							<h3>OP Note: </h3>
							<div>
								<?php echo CHtml::textArea('note', @$job->note, array('rows' => 2, 'cols' => 60, 'readonly' => true,)); ?>
							</div>
						</div>
					<?php
					}
					//$customerResponseArray = array('address_type','residential_unloading','unloading_street','OtherInquery');
					if (!empty($job->mdata['customer_confirmed'])) {
					?>
						<div>
							<h3>Customer Response: </h3>
							<div>
								<?php echo CHtml::textArea('customer_response', $model->getCustomerResponseForDriverPart(), array('rows' => 2, 'cols' => 60, 'readonly' => true,)); ?>
							</div>
						</div>
				<?php
					}
				}
				?>

				<div>
					<h3>Return Reason: </h3>
					<div>
						<?php if (empty($job->mdata["CargoProcessErrorType"])) : ?>
							<?php echo CHtml::dropDownList('CargoProcess[CargoProcessErrorType]', "", CargoProcess::$errorTypeList)
							//CHtml::textField('CargoProcess[CargoProcessErrorType]',CargoProcess::$errorTypeList); 
							?>
						<?php else : ?>

							<label><?= $job->mdata["CargoProcessErrorType"] ?></label>

						<?php endif; ?>
					</div>
				</div>

				<br />
				<h3>Take a picture</h3>
				<?php
				$this->widget('CMultiFileUpload', array(
					'model' => $job,
					'attribute' => 'photos',
					//  'accept'=>'jpg|gif|png',
					//  'htmlOptions'=>[
					// 	"accept"=>"image/gif, image/jpeg",
					// ],
					'options' => array(),
					'denied' => 'File is not allowed',
					'max' => 10, // max 10 files
				));
				?>

				<br />
				<div>
					<h3>WareHouse Staff (Pls Print Full Names): </h3>
					<div>
						<?php if (empty($job->mdata["sprinterror"])) : ?>
							<?php echo CHtml::textField('CargoProcess[sprinterror]', ""); ?>
						<?php else : ?>

							<label><?= $job->mdata["sprinterror"] ?></label>

						<?php endif; ?>
					</div>
				</div>

				<div>
					<p>Signature:</p>
					<div>

						<?php if (empty($job->mdata["sigerror"])) : ?>
							<canvas id="sig" style="border: 1px solid #ddd;"></canvas>
							<?php echo CHtml::hiddenField('CargoProcess[sig]'); ?>

						<?php else : ?>
							<img src="<?= $job->mdata["sigerror"] ?>">

						<?php endif; ?>

					</div>
				</div>
				<div>
					<p>Date:</p>
					<div>

						<?php if (empty($job->mdata["sdateerror"])) : ?>
							<label><?= date('Y-m-d') ?></label>
						<?php else : ?>
							<label><?= $job->mdata["sdateerror"] ?></label>
						<?php endif; ?>

					</div>
				</div>

				<?php if (empty($job->mdata["sigerror"])) : ?>

					<div class="row buttons">
						<?php echo CHtml::button($this->t('Clear Signature'), array('class' => 'clear_btn')); ?> &nbsp;
						<?php echo CHtml::submitButton($this->t('Submit'), array('class' => 'save_btn', 'id' => 'savebb', 'style' => 'width:10em;')); ?>
					</div>
				<?php endif; ?>

				<div>
					<?php if ($job->type == CargoProcess::FBA_CARGO || $job->type == CargoProcess::B2B_CARGO) {
						echo CHtml::link($this->t('more details'), '#', array('id' => 'details'));
						$models = $job->getCargos();
						echo "<div id ='moreDetails' >";
						foreach ($models as $key => $model) {
							echo '<table  width="80%"cellspacing="0" cellpadding="0" style="border:1px solid #000000;">';
							echo '       <tr ><th class="even1" width="50%" align="left">REFERENCE NO</th><td width="50%" style="font-weight:bold;">' . @$model->getRef() . '</td></tr>';
							echo '       <tr ><th class="even1" width="50%" align="left">PO NO:</th><td width="50%" style="font-weight:bold;">';
							$index = 0;
							if (!empty($model->shipment->mdata['amazon_po'])) {
								foreach (preg_split("/[;,.\n\s+]/", $model->shipment->mdata['amazon_po']) as $h) {
									if (empty(trim($h)))    continue;
									echo trim($h) . "<br/>";
									$index++;
								}
							}
							echo '</td></tr>';
							echo '<tr ><th class="even1" width="50%" align="left">FBA NO:</th><td width="50%" style="font-weight:bold;">';
							if (!empty($model->shipment->mdata['amazon_shipment_ids'])) {
								foreach (preg_split("/[;,.\n\s+]/", $model->shipment->mdata['amazon_shipment_ids']) as $h) {
									if (empty(trim($h)))    continue;
									echo trim($h) . "<br/>";
								}
							}
							echo '</td></tr>';
							echo '       <tr ><th class="even1" width="50%" align="left">PACKAGES</th><td width="50%" style="font-weight:bold;">' . @$model->getPackages() . '</td></tr>';
							echo '       <tr ><th class="even1" width="50%" align="left">Weight(KG)</th><td width="50%" style="font-weight:bold;">' . @$model->getWeight() . '</td></tr>';
							echo '       <tr ><th class="even1" width="50%" align="left">CBM</th><td width="50%" style="font-weight:bold;">' . @$model->getTotalCBM() . '</td></tr>';
							echo '</table>';
							echo '</br>';
						}
						echo "</div>";
					}
					?>
				</div>

			</div>


		</div>

		<script type="text/javascript">
			var pcadbAdStatus = 0;
			$(function() {

				if (document.getElementById("sig") != null) {
					var signaturePad = new SignaturePad(document.getElementById("sig"));
				}

				$('input.clear_btn').on("click", function(event) {
					signaturePad.clear();
				});

				$('#job-acr_form').on("submit", function(event) {
					<?php //if(yii::app()->user->id != 3595) {
					?>
					// if(document.getElementsByClassName("MultiFile-label").length==0){
					// 	alert("Please Upload the Photos");
					// 	return false;
					// }
					<?php //}
					?>
					if (signaturePad._isEmpty || $('#CargoProcess_sprint').val() == "") {
						alert("Please Sign the Form");
						return false;
					}

					$('#savebb').attr("disabled", "disabled");
					$('#CargoProcess_sig').val(signaturePad.toDataURL());
					return true;
				});


				$("#job-acr_form").on('success', function(r) {
					$('#savebb').css("display", "none");
					return true;
				});

				$('#details').on('click', function() {
					if (pcadbAdStatus == 0) {
						$('#moreDetails').show();
						pcadbAdStatus = 1;
					} else {
						$('#moreDetails').hide();
						pcadbAdStatus = 0;
					}
					return false;
				});

			});
		</script>

		<?php $this->endWidget(); ?>