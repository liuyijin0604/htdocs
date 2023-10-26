<div style="right: 20px;position: absolute;">
    <a href="#" data-dropdown="#<?=$_GET['tabid'];?>_rec_dropdown" ><div style="background-position:-48px -688px" class="icon"></div>Export</a>
    <div id="<?=$_GET['tabid'];?>_rec_dropdown" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
        <ul class="dropdown-menu">
            <?php if ($model->parent->client_type == Reconciliation::STARTRACK_TYPE) { ?>
            <li><a  href="<?=$this->createUrl('invoice/exportStartractWeightDiff', ['id' => $model->parent_id]);?>" target="_blank">Export Startrack Weight Diff</a></li>
            <?php } ?>
            <li><a href="<?=$this->createUrl('invoice/reconciliationExport',['id' => $model->parent_id]);?>" target="_blank" title="Export Reconciliation">Export Detail</a></li>
            <?php if ($model->parent->client_type == Reconciliation::AUPOST_INVOICE) { ?>
            <li><a href="<?=$this->createUrl('invoice/exportManifestAmountDiff',['id' => $model->parent_id]);?>" target="_blank" title="Export Manifest Amount Diff">Export Manifest Amount Diff</a></li>
            <?php } ?>
       </ul>
    </div>

    <?php if ($model->parent->client_type == Reconciliation::AUPOST_INVOICE) { ?>
      <a href="<?=Yii::app()->createUrl('invoice/createAupostBilling', ['id' => $model->parent_id])?>" class="ajax_link"><div class="icon" style="background-position:-16px 0"></div> Create Billing</a>
    <?php } ?>
</div>
<h1><?=$this->t('Details'."-".Reconciliation::$types[$model->parent->client_type]."-".$model->parent->invoice_no."-".$model->parent->invoice_date);?></h1>
<style>
    .cloumn_red_rec{
        background-color: red;
    }
</style>
<?php 
 $columns = [];
  if($model->parent->client_type == 3)
  {
    $columns = array(
  //  'shipment_no',
       array('name' => 'shipment_no', 'header' => 'Manifest No', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/updateByNo", array("no" => $data->shipment_no))."\" class=\"tab_link\" title=\"".$data->shipment_no."\">".$data->shipment_no."</a>"'),
       // array( 'name' => 'consol_id','type' => 'raw','value' => '"<a href=\"".Yii::app()->createURL("imcoConsol/updateByNo", array("no" => !empty($data->consol) ? $data->consol->no : ""))."\" class=\"tab_link\" title=\"".!empty($data->consol) ? $data->consol->no : ""."\">".!empty($data->consol) ? $data->consol->no : ""."</a>"'),
       array('name'=>'consol_no','type'=>'raw','value' =>'empty($data->getConsol())? "" : "<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" => $data->consol_id))."\" class=\"tab_link\" title=\"".@$data->consol->no."\">".@$data->consol->no."</a>"',),
       array( 'name' => 'invoice_no','value' => '$data->getInvoiceNoColumnData()'),
       'postcode',
       'weight',
       array('header'=>'Our Charge Weight','value'=>'$data->ourChargeWeight()'),
       'manifest_weight',
       array('name'=>'value','header'=>'Amount','value'=>'$data->getPostage()'),
       array('name'=>'my_value', 'value'=>'$data->my_value'),
       // array('name'=>'my_value_m','header'=>'My Value Manifest'),
       array('name'=>'chargeInvoiceDiff','filter'=>false,'value'=>'$data->getCostDiff2()')
     );
  }else if($model->parent->isDeclare())
  {
    $columns = array(
  //  'shipment_no',
       array('name' => 'shipment_no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/updateByNo", array("no" => $data->shipment_no))."\" class=\"tab_link\" title=\"".$data->shipment_no."\">".$data->shipment_no."</a>"'),
       // array( 'name' => 'consol_id','type' => 'raw','value' => '"<a href=\"".Yii::app()->createURL("imcoConsol/updateByNo", array("no" => !empty($data->consol) ? $data->consol->no : ""))."\" class=\"tab_link\" title=\"".!empty($data->consol) ? $data->consol->no : ""."\">".!empty($data->consol) ? $data->consol->no : ""."</a>"'),
       array('name'=>'consol_no','type'=>'raw','value' =>'empty($data->consol_id)? "" : "<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" => $data->consol_id))."\" class=\"tab_link\" title=\"".@$data->consol->no."\">".@$data->consol->no."</a>"',),
       array( 'name' => 'invoice_no','value' => '$data->getInvoiceNoColumnData()'),
       'postcode',
       'weight',
       array('header'=>'Our Charge Weight','value'=>'$data->ourChargeWeight()'),
       'manifest_weight',
       array('name'=>'value','header'=>'Amount','value'=>'$data->getPostage()'),
       array('name'=>'my_value', 'value'=>'$data->my_value'),
       // array('name'=>'my_value_m','header'=>'My Value Manifest'),
       array('header'=>'charge difference','value'=>'$data->mdata["chargeDifference"]'),
       array('name'=>'chargeInvoiceDiff','filter'=>false,'value'=>'$data->getCostDiff2()')
     );
  }
  else if($model->parent->isRTS())
  {
    $columns = array(
  //  'shipment_no',
       array('name' => 'shipment_no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/updateByNo", array("no" => $data->shipment_no))."\" class=\"tab_link\" title=\"".$data->shipment_no."\">".$data->shipment_no."</a>"'),
       // array( 'name' => 'consol_id','type' => 'raw','value' => '"<a href=\"".Yii::app()->createURL("imcoConsol/updateByNo", array("no" => !empty($data->consol) ? $data->consol->no : ""))."\" class=\"tab_link\" title=\"".!empty($data->consol) ? $data->consol->no : ""."\">".!empty($data->consol) ? $data->consol->no : ""."</a>"'),
       array('name'=>'consol_no','type'=>'raw','value' =>'empty($data->consol_id)? "" : "<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" => $data->consol_id))."\" class=\"tab_link\" title=\"".@$data->consol->no."\">".@$data->consol->no."</a>"',),
       array( 'name' => 'invoice_no','value' => '$data->getInvoiceNoColumnData()'),
       'postcode',
       array('header'=>'RTS fee','value'=>'$data->mdata["RTS_fee"]'),
       array("header"=>'fuel charge exclude GST','value'=>'$data->mdata["fuel_charge"]'),
       'value'
     );
  }
  else if ($model->parent->client_type == Reconciliation::AUPOST_RTS) {
     $columns = array(
       array('name' => 'shipment_no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/updateByNo", array("no" => $data->shipment_no))."\" class=\"tab_link\" title=\"".$data->shipment_no."\">".$data->shipment_no."</a>"'),
       // array( 'name' => 'consol_id','type' => 'raw','value' => '"<a href=\"".Yii::app()->createURL("imcoConsol/updateByNo", array("no" => !empty($data->consol) ? $data->consol->no : ""))."\" class=\"tab_link\" title=\"".!empty($data->consol) ? $data->consol->no : ""."\">".!empty($data->consol) ? $data->consol->no : ""."</a>"'),
       array('name'=>'consol_no','type'=>'raw','value' =>'empty($data->getConsol())? "" : "<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" => $data->consol_id))."\" class=\"tab_link\" title=\"".@$data->consol->no."\">".@$data->consol->no."</a>"',),
       array( 'name' => 'invoice_no','value' => '$data->getInvoiceNoColumnData()'),
       'postcode',
       'weight',
       'courier_cubic',
       array('header'=>'250To167 Weight','value'=>'$data->getCourierToOurWeight()','cssClassExpression' => '$data->getCourierToOurWeight()>$data->ourChargeWeight()? "cloumn_red_rec" : ""'),
       array('header'=>'Our Charge Weight','value'=>'$data->ourChargeWeight()'),
       'manifest_weight',
       'value',
       array('name'=>'my_value'),
       array('name'=>'my_charge'),
       // array('name'=>'my_value_m','header'=>'My Value Manifest'),
       array('name'=>'chargeInvoiceDiff','filter'=>false,'value'=>'$data->getCostDiff()'),
       'subAmount'
     );
  }
  else if ($model->parent->client_type == Reconciliation::AUPOST_INVOICE) {
     $columns = array(
       array('name' => 'shipment_no', 'header' => 'Manifest No', 'type' => 'raw', 'value' => '$data->getAupostInfo()'),
       array('header' => 'Consol No', 'value' => '$data->getConsolNos()'),
       array('name' => 'postcode', 'header' => 'Type', 'value' => '$data->postcode', 'filter' => CHtml::dropDownList('ReconciliationLine[postcode]', $model->postcode, $this->t(['eParcel' => 'eParcel', 'Return to sender' => 'Return to sender', 'ELMS' => 'ELMS', 'UNKNOWN' => 'UNKNOWN']), array('prompt'=>$this->t('All'))),),
       'value',
       array('header' => 'Claim Amount (Excl. GST)', 'value' => 'floatval(@$data->mdata["claim_value"])'),
       array('header' => 'Diff(Claim Amount-Amount) (Excl. GST)', 'value' => 'number_format(floatval(@$data->mdata["claim_value"]) - $data->value, 2, ".", "")'),
       array('name'=>'my_value', 'header' => 'Accept Amount (Excl. GST)'),
       array('name'=>'chargeInvoiceDiff', 'header' => 'Diff(Accept Amount-Amount) (Excl. GST)', 'filter'=>false,'value'=>'$data->getCostDiff()'),
       // array('name'=>'my_charge'),
       // 'subAmount'
     );
  } else if ($model->parent->client_type == Reconciliation::UBI_TYPE) {
     $columns = array(
  //  'shipment_no',
       array('name' => 'shipment_no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/updateByNo", array("no" => $data->shipment_no))."\" class=\"tab_link\" title=\"".$data->shipment_no."\">".$data->shipment_no."</a>"'),
       // array( 'name' => 'consol_id','type' => 'raw','value' => '"<a href=\"".Yii::app()->createURL("imcoConsol/updateByNo", array("no" => !empty($data->consol) ? $data->consol->no : ""))."\" class=\"tab_link\" title=\"".!empty($data->consol) ? $data->consol->no : ""."\">".!empty($data->consol) ? $data->consol->no : ""."</a>"'),
       array('name'=>'consol_no','type'=>'raw','value' =>'empty($data->getConsol())? "" : "<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" => $data->consol_id))."\" class=\"tab_link\" title=\"".@$data->consol->no."\">".@$data->consol->no."</a>"',),
       array( 'name' => 'invoice_no','value' => '$data->getInvoiceNoColumnData()'),
       'postcode',
       'weight',
       'courier_cubic',
       array('header'=>'250To167 Weight','value'=>'$data->getCourierToOurWeight()','cssClassExpression' => '$data->getCourierToOurWeight()>$data->ourChargeWeight()? "cloumn_red_rec" : ""'),
       array('header'=>'Our Charge Weight','value'=>'$data->ourChargeWeight()'),
       'manifest_weight',
       'value',
       array('header' => 'Fuel', 'value' => 'floatval(@$data->mdata["fuel_charge"])'),
       array('header' => 'Subtotal', 'value' => '$data->value - floatval(@$data->mdata["fuel_charge"])'),
       array('name'=>'my_value'),
       array('name'=>'my_charge'),
       // array('name'=>'my_value_m','header'=>'My Value Manifest'),
       array('name'=>'chargeInvoiceDiff','filter'=>false,'value'=>'$data->getCostDiff()'),
       'subAmount',
    );
  } else {
     $columns = array(
  //  'shipment_no',
       array('name' => 'shipment_no', 'type' => 'raw', 'value' => '!empty($data->shipment_id) ? "<a href=\"".Yii::app()->createURL("imParcel/updateByNo", array("no" => $data->shipment_no))."\" class=\"tab_link\" title=\"".$data->shipment_no."\">".$data->shipment_no."</a>" : $data->shipment_no'),
       // array( 'name' => 'consol_id','type' => 'raw','value' => '"<a href=\"".Yii::app()->createURL("imcoConsol/updateByNo", array("no" => !empty($data->consol) ? $data->consol->no : ""))."\" class=\"tab_link\" title=\"".!empty($data->consol) ? $data->consol->no : ""."\">".!empty($data->consol) ? $data->consol->no : ""."</a>"'),
       array('name'=>'consol_no','type'=>'raw','value' =>'empty($data->getConsol())? "" : "<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" => $data->consol_id))."\" class=\"tab_link\" title=\"".@$data->consol->no."\">".@$data->consol->no."</a>"',),
       array( 'name' => 'invoice_no','value' => '$data->getInvoiceNoColumnData()'),
       'postcode',
       'weight',
       'courier_cubic',
       array('header'=>'250To167 Weight','value'=>'$data->getCourierToOurWeight()','cssClassExpression' => '$data->getCourierToOurWeight()>$data->ourChargeWeight()? "cloumn_red_rec" : ""'),
       array('header'=>'Our Charge Weight','value'=>'$data->ourChargeWeight()'),
       'manifest_weight',
       'value',
       array('name'=>'my_value', 'value' => '$data->getMyValue()'),
       array('name'=>'my_charge'),
       // array('name'=>'my_value_m','header'=>'My Value Manifest'),
       array('name'=>'chargeInvoiceDiff','filter'=>false,'value'=>'$data->getCostDiff()'),
       'subAmount',
       array('name' => 'tnt_type', 'visible' => $model->parent->client_type == Reconciliation::TNT_CLIENT, 'filter' => CHtml::dropDownList('ReconciliationLine[tnt_type]', $model->tnt_type, ['Shipment' => 'Shipment', 'Fuel' => 'Fuel', 'LR' => 'LR', 'MC' => 'MC', 'NEC' => 'NEC', 'OS0' => 'OS0', 'OS1' => 'OS1', 'OS2' => 'OS2', 'OS3' => 'OS3', 'OS4' => 'OS4', 'OS5' => 'OS5', 'PR' => 'PR', 'RES' => 'RES', 'RET' => 'RET', 'RMP' => 'RMP', 'RSP ' => 'RSP ', 'RMD' => 'RMD', 'RSD' => 'RSD', 'RED' => 'RED', 'MHP' => 'MHP'], ['prompt' => 'All']), 'value' => '@$data->mdata["tnt_type"]'),
    );
  }

  $ec = !empty($ec) ? $ec : new CDbCriteria;
  $this->widget('zii.widgets.grid.CGridView', array(
	'id'=> $_GET['tabid']. '-reconciliation-details-grid',
	'cssFile' => false,
	'dataProvider'=>$model->parent->client_type != Reconciliation::AUPOST_INVOICE ? $model->search(false, $ec) : $model->search(false, $ec, true, 30),
	'filter'=>$model,
	'columns'=>$columns,
)); ?>

<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	tab.on('onOpen', function(){
		$('#<?=$_GET['tabid'];?>-reconciliation-details-grid', panel).yiiGridView('update');
	});

  $('.ajax_link', panel).on('success', function(e, r) {
    if (r.done) {
      myApp.notice(r.msg, 5000);
    } else {
      myApp.alert(r.msg, false);
    }
  });
});
</script>
