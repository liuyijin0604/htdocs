<div class="pane">
<?php
$fr = new FileRepo('search');
$fr->unsetAttributes();
if(empty($_GET['FileRepo'])){
	$fr->status = 20;
	$fr->type = 92;
}else{
	$fr->attributes=$_GET['FileRepo'];
	$fr->type = 92;
}
$fr->fid = $model->id;
$mf = true;

$this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id'=>$_GET['tabid'].'_cartage-grid',
	'cssFile' => false,
	'summaryText'=>'',
	'dataProvider'=> $fr->search(),
	'filter'=>$fr,
	'showQuickBar' => false,
	'columns'=>array(
		array('name' => 'name', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->baseUrl."/filerepo/".$data->hash."/".$data->name."\" target=\"_blank\">".$data->name."</a>"'),
		array(
			'name'=>'size',
			'value'=>'$data->formatSize()',
			'filter' => false,
		),
		'date',
		array(
			'header' => 'note',
			'value' => '!empty($data->mdata["note"])?$data->mdata["note"]:""',
		),
		array(
			'class' => 'CEditableButtonColumn',
			'template' => '{delete}',
			'buttons' => array(
				'delete' => array(
					'imageUrl' => false,
					'options' => array('class' => 'delete_btn'),
					'url' => 'Yii::app()->createUrl("filerepo/delete", ["id" => $data->id])',
					'visible' => 'Acl::hasAccess("B:cartage/delete")',
				),
			),
		),
	),
));

if ($mf && empty($actab)) {
	echo '<br />', CHtml::label($this->t('Upload Files'),'uploader');
	$pphash = FileRepo::uploadHash($model, 92);
	$this->widget('application.extensions.plupload.PluploadWidget', array(
	 'config' => array(
		 'url' => $this->createUrl('filerepo/upload/'.$pphash),
		 'max_file_size' => Yii::app()->params['maxFileSize'],
		 'unique_names' => true,
		 'file_list_height' => 300,
		 'visible_header' => false,
		 'filters' => array(
			  array('title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg'),
		  ),
		 //'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
		 'language' => Yii::app()->language,
		 'max_file_number' => 15,
		 'autostart' => true,
		 'jquery_ui' => false,
		 'reset_after_upload' => true,
	 ),
	 'callbacks' => array(
		 'FileUploaded' => 'function(up,file,response){$("#'.$_GET["tabid"].'").trigger("reload_cartage_grid");}',
	 ),
	 'id' => $_GET['tabid'].'_prodphoto_uploader',
	));
}
//echo CHtml::HiddenField('ppupload', $pphash);
?>

	<?php if (!empty($actab) && $actab == 1) { ?>
		<div class="form">
			<?php $form = $this->beginWidget('CActiveForm', array(
				'id' => 'confirm-print-form',
				'enableAjaxValidation' => false,
				'action' => yii::app()->createUrl('cartage/print', ['id' => $model->id]),
			)); ?>

				<div class="row buttons">
					<?php echo CHtml::submitButton('Confirm print'); ?>
				</div>

			<?php $this->endWidget(); ?>
		</div>
	<?php } ?>

</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var pane = $('#<?=$_GET["tabid"];?>').data('panel');
	tab.unbind('reload_cartage_grid').bind('reload_cartage_grid', function(){
		$('#<?=$_GET["tabid"];?>_cartage-grid', tab.data('panel')).yiiGridView('update');
		return false;
	});
	tab.data('panel').off('change', 'select.pfile_status').on('change', 'select.pfile_status', function(){
		$.post('files/status', {'id': $(this).data('id'), 'status': $(this).val() });
	});
	<?php if(!$mf):?>
		$('form', pane).lockForm();
	<?php endif; ?>
});
</script>
