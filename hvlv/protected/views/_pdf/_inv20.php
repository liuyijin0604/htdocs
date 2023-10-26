<?php
$ec = false;
foreach($inv->lines as $il){
	if($il->mdata['items'][0][5] == 'EC'){
		$ec = true;
		break;
	}
}
?>
<table width="100%" cellspacing="0" class="chart">
		<thead>
			<tr style="background: rgba(100,100,100,0.4);">
				<th align="left" width="40">No</th>
				<th align="left" width="150">Connote</th>
				<th align="left">Shipper</th>
				<th align="left" width="100">Weight</th>
				<th align="left" width="100">Type</th>
				<th align="right" width="100">Rate</th>
				<?=$ec? '<th align="right" width="100">Duty</th>' : ''; ?>
				<th align="right" width="180">Amount</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$tw = 0;
		$i = 0;
		foreach($inv->lines as $il){
			$m = $il->mm();
			echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">&nbsp;</td><td valign="top" colspan="'.($ec? 7 : 6).'">Receipt List: '.$il->mdata['rlno'].' &nbsp Date: '.substr($m->created, 0, 10)."</td></tr>\n";
			$i++;
			$tw = 0;
			$tduty = 0;
			foreach($il->mdata['items'] as $si=>$sp){
				$duty = empty($sp[8])? 0 : $sp[8];
				echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">'.($si+1).'</td><td valign="top">'.$sp[0].'</td><td valign="top">'.nl2br($sp[1]).'</td><td valign="top" align="right">'.$sp[2].'</td><td valign="top" align="right">'.$sp[4].'('.$sp[5].')</td><td valign="top" align="right">'.AppHelper::money_format('%i', $sp[6]).'</td>'.($ec? '<td valign="top" align="right">'.AppHelper::money_format('%i', $duty).'</td>' : '').'<td valign="top" align="right">'.AppHelper::money_format('%i', $sp[7]+$duty)."</td></tr>\n";
				$tw += $sp[2];
				$tduty += $duty;
				$i++;
			}
			echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">&nbsp;</td><td valign="top" colspan="2" align="right">Sub Total:</td><td align="right">'.(round($tw*100)/100).'</td><td>&nbsp;</td><td>&nbsp;</td>'.($ec? '<td align="right">'.$tduty.'</td>' : '').'<td align="right"><b>'.AppHelper::money_format('%i', $il->amount)."</b></td></tr>\n<tr><td colspan=\"".($ec? 8 : 7)."\">&nbsp;</td></tr>\n";
			$i++;
		}
		$paid = 0;
		if(!empty($_GET['bal']) && ((empty($date) && !empty($inv->payments)) || (!empty($date) && ($inv->paidBefore($date) > 0)))){
		?>
			<tr><th colspan="7" style="padding: 5px">Payment Received</th></tr>
			<tr style="background: rgba(100,100,100,0.4);">
				<th align="left">&nbsp;</th>
				<th align="left">Date</th>
				<th align="left">Reference</th>
				<th align="left" colspan="2">Type</th>
				<th align="left">&nbsp;</th>
				<?=$ec? '<th align="left">&nbsp;</th>' : '';?>
				<th align="right">Amount</th>
			</tr>
		<?php
			foreach($inv->payments as $i=>$pay){
				if (!empty($date) && $pay->transaction_date > $date) continue;
				if($pay->payment->status != 6) continue;
                                     $ref=$pay->payment->ref;
                if($pay->payment->type==5){
                    if(!empty($pay->payment->no))
                    $ref.="(".$pay->payment->no.")";
                }
				echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">&nbsp;</td><td valign="top">'.$pay->transaction_date.'</td><td>'.$ref.'</td><td colspan="2">'.$pay->payment->getType().'</td><td>&nbsp;</td>'.($ec? '<td>&nbsp;</td>' : '').'<td align="right">'.AppHelper::money_format('%i', $pay->amount)."</td></tr>\n";
				$paid += $pay->amount;
			}
			echo "<tr><td colspan=\"".($ec? 8 : 7)."\">&nbsp;</td></tr>\n";
		}
		?>
			<tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><?=$ec? '<td>&nbsp;</td>' : '';?><td>&nbsp;</td></tr>
		</tbody>
		<tfoot>
		<tr<?=empty($paid)? ' style="font-size: 28px"': '';?>><td>&nbsp;</td><th align="right" colspan="<?=$ec?  6: 5;?>">Total:</th><th align="right"><?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $inv->total);?></th></tr>
		<?php
		if($paid > 0){
			echo '<tr><td>&nbsp;</td><th align="right" colspan="'.($ec? 6 : 5).'">Applied:</th><th align="right">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $paid),'</th><tr>';
			$bal = $inv->total - $paid;
			if($bal <= 0){
				echo '<tr><td>&nbsp;</td><th align="right" colspan="'.($ec? 7 : 6).'">Fully Paid</th><tr>';
			}else{
				echo '<tr style="font-size: 20px"><td>&nbsp;</td><th align="right" colspan="'.($ec? 6 : 5).'">Balance:</th><th align="right">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $bal),'</th><tr>';
			}
		}
		?>
		</tfoot>
</table>
<footer>
<table width="100%" cellspacing="0" cellpadding="0" class="last_page_only">
<tbody>
		<tr>
			<td width="60%"><p style="font-weight: bold"><?php
				echo 'Payment Terms: COD'; //$inv->mdata['payterm'];
		?></p><br /> <p>Bank: Westpac Banking Corporation<br />
         Account Name: Top Logistics Australia<br />
        BSB Number: 032010<br />
        Account Number: 199734</p>
		</td>
		<td valign="top" style="text-align:right; font-weight: bold;" width="40%">
			&nbsp;
		</td>
		</tr>
	</tbody>
</table>