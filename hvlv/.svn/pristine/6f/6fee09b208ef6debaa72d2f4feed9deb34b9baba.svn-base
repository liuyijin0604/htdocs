<div style="right: 20px;position: absolute;">
    <a href="#" data-dropdown="#<?=$_GET['tabid'];?>_rec_dropdown" ><div style="background-position:-48px -688px" class="icon"></div>Export</a>
<!--     <?php echo CHtml::button('Generate Weight Diff Invoice',array('id'=>'weight_diff_invoice'));?> -->

   <a href="<?=Yii::app()->createURL("importWeightCheck/viewWeightDiffReport", array("parent_id" => $model->parent_id))?>" class="tab_link" title="<?=$model->parent_id?>">viewWeightDiffReport</a>
    <div id="<?=$_GET['tabid'];?>_rec_dropdown" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
        <ul class="dropdown-menu">
            <li><a href="<?=$this->createUrl('importWeightCheck/weightCheckExport',['id' => $model->parent_id]);?>" target="_blank" title="Export Reconciliation">Export Detail</a></li>
       </ul>
    </div>
</div>
<h1><?=$this->t('Details'."-".Reconciliation::$types[$model->parent->client_type]."-".$model->parent->invoice_no."-".$model->parent->invoice_date);?></h1>
<style>
    .cloumn_red_rec{
        background-color: red;
    }
</style>
<?php

if($model->parent->isDeclare())
  {
    $columns = array(
  //  'shipment_no',

       array('name' => 'shipment_no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/updateByNo", array("no" => $data->shipment_no))."\" class=\"tab_link\" title=\"".$data->shipment_no."\">".$data->shipment_no."</a>"'),
       // array( 'name' => 'consol_id','type' => 'raw','value' => '"<a href=\"".Yii::app()->createURL("imcoConsol/updateByNo", array("no" => !empty($data->consol) ? $data->consol->no : ""))."\" class=\"tab_link\" title=\"".!empty($data->consol) ? $data->consol->no : ""."\">".!empty($data->consol) ? $data->consol->no : ""."</a>"'),
       array('name'=>'consol_no','type'=>'raw','value' =>'empty($data->getConsol())? "" : @$data->consol->type==Consol::DMAWBCONSOLTYPE?"<a href=\"".Yii::app()->createURL("dmawbConsol/update", array("id" => $data->consol_id))."\" class=\"tab_link\" title=\"".@$data->consol->no."\">".@$data->consol->no."</a>":"<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" => $data->consol_id))."\" class=\"tab_link\" title=\"".@$data->consol->no."\">".@$data->consol->no."</a>"',),
       array('name' => 'agentId', 'value' => '$data->getShipmentAgent()'),
       array('header'=>'weight','value' =>'$data->getCdeadwtOrWeight()'),
       array('header'=>'was weight','value' =>'$data->mdata["was_weight"]'),
       array('header'=>'declare weight','value' =>'$data->mdata["declaredWeight"]'),
        array('name'=>'manifest_weight'),
       'courier_cubic',
       array('name'=>'customerCbm','value'=>'$data->getCustomerCBM()','filter'=>false),
       array('name'=>'customerWeight','value'=>'$data->getCustomerWeight()','filter'=>false),
       array('name'=>'bulkyWeight','value'=>'$data->getBulkyWeight()','filter'=>false),
       array('name'=>'csChargeWeight','value'=>'$data->getCSChargeWeight()','filter'=>false),
       array('name'=>'chargeWeightDiff','value'=>'$data->getChargeWeightDiff()','filter'=>false,'cssClassExpression' => 'in_array($data->parent->getType(),Reconciliation::$cbmCourier)?$data->getChargeWeightDiff()>=10? "cloumn_red_rec" : "":$data->getChargeWeightDiff()>0? "cloumn_red_rec" : ""'),
       array('header'=>'viewInvoice','type'=>'raw','value'=>'!empty($data->getWeightDiffInvoice())?"<a href=\"".Yii::app()->createURL("invoice/print", array("id" => $data->getWeightDiffInvoice()->id))."\" target=\"_blank\">Invoice".$data->getWeightDiffInvoice()->no."</a>":""'),
       array(
          'class'=>'oButtonColumn',
          'template'=>'{Customer Invoice Diff}',
          'buttons'=>[
            'Customer Invoice Diff' => [
              'url'=>' Yii::app()->createURL("importWeightCheck/diffInvoiceCheck")."?id=".$data->id."&&parent_id=".$data->parent_id',
              'imageUrl'=>false,
              'visible'=>'$data->getChargeWeightDiff()>0?true:false',
              'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
            ],
          ]
        ));
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
  }else
  {
    $columns = array(
  //  'shipment_no',

       array('name' => 'shipment_no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/updateByNo", array("no" => $data->shipment_no))."\" class=\"tab_link\" title=\"".$data->shipment_no."\">".$data->shipment_no."</a>"'),
       // array( 'name' => 'consol_id','type' => 'raw','value' => '"<a href=\"".Yii::app()->createURL("imcoConsol/updateByNo", array("no" => !empty($data->consol) ? $data->consol->no : ""))."\" class=\"tab_link\" title=\"".!empty($data->consol) ? $data->consol->no : ""."\">".!empty($data->consol) ? $data->consol->no : ""."</a>"'),
       array('name'=>'consol_no','type'=>'raw','value' =>'empty($data->getConsol())? "" : @$data->consol->type==Consol::DMAWBCONSOLTYPE?"<a href=\"".Yii::app()->createURL("dmawbConsol/update", array("id" => $data->consol_id))."\" class=\"tab_link\" title=\"".@$data->consol->no."\">".@$data->consol->no."</a>":"<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" => $data->consol_id))."\" class=\"tab_link\" title=\"".@$data->consol->no."\">".@$data->consol->no."</a>"',),
       array('name' => 'agentId', 'value' => '$data->getShipmentAgent()'),
       array('header'=>'weight','value' =>'$data->getCdeadwtOrWeight()'),
        array('name'=>'manifest_weight'),
       'courier_cubic',
       array('name'=>'customerCbm','value'=>'$data->getCustomerCBM()','filter'=>false),
       array('name'=>'customerWeight','value'=>'$data->getCustomerWeight()','filter'=>false),
       array('name'=>'bulkyWeight','value'=>'$data->getBulkyWeight()','filter'=>false),
       array('name'=>'csChargeWeight','value'=>'$data->getCSChargeWeight()','filter'=>false),
       array('name'=>'chargeWeightDiff','value'=>'$data->getChargeWeightDiff()','filter'=>false,'cssClassExpression' => 'in_array($data->parent->getType(),Reconciliation::$cbmCourier)?$data->getChargeWeightDiff()>=10? "cloumn_red_rec" : "":$data->getChargeWeightDiff()>0? "cloumn_red_rec" : ""'),
       array('header'=>'viewInvoice','type'=>'raw','value'=>'!empty($data->getWeightDiffInvoice())?"<a href=\"".Yii::app()->createURL("invoice/print", array("id" => $data->getWeightDiffInvoice()->id))."\" target=\"_blank\">Invoice".$data->getWeightDiffInvoice()->no."</a>":""'),
       array(
          'class'=>'oButtonColumn',
          'template'=>'{Customer Invoice Diff}',
          'buttons'=>[
            'Customer Invoice Diff' => [
              'url'=>' Yii::app()->createURL("importWeightCheck/diffInvoiceCheck")."?id=".$data->id."&&parent_id=".$data->parent_id',
              'imageUrl'=>false,
              'visible'=>'$data->getChargeWeightDiff()>0?true:false',
              'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
            ],
          ]
        ));
  }

?>

<?php
$ec = new CDbCriteria;
$ec->addCondition('JSON_VALUE(t.meta, "$.tnt_type") IS NULL OR JSON_VALUE(t.meta, "$.tnt_type") = "Shipment"');
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=> $_GET['tabid']. '-reconciliation-details-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true, $ec),
	'filter'=>$model,
	'columns'=>$columns,
)); ?>

<script type="text/javascript">
  var tab = $("#<?=$_GET['tabid'];?>");
  var panel = tab.data('panel');
$(function(){

	tab.on('onOpen', function(){
		$('#<?=$_GET['tabid'];?>-reconciliation-details-grid', panel).yiiGridView('update');
	});
});

  // $('#weight_diff_invoice',panel).on('click',function()
  // {
  //   if( confirm('Are you sure to Generate Weight Diff Invoice?'))
  //   {
  //       $.get('<?=$this->createUrl("invoice/generateAllReWDInv")."?parentId=".$model->parent_id?>',function(r){
  //         r = JSON.parse(r);
  //           if(r.done==true){
  //                myApp.alert("success");  
  //            }else{
  //               myApp.alert(r, false);   
  //          }
  //      });
    
  //   }
  // });
</script>
