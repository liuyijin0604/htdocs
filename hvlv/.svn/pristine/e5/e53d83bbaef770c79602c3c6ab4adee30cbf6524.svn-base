<?php 
$criteria=new CDbCriteria;
//$criteria->addCondition('t.bwf&4>0 OR t.cbwf&128>0');
?>
<br/>
	<h3><?=$name?></h3>
	<style>
		.cloumn_red{
				background-color:red;
		}   
</style>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'_sea_grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true,30,$criteria),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
		'ref',
		'can',
		array('name'=>'awb_name','header'=>'awb','type'=>'raw','value'=>'empty($data->consol)?"":$data->consol->awb',),
		array('name'=>'consol_eta','header'=>'eta','type'=>'raw','value'=>'@$data->consol->eta','htmlOptions'=>array('width'=>'80px'),'cssClassExpression' => 'isset($data->consol)?(@$data->consol->getEtaToNow()>5? "cloumn_red" : ""):""'),
		array('name'=>'pkg','htmlOptions'=>array('width'=>'10px'),'filterHtmlOptions'=>array('style'=>'width:10px')),
		array('name' => 'consol_no', 'type'=>'raw', 'value' => 'empty($data->consol_id)? "" : "<a href=\"".Yii::app()->createURL(preg_match("/DW\d{8}/i",@$data->consol->no)?"dmawbConsol/update":"imcoConsol/update", array("id" => $data->consol_id))."\" class=\"tab_link\" title=\"".$data->consol->no."\">".$data->consol->no."</a>"',),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('ImParcel[status]', $model->status, $this->t(ImParcel::$states), array('prompt'=>$this->t('All'))),),
		array('name'=>'sea_process_status','header'=>'Process Status','value'=>'$data->getSeaProcessStatus()','filter'=>CHtml::dropDownList('ImParcel[sea_process_status]', $model->sea_process_status, $this->t(SeaProcess::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
		array('name'=>'postcode','htmlOptions'=>array('width'=>'10px'),'filterHtmlOptions'=>array('style'=>'width:10px')),
		array('name' => 'cnee_addr','header'=>'Address' ,'value' => 'empty($data->cnee)?"":substr($data->cnee->address,0,20)'),
		array('name' => 'cnee_company','header'=>'Company', 'value' => 'empty($data->cnee)?"":$data->cnee->company'),
		array('name' => 'cnee_tel','header'=>'Tel', 'value' => 'empty($data->cnee)?"":$data->cnee->tel'),
		array('name' => 'cnee_email','header'=>'Email', 'type' => 'raw',   'value'=>function($data){return CHtml::tag('div', array('title'=>@$data->cnee->email,),substr(@$data->cnee->email,0,25));},),
		array('header'=>'value','value'=>'$data->dvalue'),
		array('header'=>'extra Info','type'=>'raw','value'=>'$data->getWarnings()','filter'=>CHtml::dropDownList('ImParcel[bwf]',$model->bwf,$this->t(ImParcel::$bwfs),array('prompt'=>'All'))),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}{log}',
			'buttons'=>array
			(
				'update' => array(
					'label'=>'Operation',
					'url'=>'Yii::app()->createURL("seaProcess/operation",array("id"=>$data->id))',
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->hbn'),
				),
				'log' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'),
					'visible' => 'true',
						'url' => 'Yii::app()->createUrl("seaProcess/log", ["id" => $data->id])',
					'label' => 'Log'
							),
			),
		),
	),
)); ?>
