<?php
echo '<br />', CHtml::label($this->t('Upload Files'), 'uploader');
$pphash = FileRepo::uploadHash($model, 120);
$this->widget('application.extensions.plupload.PluploadWidget', array(
	'config' => array(
		'url' => $this->createUrl('filerepo/upload/' . $pphash),
		'max_file_size' => Yii::app()->params['maxFileSize'],
		'unique_names' => true,
		'file_list_height' => 60,
		'visible_header' => false,
		'filters' => array(
			array('title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg'),
		),
		'language' => Yii::app()->language,
		'max_file_number' => 5,
		'autostart' => true,
		'jquery_ui' => false,
		'reset_after_upload' => true,
	),
	'callbacks' => array(
		'FileUploaded' => 'function(up,file,response){
		}',
	),
	'id' => 'billing_file_uploader',
));
?>