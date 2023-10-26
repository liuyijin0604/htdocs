<div style="text-align: center;margin: 20px;">
<h1>Order Consumables District <?=$district?></h1>
</div>
<table width="92%" border="1" cellspacing="0" cellpadding="0">
    <?php
        $span = count($org['qtys']);

        ksort($district_total);
        echo '<tr style="background: rgba(100,100,100,0.4);"><td align="center">#</td>';
        foreach ($district_total as $name => $qty) {
            if (!empty($org['qtys'][$name])) {
                echo '<td align="center">' . $name . '</td>';
            }
        }
        echo '</tr>';

        ksort($org['qtys']);
        echo '<tr><td align="center">' . $org_id . '</td>';
        foreach ($district_total as $name => $qty) {
            if (!empty($org['qtys'][$name])) {
                echo '<td align="center">' . $org['qtys'][$name] . '</td>';
            }
        }
        echo '</tr>';
        echo '<tr>';
        echo '<td colspan="' . $span/2 . '">Invoice: ';
        foreach ($org['invoice'] as $invoice) {
            echo $invoice->no . '&nbsp';
        }
        echo '</td>';
        echo '<td colspan="100%">Task: ';
        foreach ($org['task'] as $item) {
            echo $item->getNo() . '&nbsp;';
        }
        echo '</td>';
        echo '</tr>';

        echo '<tr><td colspan="' . $span/2 . '">' . 'Client: ' . $org['name'] . '</td><td colspan="100%">Address: ' . $org['address']['address'] . ', ' . $org['address']['suburb'] . ' ' . $org['address']['state'] . ' ' . $org['address']['postcode'] . '</td></tr>';

        echo '<tr height="100"><td colspan="100%" valign="bottom"><b>Customer Sign:</b> _____________________ <span>&nbsp;&nbsp;<b>Date:</b> _____________________ <span></td>';

        echo '<tr height="100"><td colspan="100%" valign="bottom"><b>Driver Sign:</b> _____________________ <span>&nbsp;&nbsp;<b>Date:</b> _____________________ <span></td>';
	?>
</table>