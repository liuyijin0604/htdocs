<h1><?=$this->t('Chinese ID');?> <?php echo $model->no; ?></h1>
<div style="min-height: 300px">
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'name',
		'city',
		'mobile',
		array('name' => 'Connote #', 'value' => empty($model->mdata["connote"])? "" : $model->mdata["connote"]),
		'created',
		array('name' => 'joint', 'type' => 'raw', 'value' => $model->photo_joint? '<img src="'. $model->photo_joint->getUrl().'" width="400" />' : '' ),
	),
));
?>
</div>