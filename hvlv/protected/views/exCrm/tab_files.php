<h3>Files</h3>
<?php
$fr = new FileRepo('search');
$fr->unsetAttributes();
$fr->non_status=[0];
if(empty($_GET['FileRepo'])){
	
	$fr->theTypes = [100];
}else{
	$fr->attributes=$_GET['FileRepo'];
	if(empty($fr->theTypes)) $fr->theTypes =[100];
}
$fr->fid = $model->id;
$mf = Acl::hasAccess('B:org/manageFile');

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET['tabid'].'_excofile-grid',
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
                array('name'=>'type','value'=>'@FileRepo::$custom_type[$data->type]','filter'=>CHtml::dropDownList('FileRepo[type]',$fr->type, FileRepo::$custom_type,array('prompt'=>'All')),), 
		array('name' => 'status', 'type'=>'raw', 'value' => '$data->getStatus()',
		'filter'=>CHtml::dropDownList('FileRepo[status]', $fr->status, FileRepo::$states, array('prompt'=>$this->t('All'))) ),
             array(
			'class'=>'CButtonColumn',
			'template'=>'{update}{delete}',
			'buttons'=>array
			(
				'update' => array(
                                        'url'=>'Yii::app()->createUrl("imParcel/fileUpdate",array("id"=>$data->id))',
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->name'),
				),
                             'delete' => array(
					'imageUrl'=>false,
					'url'=>'Yii::app()->createUrl("filerepo/delete", array("id"=>$data->id))',
                                         'options' => array('class' => 'grid_delete_btn')
                                  
					
                              ),
			),
		),
	),
));

echo '<br />', CHtml::label($this->t('Upload Files'),'uploader');
$pphash = FileRepo::uploadHash($model, 100);
$this->widget('application.extensions.plupload.PluploadWidget', array(
 'config' => array(
	 'url' => $this->createUrl('filerepo/upload/'.$pphash),
	 'max_file_size' => Yii::app()->params['maxFileSize'],
	 'unique_names' => true,
	 'file_list_height' => 60,
	 'visible_header' => false,
	 'filters' => array(
		  array('title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg,png'),
	  ),
	 //'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
	 'language' => Yii::app()->language,
	 'max_file_number' => 2,
	 'autostart' => false,
	 'jquery_ui' => false,
	 'reset_after_upload' => true,
 ),
 'callbacks' => array(
	 'FileUploaded' => 'function(up,file,response){$("#'.$_GET["tabid"].'").trigger("reload_excofile_grid");}',
 ),
 'id' => $_GET['tabid'].'_excofile_uploader',
));
?>
<script>
    $(function(){
        var tab = $('#<?= $_GET["tabid"]; ?>');
	var panel = $('#<?= $_GET["tabid"]; ?>').data('panel');
        tab.unbind('reload_excofile_grid').bind('reload_excofile_grid', function(){
		$('#<?= $_GET["tabid"]; ?>_excofile-grid', panel).yiiGridView('update');
		return false;
	});
        $(document).off('click','#<?=$_GET["tabid"];?>_excofile-grid a.grid_delete_btn');
    })
</script>
