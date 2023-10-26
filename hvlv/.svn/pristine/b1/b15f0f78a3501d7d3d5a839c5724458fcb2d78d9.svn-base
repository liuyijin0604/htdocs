<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="wkhtmltopdf" content="--dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
    <meta name="wkhtmltoimage" content="--disable-smart-width --zoom 0.6 --width 420 --quality 80" />

    <title>Consumables Order</title>
    <style type="text/css">
        *{ margin: 0; padding: 0; letter-spacing: normal !important; }
        body{ font-family: Verdana, Geneva, sans-serif; font-size: 20px; text-rendering: optimize-speed; width: 1200px; }
        p.logo { text-align: center; padding-bottom: 30px; }
        .barcode{ font-family: IDAutomationHC39M; font-size: 36px; padding: 30px; text-align: center; }
        h1.dest { padding: 30px; font-size: 60px; text-align: center; }
        h4 { font-size: 26px; }
        small { font-size: 16px; }
        div.page-break { page-break-after: always; }
        h2.connote { text-align: center; padding: 30px;}
        .sender { font-size: 17px; }
        table td{ padding: 10px; }
        table td table td { padding: 5px; }
        div.bc_center div{ margin: 0 auto; }
        .dto{ font-size: 22px; }
        hr { border: none; border-top: #000 1px solid;}
        table tr.odd { background:#B8B8B8; }
        table tr.even { background:#F8F8F8; }
    </style>
</head>
<body width="1200">
<?php
$sub_tasks = [];
foreach ($tasks as $task) {
    $org = Org::model()->findByPk($task->mdata['org']);
    if (empty($org->extra['pickup_zone'])) {
        $district = '未划分区';
    } else {
        $district = oList::model()->findByPk($org->extra['pickup_zone'])->item;
    }
    $sub_tasks[$district][] = $task;
}

$total = [];
$district_totals = [];
$orgs = [];
foreach ($sub_tasks as $district => $sub_task) {
    $district_total = [];
    foreach ($sub_task as $index2 => $task) {
        $qtys = [];
        foreach ($task->items as $item) {
            if (empty($total[$item->mdata['sn']])) {
                $total[$item->mdata['sn']] = $item->mdata['uq'];
            } else {
                $total[$item->mdata['sn']] += $item->mdata['uq'];
            }
            if (empty($district_total[$item->mdata['sn']])) {
                $district_total[$item->mdata['sn']] = $item->mdata['uq'];
            } else {
                $district_total[$item->mdata['sn']] += $item->mdata['uq'];
            }
            if (empty($orgs[$district][$task->mdata['org']][$index2]['qtys'][$item->mdata['sn']])) {
                $orgs[$district][$task->mdata['org']][$index2]['qtys'][$item->mdata['sn']] = $item->mdata['uq'];
            } else {
                $orgs[$district][$task->mdata['org']][$index2]['qtys'][$item->mdata['sn']] += $item->mdata['uq'];
            }
        }

        $orgs[$district][$task->mdata['org']][$index2]['task'][] = $task;
        $invoice = InvLine::model()->find('fid = :task_id', array(':task_id' => $task->id))->invoice;
        $orgs[$district][$task->mdata['org']][$index2]['invoice'][] = $invoice;

        if (empty($orgs[$district][$task->mdata['org']][$index2]['info'])) {
            $org = Org::model()->findByPk($task->mdata['org']);
            $delivery_task = WmsTask::model()->find('link_id = :link_id AND type = 2120', array(':link_id' => $task->id));
            $orgs[$district][$task->mdata['org']][$index2]['address'] = $delivery_task->mdata['cnee'];
            $orgs[$district][$task->mdata['org']][$index2]['name'] = $org->name;
        }
    }
    $district_totals[$district] = $district_total;
}

Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
include(dirname(__FILE__) . '/' . 'cg_order_total.php');
echo '<div class="page-break"></div>';

foreach ($district_totals as $district => $district_total) {
    foreach ($orgs[$district] as $org_id => $org_tasks) {
        foreach ($org_tasks as $org) {
            include(dirname(__FILE__) . '/' . 'cg_order_district.php');
            echo '<div class="page-break"></div>';
            // foreach ($sub_task as $task) {
            //     $copy = 'Copy For PCA';
            //     include(dirname(__FILE__) . '/' . 'cg_order_single.php');
            //     echo '<div class="page-break"></div>';
            //     $org = Org::model()->findByPk($task->mdata['org']);
            //     $copy = 'Copy For Client';
            //     include(dirname(__FILE__) . '/' . 'cg_order_single.php');
            //     echo '<div class="page-break"></div>';
            // }
        }
    }
}
?>
</body>
</html>
