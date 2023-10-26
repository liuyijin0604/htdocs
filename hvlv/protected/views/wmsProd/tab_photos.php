<?php
$file = new FileRepo;
$file->type = 135;
$file->status = 20;
$file->fid = $model->id;
$ec = new CDbCriteria;
$ec->order = 'id ASC';
$data = $file->search(false, 0, $ec)->getData();
?>
<div class="container" style="margin: 0; padding: 0">
	<div class="table-responsive" id="files">
		<?php foreach ($data as $i => $file) {
			echo '<div style="border: 1px solid #ddd; float: left; width: 200px; height: 200px; position: relative"><img src="' . Yii::app()->baseUrl . '/filerepo/' . $file->hash . '/' . $file->name . '" style="width: 90%; max-height: 90%; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%)"></div>';
		} ?>
		<div stlye="clear: both"></div>
	</div>
</div>
<br>
<?php
$this->widget('application.extensions.plupload.PluploadWidget', array(
	'config' => array(
		'url' => $this->createUrl('wmsProd/file', ['id' => $model->id]),
		'max_file_size' => Yii::app()->params['maxFileSize'],
		'unique_names' => true,
		'file_list_height' => 400,
		'visible_header' => false,
		'filters' => array(
			array('title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg,png,jpeg'),
		),
		'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
		'language' => Yii::app()->language,
	 	'max_file_number' => 10,
		'autostart' => true,
		'jquery_ui' => false,
		'reset_after_upload' => true,
	),
	'callbacks' => array(
		'FileUploaded' => 'function(up,file,response) {
			if (response.response) {
				$("#files").append(JSON.parse(response.response).file);
			}
		}',
	),
	'id' => 'note_rf_uploader',
));
?>