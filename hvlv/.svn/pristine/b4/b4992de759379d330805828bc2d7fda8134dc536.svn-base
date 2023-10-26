
<h2>Accounting Balance Report Details</h2>

<?php
    $balanceMonth = $model->date_month;
    $prebalanceFileLink = '#';
if ( !empty($premodel) ) {
    $prebalanceFileInfo = FileRepo::model()->findByPk($premodel->mdata->balance_file_id);
    $prebalanceFileLink =  DIRECTORY_SEPARATOR . 'filerepo' . DIRECTORY_SEPARATOR . $prebalanceFileInfo->hash . DIRECTORY_SEPARATOR . $prebalanceFileInfo->name;
}
    $invoiceFileInfo = FileRepo::model()->findByPk( $model->mdata->invoice_file_id);
    $invoiceFileLink = DIRECTORY_SEPARATOR.'filerepo'.DIRECTORY_SEPARATOR.$invoiceFileInfo->hash.DIRECTORY_SEPARATOR.$invoiceFileInfo->name;

    $paymentFileInfo = FileRepo::model()->findByPk( $model->mdata->payment_file_id);
    $paymentFileLink = DIRECTORY_SEPARATOR.'filerepo'.DIRECTORY_SEPARATOR.$paymentFileInfo->hash.DIRECTORY_SEPARATOR.$paymentFileInfo->name;

    $balanceFileInfo = FileRepo::model()->findByPk($model->mdata->balance_file_id);
    $balanceFileLink =  DIRECTORY_SEPARATOR . 'filerepo' . DIRECTORY_SEPARATOR . $balanceFileInfo->hash . DIRECTORY_SEPARATOR . $balanceFileInfo->name;

    $cbalanceFileLink = '#';
    if ( isset($model->mdata->cbalance_file_id) ) {
        $cbalanceFileInfo = FileRepo::model()->findByPk($model->mdata->cbalance_file_id);
        if ( !empty($cbalanceFileInfo) ) {
            $cbalanceFileLink = DIRECTORY_SEPARATOR . 'filerepo' . DIRECTORY_SEPARATOR . $cbalanceFileInfo->hash . DIRECTORY_SEPARATOR . $cbalanceFileInfo->name;
        }
    }


?>
<h3>Balance Date : <?= $balanceMonth; ?></h3>
<div class="row">
    <span> Last month balance : <?= $model->last_balance;?>  &nbsp; <a href="<?=$balanceFileLink;?>">download details</a></span>
</div>

<div class="row">
    <span> Occurring invoice amount : <?= $model->current_invoice;?>  &nbsp; <a href="<?=$invoiceFileLink;?>">download details</a></span>
</div>

<div class="row">
    <span> Occurring payment amount : <?= $model->current_payment;?>  &nbsp; <a href="<?=$paymentFileLink;?>">download details</a></span>
</div>


<div class="row">
    <span> This month balance : <?= $model->current_balance;?>  &nbsp; <a href="<?=$balanceFileLink;?>">download details</a></span>
</div>

<div class="row">
    <span> <a href="<?=$cbalanceFileLink;?>">download balance movement by customer</a></span>
</div>