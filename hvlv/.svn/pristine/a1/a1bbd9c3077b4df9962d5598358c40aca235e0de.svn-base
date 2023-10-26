<?php
$dp = new WmsProdPack('search');
$dp->prod_id = $model->id;

$this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id'=>'prod-pack-grid',
	'cssFile' => true,
	'dataProvider'=>$dp->search(),
	'formUrl' => $this->createUrl('product/packageGrid', array('id'=>$model->id)),
	'filter'=>null,
	'summaryText' => '',
	'afterSave' => "function(r){
		if(r.done == true){
                        $('#notifc').notify({message: {html: r.msg}}).show();
		}else{
			$('#notifc').notify({message: {html: r.msg},type: 'danger'}).show();
		}
		return r.done;
	}",
	'columns'=>array(
		array('name' => 'type', 'type' => 'list', 'filter' => WmsProdPack::$types, 'value' => '$data->getType()', 'class' => 'CEditableColumn','htmlOptions'=>array('class'=>'form')),
		array('name' => 'qty', 'class' => 'CEditableColumn'),
		array('name' => 'barcode', 'class' => 'CEditableColumn'),
		array('header' => 'Weight (Kg)', 'name' => 'weight', 'class' => 'CEditableColumn'),
		array('header' => 'Dim (cm) ', 'name' => 'dim', 'type' => 'raw', 'value' => '"W<input type=\"text\" name=\"dim[w]\" size=\"5\" value=\"".(empty($data->dims["w"])? "": $data->dims["w"])."\" /> H<input type=\"text\" name=\"dim[h]\" size=\"5\" value=\"".(empty($data->dims["h"])? "": $data->dims["h"])."\" /> D<input type=\"text\" name=\"dim[d]\" size=\"5\" value=\"".(empty($data->dims["d"])? "": $data->dims["d"])."\" />"', 'class' => 'CEditableColumn'),
		array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save}'),
	),
));
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('#fetchTracking', panel).on('success', function(e, r){
            console.log('1111');
		$('#prod-pack-grid').yiiGridView('update');
//                 $("task_grid_view").yiiGridView.update("task_grid_view");   
	});
});
</script>