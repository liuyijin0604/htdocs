<table width="100%" cellspacing="0" class="chart">
		<thead>
			<tr style="background: rgba(100,100,100,0.4);">
				<th align="left" width="40">No</th>
				<th align="left" width="180">Order #</th>
				<th align="left">Description</th>
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
				echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">'.($si+1).'</td><td valign="top">'.$sp[0].'</td><td valign="top">'.nl2br($sp[1]).'</td><td valign="top" align="right">'.AppHelper::money_format('%i',$sp[2])."</td></tr>\n";
				$tot += $sp[2];
				$i++;
			}
		}
		?>
			<!-- <tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr> -->
		</tbody>
		<tfoot>
		<!-- <tr><td>&nbsp;</td>
			<th align="right" colspan="2">Sub Total:</th><th align="right"><?php echo AppHelper::money_format('%i', $inv->total);?></th></tr> -->
		</tfoot>
</table>
<footer>
<table width="100%" cellspacing="0" cellpadding="0" class="last_page_only">
<tbody>
		<tr>
			<td style="font-weight: bold" width="40%"><p><?php
				echo 'Payment Terms: ', $inv->mdata['payterm'];
			?></p>
			        <p>Bank: Westpac Banking Corporation<br />
         Account Name: Top Logistics Australia<br />
        BSB Number: 032010<br />
        Account Number: 199734</p>

			</td>
			<td valign="top" style="text-align:right;" width="60%">

				<p style="font-size:20px;">Sub Total: <?php echo $inv->getCurrency(), ' ',AppHelper::money_format('%i', $inv->total);?></p>
	        	<p style="font-size:28px;font-weight: bold;">TOTAL AMOUNT PAYABLE: <?php echo $inv->getCurrency(), AppHelper::money_format('%i',$inv->total);?></p>
			</td>
		</tr>
	</tbody>
</table>