<table width="100%" cellspacing="0" class="chart">
		<thead>
			<tr style="background: rgba(100,100,100,0.4);">
			<th align="left" width="40">No</th>
					<th align="left" width="180">AWB</th>
                    <th align="left">Postcode</th>
					<th align="left"width="400">Description</th>
					<th align="right" width="80">Packs</th>
					<th align="right" width="125">Weight</th>
					<th align="right" width="125">CBM</th>
					<th align="right" width="150">Amount<br /><?=$inv->getCurrency();?></th>
				</tr>
		</thead>
		<tbody>
		<?php
		$tot = 0;
		$qty = 0;
		$wei = 0;
		$cbm = 0;
		$i = 0;
		foreach($inv->lines as $il){
			foreach($il->mdata['items'] as $si=>$sp){
                // get postcode from description
                // for jiacheng only
                $mixDesc = nl2br($sp[1]);
                $pos = strrpos($mixDesc, "-");
                $desc = $mixDesc;
                $postcode = '';

                $cal_wei=empty($sp[9])?$sp[3]:$sp[9];
                $cal_wei = round($cal_wei,2);
                if ($pos !== false) {
                    $desc = substr($mixDesc,0,$pos);
               
                    $postcode = substr($mixDesc,$pos+1);
                }else{
                         if($desc=='Insurance Fee'){
                        if(!empty($sp[0])) {
                            $r= ImParcel::model()->find('hbn=:hbn',array(':hbn'=>$sp[0]));
                            if(!empty($r->insurance)){
                                $desc=$desc.'(Order Value:$'.$r->insurance.')';
                            }
                        }
                    }
                }

				echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">'.($si+1).'</td><td valign="top">'.$sp[0].'</td><td>'.$postcode.'</td><td valign="top">'.$desc.'</td><td valign="top" align="right">'.AppHelper::qty_format($sp[2]).'</td><td align="right" valign="top">'.$cal_wei.'</td><td align="right" valign="top">'.$sp[4].'</td><td align="right" valign="top">'.AppHelper::money_format('%i',$sp[5])."</td></tr>";
				$tot += $sp[5];
				$qty += $sp[2];
				$wei += $cal_wei;
				$cbm += $sp[4];
				$i++;
			}
            echo '<tr class="'. ($i%2 == 1? 'even' : 'odd') . '"><td align="right" colspan="4">Sub Total:</td><td align="right">' . AppHelper::qty_format($qty) . '</td><td align="right">' . round($wei*100)/100 . 'Kg</td><td align="right">' . round($cbm*1000000)/1000000 . 'M<sup>3</sup></td><td align="right">' . AppHelper::money_format('%i',$tot) . '</td></tr>';
            $i++;
		}
        $paid = 0;
        $bal = 0;
        if(!empty($_GET['bal']) && !empty($inv->payments)){
            ?>
            <tr><th colspan="8" style="padding: 5px">Payment Received</th></tr>
            <tr style="background: rgba(100,100,100,0.4);">
                <th align="left">&nbsp;</th>
                <th align="left">Date</th>
                <th align="left">Reference</th>
                <th align="left" colspan="3">Type</th>
                <th align="left">&nbsp;</th>
                <th align="right">Amount</th>
            </tr>
            <?php
            foreach($inv->payments as $i=>$pay){
                if($pay->payment->status != 6) continue;
                     $ref=$pay->payment->ref;
                if($pay->payment->type==5){
                    if(!empty($pay->payment->no))
                    $ref.="(".$pay->payment->no.")";
                }
                echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">&nbsp;</td><td valign="top">'.$pay->transaction_date.'</td><td>'.$ref.'</td><td colspan="3">'.$pay->payment->getType().'</td><td>&nbsp;</td><td align="right">'.AppHelper::money_format('%i', $pay->amount)."</td></tr>";
                $paid += $pay->amount;
            }
            echo "<tr><td colspan=\"8\">&nbsp;</td></tr>";
        }
        ?>
			<tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		</tbody>
		<tfoot>

        <tr<?=empty($paid)? ' style="font-size: 20px"': '';?>><td>&nbsp;</td><th align="right" colspan="6">Sub Total:</th><th align="right"><?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $tot);?></th></tr>

        <?php
            if ( $inv->gst > 0 ) {
                echo '<tr class="'.($i%2 == 1? 'even' : 'odd') . '"><td>&nbsp;</td><th align="right" colspan="6">GST 10.00%:</th><th align="right">' .AppHelper::money_format('%i', $inv->gst) . '</th></tr>';
            }
        ?>

        <?php
        if ( $paid > 0 ) {
            echo '<tr><td>&nbsp;</td><th align="right" colspan=" 6">Applied:</th><th align="right">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $paid),'</th><tr>';
            $bal = $inv->total - $paid;
            if($bal <= 0){
                echo '<tr><td>&nbsp;</td><th align="right" colspan="7">Fully Paid</th><tr>';
            }else{
                echo '<tr style="font-size: 20px"><td>&nbsp;</td><th align="right" colspan="6">Balance:</th><th align="right">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $bal),'</th><tr>';
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
		?></p><br /><p>Bank: Westpac Banking Corporation<br />
         Account Name: Top Logistics Australia<br />
        BSB Number: 032010<br />
        Account Number: 199734</p>
		</td>
		<td valign="top" style="text-align:right; font-weight: bold;" width="40%">
		<p style="font-size:20px;">Total: <?php echo $inv->getCurrency(), AppHelper::money_format('%i',$bal > 0 ? $bal : $inv->total);?></p></td>
		</tr>
	</tbody>
</table>
</footer>