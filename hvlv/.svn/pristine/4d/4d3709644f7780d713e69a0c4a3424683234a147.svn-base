<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'crm-mass-msg-form',
	'enableAjaxValidation'=>false,
	'action'=>Yii::app()->createUrl('crmmsg/massmsg/update', array('id' => $model->id))
)); ?>
	<div class="row">
		<?php echo CHtml::label('Title', 'title'); ?>
		<?php echo CHtml::textField('title', empty($model->mdata['title']) ? '' : $model->mdata['title'], array('style' => 'height:30px;', 'size' => '50')); ?>
	</div>
	<div class="row">
		<?php echo CHtml::label('Thumb Pic', 'thumb'); ?>
		<input type="file" name="thumb" id="thumb" />
		<?php if (!empty($model->mdata['thumb'])) {
			$file = FileRepo::model()->findByPk($model->mdata['thumb']);
			echo '<a href="' . Yii::app()->baseUrl . '/filerepo/' . $file->hash . '/' . $file->name . '" target="_blank">' . $file->name .'</a>';
		} ?>
	</div>
	<textarea id="ck_content" name="ck_content" rows="20"><?=!empty($model->mdata['content']) ? $model->mdata['content'] : ''?></textarea>
	<div class="row buttons">
		<?php echo CHtml::hiddenField('meta_items'); ?>
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>
<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	var editor = CKEDITOR.replace('ck_content', {
		language: 'zh-cn',
		height: 460,
		filebrowserBrowseUrl: '<?=Yii::app()->createUrl("crmmsg/massmsg/picBrowser", array("id" => $model->id));?>',
		filebrowserUploadUrl: '<?=Yii::app()->createUrl("crmmsg/massmsg/picUploader");?>',
		image_previewText: CKEDITOR.tools.repeat(' ', 1)
	});

	$('#crm-mass-msg-form', panel).on({
		'success': function(e, r) {
		},
		'error': function(e, r) {
		}
	});
});
</script>