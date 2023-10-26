<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink' => CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
		'Return List',
	),
));
?>

<h2>Return List</h2>
<div class="row">
	<div class="col-xs-12">
		<div class="dropdown" style="display: inline-block; margin-right: 1em">
			<a class="btn btn-default ajax-link" href="<?=$this->createUrl('return/create', ['jid' => $job->id])?>">New Return</a>
			</ul>
		</div>
	</div>
</div>

<br>
<?php
$model = new WmsTask('search');
$model->unsetAttributes();
if (!empty($_GET['WmsTask'])) {
	$model->setAttributes($_GET['WmsTask']);
}

$ec = new CDbCriteria;
$ec->addCondition('t.type = 7010 OR t.bwf & 128 > 0');
if (Yii::app()->user->org != 1) {
	$ec->with = ['job'];
	$ec->addCondition('job.org_id = ' . Yii::app()->user->org);
}

$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'fixedHeader' => true,
	'id' => 'return_grid_view',
	'filter' => $model,
	'type' => 'striped bordered',
	'headerOffset' => 40,
	'responsiveTable' => true,
	'dataProvider' => $model->search(true, 30, $ec),
	'template' => "{summary}\n{items}\n{pager}",
	'columns' => array(
		array('name' => 'id', 'type' => 'raw', 'value' => '$data->getReturnLink()'),
		array('name' => 'redelivery', 'header' => 'Redelivery', 'type' => 'raw', 'value' => '$data->getRedeliveryLink()'),
		array('name' => 'return_type', 'header' => 'Type', 'value' => '$data->getReturnType()', 'filter' => CHtml::dropDownList('WmsTask[return_type]', $model->return_type, $this->t(WmsTask::$types_return), array('prompt' => $this->t('All'), 'class' => 'form-control'))),
		array('name' => 'cust_name', 'value' => '$data->job->customer->name', 'visible' => sizeof(User::getOrgIds()) > 1 ? true : false),
		array('name' => 'ref', 'header' => 'Ref #'),
		array('name' => 'cnee_name', 'header' => 'Sender Name', 'value' => '$data->getCneeName()'),
		array('name' => 'cnee_full_address', 'header' => 'Sender Address', 'value' => '$data->getCneeFullAddress()'),
		array('name' => 'return_status', 'header' => 'Status', 'value' => '$data->getReturnStatus()', 'filter' => CHtml::dropDownList('WmsTask[return_status]', $model->return_status, $this->t(WmsTask::$states_return), array('prompt' => $this->t('All'), 'class' => 'form-control'))),
		array(
			'class' => 'application.extensions.booster.TbButtonColumn',
			'template' => '{Label1}{Label2}',
			'header' => 'Return Label',
			'buttons' => array(
				// 'Update' => array(
				// 	'visible' => 'true',
				// 	'icon' => 'glyphicon glyphicon-edit',
				// 	'url' => 'Yii::app()->createUrl("pcaw/return/update", array("id" => $data->id))',
				// 	'options' => array('class' => 'ajax-link', 'title' => 'Update'),
				// ),
				'Label1' => array(
					'visible' => 'true',
					// 'icon' => 'glyphicon glyphicon-download',
					'visible' => '$data->type == WmsTask::TYPE_RETURN && empty($data->deliveryTask->mdata["return_shipment_id"])',
					'url' => 'Yii::app()->createUrl("pcaw/return/label", array("id" => $data->id))',
					'options' => array('title' => 'Download', 'target' => '_blank', 'class' => 'label_btn'),
					'label' => 'Download <i class="glyphicon glyphicon-download"></i>',
				),
				'Label2' => array(
					'visible' => 'true',
					// 'icon' => 'glyphicon glyphicon-download',
					'visible' => '$data->type == WmsTask::TYPE_RETURN && !empty($data->deliveryTask->mdata["return_shipment_id"])',
					'url' => 'Yii::app()->createUrl("pcaw/return/label", array("id" => $data->id))',
					'options' => array('title' => 'Download', 'target' => '_blank'),
					'label' => 'Download <i class="glyphicon glyphicon-download"></i>',
				),
			),
		),
	),
));
?>

<script>
$(function() {
	$('#return_grid_view').off('click', '.label_btn').on('click', '.label_btn', function() {
		if (!confirm('Once submit, the address and weight will not be able to be modified. Are you sure to create label(s)?')) {
			return false;
		}
	});
});
</script>