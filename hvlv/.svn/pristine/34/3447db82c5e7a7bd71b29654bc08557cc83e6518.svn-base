<?php if (!empty($bill)) { ?>
	<div style="right: 20px;position: absolute;">
		<div class="icon" style="background-position:-16px 0"></div>
		<a class="export_search" href="#" data-baseurl="<?=$this->createUrl('consolWeightCheck/export', array('id' => $bill->id, 'type' => 'bill'));?>" target="_blank" title="Export">Export</a>
	</div>
	<h1><?=$this->t($bill->no);?></h1>
	<table style="width:100%; text-align:center;">
		<thead>
			<tr>
				<th rowspan="2">No</th>
				<th rowspan="2">AWB</th>
				<th rowspan="2">ETD</th>
				<th rowspan="2">Channel</th>
				<th rowspan="2">Invoice #</th>
				<th rowspan="2">Adjust Rate</th>
				<th colspan="3">Wt./Awb Wt.</th>
				<th colspan="3">Pks</th>
				<th colspan="3">Clearance</th>
				<th colspan="3">Delivery</th>
				<th colspan="3">Duty</th>
				<th colspan="3">Others</th>
				<th colspan="3">Total</th>
			</tr>
			<tr>
				<th style="color:rgba(0,0,200,0.4)">ACCR</th>
				<th style="color:rgba(200,0,0,0.4)">ACT</th>
				<th>Diff</th>
				<th style="color:rgba(0,0,200,0.4)">ACCR</th>
				<th style="color:rgba(200,0,0,0.4)">ACT</th>
				<th>Diff</th>
				<th style="color:rgba(0,0,200,0.4)">ACCR</th>
				<th style="color:rgba(200,0,0,0.4)">ACT</th>
				<th>Diff</th>
				<th style="color:rgba(0,0,200,0.4)">ACCR</th>
				<th style="color:rgba(200,0,0,0.4)">ACT</th>
				<th>Diff</th>
				<th style="color:rgba(0,0,200,0.4)">ACCR</th>
				<th style="color:rgba(200,0,0,0.4)">ACT</th>
				<th>Diff</th>
				<th style="color:rgba(0,0,200,0.4)">ACCR</th>
				<th style="color:rgba(200,0,0,0.4)">ACT</th>
				<th>Diff</th>
				<th style="color:rgba(0,0,200,0.4)">ACCR</th>
				<th style="color:rgba(200,0,0,0.4)">ACT</th>
				<th>Diff</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = 0;
			$tot = array('wt_accr' => 0, 'wt_awb' => 0, 'wt_act' => 0, 'wt_diff' => 0, 'pks_accr' => 0, 'pks_act' => 0, 'pks_diff' => 0, 'clear_accr' => 0, 'clear_act' => 0, 'clear_diff' => 0, 'delivery_accr' => 0, 'delivery_act' => 0, 'delivery_diff' => 0, 'duty_accr' => 0, 'duty_act' => 0, 'duty_diff' => 0, 'others_accr' => 0, 'others_act' => 0, 'others_diff' => 0, 'total_accr' => 0, 'total_act' => 0, 'total_diff' => 0);
			foreach ($data as $id => $consol) {
				$i ++;
				$subtot = array('wt_accr' => 0, 'wt_awb' => 0, 'wt_act' => 0, 'wt_diff' => 0, 'pks_accr' => 0, 'pks_act' => 0, 'pks_diff' => 0, 'clear_accr' => 0, 'clear_act' => 0, 'clear_diff' => 0, 'delivery_accr' => 0, 'delivery_act' => 0, 'delivery_diff' => 0, 'duty_accr' => 0, 'duty_act' => 0, 'duty_diff' => 0, 'others_accr' => 0, 'others_act' => 0, 'others_diff' => 0, 'total_accr' => 0, 'total_act' => 0, 'total_diff' => 0);
				$adjust_rate = floatval($consol['consol']['weight']) ? floatval($consol['consol']['awb_weight']) / floatval($consol['consol']['weight']) : 1;
				echo '<tr style="background: ' . ((($i-1) % 2 == 0) ? 'white' : '#DEDEDE') . '">
					<td><a href="' . Yii::app()->createUrl('consolWeightCheck/detail', array('id' => $consol['consol']['id'], 'type' => 'consol')) . '" class="tab_link" title="' . $consol['consol']['no'] . '">' . $consol['consol']['no'] . '</a></td>
					<td>' . $consol['consol']['awb'] . '</td>
					<td>' . $consol['consol']['etd'] . '</td>
					<td>' . ExChannel::getName($consol['consol']['poc']) . '</td>
					<td><a href="' . Yii::app()->createUrl('consolWeightCheck/detail', array('id' => $bill->id, 'type' => 'billing')) . '" class="tab_link" title="' . $bill->no . '">' . $bill->billing_cref . ' / ' . $bill->no . '</a></td>
					<td>' . round(floatval($adjust_rate), 2) . '</td>
					<td>' . floatval($consol['consol']['weight']) . '/' . floatval($consol['consol']['awb_weight']) . '</td>
					<td>' . floatval($consol['bill'][2]) . '</td>
					<td>' . round(floatval($consol['consol']['awb_weight']) - floatval($consol['bill'][2]), 2) . '</td>
					<td>' . intval($consol['consol']['qty']) . '</td>
					<td>' . intval($consol['bill'][1]) . '</td>
					<td>' . round(intval($consol['consol']['qty']) - intval($consol['bill'][1]), 2) . '</td>
					<td>' . round(floatval($consol['cost'][4]) * floatval($adjust_rate), 2) . '</td>
					<td>' . floatval($consol['bill'][4]) . '</td>
					<td>' . round(floatval($consol['cost'][4]) * floatval($adjust_rate) - floatval($consol['bill'][4]), 2) . '</td>
					<td>' . round(floatval($consol['cost'][5]) * floatval($adjust_rate), 2) . '</td>
					<td>' . floatval($consol['bill'][5]) . '</td>
					<td>' . round(floatval($consol['cost'][5]) * floatval($adjust_rate) - floatval($consol['bill'][5]), 2) . '</td>
					<td>' . round(floatval($consol['cost'][6]) * floatval($adjust_rate), 2) . '</td>
					<td>' . floatval($consol['bill'][6]) . '</td>
					<td>' . round(floatval($consol['cost'][6]) * floatval($adjust_rate) - floatval($consol['bill'][6]), 2) . '</td>
					<td>' . round(floatval($consol['cost'][9]) * floatval($adjust_rate), 2) . '</td>
					<td>' . floatval($consol['bill'][9]) . '</td>
					<td>' . round(floatval($consol['cost'][9]) * floatval($adjust_rate) - floatval($consol['bill'][9]), 2) . '</td>
					<td>' . round((floatval($consol['cost'][4]) + floatval($consol['cost'][5]) + floatval($consol['cost'][6]) + floatval($consol['cost'][9])) * floatval($adjust_rate), 2) . '</td>
					<td>' . round(floatval($consol['bill'][4]) + floatval($consol['bill'][5]) + floatval($consol['bill'][6]) + floatval($consol['bill'][9]), 2) . '</td>
					<td>' . round((floatval($consol['cost'][4]) + floatval($consol['cost'][5]) + floatval($consol['cost'][6]) + floatval($consol['cost'][9])) * floatval($adjust_rate) - floatval($consol['bill'][4]) - floatval($consol['bill'][5]) - floatval($consol['bill'][6]) - floatval($consol['bill'][9]), 2) . '</td>
				</tr>';

				$subtot['wt_accr'] += floatval($consol['consol']['weight']);
				$subtot['wt_awb'] += floatval($consol['consol']['awb_weight']);
				$subtot['wt_act'] += floatval($consol['bill'][2]);
				$subtot['wt_diff'] += floatval($consol['consol']['awb_weight']) - floatval($consol['bill'][2]);
				$subtot['pks_accr'] += intval($consol['consol']['qty']);
				$subtot['pks_act'] += intval($consol['bill'][1]);
				$subtot['pks_diff'] += intval($consol['consol']['qty']) - intval($consol['bill'][1]);
				$subtot['clear_accr'] += floatval($consol['cost'][4]) * floatval($adjust_rate);
				$subtot['clear_act'] += floatval($consol['bill'][4]);
				$subtot['clear_diff'] += floatval($consol['cost'][4]) * floatval($adjust_rate) - floatval($consol['bill'][4]);
				$subtot['delivery_accr'] += floatval($consol['cost'][5]) * floatval($adjust_rate);
				$subtot['delivery_act'] += floatval($consol['bill'][5]);
				$subtot['delivery_diff'] += floatval($consol['cost'][5]) * floatval($adjust_rate) - floatval($consol['bill'][5]);
				$subtot['duty_accr'] += floatval($consol['cost'][6]) * floatval($adjust_rate);
				$subtot['duty_act'] += floatval($consol['bill'][6]);
				$subtot['duty_diff'] += floatval($consol['cost'][6]) * floatval($adjust_rate) - floatval($consol['bill'][6]);
				$subtot['others_accr'] += floatval($consol['cost'][9]) * floatval($adjust_rate);
				$subtot['others_act'] += floatval($consol['bill'][9]);
				$subtot['others_diff'] += floatval($consol['cost'][9]) * floatval($adjust_rate) - floatval($consol['bill'][9]);
				$subtot['total_accr'] += (floatval($consol['cost'][4]) + floatval($consol['cost'][5]) + floatval($consol['cost'][6]) + floatval($consol['cost'][9])) * floatval($adjust_rate);
				$subtot['total_act'] += floatval($consol['bill'][4]) + floatval($consol['bill'][5]) + floatval($consol['bill'][6]) + floatval($consol['bill'][9]);
				$subtot['total_diff'] += (floatval($consol['cost'][4]) + floatval($consol['cost'][5]) + floatval($consol['cost'][6]) + floatval($consol['cost'][9])) * floatval($adjust_rate) - floatval($consol['bill'][4]) - floatval($consol['bill'][5]) - floatval($consol['bill'][6]) - floatval($consol['bill'][9]);

				if (!empty($others) && !empty($consol['hists'])) {
					foreach ($consol['hists'] as $bill_id => $hist) {
						echo '<tr style="background: ' . ((($i-1) % 2 == 0) ? 'white' : '#DEDEDE') . '">
							<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
							<td><a href="' . Yii::app()->createUrl('consolWeightCheck/detail', array('id' => $bill_id, 'type' => 'billing')) . '" class="tab_link" title="' . $hist['no'] . '">' . $hist['cref'] . ' / ' . $hist['no'] . '</a></td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<td>' . ($hist[4] ? round(floatval($hist[4]), 2) : '-') . '</td>
							<td>' . ($hist[4] ? round(floatval(-$hist[4]), 2) : '-') . '</td>
							<td>-</td>
							<td>' . ($hist[5] ? round(floatval($hist[5]), 2) : '-') . '</td>
							<td>' . ($hist[5] ? round(floatval(-$hist[5]), 2) : '-') . '</td>
							<td>-</td>
							<td>' . ($hist[6] ? round(floatval($hist[6]), 2) : '-') . '</td>
							<td>' . ($hist[6] ? round(floatval(-$hist[6]), 2) : '-') . '</td>
							<td>-</td>
							<td>' . ($hist[9] ? round(floatval($hist[9]), 2) : '-') . '</td>
							<td>' . ($hist[9] ? round(floatval(-$hist[9]), 2) : '-') . '</td>
							<td>-</td>
							<td>' . (($hist[4] + $hist[5] + $hist[6] + $hist[9]) ? round(floatval($hist[4] + $hist[5] + $hist[6] + $hist[9]), 2) : '-') . '</td>
							<td>' . (($hist[4] + $hist[5] + $hist[6] + $hist[9]) ? round(floatval(-$hist[4] - $hist[5] - $hist[6] - $hist[9]), 2) : '-') . '</td>
						</tr>';

						$subtot['clear_act'] += floatval($hist[4]);
						$subtot['clear_diff'] -= floatval($hist[4]);
						$subtot['delivery_act'] += floatval($hist[5]);
						$subtot['delivery_diff'] -= floatval($hist[5]);
						$subtot['duty_act'] += floatval($hist[6]);
						$subtot['duty_diff'] -= floatval($hist[6]);
						$subtot['others_act'] += floatval($hist[9]);
						$subtot['others_diff'] -= floatval($hist[9]);
						$subtot['total_act'] += floatval($hist[4]) + floatval($hist[5]) + floatval($hist[6]) + floatval($hist[9]);
						$subtot['total_diff'] -= floatval($hist[4]) + floatval($hist[5]) + floatval($hist[6]) + floatval($hist[9]);
					}
					echo '<tr style="background: ' . ((($i-1) % 2 == 0) ? 'white' : '#DEDEDE') . '">
						<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
						<td>Subtotal</td>
						<td>' . round($subtot['wt_accr'], 2) . '/' . round($subtot['wt_awb'], 2) . '</td>
						<td>' . round($subtot['wt_act'], 2) . '</td>
						<td>' . round($subtot['wt_diff'], 2) . '</td>
						<td>' . round($subtot['pks_accr'], 2) . '</td>
						<td>' . round($subtot['pks_act'], 2) . '</td>
						<td>' . round($subtot['pks_diff'], 2) . '</td>
						<td>' . round($subtot['clear_accr'], 2) . '</td>
						<td>' . round($subtot['clear_act'], 2) . '</td>
						<td>' . round($subtot['clear_diff'], 2) . '</td>
						<td>' . round($subtot['delivery_accr'], 2) . '</td>
						<td>' . round($subtot['delivery_act'], 2) . '</td>
						<td>' . round($subtot['delivery_diff'], 2) . '</td>
						<td>' . round($subtot['duty_accr'], 2) . '</td>
						<td>' . round($subtot['duty_act'], 2) . '</td>
						<td>' . round($subtot['duty_diff'], 2) . '</td>
						<td>' . round($subtot['others_accr'], 2) . '</td>
						<td>' . round($subtot['others_act'], 2) . '</td>
						<td>' . round($subtot['others_diff'], 2) . '</td>
						<td>' . round($subtot['total_accr'], 2) . '</td>
						<td>' . round($subtot['total_act'], 2) . '</td>
						<td>' . round($subtot['total_diff'], 2) . '</td>
					</tr>';
				}

				$tot['wt_accr'] += $subtot['wt_accr'];
				$tot['wt_awb'] += $subtot['wt_awb'];
				$tot['wt_act'] += $subtot['wt_act'];
				$tot['wt_diff'] += $subtot['wt_diff'];
				$tot['pks_accr'] += $subtot['pks_accr'];
				$tot['pks_act'] += $subtot['pks_act'];
				$tot['pks_diff'] += $subtot['pks_diff'];
				$tot['clear_accr'] += $subtot['clear_accr'];
				$tot['clear_act'] += $subtot['clear_act'];
				$tot['clear_diff'] += $subtot['clear_diff'];
				$tot['delivery_accr'] += $subtot['delivery_accr'];
				$tot['delivery_act'] += $subtot['delivery_act'];
				$tot['delivery_diff'] += $subtot['delivery_diff'];
				$tot['duty_accr'] += $subtot['duty_accr'];
				$tot['duty_act'] += $subtot['duty_act'];
				$tot['duty_diff'] += $subtot['duty_diff'];
				$tot['others_accr'] += $subtot['others_accr'];
				$tot['others_act'] += $subtot['others_act'];
				$tot['others_diff'] += $subtot['others_diff'];
				$tot['total_accr'] += $subtot['total_accr'];
				$tot['total_act'] += $subtot['total_act'];
				$tot['total_diff'] += $subtot['total_diff'];
			}
			echo '<tr style="background: orange">
				<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
				<td>Total</td>
				<td>' . round($tot['wt_accr'], 2) . '/' . round($tot['wt_awb'], 2) . '</td>
				<td>' . round($tot['wt_act'], 2) . '</td>
				<td>' . round($tot['wt_diff'], 2) . '</td>
				<td>' . round($tot['pks_accr'], 2) . '</td>
				<td>' . round($tot['pks_act'], 2) . '</td>
				<td>' . round($tot['pks_diff'], 2) . '</td>
				<td>' . round($tot['clear_accr'], 2) . '</td>
				<td>' . round($tot['clear_act'], 2) . '</td>
				<td>' . round($tot['clear_diff'], 2) . '</td>
				<td>' . round($tot['delivery_accr'], 2) . '</td>
				<td>' . round($tot['delivery_act'], 2) . '</td>
				<td>' . round($tot['delivery_diff'], 2) . '</td>
				<td>' . round($tot['duty_accr'], 2) . '</td>
				<td>' . round($tot['duty_act'], 2) . '</td>
				<td>' . round($tot['duty_diff'], 2) . '</td>
				<td>' . round($tot['others_accr'], 2) . '</td>
				<td>' . round($tot['others_act'], 2) . '</td>
				<td>' . round($tot['others_diff'], 2) . '</td>
				<td>' . round($tot['total_accr'], 2) . '</td>
				<td>' . round($tot['total_act'], 2) . '</td>
				<td>' . round($tot['total_diff'], 2) . '</td>
			</tr>';
			?>
		</tbody>
	</table>
	<br>
	<button type="button" style="float:left; margin-right:10px;"><a href="<?=Yii::app()->createUrl('consolWeightCheck/detail', array('id' => $bill->id, 'type' => 'billing', 'others' => true))?>" class="tab_link" title="<?=$bill->no?>">Show Other Bills</a></button>
	<?php if (empty($others) && empty($consol['hists'])) { ?>
		<form action="<?=Yii::app()->createUrl('consolWeightCheck/confirm', array('id' => $bill->id))?>" method="post" style="float:left;">
			<input type="submit" class="grid_edit_btn" title="<?=$bill->no?>" value="Confirm" style="cursor:pointer;" />
		</form>
	<?php } ?>
<?php } else if (!empty($exconsol)) { ?>
	<div style="right: 20px;position: absolute;">
		<div class="icon" style="background-position:-16px 0"></div>
		<a class="export_search" href="#" data-baseurl="<?=$this->createUrl('consolWeightCheck/export', array('id' => $exconsol->id, 'type' => 'exconsol'));?>" target="_blank" title="Export">Export</a>
	</div>
	<h1><?=$this->t($exconsol->no);?></h1>
	<table style="width:100%; text-align:center;">
		<thead>
			<tr>
				<th rowspan="2">No</th>
				<th rowspan="2">Consol #</th>
				<th rowspan="2">AWB</th>
				<th rowspan="2">ETD</th>
				<th rowspan="2">Channel</th>
				<th rowspan="2">Adujust Rate</th>
				<th colspan="3">Wt./Awb Wt.</th>
				<th colspan="3">Pks</th>
				<th colspan="3">Clearance</th>
				<th colspan="3">Delivery</th>
				<th colspan="3">Duty</th>
				<th colspan="3">Others</th>
				<th colspan="3">Total</th>
			</tr>
			<tr>
				<th style="color:rgba(0,0,200,0.4)">ACCR</th>
				<th style="color:rgba(200,0,0,0.4)">ACT</th>
				<th>Diff</th>
				<th style="color:rgba(0,0,200,0.4)">ACCR</th>
				<th style="color:rgba(200,0,0,0.4)">ACT</th>
				<th>Diff</th>
				<th style="color:rgba(0,0,200,0.4)">ACCR</th>
				<th style="color:rgba(200,0,0,0.4)">ACT</th>
				<th>Diff</th>
				<th style="color:rgba(0,0,200,0.4)">ACCR</th>
				<th style="color:rgba(200,0,0,0.4)">ACT</th>
				<th>Diff</th>
				<th style="color:rgba(0,0,200,0.4)">ACCR</th>
				<th style="color:rgba(200,0,0,0.4)">ACT</th>
				<th>Diff</th>
				<th style="color:rgba(0,0,200,0.4)">ACCR</th>
				<th style="color:rgba(200,0,0,0.4)">ACT</th>
				<th>Diff</th>
				<th style="color:rgba(0,0,200,0.4)">ACCR</th>
				<th style="color:rgba(200,0,0,0.4)">ACT</th>
				<th>Diff</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = 0;
			$tot = array('wt_accr' => 0, 'wt_awb' => 0, 'wt_act' => 0, 'wt_diff' => 0, 'pks_accr' => 0, 'pks_act' => 0, 'pks_diff' => 0, 'clear_accr' => 0, 'clear_act' => 0, 'clear_diff' => 0, 'delivery_accr' => 0, 'delivery_act' => 0, 'delivery_diff' => 0, 'duty_accr' => 0, 'duty_act' => 0, 'duty_diff' => 0, 'others_accr' => 0, 'others_act' => 0, 'others_diff' => 0, 'total_accr' => 0, 'total_act' => 0, 'total_diff' => 0);
			foreach ($data as $bill_id => $bill) {
				$i ++;
				$subtot = array('wt_accr' => 0, 'wt_awb' => 0, 'wt_act' => 0, 'wt_diff' => 0, 'pks_accr' => 0, 'pks_act' => 0, 'pks_diff' => 0, 'clear_accr' => 0, 'clear_act' => 0, 'clear_diff' => 0, 'delivery_accr' => 0, 'delivery_act' => 0, 'delivery_diff' => 0, 'duty_accr' => 0, 'duty_act' => 0, 'duty_diff' => 0, 'others_accr' => 0, 'others_act' => 0, 'others_diff' => 0, 'total_accr' => 0, 'total_act' => 0, 'total_diff' => 0);
				foreach ($bill['consols'] as $id => $consol) {
					$adjust_rate = floatval($consol['consol']['weight']) ? floatval($consol['consol']['awb_weight']) / floatval($consol['consol']['weight']) : 1;
					echo '<tr style="background: ' . ((($i-1) % 2 == 0) ? 'white' : '#DEDEDE') . '">
						<td>' . (($consol['consol']['no'] == $exconsol->no) ? '<a href="' . Yii::app()->createUrl('consolWeightCheck/detail', array('id' => $bill_id, 'type' => 'billing')) . '" class="tab_link" title="' . $bill['bill']->no . '">' . $bill['bill']->billing_cref . ' / ' . $bill['bill']->no . '</a>' : '-') . '</td>
						<td><a href="' . Yii::app()->createUrl('consolWeightCheck/detail', array('id' => $consol['consol']['id'], 'type' => 'consol')) . '" class="tab_link" title="' . $consol['consol']['no'] . '">' . $consol['consol']['no'] . '</a></td>
						<td>' . $consol['consol']['awb'] . '</td>
						<td>' . $consol['consol']['etd'] . '</td>
						<td>' . ExChannel::getName($consol['consol']['poc']) . '</td>
						<td>' . round(floatval($adjust_rate), 2) . '</td>
						<td>' . ($i == 1 ? floatval($consol['consol']['weight']) . '/' . floatval($consol['consol']['awb_weight']) : '-') . '</td>
						<td>' . floatval($consol['bill'][2]) . '</td>
						<td>' . ($i == 1 ? round(floatval($consol['consol']['awb_weight']) - floatval($consol['bill'][2]), 2) : floatval(-$consol['bill'][2])) . '</td>
						<td>' . ($i == 1 ? intval($consol['consol']['qty']) : '-') . '</td>
						<td>' . intval($consol['bill'][1]) . '</td>
						<td>' . ($i == 1 ? round(intval($consol['consol']['qty']) - intval($consol['bill'][1]), 2) : intval(-$consol['bill'][1])) . '</td>
						<td>' . ($i == 1 ? round(floatval($consol['cost'][4]) * floatval($adjust_rate), 2) : '-') . '</td>
						<td>' . floatval($consol['bill'][4]) . '</td>
						<td>' . ($i == 1 ? round(floatval($consol['cost'][4]) * floatval($adjust_rate) - floatval($consol['bill'][4]), 2) : floatval(-$consol['bill'][4])) . '</td>
						<td>' . ($i == 1 ? round(floatval($consol['cost'][5]) * floatval($adjust_rate), 2) : '-') . '</td>
						<td>' . floatval($consol['bill'][5]) . '</td>
						<td>' . ($i == 1 ? round(floatval($consol['cost'][5]) * floatval($adjust_rate) - floatval($consol['bill'][5]), 2) : floatval(-$consol['bill'][5])) . '</td>
						<td>' . ($i == 1 ? round(floatval($consol['cost'][6]) * floatval($adjust_rate), 2) : '-') . '</td>
						<td>' . floatval($consol['bill'][6]) . '</td>
						<td>' . ($i == 1 ? round(floatval($consol['cost'][6]) * floatval($adjust_rate) - floatval($consol['bill'][6]), 2) : floatval(-$consol['bill'][6])) . '</td>
						<td>' . ($i == 1 ? round(floatval($consol['cost'][9]) * floatval($adjust_rate), 2) : '-') . '</td>
						<td>' . floatval($consol['bill'][9]) . '</td>
						<td>' . ($i == 1 ? round(floatval($consol['cost'][9]) * floatval($adjust_rate) - floatval($consol['bill'][9]), 2) : floatval(-$consol['bill'][9])) . '</td>
						<td>' . ($i == 1 ? round((floatval($consol['cost'][4]) + floatval($consol['cost'][5]) + floatval($consol['cost'][6]) + floatval($consol['cost'][9])) * floatval($adjust_rate), 2) : '-') . '</td>
						<td>' . round(floatval($consol['bill'][4]) + floatval($consol['bill'][5]) + floatval($consol['bill'][6]) + floatval($consol['bill'][9]), 2) . '</td>
						<td>' . ($i == 1 ? round((floatval($consol['cost'][4]) + floatval($consol['cost'][5]) + floatval($consol['cost'][6]) + floatval($consol['cost'][9])) * floatval($adjust_rate) - floatval($consol['bill'][4]) - floatval($consol['bill'][5]) - floatval($consol['bill'][6]) - floatval($consol['bill'][9]), 2) : (floatval(-$consol['bill'][4]) + floatval(-$consol['bill'][5]) + floatval(-$consol['bill'][6]) + floatval(-$consol['bill'][9]))) . '</td>
					</tr>';

					$subtot['wt_accr'] += ($i == 1) ? floatval($consol['consol']['weight']) : 0;
					$subtot['wt_awb'] += ($i == 1) ? floatval($consol['consol']['awb_weight']) : 0;
					$subtot['wt_act'] += floatval($consol['bill'][2]);
					$subtot['wt_diff'] += ($i == 1) ? floatval($consol['consol']['awb_weight']) - floatval($consol['bill'][2]) : floatval(-$consol['bill'][2]);
					$subtot['pks_accr'] += ($i == 1) ? intval($consol['consol']['qty']) : 0;
					$subtot['pks_act'] += intval($consol['bill'][1]);
					$subtot['pks_diff'] += ($i == 1) ? intval($consol['consol']['qty']) - intval($consol['bill'][1]) : intval(-$consol['bill'][1]);
					$subtot['clear_accr'] += ($i == 1) ? floatval($consol['cost'][4]) * floatval($adjust_rate) : 0;
					$subtot['clear_act'] += floatval($consol['bill'][4]);
					$subtot['clear_diff'] += ($i == 1) ? floatval($consol['cost'][4]) * floatval($adjust_rate) - floatval($consol['bill'][4]) : intval(-$consol['bill'][4]);
					$subtot['delivery_accr'] += ($i == 1) ? floatval($consol['cost'][5]) * floatval($adjust_rate) : 0;
					$subtot['delivery_act'] += floatval($consol['bill'][5]);
					$subtot['delivery_diff'] += ($i == 1) ? floatval($consol['cost'][5]) * floatval($adjust_rate) - floatval($consol['bill'][5]) : floatval(-$consol['bill'][5]);
					$subtot['duty_accr'] += ($i == 1) ? floatval($consol['cost'][6]) * floatval($adjust_rate) : 0;
					$subtot['duty_act'] += floatval($consol['bill'][6]);
					$subtot['duty_diff'] += ($i == 1) ? floatval($consol['cost'][6]) * floatval($adjust_rate) - floatval($consol['bill'][6]) : floatval(-$consol['bill'][6]);
					$subtot['others_accr'] += ($i == 1) ? floatval($consol['cost'][9]) * floatval($adjust_rate) : 0;
					$subtot['others_act'] += floatval($consol['bill'][9]);
					$subtot['others_diff'] += ($i == 1) ? floatval($consol['cost'][9]) * floatval($adjust_rate) - floatval($consol['bill'][9]) : floatval(-$consol['bill'][9]);
					$subtot['total_accr'] += ($i == 1) ? (floatval($consol['cost'][4]) + floatval($consol['cost'][5]) + floatval($consol['cost'][6]) + floatval($consol['cost'][9])) * floatval($adjust_rate) : 0;
					$subtot['total_act'] += floatval($consol['bill'][4]) + floatval($consol['bill'][5]) + floatval($consol['bill'][6]) + floatval($consol['bill'][9]);
					$subtot['total_diff'] += ($i == 1) ? ((floatval($consol['cost'][4]) + floatval($consol['cost'][5]) + floatval($consol['cost'][6]) + floatval($consol['cost'][9])) * floatval($adjust_rate) - floatval($consol['bill'][4]) - floatval($consol['bill'][5]) - floatval($consol['bill'][6]) - floatval($consol['bill'][9])) : (floatval(-$consol['bill'][4]) + floatval(-$consol['bill'][5]) + floatval(-$consol['bill'][6]) + floatval(-$consol['bill'][9]));
				}

				if (!empty($others)) {
					echo '<tr style="background: ' . ((($i-1) % 2 == 0) ? 'white' : '#DEDEDE') . '">
						<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
						<td>Subtotal</td>
						<td>' . round($subtot['wt_accr'], 2) . '/' . round($subtot['wt_awb'], 2) . '</td>
						<td>' . round($subtot['wt_act'], 2) . '</td>
						<td>' . round($subtot['wt_diff'], 2) . '</td>
						<td>' . round($subtot['pks_accr'], 2) . '</td>
						<td>' . round($subtot['pks_act'], 2) . '</td>
						<td>' . round($subtot['pks_diff'], 2) . '</td>
						<td>' . round($subtot['clear_accr'], 2) . '</td>
						<td>' . round($subtot['clear_act'], 2) . '</td>
						<td>' . round($subtot['clear_diff'], 2) . '</td>
						<td>' . round($subtot['delivery_accr'], 2) . '</td>
						<td>' . round($subtot['delivery_act'], 2) . '</td>
						<td>' . round($subtot['delivery_diff'], 2) . '</td>
						<td>' . round($subtot['duty_accr'], 2) . '</td>
						<td>' . round($subtot['duty_act'], 2) . '</td>
						<td>' . round($subtot['duty_diff'], 2) . '</td>
						<td>' . round($subtot['others_accr'], 2) . '</td>
						<td>' . round($subtot['others_act'], 2) . '</td>
						<td>' . round($subtot['others_diff'], 2) . '</td>
						<td>' . round($subtot['total_accr'], 2) . '</td>
						<td>' . round($subtot['total_act'], 2) . '</td>
						<td>' . round($subtot['total_diff'], 2) . '</td>
					</tr>';
				}

				$tot['wt_accr'] += $subtot['wt_accr'];
				$tot['wt_awb'] += $subtot['wt_awb'];
				$tot['wt_act'] += $subtot['wt_act'];
				$tot['wt_diff'] += $subtot['wt_diff'];
				$tot['pks_accr'] += $subtot['pks_accr'];
				$tot['pks_act'] += $subtot['pks_act'];
				$tot['pks_diff'] += $subtot['pks_diff'];
				$tot['clear_accr'] += $subtot['clear_accr'];
				$tot['clear_act'] += $subtot['clear_act'];
				$tot['clear_diff'] += $subtot['clear_diff'];
				$tot['delivery_accr'] += $subtot['delivery_accr'];
				$tot['delivery_act'] += $subtot['delivery_act'];
				$tot['delivery_diff'] += $subtot['delivery_diff'];
				$tot['duty_accr'] += $subtot['duty_accr'];
				$tot['duty_act'] += $subtot['duty_act'];
				$tot['duty_diff'] += $subtot['duty_diff'];
				$tot['others_accr'] += $subtot['others_accr'];
				$tot['others_act'] += $subtot['others_act'];
				$tot['others_diff'] += $subtot['others_diff'];
				$tot['total_accr'] += $subtot['total_accr'];
				$tot['total_act'] += $subtot['total_act'];
				$tot['total_diff'] += $subtot['total_diff'];
			}
			echo '<tr style="background: orange">
				<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
				<td>Total</td>
				<td>' . round($tot['wt_accr'], 2) . '/' . round($tot['wt_awb'], 2) . '</td>
				<td>' . round($tot['wt_act'], 2) . '</td>
				<td>' . round($tot['wt_diff'], 2) . '</td>
				<td>' . round($tot['pks_accr'], 2) . '</td>
				<td>' . round($tot['pks_act'], 2) . '</td>
				<td>' . round($tot['pks_diff'], 2) . '</td>
				<td>' . round($tot['clear_accr'], 2) . '</td>
				<td>' . round($tot['clear_act'], 2) . '</td>
				<td>' . round($tot['clear_diff'], 2) . '</td>
				<td>' . round($tot['delivery_accr'], 2) . '</td>
				<td>' . round($tot['delivery_act'], 2) . '</td>
				<td>' . round($tot['delivery_diff'], 2) . '</td>
				<td>' . round($tot['duty_accr'], 2) . '</td>
				<td>' . round($tot['duty_act'], 2) . '</td>
				<td>' . round($tot['duty_diff'], 2) . '</td>
				<td>' . round($tot['others_accr'], 2) . '</td>
				<td>' . round($tot['others_act'], 2) . '</td>
				<td>' . round($tot['others_diff'], 2) . '</td>
				<td>' . round($tot['total_accr'], 2) . '</td>
				<td>' . round($tot['total_act'], 2) . '</td>
				<td>' . round($tot['total_diff'], 2) . '</td>
			</tr>';
			?>
		</tbody>
	</table>
	<br>
	<button type="button"><a href="<?=Yii::app()->createUrl('consolWeightCheck/detail', array('id' => $exconsol->id, 'type' => 'consol', 'others' => true))?>" class="tab_link" title="<?=$exconsol->no?>">Show Other Consols</a></button>
<?php } ?>

<script type="text/javascript">
	$(function(){
		var tab = $("#<?=$_GET['tabid'];?>");
		var panel = tab.data('panel');

		$('a.export_search', panel).on('mousedown', function(){
			var q = $('.filters input, .filters select', panel).serialize();
			$(this).attr('href', $(this).data('baseurl') + '&' + q);
		});
	});
</script>