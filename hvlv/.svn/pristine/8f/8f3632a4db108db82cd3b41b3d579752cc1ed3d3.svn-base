<h2>Pickup: <?=$model->ref;?></h2>
<?php
$parcel = new ExParcel('search');
if(isset($_GET['ExParcel'])){
	$parcel->unsetAttributes();
	$parcel->attributes=$_GET['ExParcel'];
}
$mids = $model->getFids();
$parcel->mids = empty($mids)? [-1] : $mids;
$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'fixedHeader' => true,
	'headerOffset' => 40,
	'type' => 'striped',
	'dataProvider' => $parcel->search(true, 20),
	'responsiveTable' => true,
	'template' => "{summary}\n{items}\n{pager}",
	'filter'=>$parcel,
	'selectableRows' => 2,
	'enableSorting' => false,
	'columns' => array(
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("pos/shipment/update", array("id" => $data->id))."\" class=\"ajax-link\">".$data->hbn."</a>"'),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList(get_class($parcel).'[status]', $parcel->status, $this->t($parcel->statusList()), array('prompt' => $this->t('All'), 'class' => 'form-control')),),
		array('name' => 'weight'),
		array('name' => 'cnor_name', 'value' => 'empty($data->cnor)? "" : $data->cnor->name'),
		array('name' => 'cnor_tel', 'value' => 'empty($data->cnor)? "" : $data->cnor->tel'),
		array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name'),
		array('name' => 'cnee_tel', 'value' => 'empty($data->cnee)? "" : $data->cnee->tel'),
		'state',
	),
)
);
