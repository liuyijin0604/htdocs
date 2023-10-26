<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait --page-size A4" />
    <?php //--disable-smart-shrinking 
    ?>
    <title>Top Logistics Booking Confirmation</title>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            letter-spacing: normal !important;
        }

        body {
            font-family: Verdana, Geneva, sans-serif;
            font-size: 18px;
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

        tr.even td,
        tr.even th {
            background: rgba(200, 200, 200, 0.6);
        }

        header {
            padding-bottom: 15px;
        }

        footer {
            padding-top: 10px;
            page-break-after: always;
        }
    </style>
</head>

<body width="1120">
    <header>
        <?php
        $hdr = '_header_tla.php';
        if (!empty($behalf)) {
            switch ($behalf) {
                case 'priority':
                    $hdr = '_header_tla.php';
                    break;
            }
            if (!empty($inv->mdata['suborg'])) {
                $sorg = Org::model()->findByPk($inv->mdata['suborg']);
                $inv->mdata['name'] = $sorg->name;
                $inv->mdata['address'] = $sorg->getAddress();
            }
        }
        if (Yii::app()->name == 'TLA') {
            $hdr = '_header_tla.php';
        }
        include($hdr);
        ?>
        <table width="100%" cellspacing="0" cellpadding="0">
            <tbody>
                <tr>
                    <td style="text-align: center; font-size: 28px; font-weight: bold;padding: 10px;" colspan="2">Booking Confirmation</td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
        </table>
    </header>
    <table width="100%" cellspacing="0" class="chart">
        <thead>
            <tr style="background: rgba(100,100,100,0.4);">
                <th align="center">Booking#</th>
                <th align="center">Time</th>
                <th align="center">Rego</th>
                <th align="center">Driver Name</th>
                <th align="center">Company Name</th>
                <th align="center">Number of Pallets</th>
                <th align="center">Plain Pallet</th>
                <th align="center">Shrink Wrap</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center"><?php echo $confirmationData['booking_no']; ?></td>
                <td align="center"><?php echo $confirmationData['time']; ?></td>
                <td align="center"><?php echo $confirmationData['rego']; ?></td>
                <td align="center"><?php echo $confirmationData['driver']; ?></td>
                <td align="center"><?php echo $confirmationData['company']; ?></td>
                <td align="center"><?php echo $confirmationData['palletCount']; ?></td>
                <td align="center"><?php echo $confirmationData['plain_pallet']; ?></td>
                <td align="center"><?php echo $confirmationData['wrap']; ?></td>
            </tr>
            <tr style="background: rgba(100,100,100,0.4);">
                <td align="center" colspan="9">QR Code</td>
            </tr>
            <tr>
                <td align="center" colspan="9">
                    <img src="data:image/svg+xml;base64,<?php
                                    Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
                                    Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
                                    $dm = new TCPDF2DBarcode(chr(232).$confirmationData['booking_no'].chr(29), 'DATAMATRIX');
                                    echo base64_encode(str_replace([chr(232),chr(29)], ['',''], $dm->getBarcodeSVGcode(8, 8, 'black')));
                                    ?>" width="160" style="position:relative;"/>
                </td>
            </tr>
        </tbody>
    </table>
    <?php include('_pagination.php'); ?>
</body>

</html>