<?php 
$p = new ImParcel('search');
if (!empty($_GET['ImParcel'])) {
	$p->setAttributes($_GET['ImParcel']);
}
$p->consol_id = $model->id;

$this->widget('zii.widgets.grid.CGridView', [
	'id'=>'acr-grid_'.$_GET["tabid"],
	'cssFile' => false,
	'dataProvider'=>$p->search(),
	'filter'=>$p,
	'columns'=>[
		['name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',],
		'ref',
		'can',
		['header'=>'Agent','name'=>'agent_name','value'=>'$data->agent->name'],
		['name' => 'status', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('ImParcel[status]', $p->status, $this->t(ImParcel::$states), ['prompt'=>$this->t('All')]),],
		['name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',],
		'postcode',
		['name' => 'cnor_tel', 'value' => '@$data->cnor->tel',],
		'pkg',
		'weight',
		'dvalue',
		['header'=>'ChargeCode','value'=>'$data->getChargecode()'],
		['header'=>'extra Info','type'=>'raw','value'=>'$data->getWarnings().$data->getDGWarnings()','filter'=>CHtml::dropDownList('ImParcel[bwf]', $p->bwf, $this->t(ImParcel::$bwfs), ['prompt'=>'All']).CHtml::dropDownList('ImParcel[exInfo]', $p->exInfo, $this->t(ImParcel::$exInfos), ['prompt'=>'All'])],
		[
			'class'=>'oButtonColumn',
			'template'=>'{update}',//{view}
			'buttons'=>[
				'view' => [
					'imageUrl' => false,
					'url' => 'Yii::app()->createURL("imParcel/view", array("id" => $data->id))',
					'options' => ['class' => 'jqm_link grid_view_btn'],
				],
				'update' => [
					'imageUrl' => false,
					'url' => 'Yii::app()->createURL("imParcel/update", array("id" => $data->id))',
					'visible' => 'true',
					'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->hbn'],
				],
			],
		],
	],
]);
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('input.send_btn', panel).on('click', function(){
		var isFCL = <?=(!empty($model->mdata['cargo_type'])&&$model->mdata['cargo_type']=='FCL'?'true':'false')?>;
		if(isFCL&&!confirm("cargo type FCL, pls confirm before sending SCR!!!"))
		{
			return false;
		}

        var me = $(this);
        me.prop('disabled', true);
		$.get('<?=$this->createURL("imcoConsol/acr", ["id" => $model->id]);?>', function(r){
			if(r.done){
				myApp.notice(r.msg);
			}else{
				myApp.alert(r.msg);
			}
			me.prop('disabled', false);
		}, 'json');
	});

});
</script>