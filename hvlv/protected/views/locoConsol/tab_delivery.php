<div style="text-align:left;position:absolute;"><a class="export" href="<?=$this->createUrl('locoConsol/ap_manifest', array('id'=>$model->id));?>" target="_blank"><div style="background-position:-48px -688px" class="icon"></div> <?=$this->t('Post Manifest');?></a> &nbsp; <a class="export" href="<?=$this->createUrl('locoConsol/cp_manifest', array('id'=>$model->id));?>" target="_blank"><div style="background-position:-48px -688px" class="icon"></div> <?=$this->t('CP Manifest');?></a></div>

<?php
if(!empty($_GET['zr_id'])){
	foreach($_GET['zr_id'] as $k=>$v){
		$p = CoParcel::model()->findByPk($k);
		$p->zr_id = $v;
		$p->save();
	}
}
$cols = array(
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("coParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
		array('name' => 'cnee_id', 'value' => '$data->cnee->name',),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('CoParcel[status]', empty($_GET['CoParcel']['status'])? '' : $_GET['CoParcel']['status'], $this->t(CoParcel::$states), array('prompt'=>$this->t('All'))),),
		'postcode',
		'weight',
		'value',
		'pkg',
		array('header' => 'Rate', 'type' => 'raw', 'value' => '$data->rateSelection();',),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',//{view}
			'buttons'=>array(
				'view' => array(
					'imageUrl'=>false,
					'url' => 'Yii::app()->createUrl("coParcel/view", array("id" => "$data->id"))',
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'update' => array(
					'imageUrl'=>false,
					'url' => 'Yii::app()->createUrl("coParcel/update", array("id" => "$data->id"))',
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->hbn'),
				),
			),
		),
	);
$p = new CoParcel('search');
$p->unsetAttributes();
if(!empty($_GET['CoParcel'])){
	$p->setAttributes($_GET['CoParcel']);
}
$p->consol_id = $model->id;

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'delivery-grid',
	'cssFile' => false,
	'dataProvider'=>$p->search(),
	'filter'=>$p,
	'columns'=>$cols,
));
?>
<h3>Summary</h3>
<div id="delivery_summary">
<table class="chart">
<thead><tr><th>Courier</th><th>Parcels</th><th>Amount</th></tr></thead>
<tbody>
<?php
$rs = CoParcel::model()->findAll('consol_id = '.$model->id);
$ds = array();
$err = array();
foreach($rs as $r){
	if(empty($r->zr_id)){
		$err[] = $r->hbn.' no rate can be found!';
		continue;
	}
	$orid = $r->zrate->rate_id;
	if(!isset($ds[$orid])) $ds[$orid] = array(0,0);
	$ds[$orid][0]++;
	$ds[$orid][1] += $r->getRate();
}
$t = 0;
foreach($ds as $r => $s){
	$or = OrgRate::model()->findByPk($r);
	echo '<tr><td>'.$or->name.'</td><td>'.$s[0].'</td><td>$'.$s[1].'</td></tr>';
	$t+=$s[1];
}
?>
</tbody>
<tfoot><tr><th colspan="2" align="right">Total:</th><th>$<?=$t;?></th></tr></tfoot>
</table>
<?php
if(!empty($err)){
	echo '<p class="red">Error:<br />';
	echo implode('<br />', $err);
	echo '</p><br />';
}
?>
</div>
<script type="text/javascript">
$(function(){
	var panel = $('#<?=$_GET["tabid"];?>').data('panel');
	$(panel).off('change', 'select.rate_sel').on('change', 'select.rate_sel', function(){
		$.get('locoConsol/update/<?=$model->id;?>.app?tab=delivery&tabid=<?=$_GET["tabid"];?>&'+$(this).attr('name')+'='+$(this).val(), function(r){
			$('#delivery_summary', panel).html($(r).find('#delivery_summary').html());
		});
	});
});
</script>