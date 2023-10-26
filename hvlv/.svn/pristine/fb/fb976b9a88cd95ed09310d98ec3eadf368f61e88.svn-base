
<div class="row rowcol">

    <div class="grid-view">

        <span style="font-size: 18px;"> Flex Price by All: <b>[P] = [Price/Kg]</b> , <b>[B] = [Base Price]</b></span>

            <table class="items">
                <thead>
                <tr>
                   <?php
                   foreach ( $data['header'] as $ttl ) {
                       echo '<th>' . $ttl . '</th>';
                   }
                   echo '<th>Mix</th>';
                   ?>

                </tr>
                </thead>
                <tbody>
                    <?php

                    $pcaZones = ZoneMap::getOrgZoneMap(114, 0, true);

                    $index = 0;
                    $totalWeight = 0;
                    $allCount = 0;
                    $totals = array();
                    $mxTotal = 0;
                    $minTotalCost = 0;
                    $rateZones = array();
                    $rateZonesName = array();

                    // add one for PCA ownself mix rate
                    //$mixRateId = 114; // pca own id
                  //  $rateZonesName[$mixRateId] = 'Mix';
                   // $rateZones[$mixRateId] = array();
                    foreach ( $data['data'] as $row ) {
                        $allCount++;
                        $totalWeight += floatval($row['weight']);
                        $minTotalCost += $row['data']['cheap']['oprice'];
                        foreach ( $row['data']['others']  as $rate ) {
                            if ( !isset($totals[$rate['id']]) ) $totals[$rate['id']] = 0;
                            $totals[$rate['id']] += $rate['oprice'];
                            if ( !isset($rateZones[$rate['id']]) ) $rateZones[$rate['id']] = array();
                            $rateZonesName[$rate['id']] = $rate['name'];
                            if ( !isset($rateZones[$rate['id']][$rate['zcode']]) ) {
                                $rateZones[$rate['id']][$rate['zcode']] = array(
                                    'name' => $rate['name'],
                                    'price' => $rate['oprice'],
                                    'weight' => $row['weight'],
                                    'count' => 1
                                );
                            } else {
                                $rateZones[$rate['id']][$rate['zcode']]['price'] += $rate['oprice'];
                                $rateZones[$rate['id']][$rate['zcode']]['weight'] += floatval($row['weight']);
                                $rateZones[$rate['id']][$rate['zcode']]['count'] += 1;
                            }
/*
                            if ( !isset($rateZones[$mixRateId][$rate['zcode']]) ) {
                                $rateZones[$mixRateId][$rate['zcode']] = array(
                                    'name' => $rate['name'],
                                    'price' => $rate['oprice'],
                                    'weight' => $row['weight'],
                                    'count' => 1
                                );
                            } else {
                                $rateZones[$mixRateId][$rate['zcode']]['price'] += $rate['oprice'];
                                $rateZones[$mixRateId][$rate['zcode']]['weight'] += $row['weight'];
                                $rateZones[$mixRateId][$rate['zcode']]['count'] += 1;
                            }
*/
                        }
                    }

                    // add total row
                    $rowData = '<tr class="odd">';
                    if ( $index++ % 2 == 0  ) {
                        $rowData = '<tr class="even">';
                    }
                    $rowData .= '<td>Total</td>';
                    $rowData .= '<td>' . $totalWeight .'</td>';
                    foreach ($totals as $k => $total ) {
                        $rowData .= '<td>' .  AppHelper::money_format('%i', $total) . '</td>';
                    }
                    $rowData .= '<td>' .  AppHelper::money_format('%i', $minTotalCost) . '</td>';
                    $rowData .= '</tr>';
                    echo $rowData;

                    // add average cost
                    $rowData = '<tr class="odd">';
                    if ( $index++ % 2 == 0  ) {
                        $rowData = '<tr class="even">';
                    }
                    $rowData .= '<td></td>';
                    if ( $data['rate'] >  0 ) {
                        $rowData .= '<td>Average(Price/Kg+Base)</td>';
                    } else {
                        $rowData .= '<td>Average(Price/Kg)</td>';
                    }
                    $totalAverage = 0;
                    foreach ($totals as $k => $total ) {
                        $average = ( $total - $data['rate'] * $allCount )   / $totalWeight;
                        if ( $data['rate'] >  0 ) {
                            $rowData .= '<td> <b>' . AppHelper::money_format('%i', $average) . '</b>[P] +  <b>' .  $data['rate'] .'</b>[B]'.'</td>';
                        } else {
                            $rowData .= '<td>' . AppHelper::money_format('%i', $average) . '</td>';
                        }
                        $totalAverage += $average;
                    }
                    $rowData .= '</tr>';
                    echo $rowData;

                    // add total average cost
                    $rowData = '<tr class="odd">';
                    if ( $index++ % 2 == 0  ) {
                        $rowData = '<tr class="even">';
                    }
                    $rowData .= '<td></td>';

                    if ( $data['rate'] >  0 ) {
                        $rowData .= '<td>Total Average(Price/Kg+Base)</td>';
                    } else {
                        $rowData .= '<td>Total Average(Price/Kg)</td>';
                    }
                    if ( $data['rate'] >  0 ) {
                        $rowData .= '<td><b>' . AppHelper::money_format('%i', $totalAverage / count($totals)) . '</b>[P] +  <b>' .  $data['rate'] .'</b>[B]'.'</td>';
                    }else {
                        $rowData .= '<td>' . AppHelper::money_format('%i', $totalAverage / count($totals)) . '</td>';
                    }
                    $rowData .= '<td></td><td></td><td></td><td></td><td></td>';
                    $rowData .= '</tr>';
                    echo $rowData;

                    // add total average cost
                    $rowData = '<tr class="odd">';
                    if ( $index++ % 2 == 0  ) {
                        $rowData = '<tr class="even">';
                    }
                    $rowData .= '<td></td>';
                    if ( $data['rate'] >  0 ) {
                        $rowData .= '<td>Real Price Offer(Price/Kg+Base)</td>';
                    } else {
                        $rowData .= '<td>Real Price Offer(Price/Kg)</td>';
                    }
                    if ( $data['rate'] >  0 ) {
                        $rowData .= '<td><b>' . AppHelper::money_format('%i', (($totalAverage / count($totals)) / ( (100 - $data['profit'])/100) )) . '</b>[P] +  <b>' . AppHelper::money_format('%i', $data['rate'] / ( (100 - $data['profit'])/100)) .'</b>[B]'.'</td>';
                    }else {
                        $rowData .= '<td>' . AppHelper::money_format('%i', (($totalAverage / count($totals)) / ( (100 - $data['profit'])/100))) . '</td>';
                    }
                    $rowData .= '<td></td><td></td><td></td><td></td><td></td>';
                    $rowData .= '</tr>';
                    echo $rowData;
                    ?>
                </tbody>
            </table>

        <br><br>

        <!--  output all cost by zone code -->

        <?php foreach ( $rateZones as $rateKey =>  $zones ) : ?>
            <div style="margin-top: 15px;"></div>
            <table class="items">
                <thead>
                <tr>
                    <?php
                    $index = 1;
                    $rowStr = '';
                    //foreach ( $zones as $k =>  $zone ) {
                    foreach ( $pcaZones as $k =>  $zone ) {
                        if ( $index == 1 ) {
                            $rowStr = '<th width="100px"></th>';
                        }
                        $rowStr .= '<th>' . $k . '</th>';
                        $index++;
                    }
                    echo $rowStr;
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php
                $index = 1;
                $allWeight = 0;
                $allCount = 0;
                //     foreach ( $zones as $k =>  $zone ) {
                foreach ( $pcaZones as $k =>  $v ) {
                    if (!isset($zones[$k])) {
                        continue;
                    } else {
                        $zone = $zones[$k];
                    }
                    $allWeight += $zone['weight'];
                    $allCount += $zone['count'];
                }
                // output all weights percent

                $pWeight = '<tr class="even"><td>Weight Percent</td>';
                $pCount = '<tr class="odd"><td>Qty Percent</td>';
                $allWeight = $allWeight > 0 ? $allWeight : 1;
                $allCount  = $allCount > 0 ? $allCount : 1;
                foreach ( $pcaZones as $k =>  $v ) {
                    // foreach ( $zones as $k =>  $zone ) {
                    if ( !isset( $zones[$k]) ) {
                        $pWeight .=  '<td></td>';
                        $pCount .= '<td></td>';
                        continue;
                    } else {
                        $zone = $zones[$k];
                    }

                    $pWeight .= '<td>' . AppHelper::money_format('%i', ($zone['weight'] / $allWeight) * 100.0 ) . '%</td>';
                    $pCount .= '<td>' . AppHelper::money_format('%i', ($zone['count'] / $allCount) * 100.0 ) . '%</td>';
                }
                $pWeight .= '</tr>';
                $pCount .= '</tr>';
                echo $pWeight;
                echo $pCount;


                // output weight range price now
                $rateWeightRanges = $data['wranges'][$rateKey];
                $index = 0;
                $totalWeightRangeWeight = 0;
                $totalWeightRangeQty = 0;
                foreach ( $rateWeightRanges as $weightKey => $weighRange ) {
                    //  foreach ($zones as $k => $zone) {
                    foreach ( $pcaZones as $k =>  $v ) {
                        if ( !isset($weighRange[$k]) ) {
                            continue;
                        }
                        $zoneWeight = $weighRange[$k]['totalWeight'];
                        $zoneWeight = $zoneWeight > 0 ? $zoneWeight : 1;
                        $totalWeightRangeWeight += $zoneWeight;
                        $totalWeightRangeQty += $weighRange[$k]['totalQty'];
                    }
                }

                // output weight range's weight and qty percent
                $rowStr = '<tr class="even">';
                if ($index++ % 2 == 0) {
                    $rowStr = '<tr class="odd">';
                }
                $rowStr .= '<td></td>';
                foreach ( $rateWeightRanges as $weightKey => $weighRange ) {
                    $rowStr .= '<td>' . $weightKey . '</td>';
                }
                $rowStr .= '</tr>';
                echo $rowStr;

                $rowStr = '<tr class="even">';
                if ($index++ % 2 == 0) {
                    $rowStr = '<tr class="odd">';
                }
                $rowWStr = $rowStr . '<td>Weight Percent</td>';
                $rowStr = '<tr class="even">';
                if ($index++ % 2 == 0) {
                    $rowStr = '<tr class="odd">';
                }
                $rowQStr = $rowStr . '<td>Qty Percent</td>';
                $totalWeightRangeWeight = $totalWeightRangeWeight > 0 ? $totalWeightRangeWeight : 1;
                $totalWeightRangeQty = $totalWeightRangeQty > 0 ? $totalWeightRangeQty : 1;
                foreach ( $rateWeightRanges as $weightKey => $weighRange ) {
                    $zoneWeight = 0;
                    $zoneQty = 0;
                    foreach ( $pcaZones as $k =>  $v ) {
                        if (isset($weighRange[$k])) {
                            $zoneWeight += floatval($weighRange[$k]['totalWeight']);
                            $zoneQty +=  $weighRange[$k]['totalQty'];
                        }
                    }
                    $rowWStr .= '<td>' . AppHelper::money_format('%i', ($zoneWeight/$totalWeightRangeWeight) * 100.0)  . '%</td>';
                    $rowQStr .= '<td>' . AppHelper::money_format('%i', ($zoneQty/$totalWeightRangeQty) * 100.0)  . '%</td>';
                }
                $rowWStr .= '</tr>';
                $rowQStr .= '</tr>';
                echo $rowWStr;
                echo $rowQStr;
                ?>
                </tbody>
            </table>
        <?php break; ?>
        <?php endforeach;?>

        <br><br>
        <span style="font-size: 18px;"> Cost Price by Zones</span>

<?php

// in order to get cheapest one price for zone by total and by weight range
$mixRateZone = array(
    'Mix' => array()
);
foreach ( $rateZones as $rateKey =>  $zones ) :
?>
    <div style="margin-top: 15px;"></div>
        <table class="items">
            <thead>
            <tr>
                <?php
                    $index = 1;
                    $rowStr = '';
                    //foreach ( $zones as $k =>  $zone ) {
                     foreach ( $pcaZones as $k =>  $zone ) {
                       if ( $index == 1 ) {
                           $rowStr = '<th width="100px"></th>';
                       }
                        $rowStr .= '<th>' . $k . '</th>';
                        $index++;
                    }
                    echo $rowStr;
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            $index = 1;
            $rowStr = '<tr class="odd">';
           // $allWeight = 0;
          //  $allCount = 0;
       //     foreach ( $zones as $k =>  $zone ) {
            foreach ( $pcaZones as $k =>  $v ) {
                if ( $index == 1 ) {
                    $rowStr .=  '<td>'.$rateZonesName[$rateKey].'</td>';
                }
                if ( !isset( $zones[$k]) ) {
                    $rowStr .=  '<td></td>';
                    continue;
                } else {
                    $zone = $zones[$k];
                }
             //   $allWeight += $zone['weight'];
            //    $allCount += $zone['count'];
                $zoneWeight = $zone['weight'] > 0 ? $zone['weight'] : 1;
                $average = (( $zone['price'] - $data['rate'] * $zone['count'] )   / $zoneWeight) / ( (100 - $data['profit'])/100);
                if ( isset($mixRateZone['Mix'][$k]) ) {
                    if ( $average > 0 && ( $mixRateZone['Mix'][$k] > $average || $mixRateZone['Mix'][$k] == 0 ) ) $mixRateZone['Mix'][$k] = $average;
                } else {
                    $mixRateZone['Mix'][$k] = $average;
                }

                if ( $data['rate'] >  0 ) {
                    $rowStr .= '<td><b>' . AppHelper::money_format('%i', $average) . '</b>[P] +  <b>' . AppHelper::money_format('%i', $data['rate'] / ( (100 - $data['profit'])/100) ) .'</b>[B]'.'</td>';
                }else {
                    $rowStr .= '<td>' . AppHelper::money_format('%i', $average) . '</td>';
                }
                $index++;
            }
            $rowStr .= '</tr>';
            echo $rowStr;

            // output all weights percent
/*
            $pWeight = '<tr class="even"><td>Weight Percent</td>';
            $pCount = '<tr class="odd"><td>Qty Percent</td>';
            $allWeight = $allWeight > 0 ? $allWeight : 1;
            $allCount  = $allCount > 0 ? $allCount : 1;
            foreach ( $pcaZones as $k =>  $v ) {
           // foreach ( $zones as $k =>  $zone ) {
                if ( !isset( $zones[$k]) ) {
                    $pWeight .=  '<td></td>';
                    $pCount .= '<td></td>';
                    continue;
                } else {
                    $zone = $zones[$k];
                }

                $pWeight .= '<td>' . AppHelper::money_format('%i', ($zone['weight'] / $allWeight) * 100.0 ) . '%</td>';
                $pCount .= '<td>' . AppHelper::money_format('%i', ($zone['count'] / $allCount) * 100.0 ) . '%</td>';
            }
            $pWeight .= '</tr>';
            $pCount .= '</tr>';
            echo $pWeight;
            echo $pCount;
*/

            // output weight range price now
            $rateWeightRanges = $data['wranges'][$rateKey];
            $index = 0;
          //  $totalWeightRangeWeight = 0;
         //   $totalWeightRangeQty = 0;
            foreach ( $rateWeightRanges as $weightKey => $weighRange ) {
                $rowStr = '<tr class="even">';
                if ( $index++ % 2 == 0  ) {
                    $rowStr = '<tr class="odd">';
                }
                $rowStr .= '<td>'. $weightKey . '</td>';

              //  foreach ($zones as $k => $zone) {
                foreach ( $pcaZones as $k =>  $v ) {
                    if ( !isset($weighRange[$k]) ) {
                        $rowStr .= '<td></td>';
                        continue;
                    }
                    $zoneWeight = $weighRange[$k]['totalWeight'];
                    $zoneWeight = $zoneWeight > 0 ? $zoneWeight : 1;
                  //  $totalWeightRangeWeight += $zoneWeight;
                 //   $totalWeightRangeQty += $weighRange[$k]['totalQty'];
                    $average = (( $weighRange[$k]['totalAmount'] - $data['rate'] * $weighRange[$k]['totalQty']) / $zoneWeight ) / ( (100 - $data['profit'])/100)  ;

                    if ( isset( $mixRateZone[$weightKey]) ) {
                        if ( isset($mixRateZone[$weightKey][$k]) ) {
                            if ( $average > 0 && ( $mixRateZone[$weightKey][$k] > $average || $mixRateZone[$weightKey][$k] == 0 ) ) $mixRateZone[$weightKey][$k] = $average;
                        } else {
                            $mixRateZone[$weightKey][$k] = $average;
                        }
                    } else {
                        $mixRateZone[$weightKey] = array($k => $average);
                    }


                    if ($data['rate'] > 0) {
                        $rowStr .= '<td><b>' . AppHelper::money_format('%i', $average) . '</b>[P] +  <b>' . AppHelper::money_format('%i', $data['rate'] / ( (100 - $data['profit'])/100) ) . '</b>[B]' . '</td>';
                    } else {
                        $rowStr .= '<td>' . AppHelper::money_format('%i', $average) . '</td>';
                    }
                }
                $rowStr .= '</tr>';
                echo $rowStr;

            }

            // output weight range's weight and qty percent
            /*
            $rowStr = '<tr class="even">';
            if ($index++ % 2 == 0) {
                $rowStr = '<tr class="odd">';
            }
            $rowStr .= '<td></td>';
            foreach ( $rateWeightRanges as $weightKey => $weighRange ) {
                $rowStr .= '<td>' . $weightKey . '</td>';
            }
            $rowStr .= '</tr>';
            echo $rowStr;

            $rowStr = '<tr class="even">';
            if ($index++ % 2 == 0) {
                $rowStr = '<tr class="odd">';
            }
            $rowWStr = $rowStr . '<td>Weight Percent</td>';
            $rowStr = '<tr class="even">';
            if ($index++ % 2 == 0) {
                $rowStr = '<tr class="odd">';
            }
            $rowQStr = $rowStr . '<td>Qty Percent</td>';
            $totalWeightRangeWeight = $totalWeightRangeWeight > 0 ? $totalWeightRangeWeight : 1;
            $totalWeightRangeQty = $totalWeightRangeQty > 0 ? $totalWeightRangeQty : 1;
            foreach ( $rateWeightRanges as $weightKey => $weighRange ) {
                $zoneWeight = 0;
                $zoneQty = 0;
                    foreach ( $pcaZones as $k =>  $v ) {
                        if (isset($weighRange[$k])) {
                            $zoneWeight +=  $weighRange[$k]['totalWeight'];
                            $zoneQty +=  $weighRange[$k]['totalQty'];
                        }
                    }
                $rowWStr .= '<td>' . AppHelper::money_format('%i', ($zoneWeight/$totalWeightRangeWeight) * 100.0)  . '%</td>';
                $rowQStr .= '<td>' . AppHelper::money_format('%i', ($zoneQty/$totalWeightRangeQty) * 100.0)  . '%</td>';
            }
            $rowWStr .= '</tr>';
            $rowQStr .= '</tr>';
            echo $rowWStr;
            echo $rowQStr;
*/



            ?>
            </tbody>
       </table>
<?php endforeach; ?>

        <div style="margin-top: 15px;"></div>
        <!-- show mix result -->
        <table class="items">
            <thead>
            <tr>
                <?php
                $index = 1;
                $rowStr = '';
                //foreach ( $zones as $k =>  $zone ) {
                foreach ( $pcaZones as $k =>  $zone ) {
                    if ( $index == 1 ) {
                        $rowStr = '<th width="100px"></th>';
                    }
                    $rowStr .= '<th>' . $k . '</th>';
                    $index++;
                }
                echo $rowStr;
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ( $mixRateZone as $ttlKey => $zones ) {
                $rowStr = '<tr class="odd">';
                $rowStr .= '<td>' . $ttlKey . '</td>';
                foreach ($pcaZones as $k => $v) {
                    $mixAverage = '';
                    if (!isset($zones[$k])) {
                        $rowStr .= '<td></td>';
                        continue;
                    } else {
                        $mixAverage = $zones[$k];
                    }
                    if ($data['rate'] > 0) {
                        $rowStr .= '<td><b>' . AppHelper::money_format('%i', $mixAverage) . '</b>[P] +  <b>' . AppHelper::money_format('%i', $data['rate'] / ((100 - $data['profit']) / 100)) . '</b>[B]' . '</td>';
                    } else {
                        $rowStr .= '<td>' . AppHelper::money_format('%i', $mixAverage) . '</td>';
                    }
                }
                $rowStr .= '</tr>';
                echo $rowStr;

            }

            // base on mix price , we calculate our raw data again
            // in order to show how much we need based on mix price
            $mixTotalCost = 0;
            $mixTotalCostByWeight = 0;
            $mixAveragePriceZone = $mixRateZone['Mix'];
            foreach ( $rawdata as $row ) {
                $postCode = $row[1];
                $weight = floatval($row[2]);

                $chargeCode = 'SYD';
                $zoneMap = ZoneMap::model()->find('org_id = 114 AND zone_id = 0 AND pc_lo <= :code AND pc_hi >= :code', [':code' => $postCode]);
                if (!empty($zoneMap) && !empty($zoneMap['z1']) ) {
                    $chargeCode = $zoneMap['z1'];
                }

                // get price
                $mixAverage = 0;
                $mixBase = 0;
                if (!isset($mixAveragePriceZone[$chargeCode])) {
                    continue;
                } else {
                    $mixAverage = $mixAveragePriceZone[$chargeCode];
                }
                if ($data['rate'] > 0) {
                    $mixBase =  $data['rate'] / ((100 - $data['profit']) / 100);
                }

                $mixTotalCost += $weight * $mixAverage + $mixBase;


                // get total cost by mix weight
                foreach ( $mixRateZone as $ttlKey => $zones ) {
                    // skip mix row
                    if ( $ttlKey == 'Mix' ) continue;

                    // base on weight to get weight key
                    $wkeys = explode('-',$ttlKey);
                    $wlo = floatval(substr(trim($wkeys[0]),0,-2));
                    $whi = floatval(substr(trim($wkeys[1]),0,-2));
                    if ( $weight >= $wlo && $weight <= $whi ) {
                        // get price
                        $mixAverage = 0;
                        $mixBase = 0;
                        if (!isset($zones[$chargeCode])) {
                            continue;
                        } else {
                            $mixAverage = $zones[$chargeCode];
                        }
                        if ($data['rate'] > 0) {
                            $mixBase =  $data['rate'] / ((100 - $data['profit']) / 100);
                        }

                        $mixTotalCostByWeight += $weight * $mixAverage + $mixBase;
                        break;
                    }

                }
            }

            $mixTotalCost = round($mixTotalCost,2);
            $rowStr = '<tr class="odd">';
            $rowStr .= '<td>Mix Total</td>';
            $rowStr .= '<td colspan="12">' . $mixTotalCost . '</td>';
            $rowStr .= '</tr>';
            echo $rowStr;

            $mixTotalCostByWeight = round($mixTotalCostByWeight,2);
            $rowStr = '<tr class="odd">';
            $rowStr .= '<td>Mix Total By Weight</td>';
            $rowStr .= '<td colspan="12">' . $mixTotalCostByWeight . '</td>';
            $rowStr .= '</tr>';
            echo $rowStr;

            ?>
            </tbody>
        </table>

    </div>

</div>