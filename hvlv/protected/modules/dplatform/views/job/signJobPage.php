<style type="text/css">
	table.chart1 {
		margin: auto;
	}
</style>
<div class="form">
	<?php
	$id = $job->id;
	$url = $this->createUrl('job/signJobPage');
	if (isset($ids) && $ids != "") {
		$id = $ids;
	}
	$camodel = new CargoProcess();
	$form = $this->beginWidget(
		'CActiveForm',
		array(
			'id' => 'job-acr_form',
			'enableAjaxValidation' => false,
			'action' => $url . "?id=" . $id,
			//'htmlOptions' => ["class" => "ifrm-form", "enctype" => "multipart/form-data"]
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
			<div style="font-size:1em;line-height: 2em;margin:auto;width: 80%">
				<div>
					<h3>C-Job No:<?php echo " ".$job->job_no?></h3>
				</div>
				<div>
					<h3>Receiver(Pls Print Full Names): </h3>
					<div>
						<?php if (empty($job->mdata["sprint"])) : ?>
							<?php echo CHtml::textField('CargoProcess[sprint]'); ?>
						<?php else : ?>
							<label><?= $job->mdata["sprint"] ?></label>
						<?php endif; ?>
					</div>
				</div>
				<br />
				<h3>Take a picture</h3>
				<?php
				$this->widget('CMultiFileUpload', array(
					'model' => $camodel,
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
					<p>Signature:</p>
					<div>
						<?php if (empty($job->mdata["sig"])) : ?>
							<canvas id="sig" style="border: 1px solid #ddd;"></canvas>
							<?php echo CHtml::hiddenField('CargoProcess[sig]'); ?>
						<?php else : ?>
							<img src="<?= $job->mdata["sig"] ?>">
						<?php endif; ?>
					</div>
				</div>
				<div>
					<p>Date:</p>
					<div>
						<?php if (empty($job->mdata["sdate"])) : ?>
							<label><?= date('Y-m-d') ?></label>
						<?php else : ?>
							<label><?= $job->mdata["sdate"] ?></label>
						<?php endif; ?>
					</div>
				</div>
				<?php if (empty($job->mdata["sig"])) : ?>
					<div class="row buttons">
						<?php echo CHtml::button($this->t('Clear Signature'), array('class' => 'clear_btn')); ?> &nbsp;
						<?php echo CHtml::submitButton($this->t('Submit'), array('class' => 'save_btn', 'id' => 'savebb', 'style' => 'width:10em;')); ?>
					</div>
				<?php endif; ?>
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
					<?php if (yii::app()->user->id != 3595) { ?>
						if (document.getElementsByClassName("MultiFile-label").length == 0) {
							alert("Please Upload the Photos");
							return false;
						}
					<?php } ?>
					//alert(signaturePad._isEmpty);
					// return false;
					if (signaturePad._isEmpty) {
						alert("Please Sign the Form");
						return false;
					}
					$('#savebb').attr("disabled", "disabled");
					$('#CargoProcess_sig').val(signaturePad.toDataURL());
					// return true;
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