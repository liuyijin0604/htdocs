<?php if (empty($inv->mdata['new_format'])) { ?>
<table width="100%" cellspacing="0" class="chart">
		<thead>
			<tr style="background: rgba(100,100,100,0.4);">
				<th align="left" width="40">No</th>
				<th align="left">Description</th>
				<th align="left" width="120">GST</th>
				<th align="right" width="150">Amount</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$i = 0;
		foreach($inv->lines as $si => $il){
			echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">'.($si+1).'</td><td valign="top">'.$il->det.'</td><td valign="top" align="right">'.AppHelper::money_format('%i', $il->gst).'</td><td valign="top" align="right">'.AppHelper::money_format('%i', $il->amount)."</td></tr>\n";
			$i++;
		}
        $paid = 0;
        $bal = 0;
        if(!empty($_GET['bal']) && ((empty($date) && !empty($inv->payments)) || (!empty($date) && ($inv->paidBefore($date) > 0)))){
            ?>
            <tr><th colspan="6" style="padding: 5px">Payment Received</th></tr>
            <tr style="background: rgba(100,100,100,0.4);">
                <th align="left">&nbsp;</th>
                <th align="left">Date & Reference</th>
                <th align="left">Type</th>
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
                echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">&nbsp;</td><td valign="top">'.$pay->transaction_date.'&nbsp;&nbsp;&nbsp;'.$ref.'</td><td>'.$pay->payment->getType().'</td><td align="right">'.AppHelper::money_format('%i', $pay->amount)."</td></tr>";
                $paid += $pay->amount;
            }
            echo "<tr><td colspan=\"4\">&nbsp;</td></tr>";
        }
		?>
			<tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		</tbody>
		<tfoot>
		<tr><td>&nbsp;</td><th align="right">Sub Total:</th><th colspan="2" align="right"><?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $inv->total-$inv->gst);?></th></tr>
		<tr><td>&nbsp;</td><th align="right">GST:</th><th colspan="2" align="right"><?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $inv->gst);?></th></tr>
		<tr<?=empty($paid)? ' style="font-size: 28px"': '';?>><td>&nbsp;</td><th align="right">Total:</th><th colspan="2" align="right"><?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $inv->total);?></th></tr>
		<?php
		$paid = empty($date) ? $inv->paid() : $inv->paidBefore($data);
		if($paid > 0){
			echo '<tr><td>&nbsp;</td><th align="right" colspan="2">Applied:</th><th align="right">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $paid),'</th><tr>';
			$bal = $inv->total - $paid;
			if($bal <= 0){
				echo '<tr><td>&nbsp;</td><th align="right" colspan="3">Fully Paid</th><tr>';
			}else{
				echo '<tr style="font-size: 20px"><td>&nbsp;</td><th align="right" colspan="2">Balance:</th><th align="right">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $bal),'</th><tr>';
			}
		}
		?>
		</tfoot>
</table>
<?php } else { ?>
<table width="100%" cellspacing="0" class="chart">
		<thead>
			<tr style="background: rgba(100,100,100,0.4);">
				<th align="left" width="40">No</th>
				<th align="left">Description</th>
				<th align="left" width="120">Rate</th>
				<th align="left" width="120">Qty</th>
				<th align="right" width="150">Amount</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$i = 0;
		// foreach($inv->lines as $si => $il){
		$listInvLine = InvLine::model()->findAll('inv_id = :inv_id',[':inv_id'=>$inv->id]);
		foreach($listInvLine as $si => $il){
			if (!empty($il->mdata['regular'])) {
				echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">'.($si+1).'</td><td valign="top">Regular Pallet</td><td valign="top" align="right">'.AppHelper::money_format('%i', $il->mdata['regular'][1]).'</td><td valign="top" align="right">'.$il->mdata['regular'][0].'</td><td valign="top" align="right">'.AppHelper::money_format('%i', $il->mdata['regular'][1]*$il->mdata['regular'][0])."</td></tr>\n";
				$i++;
			}
			if (!empty($il->mdata['oversize'])) {
				echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">'.($si+intval(@$il->mdata['regular'])+1).'</td><td valign="top">Oversize Pallet</td><td valign="top" align="right">'.AppHelper::money_format('%i', $il->mdata['oversize'][1]).'</td><td valign="top" align="right">'.$il->mdata['oversize'][0].'</td><td valign="top" align="right">'.AppHelper::money_format('%i', $il->mdata['oversize'][1]*$il->mdata['oversize'][0])."</td></tr>\n";
				$i++;
			}
			if (!empty($il->mdata['racking'])) {
				echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">'.($si+intval(@$il->mdata['regular'])+intval(@$il->mdata['oversize'])+1).'</td><td valign="top">Racking</td><td valign="top" align="right">'.AppHelper::money_format('%i', $il->mdata['racking'][1]).'</td><td valign="top" align="right">'.$il->mdata['racking'][0].'</td><td valign="top" align="right">'.AppHelper::money_format('%i', $il->mdata['racking'][1]*$il->mdata['racking'][0])."</td></tr>\n";
				$i++;
			}
		}
        $paid = 0;
        $bal = 0;
        if(!empty($_GET['bal']) && ((empty($date) && !empty($inv->payments)) || (!empty($date) && ($inv->paidBefore($date) > 0)))){
            ?>
            <tr><th colspan="6" style="padding: 5px">Payment Received</th></tr>
            <tr style="background: rgba(100,100,100,0.4);">
                <th align="left">&nbsp;</th>
                <th align="left" colspan="2">Date & Reference</th>
                <th align="left">Type</th>
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
                echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">&nbsp;</td><td valign="top" colspan="2">'.$pay->transaction_date.'&nbsp;&nbsp;&nbsp;'.$ref.'</td><td>'.$pay->payment->getType().'</td><td align="right">'.AppHelper::money_format('%i', $pay->amount)."</td></tr>";
                $paid += $pay->amount;
            }
            echo "<tr><td colspan=\"5\">&nbsp;</td></tr>";
        }
		?>
			<tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		</tbody>
		<tfoot>
		<tr><td>&nbsp;</td><th align="right" colspan="2">Sub Total:</th><th colspan="2" align="right"><?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $inv->total-$inv->gst);?></th></tr>
		<tr><td>&nbsp;</td><th align="right" colspan="2">GST:</th><th colspan="2" align="right"><?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $inv->gst);?></th></tr>
		<tr<?=empty($paid)? ' style="font-size: 28px"': '';?>><td>&nbsp;</td><th align="right" colspan="2">Total:</th><th colspan="2" align="right"><?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $inv->total);?></th></tr>
		<?php
		$paid = empty($date) ? $inv->paid() : $inv->paidBefore($data);
		if($paid > 0){
			echo '<tr><td>&nbsp;</td><th align="right" colspan="3">Applied:</th><th align="right">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $paid),'</th><tr>';
			$bal = $inv->total - $paid;
			if($bal <= 0){
				echo '<tr><td>&nbsp;</td><th align="right" colspan="4">Fully Paid</th><tr>';
			}else{
				echo '<tr style="font-size: 20px"><td>&nbsp;</td><th align="right" colspan="3">Balance:</th><th align="right">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $bal),'</th><tr>';
			}
		}
		?>
		</tfoot>
</table>
<?php } ?>
<footer>
<table width="100%" cellspacing="0" cellpadding="0" class="last_page_only">
<tbody>
		<tr>
			<td width="60%"><p>
		<?php if(empty($behalf)): ?>
		Bank: Westpac Banking Corporation<br />
		Account Name: Top Logistics Australia<br />
		<?php if(empty($inv->dpmt) || $inv->dpmt == 30):?>
		BSB Number: 032010<br />
        Account Number: 199734<br />
        <?php else: ?>
		BSB Number: 032010<br />
        Account Number: 199734<br />
        <?php endif; ?>
		<!-- Swift Code: WPACAU2S</p> -->
		<?php elseif($behalf == 'priority'):?>
		Bank: Westpac Banking Corporation<br />
		Account Name: Priority Cargo Australia Pty Ltd<br />
		BSB Number: 032099<br />
		Account Number: 503009</p>
		<?php endif; ?>
		</td>
		<td valign="top" style="text-align:right; font-weight: bold;" width="40%">
			&nbsp;
		</td>
		</tr>
	</tbody>
</table>