<table width="100%" cellspacing="0" class="chart">
    <thead>
      <tr style="background: rgba(100,100,100,0.4);">
        <th align="left" width="40">No</th>
        <th align="left" width="130">Date</th>
        <th align="left" width="780">Description</th>
        <th align="right" width="100">Unit Price</th>
        <th align="right" width="80">Qty</th>
        <th align="right" width="100">Amount<br /><?=$inv->getCurrency();?></th>
      </tr>
    </thead>
    <tbody>
    <?php
    $tot = 0;
    $qty = 0;
    $wei = 0;
    $cbm = 0;
    $i = 0;
    $si_offset = 0;
    $lines = $inv->lines2;
    usort($lines, function ($a, $b) {
        return $a->mdata['items'][0][0] >= $b->mdata['items'][0][0];
    });
    foreach($lines as $index => $il){
      if (!isset($lines[$index-1]) || $lines[$index]->mdata['items'][0][0] != $lines[$index-1]->mdata['items'][0][0]) {
        echo '<tr class="' . ($i++ % 2 == 1 ? 'even' : 'odd') . '"><td align="center" colspan="6" valign="top">Task: ' . $lines[$index]->mdata['items'][0][0] . ' - Ref: ' . (!empty($lines[$index]->mdata['ref']) ? $lines[$index]->mdata['ref'] : $lines[$index]->mdata['items'][0][1]) . '</td></tr>';
      }
      foreach($il->mdata['items'] as $si=>$sp){

                           
                            $descr='';
                $postcode = '';
                if ( isset($sp[7]) ) $postcode = $sp[7];
        echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">'.($si+$si_offset+1).'</td><td valign="top">'.nl2br(explode(' ', $sp[2])[0]).'</td><td valign="top">'.nl2br($sp[3]).'</td><td valign="top" align="right">'.AppHelper::money_format('%i',$sp[4]).'</td><td align="right" valign="top">'.$sp[5].'</td><td align="right" valign="top">'.AppHelper::money_format('%i',$sp[6]).'</td></tr>';
        $tot += $sp[6];
        $qty += $sp[5];
        
        $i++;
      }
      if (isset($lines[$index+1]) && $sp[0] == $lines[$index+1]->mdata['items'][0][0]) {
        $si_offset = $si + $si_offset + 1;
        continue;
      }
            echo '<tr class="'. ($i%2 == 1? 'even' : 'odd') . '"><td align="right" colspan="4">Sub Total:</td><td align="right">' . AppHelper::qty_format($qty) . '</td><td align="right">' . AppHelper::money_format('%i',$tot) . '</td></tr>';
            $i++;
            $qty = 0;
            $tot = 0;
            $si_offset = 0;
    }
        $paid = 0;
    if(!empty($_GET['bal']) && ((empty($date) && !empty($inv->payments)) || (!empty($date) && ($inv->paidBefore($date) > 0)))){
    ?>
      <tr><td colspan="6">&nbsp;</td></tr>
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
        echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">&nbsp;</td><td valign="top">'.$pay->transaction_date.'</td><td>'.$ref.'</td><td>'.$pay->payment->getType().'</td><td>'.$pay->payment->getCurrency().'</td><td align="right">'.AppHelper::money_format('%i', $pay->amount)."</td></tr>\n";
        $paid += $pay->amount;
      }
      echo "<tr><td colspan=\"6\">&nbsp;</td></tr>\n";
    }
    ?>
          <tr><td class="plc1" colspan="6">&nbsp;</td></tr>
    </tbody>
    <tfoot>

        <tr<?=empty($paid)? ' style="font-size: 20px"': '';?>><th align="right" colspan="4">Sub Total:</th><th align="right" colspan="2"><?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $inv->total - $inv->gst);?></th></tr>

        <?php
            if ( $inv->gst > 0 ) {
                echo '<tr class="'.($i%2 == 1? 'even' : 'odd') . '"><th align="right" colspan="4">GST 10.00%:</th><th align="right" colspan="2">' .AppHelper::money_format('%i', $inv->gst) . '</th></tr>';
            }
        ?>

        <?php
        $bal=0;
        if ( $paid > 0 ) {
            echo '<tr><th align="right" colspan="4">Applied:</th><th align="right" colspan="2">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $paid),'</th><tr>';
            $bal = $inv->total - $paid;
            if($bal <= 0){
                echo '<tr><th align="right" colspan="6">Fully Paid</th><tr>';
            }else{
                echo '<tr style="font-size: 20px"><th align="right" colspan="4">Balance:</th><th align="right" colspan="2">',$inv->getCurrency(), ' ', AppHelper::money_format('%i', $bal),'</th><tr>';
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
    ?></p><br /><p>
        <?php if(empty($behalf)): ?>
        Bank: Westpac Banking Corporation<br />
        Account Name: Top Logistics Australia<br />
        <?php if(empty($inv->dpmt) || $inv->dpmt == 30):?>
        BSB Number: 032010<br />
        Account Number: 199734<br />
        <?php else: ?>
        BSB Number:  032010<br />
        Account Number:  199734<br />
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
    <p style="font-size:28px;">Total: <?php echo $inv->getCurrency(), AppHelper::money_format('%i',$bal > 0 ? $bal : $inv->total);?></p></td>
    </tr>
  </tbody>
</table>
</footer>