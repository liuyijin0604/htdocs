<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
?>

<div style="margin-top: 20px;text-align: center;">
    <h1><?=$copy?></h1>
    <h1><?=$district?></h1>
    <br>
    <p style="padding:5px;">
        <?php
        if ( !empty($task->getNo()) ) {
            $bc = new TCPDFBarcode($task->getNo(), 'C128');
            $bc->getBarcodeSVG(2.5, 120, 'black');
        }
        ?></p>
    <p style="padding: 15px 0;"><?= $task->getNo(); ?></p>
</div>

<?php
$org = Org::model()->findByPk($task->mdata['org']);
echo '<h2>Client : </h2><span>'. $org->name . '(' . $org->id .')</span><br/><br/>';

echo '<h3>Order Date :</h3><span> '. $task->schd_time . '</span>';
echo '<h3>Delivery Date :</h3><span> '. $task->mdata['delivery_time'] . '</span>';

?>
<div style="margin-top: 30px;margin-bottom: 10px;">
<span><b>Driver Name:</b> _____________________ <span><br/><br/><br/>
<span><b> Customer Sign:</b> ____________________________ </span>
</div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">


    <tr style="background: rgba(100,100,100,0.4);">
        <td align="left">#</td>
        <td align="left">Name</td>
        <td align="left">Quantity</td>
    </tr>

    <?php

    $index = 1;
    foreach( $task->items as $item ){
        echo '<tr class="'.($index & 2 > 0? 'even' : 'odd').'"><td valign="top">'.$index++.'</td><td valign="top">'.$item->mdata['sn'].'</td><td valign="top">'.$item->mdata['uq'].'</td></tr>';
    }
    ?>

</table>


