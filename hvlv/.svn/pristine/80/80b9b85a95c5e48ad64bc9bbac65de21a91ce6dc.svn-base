
<div style="margin-top: 10px;">
<p><span>Printed Date: </span> <span> <?php echo date('D d/m/Y'); ?></span><span style="font-size: 25px;text-align: center;padding-left: 180px;"><strong>Manifest Report for TOLL PRIORITY (AUS)</strong></span></p>
</div>
<div style="margin-top: 10px;">
<p><strong>Manifest Id : <?php echo $model->no; ?> </strong><strong style="margin-left: 150px;">Sender : </strong>PCA Express <strong style="margin-left: 40px;">Sender Account : </strong>209253</p>
<?php
    $tollSendDate = time();
    if (isset($model->mdata['tollsent_date']) ) $tollSendDate = strtotime($model->mdata['tollsent_date']);
?>
<p><strong>Dispatch Date </strong><strong>: </strong> <?php echo date('D d/m/Y',$tollSendDate); ?> <span style="margin-left: 235px;">6C The Crescent, Kingsgrove, NSW 2208</span></p>
</div>

<div style="margin-top: 20px;">
<p style="font-size: 36px;"><strong>Service : Parcels - Overnight / OffPeak</strong></p>
</div>

<div style="margin-top: 30px;">
<table>
    <thead style="border-bottom: 1px solid #000000;">
    <th>ConNote ID</th>
    <th>Deliver To</th>
    <th>Who Pays</th>
    <th>Suburb</th>
    <th>PostCode</th>
    <th>Total Items</th>
    <th>Total Cubic<br>(m<sup>3</sup>)</th>
    <th>Total Weight<br>(kg)</th>
    <th>DG</th>
    </thead>
    <tbody>
    <?php
    $totalItems = 0;
    $totalcbm = 0;
    $totalWeight = 0;
    ?>
    <?php foreach ( $rs as $s ) : ?>
    <tr>
        <?php
        $totalItems += $s->pkg;
        $totalcbm += $s->cbm;
        $totalWeight += $s->weight;
        ?>
        <td  width="46"><?php echo $s->ref; ?><br><?php echo $s->hbn; ?></td>
        <td  width="346"><?php echo $s->cnee->address; ?></td>
        <td  width="46">Sender</td>
        <td  width="46"><?php echo $s->cnee->suburb; ?></td>
        <td  width="46"><?php echo $s->cnee->postcode; ?></td>
        <td  width="46"><?php echo $s->pkg; ?></td>
        <td  width="46"><?php echo $s->cbm; ?></td>
        <td  width="46"><?php echo $s->weight; ?></td>
        <td  width="46">N</td>
    </tr>
    <?php endforeach; ?>
<tr style="margin-top: 30px;padding-top: 30px;">
    <td colspan="2"><p style="text-align: left;"><b>Total No. Of ConNotes:</b> <?php echo count($rs); ?></p></td>

    <td colspan="3" style="text-align: right;"><b>Total for Manifest:</b></td>
    <td><?php echo $totalItems; ?></td>
    <td><?php echo number_format($totalcbm,6); ?></td>
    <td><?php echo $totalWeight; ?></td>
</tr>
    </tbody>
</table>
</div>


<div style="width: 90%; text-align: center; border: 1px solid black; margin-top: 20px;padding: 10px;">
    <p style="font-size: 26px;"><b>DOES THIS CONSIGNMENT CONTAIN DANGEROUS GOODS? (  ) YES  (X) NO </b> </p>
    <p>
       <table>
        <tbody>
        <tr style="width: 100%">
            <td width="60%"><p style="text-align: left;">I HEREBY DECLARE THAT THESE CONSIGNMENTS DO NOT	CONTAIN DANGEROUS GOODS, AND THAT THESE	 DO NOT CONTAIN ANY UNAUTHORISED EXPLOSIVE OR INCENDIARY DEVICES. PLEASE ACCEPT FOR CARRIAGE THE GOODS DESCRIBED HEREON SUBJECT TO THE TERMS AND CONDITIONS OF THE CARRIER. I AM ALSO AWARE THAT THESE CONSIGNMENTS WILL BE SUBJECT TO SECURITY EXAMINATION AND CLEARING.</p>
            </td>
            <td><p style="text-align: left;">GOODS DESCRIBED HEREON NUMBERING 1 ITEMS RECEIVED FOR CARRIAGE IN ACCORDANCE WITH CARRIER'S CONSIGNMENTS CONDITION OF TRANSPORT</p></td>
        </tr>
        </tbody>
        </table>
    </p>

    <p style="margin-top: 20px;">
    <table>
        <tbody>
        <tr style="width: 100%">
            <td width="80%"><p style="text-align: left;">Sender's Signature:</p></td>
            <td><p style="text-align: left;">Driver's Signature:</p></td>
        </tr>
        <tr style="width: 100%">
            <td width="80%"><p style="text-align: left;">Date:</p></td>
            <td><p style="text-align: left;">Date:</p></td>
        </tr>
        </tbody>
    </table>
    </p>
</div>