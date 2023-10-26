<?php
$wph = new WmsProdHscode('search');
$wph->prod_id = $model->id;

$list = AppHelper::setting2List('pols');
ksort($list);

$this->widget('application.extensions.editablegrid.CEditableGridView', array(
		'id' => $_GET["tabid"].'_hscode-grid',
		'cssFile' => false,
		'summaryText' => '',
		'dataProvider' => $wph->search(),
		'formUrl' => $this->createUrl('wmsProd/hscodeGrid', array('id' => $model->id)),
		'afterSave' => "function(r){
			if(r.done == true){
				myApp.notice(r.msg, 5000);
			}else{
				myApp.alert(r.msg, false);
			}
			return r.done;
		}",
		'columns' => array(
			array('name' => 'port', 'class' => 'CEditableColumn', 'value' => '$data->port', 'type' => 'list', 'filter' => $list),
			array('name' => 'hscode', 'class' => 'CEditableColumn'),
			array(
				'class' => 'CEditableButtonColumn',
				'template' => '{edit} {cancel} {save} {delete}',
				'buttons' => array(
					'delete' => array(
						'imageUrl' => false,
						'url' => 'Yii::app()->createUrl("wmsProd/hscodeGridDelete", ["id" => $data->id])',
						'visible' => 'true',
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

	tab.bind('onOpen', function(){
		$('#<?=$_GET["tabid"]?>_hscode-grid', panel).yiiGridView('update');
	});
});
</script>