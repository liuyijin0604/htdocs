<table width="100%" cellspacing="0" class="chart">
		<thead>
			<tr style="background: rgba(100,100,100,0.4);">
				<th align="left" width="40">No</th>
				<th align="left" width="180">Item</th>
				<th align="left">Description</th>
				<th align="right" width="150">Unit Price<br /><?=$inv->getCurrency();?></th>

				<th align="right" width="150">Qty</th>
				<th align="right" width="150">Tax</th>
				<!--<th align="right" width="150">Sub Total<br />(inc.GST)</th> -->
				<th align="right" width="150">Sub Total</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$tot = 0;
		$qty = 0;
		$wei = 0;
		$cbm = 0;
		$i = 0;
		$taxValue = 0;
		$shouldAddTax = 0;
		$subTotal = 0;
		$chargeTypes = EdiJob::getChargeItemTypes();
		$tempLines=[];
		$theLines=[];
		// foreach($inv->lines as $il){
		//    $tempLines[$il->ccode][]=$il;
		// }
		// ksort($tempLines);
		// foreach ($tempLines as $groupLine){
		// 	foreach ($groupLine as $line_1){
		// 		$theLines[]=$line_1;
		// 	}
		// }
		$theLines = $inv->lines;
		   foreach($theLines as $il){
			$desc = '';
			if ( isset($chargeTypes[$il->ccode]) ) $desc = $chargeTypes[$il->ccode];
			echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">'.($i+1).'</td><td valign="top">'.$desc.'</td><td valign="top">'.nl2br($il->det).'</td><td valign="top" align="right">'.AppHelper::money_format('%i',($il->amount - $il->gst), 3, 2).'</td><td valign="top" align="right">'.$il->qty.'</td><td valign="top" align="right">'.$il->getTaxType().'</td><td valign="top" align="right">'.AppHelper::money_format('%i',($il->amount - $il->gst) * $il->qty)."</td></tr>";
			$i++;
			$subTotal += ($il->amount - $il->gst) * $il->qty;
			$oneTaxV = $il->gst * $il->qty;
			$taxValue += $oneTaxV;
		}
		$paid = 0;
		if(!empty($_GET['bal']) && ((empty($date) && !empty($inv->payments)) || (!empty($date) && ($inv->paidBefore($date) > 0)))){
			?>
			<tr><th colspan="7" style="padding: 5px">Payment Received</th></tr>
			<tr style="background: rgba(100,100,100,0.4);">
				<th align="left">&nbsp;</th>
				<th align="left">Date</th>
				<th align="left">Reference</th>
				<th align="left">Type</th>
				<th align="left">&nbsp;</th>
				<th align="left">&nbsp;</th>
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
				echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">&nbsp;</td><td valign="top">'.$pay->transaction_date.'</td><td>'.$ref.'</td><td>'.$pay->payment->getType().'</td><td>&nbsp;</td><td>'.$pay->payment->getCurrency().' '.AppHelper::money_format('%i', $pay->amount / $pay->exrate).'</td><td align="right">'.AppHelper::money_format('%i', $pay->amount)."</td></tr>";
				$paid += $pay->amount;
			}
			echo "<tr><td colspan=\"7\">&nbsp;</td></tr>";
		}
		?>


			<tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		</tbody>


	<tfoot>


	<tr<?=empty($paid)? ' style="font-size: 20px"': '';?>><td>&nbsp;</td><th align="right" colspan="5">Sub Total:</th><th align="right"><?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $subTotal );?></th></tr>

	<?php
	if ( $taxValue > 0 ) {
		echo '<tr class="'.($i%2 == 1? 'even' : 'odd') . '"><td>&nbsp;</td><th align="right" colspan="5">Total GST 10.00%:</th><th align="right">' .AppHelper::money_format('%i', ($taxValue)) . '</th></tr>';
	}
	?>

	<?php
	if ( $paid > 0 ) {
		echo '<tr><td>&nbsp;</td><th align="right" colspan="5">Applied:</th><th align="right">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $paid),'</th><tr>';
		$bal = $inv->total + $shouldAddTax - $paid;
		if($bal <= 0){
			echo '<tr><td>&nbsp;</td><th align="right" colspan="6">Fully Paid</th><tr>';
		}else{
			echo '<tr style="font-size: 20px"><td>&nbsp;</td><th align="right" colspan="5">Balance:</th><th align="right">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $bal),'</th><tr>';
		}
	}
	?>

	</tfoot>

</table>
<footer>
<table width="100%" cellspacing="0" cellpadding="0" class="last_page_only">
<tbody>
		<tr>
			<td style="font-weight: bold" width="60%"><p><?php
				echo 'Payment Terms: ', $inv->mdata['payterm'];
		?></p>
		</td>
		<td valign="top" style="text-align:right; font-weight: bold;" width="40%">
		<p style="font-size:28px;">Total: <?php echo $inv->getCurrency(), AppHelper::money_format('%i',$inv->total + $shouldAddTax - $paid );?></p><?php
if(!empty($inv->mdata['currency_2nd'])){
	echo '<p style="font-size:28px;">'.$inv->mdata['currency_2nd'].AppHelper::money_format('%i', ($inv->total + $shouldAddTax - $paid) * $inv->mdata['exrate']).'<br /><br /><small style="font-weight: normal; font-size: 0.5em;">Exchange Rate: '.$inv->mdata['exrate'].' '.$inv->mdata['exrate_date'].'</small></p>';
}
?></td>
		</tr>
		<tr><td>        <p>Bank: Westpac Banking Corporation<br />
         Account Name: Top Logistics Australia<br />
        BSB Number: 032010<br />
        Account Number: 199734</p>
		</td>
		<td>&nbsp;</td>
		</tr>
	</tbody>
</table>