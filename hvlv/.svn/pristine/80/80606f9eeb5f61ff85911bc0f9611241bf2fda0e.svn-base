<div style="text-align: center;margin: 20px;">
<h1>Order Consumables Total</h1>
</div>
<table width="92%" border="1" cellspacing="0" cellpadding="0">
    <?php
        ksort($total);
        echo '<tr style="background: rgba(100,100,100,0.4);"><td align="center">#</td>';
        foreach ($total as $name => $qty) {
            echo '<td align="center">' . $name . '</td>';
        }
        echo '</tr>';

        $index = 1;
        foreach ($district_totals as $district => $district_total) {
            foreach ($orgs[$district] as $org_id => $org_tasks) {
                foreach ($org_tasks as $org) {
                    ksort($district_total);
                    echo '<tr class="' . ($index++ & 2 > 0 ? 'even' : 'odd') . '"><td align="center">' . $org_id . '</td>';
                    foreach ($total as $name => $qty) {
                        if (empty($org['qtys'][$name])) {
                            echo '<td align="center">0</td>';
                        } else {
                            echo '<td align="center">' . $org['qtys'][$name] . '</td>';
                        }
                    }
                    echo '</tr>';
                    echo '<tr class="' . ($index-1 & 2 > 0 ? 'even' : 'odd') . '" height="100"><td colspan="100%" valign="bottom"><b>Task: </b>' . $org['task'][0]->getNo() .' <b>Order Date: </b>' . $org['task'][0]->schd_time . '<br><br><b>Driver Sign:</b> _____________________ <span>&nbsp;&nbsp;<b>Date:</b> _____________________ <span></td></tr>';
                }
            }
        }

        echo '<tr class="' . ($index++ & 2 > 0 ? 'even' : 'odd') . '"><td align="center">Total</td>';
        foreach ($total as $name => $qty) {
            echo '<td align="center">' . $qty . '</td>';
        }
        echo '</tr>';
	?>
</table>
