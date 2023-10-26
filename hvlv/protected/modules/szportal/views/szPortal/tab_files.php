<?php
$fr = new FileRepo('search');
$fr->unsetAttributes();
if(empty($_GET['FileRepo'])){
	$fr->status = 20;
	$fr->type = 80;
}else{
	$fr->attributes=$_GET['FileRepo'];
	if(empty($fr->type)) $fr->type = 80;
}
$fr->fid = $model->id;
$mf = Acl::hasAccess('B:org/manageFile');

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET['tabid'].'_szPortalfile-grid',
	'cssFile' => false,
	'summaryText'=>'',
	'dataProvider'=> $fr->search(),
	'filter'=>$fr,
	'columns'=>array(
		array('name' => 'name', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->baseUrl."/filerepo/".$data->hash."/".$data->name."\" target=\"_blank\">".$data->name."</a>"'),
		array(
			'name'=>'size',
			'value'=>'$data->formatSize()',
			'filter' => false,
		),
		'date',
		array('name' => 'status', 'type'=>'raw', 'value' => '$data->getStatus()',
		'filter'=>CHtml::dropDownList('FileRepo[status]', $fr->status, FileRepo::$states, array('prompt'=>$this->t('All'))) ),
	),
));

echo '<br />', CHtml::label($this->t('Upload Files'),'uploader');
$pphash = FileRepo::uploadHash($model, 80);
$this->widget('application.extensions.plupload.PluploadWidget', array(
 'config' => array(
	 'url' => $this->createUrl('filerepo/upload/'.$pphash),
	 'max_file_size' => Yii::app()->params['maxFileSize'],
	 'unique_names' => true,
	 'file_list_height' => 60,
	 'visible_header' => false,
	 'filters' => array(
		  array('title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg'),
	  ),
	 //'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
	 'language' => Yii::app()->language,
	 'max_file_number' => 2,
	 'autostart' => true,
	 'jquery_ui' => false,
	 'reset_after_upload' => true,
 ),
 'callbacks' => array(
	 'FileUploaded' => 'function(up,file,response){$("#'.$_GET["tabid"].'").trigger("reload_szPortalfile_grid");}',
 ),
 'id' => $_GET['tabid'].'_szPortalfile_uploader',
));
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var pane = $('#<?=$_GET["tabid"];?>').data('panel');
	tab.unbind('reload_szPortalfile_grid').bind('reload_szPortalfile_grid', function(){
		$('#<?=$_GET["tabid"];?>_szPortalfile-grid', tab.data('panel')).yiiGridView('update');
		return false;
	});
	tab.data('panel').off('change', 'select.pfile_status').on('change', 'select.pfile_status', function(){
		$.post('files/status', {'id': $(this).data('id'), 'status': $(this).val() });
	});
});
</script>
