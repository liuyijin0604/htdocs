<table width="100%" cellspacing="0" class="chart">
		<thead>
			<tr style="background: rgba(100,100,100,0.4);">
			<th align="left" width="40">No</th>
					<th align="left" width="180">Consol No</th>
                                        <th align="left"width="230">Desc</th>
					<th align="left"width="230">Rate</th>
                                        <th align="right" width="230">Chargable Weight</th>
					<th align="right" width="150">Amount<br /><?=$inv->getCurrency();?></th>
				</tr>
		</thead>
		<tbody>
		<?php
		$tot = 0;
		$qty = 0;
		$i = 0;
		foreach($inv->lines as $il){
			foreach($il->mdata['items'] as $si=>$sp){

				echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">'.($si+1).'</td><td valign="top">'.$sp[0].'</td><td valign="top">'.$sp[1].'</td><td valign="top">'.AppHelper::money_format("%i",$sp[2]).'</td><td valign="top" align="right">'.AppHelper::qty_format($sp[3],2).'</td><td align="right" valign="top">'.AppHelper::money_format('%i',$sp[4]).'</td></tr>';
				$tot += $sp[4];
				$qty += $sp[3];
				$i++;
			}
            echo '<tr class="'. ($i%2 == 1? 'even' : 'odd') . '"><td align="right" colspan="4">Sub Total:</td><td align="right">' . AppHelper::qty_format($qty,2) . '</td><td align="right">' . AppHelper::money_format('%i',$tot) . '</td></tr>';
            $i++;
		}
        $paid = 0;
        $bal = 0;
                if(!empty($_GET['bal']) && ((empty($date) && !empty($inv->payments)) || (!empty($date) && ($inv->paidBefore($date) > 0)))){
            ?>
            <tr><th colspan="6" style="padding: 5px">Payment Received</th></tr>
            <tr style="background: rgba(100,100,100,0.4);">
                <th align="left">&nbsp;</th>
                <th align="left">Date</th>
                <th align="left">Reference</th>
                <th align="left">Type</th>
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
                echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">&nbsp;</td><td valign="top">'.$pay->transaction_date.'</td><td>'.$ref.'</td><td>'.$pay->payment->getType().'</td><td>'.$pay->payment->getCurrency().'</td><td align="right">'.AppHelper::money_format('%i', $pay->amount)."</td></tr>";
                $paid += $pay->amount;
            }
            echo "<tr><td colspan=\"6\">&nbsp;</td></tr>";
        }
   
        ?>
<!-- 	        <tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr> -->
		</tbody>
		<tfoot>

<!--         <tr<?=empty($paid)? ' style="font-size: 20px"': '';?>><td>&nbsp;</td><th align="right" colspan="4">Sub Total:</th><th align="right"><?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $tot);?></th></tr> -->

        <?php
            // if ( $inv->gst > 0 ) {
            //     echo '<tr class="'.($i%2 == 1? 'even' : 'odd') . '"><td>&nbsp;</td><th align="right" colspan="4">GST 10.00%:</th><th align="right">' .AppHelper::money_format('%i', $inv->gst) . '</th></tr>';
            // }
        ?>

        <?php
        if ( $paid > 0 ) {
            echo '<tr><td>&nbsp;</td><th align="right" colspan="4">Applied:</th><th align="right">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $paid),'</th><tr>';
            $bal = $inv->total - $paid;
            if($bal <= 0){
                echo '<tr><td>&nbsp;</td><th align="right" colspan="4">Fully Paid</th><tr>';
            }else{
                echo '<tr style="font-size: 20px"><td>&nbsp;</td><th align="right" colspan="4">Balance:</th><th align="right">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $bal),'</th><tr>';
            }
        }
        ?>
	</tfoot>
</table>
<footer>
<table width="100%" cellspacing="0" cellpadding="0" class="last_page_only">
<tbody>
		<tr>
            <td style="font-weight: bold" width="40%"><p><?php
                echo 'Payment Terms: ', $inv->mdata['payterm'];
        ?></p><br />        <p>Bank: Westpac Banking Corporation<br />
         Account Name: Top Logistics Australia<br />
        BSB Number: 032010<br />
        Account Number: 199734</p>
        </td>

        
		<td valign="top" style="text-align:right;" width="60%">
		   <p style="font-size:20px;">Sub Total(Ex. GST): <?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $tot);?></p>
            <?php if($inv->gst>0):?>
            <p style="font-size:20px;">GST 10.00%: <?php echo $inv->getCurrency(),' ', AppHelper::money_format('%i',$inv->gst);?></p>
            <?php endif;?>
            <p style="font-size:28px;font-weight: bold;">TOTAL AMOUNT PAYABLE: <?php echo $inv->getCurrency(),' ', AppHelper::money_format('%i',$bal > 0 ? $bal : $inv->total);?></p>
        </td>
		</tr>
	</tbody>
</table>
</footer>