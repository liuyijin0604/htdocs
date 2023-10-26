<h3><?=$this->t('Pre Alert Summary')?></h3>
<style>
    .cloumn_red{
        background-color:pink;
    }  
    .column_direct{
        color: green;
        font-weight: bold;
    }
</style>

</div>

<?php $this->widget('zii.widgets.grid.CGridView', [
	'id'=>$_GET["tabid"].'pre-alert-im-cargoprocess-parcel-grid',
	'cssFile' => false,
	'dataProvider'=>$preAlert->search(true, 30, false, false),
	'filter'=>$preAlert,
	'columns'=>[
		['name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',],
		'ref',
		'can',
		'agent_id',
		['name'=>'awb_name','header'=>'awb','type'=>'raw','value'=>'empty($data->consol)?"":$data->consol->awb',],
		//        array('header' => 'Awb', 'type' => 'raw', 'value' => 'empty($data->consol)? "" : $data->consol->awb',),
		'pkg',
		['name' => 'consol_no', 'type'=>'raw', 'value' => 'empty($data->consol_id)? "" : "<a href=\"".Yii::app()->createURL(Consol::getTheConsolType($data->consol_id)==70?"dmawbConsol/update":"imcoConsol/update", array("id" => $data->consol_id))."\" class=\"tab_link\" title=\"".@$data->consol->no."\">".@$data->consol->no."</a>"',],
		['name' => 'status', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('ImParcel[status]', $preAlert->status, $this->t(ImParcel::$states), ['prompt'=>$this->t('All')]),],
		['name' => 'ddpt_id', 'value' => '@$data->ddepot->name',
			'filter'=>CHtml::dropDownList('ImParcel[ddpt_id]', $preAlert->ddpt_id, $this->t(Org::dptList()), ['prompt'=>$this->t('All')]),],
		['name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',],
		'postcode',
		'weight',
		// ['header' => 'Location', 'type' => 'raw', 'value' => '$data->getLocation()',],
		// ['header' => 'Clear Log', 'type' => 'raw',   'value'=>function ($data) {
		// 	return CHtml::tag('div', ['title'=>@$data->mdata['clear_log'],], $data->getClearLog());
		// },],
		// ['header' => 'Tranship', 'value' => '$data->getTranships()'],
		['name'=>'scan_no','header'=>'unScan', 'value' => '$data->pkg - $data->getOutPkg()'],
		['name' => 'rack', 'value' => '$data->getRackName(true)'],
		['name' => 'created', 'value' => 'substr($data->created,0,10)'],
		['header'=>'ChargeCode','value'=>'$data->getChargecode()'],
		// ['name' => 'bwf', 'header' => 'Warnings', 'type' => 'raw', 'value' => '$data->getWarnings()','filter'=>CHtml::dropDownList('ImParcel[bwf]', $model->bwf, $this->t(ImParcel::$bwfs), ['prompt'=>$this->t('All')]),],
		['header'=>'value','value'=>'$data->dvalue'],
		['header'=>'extra Info','type'=>'raw','value'=>'$data->getWarnings()','filter'=>CHtml::dropDownList('ImParcel[bwf]', $preAlert->bwf, $this->t(ImParcel::$bwfs), ['prompt'=>'All'])]
	],
]); ?>


<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

});
</script>
