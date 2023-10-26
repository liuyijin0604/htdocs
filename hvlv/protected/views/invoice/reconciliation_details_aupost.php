<div style="right: 20px;position: absolute;">
    <a href="#" data-dropdown="#<?=$_GET['tabid'];?>_rec_dropdown" ><div style="background-position:-48px -688px" class="icon"></div>Export</a>
    <div id="<?=$_GET['tabid'];?>_rec_dropdown" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
        <ul class="dropdown-menu">
            <li><a href="<?=$this->createUrl('invoice/reconciliationExport2',['manifest_no' => $manifest_no]);?>" target="_blank" title="Export Reconciliation">Export Detail</a></li>
       </ul>
    </div>
</div>
<h1><?=$this->t('Details'."-".$manifest_no);?></h1>
<style>
    .cloumn_red_rec{
        background-color: red;
    }
</style>
<?php 
 $columns = [];
 if ($type == 10) {
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
       array('name'=>'my_value','header'=>'Our Cost'),
       array('name'=>'my_charge','header'=>'Our Charge'),
       array('name'=>'my_value_m','header'=>'My Value Manifest'),
       array('name'=>'chargeInvoiceDiff','filter'=>false,'value'=>'$data->getCostDiff()'),
       'subAmount'
     );
  }
  else
  {
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
       array('name'=>'my_value','header'=>'Our Cost', 'value' => '$data->getMyValue()'),
       array('name'=>'my_charge','header'=>'Our Charge'),
       array('name'=>'my_value_m','header'=>'My Value Manifest'),
       array('name'=>'chargeInvoiceDiff','filter'=>false,'value'=>'$data->getCostDiff()'),
       'subAmount'
    );
  }

  $ec = !empty($ec) ? $ec : new CDbCriteria;
  $this->widget('zii.widgets.grid.CGridView', array(
	'id'=> $_GET['tabid']. '-reconciliation-details-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(false, $ec),
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
});
</script>
