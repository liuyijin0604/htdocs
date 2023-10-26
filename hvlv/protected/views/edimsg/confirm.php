<div class="form">
	<h1>MsgId: <?=$model->mid?></h1>
	<h3>Are you sure to change the status to "ERR"?</h3>
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'edimsg-error-confirm-form',
		'enableAjaxValidation' => false,
	)); ?>

		<div class="row buttons submit_button">
			<?php echo CHtml::submitButton('Confirm'); ?>
		</div>

	<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$tabid;?>');
	var tab = $('#<?=$tabid;?>');
	var panel = $('#<?=$tabid;?>').data('panel');

	tab.unbind('reload_edimsg-grid').bind('reload_edimsg-grid', function(){
		$('#edimsg-grid-<?=$tabid?>', tab.data('panel')).yiiGridView('update');
		return false;
	});

	$('.submit_button').on('click',function()
	{
		$.ajax({
		            url: '<?=$this->createUrl('edimsg/changeToError')."?id=".$model->id?>',
		            type: "post",
		            processData: false,
		            contentType: false,
		            success: function(r) {
		            	r = JSON.parse(r);
		                if(r.done)
		                 {
							myApp.notice(r.msg, 5000);
						 }else
						 {
							myApp.alert(r.msg, false);   
			             }
			             tab.trigger('reload_edimsg-grid');
			         },
		            error: function(e) {
		                console.log(e);
		            }
		        });	
		return false;
	});

});
</script>