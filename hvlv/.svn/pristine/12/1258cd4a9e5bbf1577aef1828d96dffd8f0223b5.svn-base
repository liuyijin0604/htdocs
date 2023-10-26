<table width="100%" cellspacing="0" class="chart">
		<thead>
			<tr style="background: rgba(100,100,100,0.4);">
			<th align="left" width="40">No</th>
					<th align="left" width="260">MAWB No.</th>
					<th align="left"width="250">Submit Weight</th>
                                        <th align="right" width="160">Chargable Weight</th>
                    <th align="right"width="150">Rate</th>                    
					<th align="right" width="180">Weight Diff</th>
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

                if ( isset($sp[6]) ) $postcode = $sp[6];
				echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">'.($si+1).'</td><td valign="top">'.$sp[0].'</td><td valign="top">'.AppHelper::qty_format($sp[2],2).'</td><td valign="top" align="right">'.AppHelper::money_format('%i',$sp[3]).'</td><td valign="top" align="right">'.AppHelper::money_format("%i",$sp[1]).'</td><td align="right" valign="top">'. AppHelper::qty_format($sp[4],2).'</td><td align="right" valign="top">'.AppHelper::money_format('%i',$sp[5]).'</td></tr>';
				$tot += $sp[5];
				$qty += $sp[4];
				
				$i++;
			}
            echo '<tr class="'. ($i%2 == 1? 'even' : 'odd') . '"><td align="right" colspan="5">Sub Total:</td><td align="right">' . AppHelper::qty_format($qty) . '</td><td align="right">' . AppHelper::money_format('%i',$tot) . '</td></tr>';
            $i++;
		}
        $paid = 0;
        $bal = 0;
   
        ?>
<!-- 	        <tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr> -->
		</tbody>
		<tfoot>

<!--         <tr<?=empty($paid)? ' style="font-size: 20px"': '';?>><td>&nbsp;</td><th align="right" colspan="5">Sub Total:</th><th align="right"><?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $tot);?></th></tr> -->

        <?php
            // if ( $inv->gst > 0 ) {
            //     echo '<tr class="'.($i%2 == 1? 'even' : 'odd') . '"><td>&nbsp;</td><th align="right" colspan="5">GST 10.00%:</th><th align="right">' .AppHelper::money_format('%i', $inv->gst) . '</th></tr>';
            // }
        ?>

        <?php
        if ( $paid > 0 ) {
            echo '<tr><td>&nbsp;</td><th align="right" colspan="5">Applied:</th><th align="right">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $paid),'</th><tr>';
            $bal = $inv->total - $paid;
            if($bal <= 0){
                echo '<tr><td>&nbsp;</td><th align="right" colspan="7">Fully Paid</th><tr>';
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
        <p style="font-size:28px;font-weight: bold;">TOTAL AMOUNT PAYABLE: <?php echo $inv->getCurrency(),' ', AppHelper::money_format('%i',$bal > 0 ? $bal : $inv->total);?></p></td>
		</tr>
	</tbody>
</table>
</footer>