<?php
$tp = $model->translationList();

if(empty($tp)){
	echo '<p>No translation needed</p>';
}else{
sort($tp);
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'ex-translation-form',
	'enableAjaxValidation'=>false,
	'action' => $this->createUrl('excoConsol/trans'),
));
foreach($tp as $t){
	echo '<p><label>',$t,'</label> => ',CHtml::textField('t['.$t.']','',array('class'=>'trans', 'data-t'=> $t)),'</p>';
}
?>
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Save')); ?>
	</div>
<?php
$this->endWidget();
}
?>
<script type="text/javascript">
$(function(){
	var pane = $('#<?=$_GET["tabid"];?>').data('panel');
	$('input.trans', pane).each(function(i){
		var that = $(this);
		that.addClass('loading');
		$.get('excoConsol/trans?w='+$(this).data('t'), function(r){
			that.val(r).removeClass('loading');
		});
	});
	
	$('form#ex-translation-form', pane).on('success', function(e, r){
		var t = $('.ui-tabs', pane);
		t.tabs('load', t.tabs('option','active'));
	});
});
</script>