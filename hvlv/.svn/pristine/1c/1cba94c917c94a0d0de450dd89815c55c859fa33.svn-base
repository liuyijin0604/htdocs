<?php
$fr = new FileRepo('search');
$fr->unsetAttributes();
$model=$model->linkShipment(false);

if (empty($_GET['FileRepo'])) {
	$fr->theTypes = [18,19,20,21,25];
} else {
	$fr->attributes=$_GET['FileRepo'];
	if (empty($fr->theTypes)) {
		$fr->theTypes = [18,19,20,21,25];
	}
}
$fr->fid = $model->id;
$fr->non_status=[0];

$this->widget('zii.widgets.grid.CGridView', [
	'id'=>$_GET['tabid'].'_imcofile-grid',
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
$pphash = FileRepo::uploadHash($model, 19);
$this->widget('application.extensions.plupload.PluploadWidget', [
	'config' => [
		'url' => $this->createUrl('filerepo/upload/'.$pphash),
		'max_file_size' => Yii::app()->params['maxFileSize'],
		'unique_names' => true,
		'file_list_height' => 60,
		'visible_header' => false,
		'filters' => [
			['title' => Yii::t('app', 'JPG, PDF, Word, Htm, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg,txt,eml,htm'],
		],
		//'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
		'language' => Yii::app()->language,
		'max_file_number' => 2,
		'autostart' => true,
		'jquery_ui' => false,
		'reset_after_upload' => true,
	],
	'callbacks' => [
		'FileUploaded' => 'function(up,file,response){$("#'.$_GET["tabid"].'").trigger("reload_imcofile_grid");}',
	],
	'id' => $_GET['tabid'].'_imcofile_uploader',
]);
?>

<?php
echo '<br /><h3>', CHtml::label($this->t('Upload POD Files'), '</h3>uploader'),"(can be download by client)";
$pphash = FileRepo::uploadHash($model, 25);
$this->widget('application.extensions.plupload.PluploadWidget', [
	'config' => [
		'url' => $this->createUrl('filerepo/upload/'.$pphash),
		'max_file_size' => Yii::app()->params['maxFileSize'],
		'unique_names' => true,
		'file_list_height' => 60,
		'visible_header' => false,
		'filters' => [
			['title' => Yii::t('app', 'JPG, PDF, Word, Html, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg,png,txt,htm,html'],
		],
		//'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
		'language' => Yii::app()->language,
		'max_file_number' => 2,
		'autostart' => true,
		'jquery_ui' => false,
		'reset_after_upload' => true,
	],
	'callbacks' => [
		'FileUploaded' => 'function(up,file,response){$("#'.$_GET["tabid"].'").trigger("reload_excofile_grid");$("#'.$_GET["tabid"].'").trigger("reload_excofile_grid_file");}',
	],
	'id' => $_GET['tabid'].'_excofile_uploader_1',
]);
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var pane = $('#<?=$_GET["tabid"];?>').data('panel');
	tab.unbind('reload_imcofile_grid').bind('reload_imcofile_grid', function(){
		$('#<?=$_GET["tabid"];?>_imcofile-grid', tab.data('panel')).yiiGridView('update');
		return false;
	});
        tab.unbind('reload_excofile_grid_file').bind('reload_excofile_grid_file', function(){
            $.get("<?=Yii::app()->createUrl('imParcel/updatePodFile', ['id'=>$model->id])?>",function(r){
                console.log(r);
                
            })
		return false;
	});
	tab.data('panel').off('change', 'select.pfile_status').on('change', 'select.pfile_status', function(){
		$.post('files/status', {'id': $(this).data('id'), 'status': $(this).val() });
	});
});
</script>
