<h3>Note Detail</h3>

<br>
<div class="form">
<?php 
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'note_org_form',
	'enableAjaxValidation'=>false,
	'action'=> $this->createUrl('org/editNote')."?id=".$id."&&orgId=".$orgId)
	);
?>
	<div class="row buttons">
		<?php echo CHtml::label("subject","subject")?>
		<?php echo $form->textField($model,'subject'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::label("Created Time","Created Time")?>
		<?php echo $form->textField($model,'created', array('size' => 20,'class'=>"datetime_input",'id'=>'created'.$_GET["tabid"])) ?>
	</div>

	<div class="row" style="height:300px">
		<?php echo $form->labelEx($model,'content'); ?>
		<?php //echo $form->textArea($model,'body',array('rows'=>6, 'cols'=>50));
		$this->widget('ext.ckeditor.CKEditorWidget',array(
		  "model"=>$model,
		  "attribute"=>'content',
		  "config" => array(
			  "height"=>"200px",
			  "width"=>"570px",
			  "toolbar"=>"Basic",
			  ),
		  ));
		?>
		
	</div>
	</br>
	</br>
	</br>
	</br>
	</br>
	</br>
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

</div><!-- form -->
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = $('#<?=$_GET["tabid"];?>').data('panel');
	tab.unbind('reload_org_note_grid').bind('reload_org_note_grid', function(){
				$('#<?=$_GET["tabid"];?>_org_note_grid', tab.data('panel')).yiiGridView('update');
				return false;
			});

	win.on('close', function(){
		if(typeof CKEDITOR != 'undefined'){
			for(i in CKEDITOR.instances){
				if($('#'+i, win).length > 0) CKEDITOR.instances[i].destroy(true);
			}
		}
	});
	$('form#note_org_form', win).on('success', function(e, r){
		tab.trigger('reload_org_note_grid');
		win.jqmHide();
	});
});
</script>


<?php $this->endWidget();?>
		
