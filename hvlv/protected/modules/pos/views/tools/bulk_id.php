<h2>Batch Upload ID</h2>
<?php
$this->widget('application.extensions.plupload.PluploadWidget', array(
	 'config' => array(
		 'url' => $this->createUrl('tools/bulkID'),
		 'max_file_size' => '5mb',
		 'unique_names' => true,
		 'file_list_height' => 60,
		 'visible_header' => false,
		 'filters' => array(
			  array('title' => Yii::t('app', 'Image files'), 'extensions' => 'jpg,jpeg'),
		  ),
		 'resize' => array('width' => 600, 'height' => 600, 'quality' => 80),
		 'language' => Yii::app()->language,
		 'max_file_number' => 50,
		 'autostart' => true,
		 'jquery_ui' => false,
		 'reset_after_upload' => true,
		 'file_list_height' => '300',
	 ),
	 'callbacks' => array(
		 'FileUploaded' => 'function(up,file,res){ $("#result").append(res); }',
		 'FileFiltered' => 'function(up,file){ if(file.name.match(/^([0-9X]{18})[_-]*([^_-]*)[_-]*([abAB12]{1})\.(jpg|jpeg)$/) === null && file.name.match(/^([^_-]*)[_-]*([0-9X]{18})[_-]*([abAB12]{1})\.(jpg|jpeg)$/) === null){ $("#notifc").notify({message: {html: "文件名格式错误"}, type: "danger"}).show(); up.removeFile(file); } }',
	 ),
	 'id' => 'bulk_id_uploader',
	));
?>
<div id="result">
</div>
<p>说明:<br />
1. 文件名格式: 身份证号码[_-][姓名][_-](1,2,A,B).jpg, e.g. 10101019010101006XA.jpg,10101019010101006X张三1.jpg, 10101019010101006X_张三_B.jpg, 10101019010101006X-张三-a.jpg<br />
或, [姓名][_-]身份证号码[_-](1,2,A,B).jpg, e.g. 张三10101019010101006X1.jpg, 张三_10101019010101006X_B.jpg, 张三-10101019010101006X-a.jpg<br />
2. 必须上传独立的正反面照片<br />
</p>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	
});
</script>
<?php $this->registerJS(ob_get_clean(),8); ?>
