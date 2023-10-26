<?php
$cdp = new CArrayDataProvider($rs, array(
	'sort'=>array(
		'attributes'=>array(
			'id', 'hbn', 'state', 'weight', 'dvalue',
		),
	),
	'pagination' => false
));

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'ex-parcel-grid',
	'cssFile' => false,
	'dataProvider' => $cdp,
	'filter'=> null,
	'columns'=>array(
		'hbn',
		array('name' => 'agent_name', 'value' => 'empty($data->agent)? "" : $data->agent->shortName(2)',),
		array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
		array('name' => 'cnor_name', 'value' => 'empty($data->cnor)? "" : $data->cnor->name',),
		'state',
		'weight',
		'dvalue',
		array('header' => 'Action', 'type' => 'raw', 'value' => '"<label><input type=\"radio\" name=\"pm[".$data->id."]\" value=\"20\" checked /> No Action</label><label><input type=\"radio\" name=\"pm[".$data->id."]\" value=\"18\" /> Re-Queue</label> <label><input type=\"radio\" name=\"pm[".$data->id."]\" value=\"101\" /> Problem</label> <label><input type=\"radio\" name=\"pm[".$data->id."]\" value=\"102\" /> RTS</label>"'),
	),
));
?>