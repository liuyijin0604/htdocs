<h1><?=$this->t('Manage Files');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'fr-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(

		'name',
		array(
			'name'=>'size',
			'value'=>'$data->formatSize()',
		),
		'date',
		array('name' => 'type', 'value' => '$data->getType()',
			'filter'=>CHtml::dropDownList('FileRepo[type]', $model->type, $this->t(FileRepo::$types), array('prompt'=>$this->t('All'))),
		),
		array(
			'class'=>'CButtonColumn',
			'template'=>'{view} {delete}',
			'buttons'=>array(
				'view' => array(
					'url'=>'Yii::app()->baseUrl."/filerepo/".$data->hash."/".$data->name',
					'imageUrl'=>false,
					'options' => array('class' => 'grid_view_btn', 'target' => '_blank', 'title'=>'View File'),
				),
				'delete' => array(
					'imageUrl'=>false,
					'url'=>'Yii::app()->createUrl("filerepo/delete", array("id"=>$data->id))',
					'options' => array('class' => 'grid_delete_btn'),
				),
			),
		),
	),
));
$pphash = FileRepo::uploadHash($model, 50);
$this->widget('application.extensions.plupload.PluploadWidget', array(
 'config' => array(
	 'url' => $this->createUrl('filerepo/upload/'.$pphash),
	 'max_file_size' => Yii::app()->params['maxFileSize'],
	 'unique_names' => true,
	 'file_list_height' => 60,
	 'visible_header' => false,
	 'filters' => array(
		  array('title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg,csv'),
	  ),
	 'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
	 'language' => Yii::app()->language,
	 'max_file_number' => 10,
	 'autostart' => true,
	 'jquery_ui' => false,
	 'reset_after_upload' => true,
 ),
 'callbacks' => array(
	 'FileUploaded' => 'function(up,file,response){$("#'.$_GET["tabid"].'").trigger("onOpen");}',
 ),
 'id' => $_GET["tabid"].'_rf_uploader',
));
//echo CHtml::HiddenField('ppupload', $pphash);
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	tab.bind('onOpen', function(){
		$('#fr-grid', panel).yiiGridView('update');
	});
});
</script>
