<table width="100%" cellspacing="0" class="chart">
    <?php  if(isset($inv->mdata['delivery_method'])&&$inv->mdata['delivery_method']==2){
                 $unit="CBM(M<sup>3</sup>)";
        
                 }else{
                    $unit="Weight(KG)"  ;
                 }
    
     ?>
		<thead>
			 <tr style="background: rgba(100,100,100,0.4);">
			                 <th align="left" width="40">No</th>
                                       <th align="left" colspan="3">Description</th>
					<th align="right" width="80">Unit</th>
					<th align="right" width="125"><?=$unit?></th>
					<th align="right" width="125">Rate</th>
					<th align="right" width="150">Amount<br /><?=$inv->getCurrency();?></th>
		    </tr>
		</thead>
		<tbody>
		<?php
		$tot = 0;
                   $deliveryTot=0;
		$i = 0;
                $delivery_line=[];
                $direct_line=[];
                foreach ($inv->lines as $il){
                    if(!empty($il->mdata['delivery'])){
                        $delivery_line[]=$il;
                    }else{
                        $direct_line[]=$il;
                    }
                }
		foreach($direct_line as $il){
			foreach($il->mdata['items'] as $si=>$sp){
				echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top" >'.($si+1).'</td><td valign="top" colspan="3" >'.nl2br($sp[1]).'</td><td valign="top" align="right">'.AppHelper::qty_format($sp[2]).'</td><td align="right" valign="top">'.$sp[3].'</td><td align="right" valign="top">'.$sp[4].'</td><td align="right" valign="top">'.AppHelper::money_format('%i',$sp[5])."</td></tr>";
				$tot += $sp[5];
				$i++;
			}
                       echo '<tr class="'. ($i%2 == 1? 'even' : 'odd') . '"><td align="right" colspan="7">Sub Total:</td><td align="right">' . AppHelper::money_format('%i',$tot) . '</td></tr>';
                    $i++;
		}
                ?>
               <?php if(!empty($delivery_line)):?>
             <tr><th colspan="8" style="padding: 5px">Local Delivery</th></tr>
                <?php 
                $deliveryStr = "";
                $qty = 0;
                $wei = 0;
                $cbm = 0;
             
                foreach ($delivery_line as $il){
                    foreach($il->mdata['items'] as $si=>$sp){
                               $cal_wei=empty($sp[9])?$sp[3]:$sp[9];
                               $cal_wei = round($cal_wei,2);
                                $descr='';
                            if(nl2br($sp[1])=='Insurance Fee'){
                            if(!empty($sp[0])) {
                                $r= ImParcel::model()->find('hbn=:hbn',array(':hbn'=>$sp[0]));
                                if(!empty($r->insurance)){
                                    $descr='(Order Value:$'.$r->insurance.')';
                                }
                              
                            }
                        }
                       $postcode = '';
                       
                    if ( isset($sp[6]) ) $postcode = $sp[6];
                    $deliveryStr.= '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">'.($si+1).'</td><td valign="top">'.$sp[0].'</td><td valign="top">'.nl2br($sp[1]).$descr.'</td><td align="right" valign="top">'.$postcode.'</td><td valign="top" align="right">'.AppHelper::qty_format($sp[2]).'</td><td align="right" valign="top">'.$cal_wei.'</td>';
                    if(!empty($sp[5]))
                    {
                            $deliveryStr.='<td align="right" valign="top">'.$sp[4].'</td><td align="right" valign="top">'.AppHelper::money_format('%i',$sp[5]).'</td></tr>';
                    }else
                    {
                         $deliveryStr.='<td align="right" valign="top" colspan="2">'.$sp[4].'</td></tr>';
                    }
                                    $deliveryTot += $sp[5];
                    $qty += $sp[2];
                    $wei += $cal_wei;
                    $cbm += $sp[4];
                    $i++;
                         }
                    $deliveryStr.= '<tr class="'. ($i%2 == 1? 'even' : 'odd') . '"><td align="right" colspan="4">Sub Total:</td><td align="right">' . AppHelper::qty_format($qty) . '</td><td align="right">' . round($wei*100)/100 . 'Kg</td>';

                    if(!empty($sp[5]))
                    {
                        $deliveryStr.='<td align="right">' . round($cbm*1000000)/1000000 . 'M<sup>3</sup></td><td align="right">' . AppHelper::money_format('%i',$deliveryTot) . '</td></tr>';
                    }else
                    {
                        $deliveryStr.='<td align="right" colspan="2">' . round($cbm*1000000)/1000000 . 'M<sup>3</sup></td>></tr>';
                    }

                    $i++;
                  }
            ?>


             <tr style="background: rgba(100,100,100,0.4);">
                <th align="left">No</th>
                <th align="left">Ref</th>
                <th align="left">Description</th>
                <th align="left">Postcode</th>
                <th align="left">Packs</th>
                <th align="left">Weight</th>
                <?php
                    if(!empty($deliveryTot))
                    {
                        echo'<th align="left" >cbm</th>';
                        echo'<th align="right">Amount</th>';
                    }else
                    {
                         echo'<th align="left" colspan="2">cbm</th>';
                    }
                ?>
            </tr>   
                <?php echo $deliveryStr;?>
               <?php endif;  
                
        $paid = 0;
        $bal = 0;
        if(!empty($_GET['bal']) && ((empty($date) && !empty($inv->payments)) || (!empty($date) && ($inv->paidBefore($date) > 0)))){
            ?>
            <tr><th colspan="8" style="padding: 5px">Payment Received</th></tr>
            <tr style="background: rgba(100,100,100,0.4);">
                <th align="left">&nbsp;</th>
                <th align="left"  colspan="3">Date</th>
                <th align="left">Reference</th>
                <th align="left" colspan="2">Type</th>
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
                echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">&nbsp;</td><td valign="top" colspan="3">'.$pay->transaction_date.'</td><td>'.$ref.'</td><td colspan="2">'.$pay->payment->getType().'</td><td align="right">'.AppHelper::money_format('%i', $pay->amount)."</td></tr>";
                $paid += $pay->amount;
            }
            echo "<tr><td colspan=\"8\">&nbsp;</td></tr>";
        }
        ?>
<!-- 			<tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr> -->
		</tbody>
		<tfoot>

<!--         <tr<?=empty($paid)? ' style="font-size: 20px"': '';?>><td>&nbsp;</td><th align="right" colspan="6">Sub Total:</th><th align="right"><?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $tot+$deliveryTot);?></th></tr> -->

        <?php
            // if ( $inv->gst > 0 ) {
            //     echo '<tr class="'.($i%2 == 1? 'even' : 'odd') . '"><td>&nbsp;</td><th align="right" colspan="6">GST 10.00%:</th><th align="right">' .AppHelper::money_format('%i', $inv->gst) . '</th></tr>';
            // }
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
			<td style="font-weight: bold" width="40%"><p><?php
				echo 'Payment Terms: ', $inv->mdata['payterm'];
		?></p><br /><p>Bank: Westpac Banking Corporation<br />
        <?php
        if(Yii::app()->name == 'TLA'):
        ?>
        Account Name: Top Logistics Australia<br />
        BSB Number: 032010<br />
        Account Number: 199734
        <?php else: ?>
        Account Name: Top Logistics<br />
        <?php if(empty($inv->dpmt) || $inv->dpmt == 30):?>
        BSB Number: 032090<br />
        Account Number: 876589<br />
        <?php else: ?>
        BSB Number: 032166<br />
        Account Number: 366789<br />
        <?php endif; ?>
        Swift Code: WPACAU2S
        <?php endif; ?></p>
        </td>
		<td valign="top" style="text-align:right; " width="60%">
		   <p style="font-size:20px;">Sub Total(Ex. GST): <?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $tot+$deliveryTot);?></p>
        <?php if($inv->gst>0):?>
        <p style="font-size:20px;">GST 10.00%: <?php echo $inv->getCurrency(),' ', AppHelper::money_format('%i',$inv->gst);?></p>
        <?php endif;?>
        <p style="font-size:28px;font-weight: bold;">TOTAL AMOUNT PAYABLE: <?php echo $inv->getCurrency(),' ', AppHelper::money_format('%i',$bal > 0 ? $bal : $inv->total);?></p></td>
		</tr>
	</tbody>
</table>
</footer>