<?php
$fr = new CnlDoc('search');
$fr->unsetAttributes();
$fr->order_id = $model->id;

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET['tabid'].'_cnldoc-grid',
	'cssFile' => false,
	'summaryText'=>'',
	'dataProvider'=> $fr->search(),
	'filter'=>null,
	'columns'=>array(
		array('name' => 'type', 'value' => '$data->getType()'),
		array('name' => 'status', 'value' => '$data->getStatus()'),
		array('header' => 'Name', 'type' => 'raw', 'value' => '"<a href=\"".$data->file->getUrl()."\" target=\"_blank\">".$data->file->name."</a>"'),
		array('header' => 'Date', 'value' => '$data->file->date'),
		[
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>[
				'update' => [
					'url'=>'Yii::app()->createUrl("cnlOrder/fileUpdate",array("id"=>$data->id))',
					'imageUrl'=>false,
					'visible'=>'in_array($data->status, [20, 40, 100])',
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->id'],
				],
			],
		],
	),
));

echo '<br /><h3>Upload Files</h3>';

$pphash = FileRepo::uploadHash($model, 110);
$this->widget('application.extensions.plupload.PluploadWidget', [
	'config' => [
		'url' => $this->createUrl('filerepo/upload/'.$pphash),
		'max_file_size' => Yii::app()->params['maxFileSize'],
		'unique_names' => true,
		'file_list_height' => 60,
		'visible_header' => false,
		'filters' => [
			['title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg,txt'],
		],
		//'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
		'language' => Yii::app()->language,
		'max_file_number' => 2,
		'autostart' => true,
		'jquery_ui' => false,
		'reset_after_upload' => true,
	],
	'callbacks' => [
		'FileUploaded' => 'function(up,file,response){$("#'.$_GET["tabid"].'").trigger("reload_cnldoc_grid");}',
	],
	'id' => $_GET['tabid'].'_cnlfile_uploader',
]);
?>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var pane = $('#<?=$_GET["tabid"];?>').data('panel');
	tab.unbind('reload_cnldoc_grid').bind('reload_cnldoc_grid', function(){
		$.get('<?=$this->createUrl("cnlOrder/upFiles", ["id" => $model->id]);?>', function(){
			$('#<?=$_GET["tabid"];?>_cnldoc-grid', tab.data('panel')).yiiGridView('update');
		});
		return false;
	});
});
</script>
