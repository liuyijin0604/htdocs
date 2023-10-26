<?php
$dp = new WmsProdPack('search');
$dp->prod_id = $model->id;

$this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id'=>$_GET["tabid"].'_prod-pack-grid',
	'cssFile' => false,
	'dataProvider'=>$dp->search(),
	'formUrl' => $this->createUrl('wmsProd/packageGrid', array('id'=>$model->id)),
	'filter'=>null,
	'summaryText' => '',
	'afterSave' => "function(r){
		if(r.done == true){
			myApp.notice(r.msg, 5000);
		}else{
			myApp.alert(r.msg, false);
		}
		return r.done;
	}",
	'columns'=>array(
		array('name' => 'type', 'type' => 'list', 'filter' => WmsProdPack::$types, 'value' => '$data->getType()', 'class' => 'CEditableColumn'),
		array('name' => 'qty', 'class' => 'CEditableColumn'),
		array('name' => 'barcode', 'class' => 'CEditableColumn'),
		array('header' => 'Weight (Kg)', 'name' => 'weight', 'class' => 'CEditableColumn'),
		array('header' => 'Dim (cm) ', 'name' => 'dim', 'type' => 'raw', 'value' => '"W<input type=\"text\" name=\"dim[w]\" size=\"5\" value=\"".(empty($data->dims["w"])? "": $data->dims["w"])."\" /> H<input type=\"text\" name=\"dim[h]\" size=\"5\" value=\"".(empty($data->dims["h"])? "": $data->dims["h"])."\" /> D<input type=\"text\" name=\"dim[d]\" size=\"5\" value=\"".(empty($data->dims["d"])? "": $data->dims["d"])."\" />"', 'class' => 'CEditableColumn'),
		array(
			'class' => 'CEditableButtonColumn',
			'template' => '{edit} {cancel} {save} {delete}',
			'buttons' => array(
				'delete' => array(
					'imageUrl' => false,
					'url' => 'Yii::app()->createUrl("wmsProd/packageGridDelete", ["id" => $data->id])',
					'visible' => 'User::model()->findByPk(Yii::app()->user->id)->type == 0',
					'options' => array('class' => 'delete_btn'),
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
	$('#fetchTracking', panel).on('success', function(e, r){
		$('#<?=$_GET["tabid"];?>_prod-pack-grid', panel).yiiGridView('update');
	});
});
</script>