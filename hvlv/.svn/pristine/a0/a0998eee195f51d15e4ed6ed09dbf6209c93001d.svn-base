<h2>Cost</h2>
<?php
$ledger = new Ledger('search');
$ledger->fid = $model->id;
$ledger->model = get_class($model);

$this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id'=>$_GET["tabid"].'_ledger-grid',
	'cssFile' => false,
	'dataProvider'=>$ledger->search(),
	'formUrl' => $this->createUrl('ledger/grid', array('type' => 50, 'model' => $ledger->model, 'fid'=>$model->id)),
	'filter'=>null,
	'summaryText' => '',
	'showQuickBar' => true,
	'afterSave' => "function(r){
		if(r.done == true){
			myApp.notice(r.msg, 5000);
		}else{
			myApp.alert(r.msg, false);
		}
		return r.done;
	}",
	'columns'=>array(
		array('name' => 'date', 'class' => 'CEditableColumn', 'inputOptions' => array('class' => 'date_input')),
		array('name' => 'chgcode', 'class' => 'CEditableColumn'),
		array('name' => 'ref', 'class' => 'CEditableColumn'),
		array('name' => 'currency', 'class' => 'CEditableColumn', 'type' => 'list', 'filter' => Ledger::$currencies),
		array('name' => 'total', 'class' => 'CEditableColumn'),
		array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save}'),
	),
));
?>
<br />
<h2>Revenue</h2>
<script type="text/javascript">
$(function(){
	var pane = $('#<?=$_GET["tabid"];?>');
});
</script>