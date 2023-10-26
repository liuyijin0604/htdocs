<div class="form">
<?php 
$was = $model->getWarnings(false);
echo '<div class="row">';
foreach($model::$bwfs as $b=>$w){
	if($b == 2) continue;
	echo '<a href="'.$this->createUrl('cnID/togWarn', array('id' => $model->id, 'b' => $b)).'" class="tog_warn" title="Click to toggle"><span class="warn '.(empty($was[$b])? 'warn_off' : 'warn_on').'">'.$w.'</span></a> &nbsp;';
}
if($model->status > 14 && $model->status < 99){
	echo '<a href="'.$this->createUrl('cnID/togExp', array('id' => $model->id)).'" class="tog_warn" title="Click to toggle"><span class="warn '.($model->status == 98? 'warn_on' : 'warn_off').'">Expired</span></a> &nbsp;';
}
echo '</div>';

$form=$this->beginWidget('CActiveForm', array(
	'id'=>'cn-id-form',
	'enableAjaxValidation'=>false,
));
if(!empty($addr->name)) $model->name = $addr->name;
if(!empty($addr->phone)) $model->mobile = $addr->phone;
?>
	<div class="row">
		<?php echo CHtml::label('Upload Photo','photo'); ?>
	<?php
	$pphash = FileRepo::uploadHash($model, 60);
	echo CHtml::hiddenField('pphash', $pphash);
	$this->widget('application.extensions.plupload.PluploadWidget', array(
	 'config' => array(
		 'url' => $this->createUrl('filerepo/upload/'.$pphash),
		 'max_file_size' => Yii::app()->params['maxFileSize'],
		 'unique_names' => true,
		 'file_list_height' => 60,
		 'visible_header' => false,
		 'filters' => array(
			  array('title' => Yii::t('app', 'JPG, PNG, GIF'), 'extensions' => 'jpg,jpeg,png,gif'),
		  ),
		 'resize' => array('width' => 600, 'height' => 600, 'quality' => 80),
		 'language' => Yii::app()->language,
		 'max_file_number' => 2,
		 'autostart' => true,
		 'jquery_ui' => false,
		 'reset_after_upload' => true,
	 ),
	 'callbacks' => array(
		 'UploadComplete' => 'function(up,file,response){$("#jqmw_'.$_GET["tabid"].'").trigger("uploaded");}',
	 ),
	 'id' => $_GET["tabid"].'_prodphoto_uploader',
	));
	?>
	
	<div style="width: 200px; min-height: 240px; float: left; position: relative;">
	<div style="position:fixed;">
	<div class="row">
		<?php echo $form->labelEx($model,'no'); ?>
		<?php echo $form->textField($model,'no',array('size'=>25,'maxlength'=>30)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'mobile'); ?>
		<?php echo $form->textField($model,'mobile',array('size'=>20,'maxlength'=>20)); ?>
	</div>
	</div>
	</div>
	<div id="files_uploaded">
	<?php if(!$model->isNewRecord): ?>
	<table>
	<?php
	if(!empty($model->photo_front)){
		$f = $model->photo_front;
		echo '<tr><td width="120"><img class="thumb" src="'.$f->getUrl().'" width="100" /><br /><i>'.$f->name.'</i></td><td><label class="radio_label"><input type="radio" class="rfrt" name="front" value="'.$f->id.'" checked /> Front</label> &nbsp; <label class="radio_label"><input type="radio" class="rbak" name="back" value="'.$f->id.'" /> Back</label></td></tr>';
	}
	if(!empty($model->photo_back)){
		$f = $model->photo_back;
		echo '<tr><td width="120"><img class="thumb" src="'.$f->getUrl().'" width="100" /><br /><i>'.$f->name.'</i></td><td><label class="radio_label"><input type="radio" class="rfrt" name="front" value="'.$f->id.'" /> Front</label> &nbsp; <label class="radio_label"><input type="radio" class="rbak" name="back" value="'.$f->id.'" checked /> Back</label></td></tr>';
	}
	?>
	</table>
	<?php endif; ?>
	</div>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save'), array('id'=>'save_btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('#CnID_state', win).load('<?=Yii::app()->baseURL.'/cnZip/province';?>');

	$('#CnID_state', win).on('change', function(){
		var s = $('option:selected', this).data('id');
		if(s == '') return;
		$('#CnID_city', win).load('<?=Yii::app()->baseURL.'/cnZip/city';?>/s/'+s, function(){
			if($('#CnID_city option', win).length == 2){
				$($('#CnID_city option', win)[1]).attr('selected', true);
			}
		});
	});

	var canSave = function(){
		if($('input.rfrt:checked', win).length + $('input.rbak:checked', win).length == 2){
			$('#save_btn').attr('disabled', false);
		}else{
			$('#save_btn').attr('disabled', true);
		}
	};

	$(win).on('mouseover', 'img.thumb', function(){
		$(this).attr('width', 400);
	}).on('mouseout', 'img.thumb', function(){
		$(this).attr('width', 100);
	});

	win.off('uploaded').on('uploaded', function(){
		$('#files_uploaded', win).load('<?=$this->createUrl("cnID/uploaded", array("hash" => $pphash, "id" => $model->id));?>'.replace('/.app', '.app'));
		return false;
	});

	win.off('change', 'input.rfrt').on('change', 'input.rfrt', function(){
		$('.rbak', $(this).parents('td')).attr('checked', false);
		canSave();
	}).off('change', 'input.rbak').on('change', 'input.rbak', function(){
		$('.rfrt', $(this).parents('td')).attr('checked', false);
		canSave();
	});

	//warning removal
	$('a.tog_warn', win).on('click', function(){
		if(window.confirm("Are you sure to toggle this warning?")){
			$.get($(this).attr('href'), function(){
				win.data('opener').trigger('onOpen');
				win.jqmHide();
			});
		}
		return false;
	});

	$('#del_btn', win).click(function(){
		if(window.confirm('Are you sure to delete?')){
			$.get('<?=$this->createUrl("cnID/delete", ["id" => $model->id]);?>', function(){
				win.data('opener').trigger('onOpen');
				win.jqmHide();
			});
		}
		return false;
	});

	canSave();

});
</script>