<h1>Images:</h1>
<br>
<?php

$fr = new FileRepo('search');
$fr->unsetAttributes();
if (empty($_GET['FileRepo'])) {
	$fr->theTypes = [133];
} else {
	$fr->attributes=$_GET['FileRepo'];
	if (empty($fr->theTypes)) {
		$fr->theTypes = [133];
	}
}
$fr->fid = $model->id;
$mf = Acl::hasAccess('C:CustomerService/getImages');

$this->widget('zii.widgets.grid.CGridView', [
	'id'=>$_GET['tabid'].'_excofile-grid',
	'cssFile' => false,
	'summaryText'=>'',
	'dataProvider'=> $fr->search(),
	'filter'=>$fr,
	'columns'=>[
		['name' => 'name', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->baseUrl."/filerepo/".$data->hash."/".$data->name."\" target=\"_blank\">".$data->name."</a>"'],
		[
			'name'=>'size',
			'value'=>'$data->formatSize()',
			'filter' => false,
		],
		'date',
		'type',
		['name' => 'status', 'type'=>'raw', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('FileRepo[status]', $fr->status, FileRepo::$states, ['prompt'=>$this->t('All')]) ],
		[
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>[
				'update' => [
					'url'=>'Yii::app()->createUrl("imParcel/fileUpdate",array("id"=>$data->id))',
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->name'],
				],
			],
		],
	],
]);

echo '<br />', CHtml::label($this->t('Upload Files'), 'uploader');
$pphash = FileRepo::uploadHash($model, 133);
$this->widget('application.extensions.plupload.PluploadWidget', [
	'config' => [
		'url' => $this->createUrl('filerepo/upload/'.$pphash),
		'max_file_size' => Yii::app()->params['maxFileSize'],
		'unique_names' => true,
		'file_list_height' => 60,
		'visible_header' => false,
		'filters' => [
			['title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg,png,txt'],
		],
		//'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
		'language' => Yii::app()->language,
		'max_file_number' => 2,
		'autostart' => false,
		'jquery_ui' => false,
		'reset_after_upload' => true,
	],
	'callbacks' => [
		'FileUploaded' => 'function(up,file,response){$("#'.$_GET["tabid"].'").trigger("reload_excofile_grid");}',
	],
	'id' => $_GET['tabid'].'_excofile_uploader',
]);
?>

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = $('#<?=$_GET["tabid"];?>').data('panel');
	tab.unbind('reload_excofile_grid').bind('reload_excofile_grid', function(){
		$('#<?=$_GET["tabid"];?>_excofile-grid', win).yiiGridView('update');
		return false;
	});

	tab.data('panel').off('change', 'select.pfile_status').on('change', 'select.pfile_status', function(){
		$.post('files/status', {'id': $(this).data('id'), 'status': $(this).val() });
	});
	 
	   
});
</script>
