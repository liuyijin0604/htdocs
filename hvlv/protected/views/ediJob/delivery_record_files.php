<?php
$fr = new FileRepo('search');
$fr->unsetAttributes();
if (empty($_GET['FileRepo'])) {
	$fr->status = 20;
} else {
	$fr->attributes = $_GET['FileRepo'];
}
$fr->type = 93;
$fr->fid = $model->id;

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => $_GET['tabid'] . '_wtfile-grid',
	'cssFile' => false,
	'summaryText' => '',
	'dataProvider' => $fr->search(),
	'filter' => $fr,
	'columns' => array(
		array('name' => 'name', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->baseUrl."/filerepo/".$data->hash."/".$data->name."\" target=\"_blank\">".$data->name."</a>"'),
		array(
			'name' => 'size',
			'value' => '$data->formatSize()',
			'filter' => false,
		),
		'date',
		array(
			'header' => 'note',
			'value' => '!empty($data->mdata["note"])?$data->mdata["note"]:""',
		),
	),
));
?>