<div style="text-align: right; padding-right: 20px;">
<a class="jqm_link" href="<?=$this->createUrl('wmsProd/kit', ['kid' => $model->id]);?>"><div class="icon" style="background-position:-16px 0"></div> New Kit Options</a>
</div>
<?php
$wpo = new WmsProdKit('search');
$wpo->unsetAttributes();
$wpo->kit_id = $model->id;

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => $_GET["tabid"] . '_org-grid',
	'cssFile' => false,
	'summaryText' => '',
	'dataProvider' => $wpo->search(),
	'columns' => array(
		array(
			'name' => 'item.name',
		),
		array(
			'name' => 'item.ean'
		),
		array(
			'header' => 'SKU',
			'value' => '!empty($data->item->orgs) ? $data->item->orgs[0]->sku : ""',
		),
		'qty',
		array(
			'class' => 'oButtonColumn',
			'template' => '{update}',
			'buttons' => array
			(
				'update' => array(
					'imageUrl' => false,
					'url' => 'Yii::app()->createUrl("wmsProd/kit", ["id" => $data->id])',
					'visible' => 'true',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Update')),
				),
			),
		),
	),
));
?>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	tab.bind('onOpen', function(){
		$('#<?=$_GET["tabid"]?>_org-grid', panel).yiiGridView('update');
	});
});
</script>