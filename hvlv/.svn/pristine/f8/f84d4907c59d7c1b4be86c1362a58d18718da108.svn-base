<table width="100%" cellspacing="0" class="chart">
		<thead>
			<tr style="background: rgba(100,100,100,0.4);">
			<th align="left" width="40">No</th>
					<th align="left">Description</th>
					<th align="left">Was</th>
					<th align="left">Now</th>
					<th align="right" width="80">QTY</th>
					<th align="right" width="125">Rate</th>
					<th align="right" width="150">Amount<br /><?=$credit->getCurrency();?></th>
				</tr>
		</thead>
		<tbody>
		<?php
		$i = 0;
		$gst = 0;
		foreach ($credit->credit_lines as $il) {
			echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">'.($i+1).'</td>';
			if (!preg_match('/was/i', $il->description)) {
				echo '<td valign="top" colspan="3" class="gridtext gridtext_small" >'.$il->description.'</td>';
			} else {
				preg_match_all('/(\d+\.*\d+)/', explode(' - ', $il->description)[1], $numbers);
				echo '<td valign="top" class="gridtext gridtext_small">'.explode(' - ', $il->description)[0].'</td><td valign="top">'.$numbers[1][0].'</td><td valign="top">'.$numbers[1][1].'</td>';
			}
			echo '<td valign="top">'.$il->qty.'</td><td valign="top" align="right">'.AppHelper::money_format('%i',$il->rate).'</td><td valign="top" align="right">'.AppHelper::money_format('%i',($il->amount - $il->gst))."</td></tr>";
			$i++;
			$gst += $il->gst;
		}
		?>
		<tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		</tbody>
		<tfoot>
	<tr><td>&nbsp;</td><th align="right" colspan="5">Sub Total:</th><th align="right"><?php echo $credit->getCurrency(), ' ', AppHelper::money_format('%i', $credit->amount - $gst);?></th></tr>
	<?php
	if ( $gst > 0 ) {
		echo '<tr class="'.($i%2 == 1? 'even' : 'odd') . '"><td>&nbsp;</td><th align="right" colspan="3">GST 10.00%:</th><th align="right">' .AppHelper::money_format('%i', ($gst)) . '</th></tr>';
	}
	?>

		</tfoot>
</table>
<footer>
<table width="100%" cellspacing="0" cellpadding="0" class="last_page_only">
<tbody>
		<tr>
			<td style="font-weight: bold" width="60%">
		</td>
		<td valign="top" style="text-align:right; font-weight: bold;" width="40%">
		<p style="font-size:20px;">Total: <?php echo $credit->getCurrency(), AppHelper::money_format('%i',$credit->amount);?></p></td>
		</tr>
			
	</tbody>
</table>
</footer>