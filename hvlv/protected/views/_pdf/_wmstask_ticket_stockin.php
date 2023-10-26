<div class="container1">
	<div style="float:right; font-size: 40px; font-weight: bold; text-align: center; padding: 15px 25px 0 0;">
		<?=empty($model->mainTask) ? $model->getNo() : $model->mainTask->getNo();?>
	</div>
	<img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl.'/images/'.(Yii::app()->name == 'PEP'? 'PEP_logo.png' : 'PCAE_Logo.png');?>" alt="PCA Express" width="360" />
</div>
<div class="container2">
	<div class="row1">
		<div class="col1">
			<div class="titleDIV">
				<span class="title">Client</span>
			</div>
			<p class="content2"><?php
				if (strlen($model->job->customer->name) > 25) {
					echo '<span style="font-size: 25px">' . $model->job->customer->name . '</span>';
				} else {
					echo $model->job->customer->name . '&nbsp;';
				}
			?></p>
		</div>
	</div>
	<div class="row1">
		<div class="col3" style="margin-right: 2%">
			<div class="titleDIV">
				<span class="title">Client's Own Delivery REF.</span>
			</div>
			<p class="content2" style="text-align: left; overflow: hidden; height: 90px; padding: 10px; font-size: 30px">
				<?php
					$total = 0;
					$words = [];
					for ($k = 0; $k < strlen($model->ref); $k++) {
						if (preg_match('/[\x{4e00}-\x{9fa5}·\.]+/u', substr($model->ref, $k, 3))) {
							$word = substr($model->ref, $k, 3);
							$total += 1.5;
							$k += 2;
						} else {
							$word = substr($model->ref, $k, 1);
							$total += 1;
						}
						$words[] = $word;
					}

					$lines = [];
					if ($total <= 18) {
						echo implode('', $words);
					} else {
						echo '<span style="font-size: 25px">' . implode('', $words) . '</span>';
					}
				?>
			</p>
		</div>
		<div class="col3" style="margin-left: 2%">
			<div class="titleDIV">
				<span class="title">Total NO. of Package</span>
			</div><p class="content2" style="height: 90px;">
			<?php if (!empty($model->getDemand()['pi_expect'])) { ?>
				<?=$i . '/' . $model->getDemand()['pi_expect'] . '&nbsp;'?>
			<?php } else { ?>
				&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;
			<?php } ?></p>
		</div>
	</div>
	<div class="row2">
		<div class="col2">
			<div class="titleDIV">
				<span class="title">Note</span>
			</div>
			<p class="content2" style="padding: 15px 0;min-height: 200px"></p>
		</div>
	</div>
	<div class="row3">
		<div class="col2" style="border-bottom: 0;">
			<div class="titleDIV">
				<span class="title">Barcode</span>
			</div>
			<?php Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true); ?>
			<p class="content1"><?php
				if (!empty($model->getDemand()['pi_expect'])) {
					$plt = 'CW' . sprintf('%06d', $model->id) . sprintf('%04d', $i);
				} else {
					$plt = $model->getNo();
				}
				$bc = new TCPDFBarcode($plt, 'C128');
				$bc->getBarcodeSVG(4, 120, 'black');
			?><br /><span style="font-size: 40px;"><?=$plt;?></span></p>
		</div>
	</div>
	<div class="row4">
		<div class="col1">
			<p class="content3" style="font-size: 25px">
				<span style="font-size: 20px; font-style: italic; color: #00467F; font-weight: bold">Top Logistics</span><br />
				6C The Crescent, Kingsgrove, NSW 2208<br />
				Contact: Kevin - 0426 162 286
			</p>
		</div>
	</div>
</div>