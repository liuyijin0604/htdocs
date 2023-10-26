<div style="margin-top: 8px">
	<div id="consol-manifest-form">
		<button type="submit" class="btn btn-primary" id="manifest_shipment" > Manifest Country Rate</button>
		<button type="submit" class="btn btn-primary" id="manifest_shipment_etower" > Manifest eTower</button>
	<!--<button class="btn btn-primary manifest_shipment">Manifest</button>-->
	</div>
</div>
<?php
$p = new ImParcel('search');
if (!empty($_GET['ImParcel'])) {
	$p->setAttributes($_GET['ImParcel']);
}
$sc1 = new CDbCriteria;
$sc1->addInCondition('id', $model->getFids());
//$p->mani_id = $model->id;

$this->widget('application.extensions.booster.TbExtendedGridView', [
	'fixedHeader'=>true,
	'id'=>'task_grid_view',
	'filter'=>$p,
	'type'=>'striped bordered',
	'headerOffset'=>40,
	'responsiveTable'=>true,
	'dataProvider'=>$p->search(true, 30, $sc1),
	'template' => "{summary}\n{items}\n{pager}",
	'columns'=>[
		['name' => 'hbn', 'type' => 'raw', 'value' => '"<a target=\"_blank\" href=\"".Yii::app()->createURL("ims/shipment/update", array("id" => $data->id,"man_id"=>"'.$model->id.'" ))."\"  >".$data->hbn."</a>"'],
		'ref',
		['header'=>'Agent','name'=>'agent_name','value'=>'$data->agent->name'],
		['name' => 'status', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('ImParcel[status]', $p->status, $this->t(ImParcel::$states), ['prompt'=>$this->t('All'),'class'=>'form-control']),],
		['name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',],
		'postcode',
		'weight',
		'pkg',
	],
 ]);
?>
<?php ob_start(); ?>
<script type="text/javascript">
	$(function(){
		var consol_id=<?=$model->id?>;
	   $("#manifest_shipment").on('click',function(){
		   if(confirm('Are you sure to Manifest?')){
				  posApp.btnLoading($(this)); 
				$.get('<?=Yii::app()->createURL("manifest", ["D2zCountryManifest" => $model->id])?>',function(r){
					if(r=='done'){
					 $('#notifc').notify({message: {html: r}}).show();
					}else{
						$('#notifc').notify({message: {html: r}, type: 'danger'}).show();
					}
					 posApp.btnLoading($("#manifest_shipment"),true); 
				});
		   }
	});
		 $("#manifest_shipment_etower").on('click',function(){
		   if(confirm('Are you sure to Manifest?')){
				posApp.btnLoading($(this)); 
				$.get('<?=Yii::app()->createURL("manifest", ["EtowerManifest" => $model->id])?>',function(r){
					if(r=='done'){
					   $('#notifc').notify({message: {html: r}}).show();
					}else{
						$('#notifc').notify({message: {html: r}, type: 'danger'}).show();
					}
					 posApp.btnLoading($('#manifest_shipment_etower'),true); 
				});
		   }
	});
	});
</script>
<?php $this->registerJS(ob_get_clean()); ?>