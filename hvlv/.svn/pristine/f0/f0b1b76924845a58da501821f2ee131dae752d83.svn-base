<div style="text-align: right; padding-right: 20px;">
<a class="jqm_link" href="<?=$this->createUrl('wmsProd/org', ['pid' => $model->id]);?>"><div class="icon" style="background-position:-16px 0"></div> New Customer Options</a>
</div>
<?php
$wpo = new WmsProdOrg('search');
$wpo->unsetAttributes();
$wpo->prod_id = $model->id;

$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>$_GET["tabid"].'_org-grid',
		'cssFile' => false,
		'summaryText'=>'',
		'dataProvider'=> $wpo->search(),
		'columns'=>array(
			array(
	            'name'=>'cust_name',
	            'value'=>'$data->customer->name',
	        ),
	        'sku',
			array(
				'class'=>'oButtonColumn',
				'template'=>'{update}',
				'buttons'=>array
				(
					'update' => array(
						'imageUrl'=>false,
						'url' => 'Yii::app()->createUrl("wmsProd/org", ["pid" => $data->prod_id, "oid" => $data->org_id])',
						'visible'=>'true',
						'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update')),
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