<table width="100%" cellspacing="0" class="chart">
		<thead>
			<tr style="background: rgba(100,100,100,0.4);">
			<th align="left" width="40">No</th>
					<th align="left" width="180">AWB</th>
					<th align="left"width="430">Description</th>
                                        <th align="right" width="125">Postcode</th>
					<th align="right" width="80">Real Weight</th>
					<th align="right" width="125">Pre Charge Weight</th>
					<th align="right" width="125">Weight Gap</th>

					<th align="right" width="150">Amount<br /><?=$inv->getCurrency();?></th>
				</tr>
		</thead>
		<tbody>
		<?php
		$tot = 0;
		$rwei = 0;
		$pwei = 0;
		$gwei = 0;
		$i = 0;
                $descr='';
		foreach($inv->lines as $il){
			foreach($il->mdata['items'] as $si=>$sp){
                if ( isset($sp[6]) ) $postcode = $sp[6];
				echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">'.($si+1).'</td><td valign="top">'.$sp[0].'</td><td valign="top">'.nl2br($sp[1]).$descr.'</td><td align="right" valign="top">'.$sp[2].'</td><td valign="top" align="right">'.$sp[3].'</td><td align="right" valign="top">'.$sp[4].'</td><td align="right" valign="top">'.$sp[5].'</td><td align="right" valign="top">'.AppHelper::money_format('%i',$sp[6]).'</td></tr>';
				$tot += $sp[6];
				$rwei += $sp[3];
				$pwei += $sp[4];
				$gwei += $sp[5];
				$i++;
			}
            echo '<tr class="'. ($i%2 == 1? 'even' : 'odd') . '"><td align="right" colspan="4">Sub Total:</td><td align="right">' . $rwei . 'Kg</td><td align="right">' . round($pwei*100)/100 . 'Kg</td><td align="right">' . round($gwei*100)/100 . 'Kg</sup></td><td align="right">' . AppHelper::money_format('%i',$tot) . '</td></tr>';
            $i++;
		}
        $paid = 0;
        $bal = 0;
   
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
            echo '<tr><td>&nbsp;</td><th align="right" colspan="6">Applied:</th><th align="right">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $paid),'</th><tr>';
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
		?></p><br />        <p>Bank: Westpac Banking Corporation<br />
         Account Name: Top Logistics Australia<br />
        BSB Number: 032010<br />
        Account Number: 199734</p>
		</td>
		<td valign="top" style="text-align:right; font-weight: bold;" width="40%">
		<p style="font-size:28px;">Total: <?php echo $inv->getCurrency(), AppHelper::money_format('%i',$bal > 0 ? $bal : $inv->total);?></p></td>
		</tr>
	</tbody>
</table>
</footer>