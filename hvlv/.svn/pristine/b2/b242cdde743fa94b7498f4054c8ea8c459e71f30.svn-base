<table width="100%" cellspacing="0" class="chart">
		<thead>
			<tr style="background: rgba(100,100,100,0.4);">
			<th align="left" width="40">No</th>
					<th align="left" width="150">Ocean Bill</th>
					<th align="left" width="150">HBN</th>
                                        <th align="right" width="120">StartDate</th>
                                        <th align="right" width="120">EndDate</th>
                                        <th align="right" width="40">Chargeable Day</th>
                                        <th align="right" width="80">CBM(M<sup>3</sup>)</th>
					<th align="right" width="120">Amount<br /><?=$inv->getCurrency();?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$tot = 0;
		$pkg = 0;
		$wei = 0;
		$i = 0;
		foreach($inv->lines as $il){
			foreach($il->mdata['items'] as $si=>$sp){
                // $items[] = array($shipment->consol->awb,$shipment->hbn, $shipment->getDesc(), $shipment->pkg, $shipment->weight, $shipment->cbm, $rt[0], 0, 0, 0,$rt[1],$shipment->consol->eta,date('Y-m-d'));
                 if(empty($sp[7])){ 
                   $r=ImParcel::model ()->find('hbn=:hbn',array(':hbn'=>$sp[1]));
                   $ref= empty($r->ref)?$sp[1]:$r->ref;
                 }else{
                     $ref=$sp[7];
                  }
                $rowData = '<tr class="'.($i%2 == 1? 'even' : 'odd').'">';
                $rowData .= '<td align="center" valign="top">'.($i+1).'</td>';
                $rowData .= '<td valign="top">'.$sp[0].'</td>';
                $rowData .= '<td valign="top">'.$ref.'</td>';
                $rowData .= '<td align="right" valign="top">'.$sp[11].'</td>';
                $rowData .= '<td align="right" valign="top">'.$sp[12].'</td>';
                $rowData .= '<td align="right" valign="top">'.$sp[10].'</td>';
                $rowData .= '<td valign="top" align="right">'.AppHelper::money_format('%i',$sp[5]).'</td>';
                $rowData .= '<td align="right" valign="top">'.AppHelper::money_format('%i',$sp[6]).'</td>';
                $rowData .= "</tr>\n";
                echo $rowData;

				$tot += $sp[5];
                $pkg += $sp[3];
				$wei += $sp[4];
				$i++;
			}
		}

        $paid = 0;
        if(!empty($_GET['bal']) && ((empty($date) && !empty($inv->payments)) || (!empty($date) && ($inv->paidBefore($date) > 0)))){
        ?>
        <tr><th colspan="8" style="padding: 5px">Payment Received</th></tr>
        <tr style="background: rgba(100,100,100,0.4);">
            <th align="left">&nbsp;</th>
            <th align="left">Date</th>
            <th colspan="2" align="left">Reference</th>
            <th colspan="2" align="left">Type</th>
            <th align="left">&nbsp;</th>
            <th colspan="2" align="right">Amount</th>
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
            echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">&nbsp;</td><td valign="top">'.$pay->transaction_date.'</td><td colspan="2">'.$ref.'</td><td colspan="2">'.$pay->payment->getType().'</td><td>'.$pay->payment->getCurrency().'</td><td colspan="2" align="right">'.AppHelper::money_format('%i', $pay->amount)."</td></tr>\n";
            $paid += $pay->amount;
        }
        echo "<tr><td colspan=\"9\">&nbsp;</td></tr>\n";
        }
        ?>


			<!-- <tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr> -->
		</tbody>
		<tfoot>


        <?php
        $gst = $inv->getGST();
        ?>

    <!--     <tr<?=empty($paid)? ' style="font-size: 20px"': '';?>><td>&nbsp;</td><th align="right" colspan="6">Sub Total:</th><th colspan="2" align="right"><?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $inv->total - $gst);?></th></tr> -->

        <?php
        // if ( $gst > 0 ) {
        //     echo '<tr class="'.($i%2 == 1? 'even' : 'odd') . '"><td>&nbsp;</td><th align="right" colspan="6">GST 10.00%:</th><th colspan="2" align="right">' .AppHelper::money_format('%i', ($gst)) . "</th></tr>\n";
        // }
        ?>

        <?php
        if ( $paid > 0 ) {
            echo '<tr><td>&nbsp;</td><th align="right" colspan="6">Applied:</th><th colspan="2" align="right">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $paid),'</th><tr>';
            $bal = $inv->total - $paid;
            if($bal <= 0){
                echo '<tr><td>&nbsp;</td><th align="right" colspan="9">Fully Paid</th><tr>';
            }else{
                echo '<tr style="font-size: 20px"><td>&nbsp;</td><th align="right" colspan="6">Balance:</th><th colspan="2" align="right">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $bal),'</th><tr>';
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
				echo 'Payment Terms: ', @$inv->mdata['payterm'];
		?></p><br />        <p>Bank: Westpac Banking Corporation<br />
         Account Name: Top Logistics Australia<br />
        BSB Number: 032010<br />
        Account Number: 199734</p>
        </td>
        <td valign="top" style="text-align:right; " width="60%">
        <p style="font-size:20px;">Sub Total(Ex. GST):<?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $inv->total - $gst);?></p>
        <?php if($gst>0):?>
        <p style="font-size:20px;">GST 10.00%: <?php echo $inv->getCurrency(),' ', AppHelper::money_format('%i',$gst);?></p>
        <?php endif;?>
        <p style="font-size:28px;font-weight: bold;">TOTAL AMOUNT PAYABLE: <?php echo $inv->getCurrency(),' ', AppHelper::money_format('%i',$inv->total - $paid);?></p></td>
		</tr>
	</tbody>
</table>