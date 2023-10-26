<div id="<?=$_GET["tabid"]?>_log-grid">
	<?=$this->renderPartial('_task_60_log', ['model' => $model])?>
</div>

<br><br>
<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'wmsprod-notes-form',
	'enableClientValidation' => true,
	'action' => $this->createUrl('wmsTask/notes', array('id' => $model->id)),
	'clientOptions' => array(
		'validateOnSubmit' => true,
	),
));
?>
<h2>Add Notes</h2>
<?php echo $form->errorSummary($model); ?>
<div class="row">
	<?php echo CHtml::textArea('notes', '', array('rows' => 8, 'style' => 'width: 100%')); ?>
</div>
<div class="row buttons">
	<?php echo CHtml::submitButton('Save'); ?>
</div>
<?php $this->endWidget();?>
</div><!-- form -->

<?php
echo '<br />', CHtml::label($this->t('Upload Files'),'uploader');
$pphash = FileRepo::uploadHash($model, 84);
$this->widget('application.extensions.plupload.PluploadWidget', array(
	'config' => array(
		'url' => $this->createUrl('filerepo/upload/'.$pphash),
		'max_file_size' => Yii::app()->params['maxFileSize'],
		'unique_names' => true,
		'file_list_height' => 100,
		'visible_header' => false,
		'filters' => array(
			array('title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg'),
		),
		//'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
		'language' => Yii::app()->language,
		'max_file_number' => 5,
		'autostart' => true,
		'jquery_ui' => false,
		'reset_after_upload' => true,
	),
	'callbacks' => array(
		'FileUploaded' => 'function(up,file,response){
			$("#'.$_GET["tabid"].'").trigger("reload_wtfile_grid");
		}',
	),
	'id' => $_GET['tabid'].'_prodphoto_uploader_'.str_replace(' ', '', $model->getType()),
));
?>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var pane = $('#<?=$_GET["tabid"];?>').data('panel');
	tab.unbind('reload_wtfile_grid').bind('reload_wtfile_grid', function(){
		$('#<?=$_GET["tabid"];?>_wtfile-grid', tab.data('panel')).yiiGridView('update');
		$('#<?=$_GET["tabid"]?>_log-grid', tab.data('panel')).load('<?=Yii::app()->createUrl("wmsTask/loadAdhocLog", ["id" => $model->id])?>');
		return false;
	});
	tab.data('panel').off('change', 'select.pfile_status').on('change', 'select.pfile_status', function(){
		$.post('files/status', {'id': $(this).data('id'), 'status': $(this).val() });
	});
});
</script>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	$('form#wmsprod-notes-form', panel).on({
		'success': function(e,r){
			$('#<?=$_GET["tabid"]?>_log-grid', panel).load('<?=Yii::app()->createUrl("wmsTask/loadAdhocLog", ["id" => $model->id])?>');
			$('#notes', panel).val('');
		},
		'reset': true
	}
	);
});
</script>