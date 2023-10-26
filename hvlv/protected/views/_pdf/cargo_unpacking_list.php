<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="wkhtmltopdf" content=" --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait --page-size A4" />
    <title>Cargo Unpacking List</title>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            letter-spacing: normal !important;
        }

        body {
            font-family: Verdana, Geneva, sans-serif;
            font-size: 16px;
            text-rendering: optimize-speed;
            width: 1120px;
        }

        table.chart td,
        table.chart th {
            border: 1px #999 solid;
            padding: 2px;
            border-right: none;
            border-bottom: none;
        }

        table.chart {
            border: none;
            border: 1px #999 solid;
            border-top: none;
            border-left: none;
        }

        table.chart1 td,
        table.chart1 th {
            border: 1px #999 solid;
            padding: 2px;
            border-right: none;
            border-bottom: none;
        }

        table.chart1 {
            border: none;
            border: 1px #999 solid;
            border-top: none;
            border-left: none;
        }

        table.charts td,
        table.charts th {
            border: none
        }

        table.charts {
            border: none;
        }

        tr.even td,
        tr.even th {
            background: rgba(200, 200, 200, 0.6);
        }

        .even1 {
            background: rgba(200, 200, 200, 0.6);
        }

        header {
            padding-bottom: 15px;
        }

        footer {
            padding-top: 10px;
            page-break-after: always;
        }

        span {
            font-size: 20px;
        }

        .unpacking_list {
            text-align: center;
        }

        .barcode_container {
            padding-top: 10px;
            padding-bottom: 10px;
        }
    </style>
</head>

<body width="1120">
    <header>
        <table width="100%" cellspacing="0" cellpadding="0" style="margin-top: 30px;height: 50px; ">
            <tr>
                <td style="text-align: left; font-size: 30px;font-weight: bold;padding-bottom:10px;" width="50%">Cargo Unpacking List</td>
            </tr>
        </table>
    </header>
    <br />
    <table width="100%" cellspacing="0" class="chart">
        <tr>
            <th class="unpacking_list enve1">Region</th>
            <th class="unpacking_list even1">Ref</th>
            <th class="unpacking_list even1">PCS</th>
            <th class="unpacking_list even1">Shipment</th>
            <th class="unpacking_list even1">Weight</th>
            <th class="unpacking_list even1">Volume</th>
            <th class="unpacking_list even1">Status</th>
            <th class="unpacking_list even1">Barcode</th>
        </tr>
        <?php
        /* foreach ($shipments as $shipment) {
            $short = $surplus = 0;
            if ($shipment->pkg > $shipment->getOutPkg()) {
                $short = $shipment->pkg - $shipment->getOutPkg();
            } else if ($shipment->getOutPkg() > $shipment->pkg) {
                $surplus = $shipment->getOutPkg() - $shipment->pkg;
            }
            $damaged = !empty($shipment->mdata['damaged_packs']) ? $shipment->mdata['damaged_packs'] : 0;
            $comments = "";
            if (!empty($shipment->mdata['amzon_pallet'])) {
                $comments =  intval($shipment->mdata['amzon_pallet']) . " " . $shipment->mdata['amzon_pallet_type'] . " Pallets";
            }
            $comments .= empty($shipment->mdata['outturn_comments']) ? "" : "</br>" . $shipment->mdata['outturn_comments'];
            echo '<tr height="90px"><td class="outturn_list">' . $shipment->hbn . '</td><td class="outturn_list">' . @$shipment->cnee->name . '</td><td class="outturn_list">' . $shipment->mdata['sea_mark_number'] . '</td><td class="outturn_list">' . $shipment->weight . 'KG</td><td class="outturn_list">' . number_format((float)$shipment->cbm * (float)$shipment->pkg, 2, '.', '') . 'M<sup>3</sup></td><td class="outturn_list">' . $shipment->pkg . '</td><td class="outturn_list">' . $shipment->getOutPkg() . '</td><td class="outturn_list">' . $short . '</td><td class="outturn_list">' . $surplus . '</td><td class="outturn_list">0</td><td class="outturn_list">' . $damaged . '</td><td class="outturn_list">' . $comments . '</td></tr>';
        } */
        //Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
        Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
        foreach ($regionSum as $key => $value) {
            if ($value['pcs'] == 0 && $value['shipment'] == 0) {
                continue;
            }
            echo '<tr height="60px" class="even"><td class="unpacking_list">' . $key . '</td><td class="unpacking_list"></td><td class="unpacking_list">' . $value['pcs'] . '</td><td class="unpacking_list">' . $value['shipment'] . '</td><td class="unpacking_list">' . $value['weight'] . '</td><td class="unpacking_list">' . $value['volumn'] . '</td><td class="unpacking_list"></td><td class="unpacking_list"></td></tr>';
            foreach ($listData[$key] as $element) {
                $shipment = ImParcel::model()->findByAttributes(array('ref' => $element->ref));
                //$bc = new TCPDF2DBarcode($shipment->ref.($shipment->pkg > 1 ? '-1' : ''), 'QRCODE');
                $bc = new TCPDFBarcode($shipment->ref.($shipment->pkg > 1 ? '-1' : ''), 'C128');
                echo '<tr height="100px"><td class="unpacking_list">' . $key . '</td><td class="unpacking_list">' . $element->ref . '</td><td class="unpacking_list">' . $element->pcs . '</td><td class="unpacking_list">1</td><td class="unpacking_list">' . $element->weight . '</td><td class="unpacking_list">' . $element->volume . '</td><td class="unpacking_list">' . ImParcel::$states[$element->status] . '</td><td class="unpacking_list barcode_container">' . $bc->getBarcodeSVGcode(3, 70) . '</td></tr>';
            }
            //echo '<tr style="height: 30px; border: solid;"><td style="height: 30px; border-top: solid 1px; border-bottom: solid 1px;"></td><td style="height: 30px; border-top: solid 1px; border-bottom: solid 1px;"></td><td style="height: 30px; border-top: solid 1px; border-bottom: solid 1px;"></td><td style="height: 30px; border-top: solid 1px; border-bottom: solid 1px;"></td><td style="height: 30px; border-top: solid 1px; border-bottom: solid 1px;"></td><td style="height: 30px; border-top: solid 1px; border-bottom: solid 1px;"></td><td style="height: 30px; border-top: solid 1px; border-bottom: solid 1px;"></td><td style="height: 30px; border-top: solid 1px; border-bottom: solid 1px;"></td></tr>';
        }
        ?>
    </table>
</body>

</html>