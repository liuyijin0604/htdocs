<style>
.uploading {
	background: url("https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif") no-repeat 0 0 !important;
	background-size: 20px 20px !important;
	background-color: white !important;
}
</style>

<h1><?=$this->t('Import File For GP');?></h1>


<div class="form">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'gatepass-import-form',
		'enableAjaxValidation'=>false,
		 'htmlOptions'=>array('enctype'=>'multipart/form-data')
	)); ?>
	<div class="row">
		<label for="postw-batch">GP No</label>
		<input type="text" name="no" id="no" />
	</div>
	<div class="row">
		<label for="postw-batch">Import File</label>
		<input type="file" name="file" id="file" />
	</div>

	<div class="row buttons">
		<?php echo CHtml::button('Submit',['id' => 'btn-save']); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
	$(function(){
		var tab = $('#<?=$_GET["tabid"];?>');
		var panel = tab.data('panel');
		tab.off('reload_tab').on('reload_tab', function(){
			var t = $('.ui-tabs', panel);
			t.tabs('load', t.tabs('option','active'));
		});

		var win = $('#jqmw_<?=$_GET["tabid"];?>');

		function uploading_on(obj) {
			obj.addClass('uploading');
			obj.val('    Uploading');
			obj.prop('disabled', 'disabled');
		}

		function uploading_off(obj) {
			obj.removeClass('uploading');
			obj.val('Submit');
			obj.removeProp('disabled');
		}

		$(win).off('click', '#btn-save').on('click', '#btn-save', function() {
			uploading_on($('#btn-save'));
			var formData = new FormData();
			formData.append('file', $('#file', win)[0].files[0]);
			formData.append('no',$('#no', win).val());
			$.ajax({
				url: '<?=Yii::app()->createUrl("gatepass/ajaxImportFileForGP")?>',
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success: function(r) {
					uploading_off($('#btn-save'));
					r = JSON.parse(r);
					if (r.done) {
						myApp.notice(r.msg, 5000);
						win.data('opener').trigger('onOpen');
						win.jqmHide();
					} else {
						myApp.alert(r.msg, false);
					}
				},
				error: function(r) {
					uploading_off($('#btn-save'));
					myApp.alert('System error', false);
				}
			});
		});

	});
</script>
