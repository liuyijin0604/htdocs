<h2>Pickup: <?=$model->ref;?></h2>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	jQuery(document).off('click','#pl-<?=$model->id;?>-grid a.delete');
});
</script>
<?php
$this->registerJS(ob_get_clean());
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
	'id' => 'pl-'.$model->id.'-grid',
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
		array(
			'class'=>'application.extensions.booster.TbButtonColumn',
			'template'=>'{delete}',
			'header' => 'Actions',
			'buttons'=>array(
				'delete' => array(
					'visible'=>'true',
					'url' => 'Yii::app()->createUrl("pos/reports/delPickup",["id" => $data->id, "mid" => '.$model->id.'])',
					'visible' => (empty($model->mdata['cc_status']) || $model->mdata['cc_status'] > 10)? 'false' : '$data->status < 12 && !empty($data->agent->extra["pos_pu"])',
				),
			),
		),
	),
)
);
?>
