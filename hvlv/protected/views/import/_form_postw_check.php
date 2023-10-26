
<div class="row rowcol">

    <div class="grid-view">
            <table class="items">
                <thead>
                <tr>
                   <?php
                   foreach ( $header as $ttl ) {
                       echo '<th>' . $ttl . '</th>';
                   }
                   ?>
                    <th>MX</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    <?php
                        $index = 0;
                        $totals = array();
                        $mxTotal = 0;
                        foreach ( $data as $row ) {
                            $rowData = '<tr class="odd">';
                            if ( $index++ % 2 == 0  ) {
                                $rowData = '<tr class="even">';
                            }
                            $rowData .= '<td>' . $row['postcode'] . '</td>';
                            $rowData .= '<td>' . number_format(floatval($row['weight']),2,'.','') . '</td>';

                            foreach ( $row['data']['others']  as $rate ) {
                                $rowData .= '<td>' . $rate['price'] . '</td>';
                                if ( !isset($totals[$rate['id']]) ) $totals[$rate['id']] = 0;
                                $totals[$rate['id']] += $rate['oprice'];
                            }
                            $rowData .= '<td>' . $row['data']['cheap']['name'] . '</td>';
                            $rowData .= '<td>' . $row['data']['cheap']['price'] . '</td>';
                            $mxTotal += $row['data']['cheap']['oprice'];

                            $rowData .= '</tr>';

                            echo $rowData;
                        }

                        // add total row
                    $rowData = '<tr class="odd">';
                    if ( $index++ % 2 == 0  ) {
                        $rowData = '<tr class="even">';
                    }
                    $rowData .= '<td></td>';
                    $rowData .= '<td>Total</td>';
                    foreach ($totals as $k => $total ) {
                        $rowData .= '<td>' .  AppHelper::money_format('%i', $total) . '</td>';
                    }
                    $rowData .= '<td></td>';
                    $rowData .= '<td>'. AppHelper::money_format('%i', $mxTotal) . '</td>';
                    $rowData .= '</tr>';
                    echo $rowData;
                    ?>
                </tbody>
            </table>
    </div>

</div>