<table width="100%" cellspacing="0" class="chart">
    <thead>
        <tr style="background: rgba(100,100,100,0.4);">
            <th align="left" width="40">No</th>
            <th align="left" width="180">AWB</th>
            <th align="left" width="430">Description</th>
            <th align="right" width="150">Amount<br /><?= $inv->getCurrency(); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $tot = 0;
        $qty = 0;
        $wei = 0;
        $cbm = 0;
        $i = 0;
        foreach ($inv->lines as $ilKey => $il) {
            echo '<tr class="' . ($i%2 == 1 ? 'even':'odd') . '"><td align="center" valign="top">' . ($i+1) . '</td><td valign="top">' . $il->ccode . '</td><td valign="top">' . $il->det . '</td><td valign="top">' . number_format((float)$il->amount/1.1, 2, '.', '') . '</td></tr>';
            $i++; 
        }
        $paid = 0;
        $bal = 0;
        ?>
    </tbody>
    <tfoot>

        <!--      <tr<?= empty($paid) ? ' style="font-size: 20px"' : ''; ?>><td>&nbsp;</td><th align="right" colspan="6">Sub Total:</th><th align="right"><?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $tot); ?></th></tr> -->

        <?php
        // if ( $inv->gst > 0 ) {
        //     echo '<tr class="'.($i%2 == 1? 'even' : 'odd') . '"><td>&nbsp;</td><th align="right" colspan="6">GST 10.00%:</th><th align="right">' .AppHelper::money_format('%i', $inv->gst) . '</th></tr>';
        // }
        ?>

        <?php
        if ($paid > 0) {
            echo '<tr><td>&nbsp;</td><th align="right" colspan="6">Applied:</th><th align="right">', $inv->getCurrency(), ' ', AppHelper::money_format('%i', $paid), '</th><tr>';
            $bal = $inv->total - $paid;
            if ($bal <= 0) {
                echo '<tr><td>&nbsp;</td><th align="right" colspan="7">Fully Paid</th><tr>';
            } else {
                echo '<tr style="font-size: 20px"><td>&nbsp;</td><th align="right" colspan="6">Balance:</th><th align="right">', $inv->getCurrency(), ' ', AppHelper::money_format('%i', $bal), '</th><tr>';
            }
        }
        ?>

    </tfoot>
</table>
<footer>
    <table width="100%" cellspacing="0" cellpadding="0" class="last_page_only">
        <tbody>
            <tr>
                <td style="font-weight: bold" width="40%">
                    <p>&nbsp;</p><br />
                    <p>Bank: Westpac Banking Corporation<br />
                        Account Name: Top Logistics Australia<br />
                        BSB Number: 032010<br />
                        Account Number: 199734
                    </p>
                </td>

                <td valign="top" style="text-align:right; " width="60%">
                    <p style="font-size:20px;">Sub Total(Ex. GST): <?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $inv->total - $inv->gst); ?></p>
                    <?php if ($inv->gst > 0) : ?>
                        <p style="font-size:20px;">GST 10.00%: <?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $inv->gst); ?></p>
                    <?php endif; ?>
                    <p style="font-size:28px;font-weight: bold;">TOTAL AMOUNT <?= ($inv->status == Invoice::INVOICE_STATUS_PAID) ? "PAID" : "PAYABLE" ?>: <?php echo $inv->getCurrency(), ' ', AppHelper::money_format('%i', $inv->total); ?></p>
                </td>
            </tr>
        </tbody>
    </table>
</footer>