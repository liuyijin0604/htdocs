<?php 
  $filtersForm=new FiltersForm;
  if (isset($_GET['FiltersForm'])) $filtersForm->filters=$_GET['FiltersForm'];
  $provide=[];
foreach ($model->shipments as $index=> $shipment){
    $provide[]=['id'=>$index,'shipment_id'=>$shipment->id,'buy_insurance'=>($shipment->hasInsurance()?"买了保险":"没买保险"),'type'=>$shipment->type,'hbn'=>$shipment->hbn,'ref'=>$shipment->ref,'agent'=>isset($shipment->agent->name)?$shipment->agent->name:'','status'=>$shipment->getStatus(),'cnee_name'=>$shipment->cnee->name];
}
 $filteredData=$filtersForm->filter($provide);
 $dataprovider=new CArrayDataProvider($filteredData); 
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'ex-parcel-grid'.$_GET['tabid'],
	'cssFile' => false,
	'dataProvider'=>$dataprovider,
        'filter'=>$filtersForm,
	'columns'=>array(
                array('name'=>'hbn','header'=>'Connote', 'type'=>'raw','value'=>'"<a href=\"".Yii::app()->createURL($data["type"]==10?"imParcel/update":"exParcel/update",["id"=>$data["shipment_id"]])."\" class=\"tab_link\" title=\"".$data["hbn"]."\" >".$data["hbn"]."</a>"'),
		'ref',
                array('header'=>'Buy Insurance', 'name'=>'buy_insurance'),
                'agent',
                'status',
                'cnee_name',
               
              

	),
)); ?>

