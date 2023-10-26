<div class="form">
<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'scan-form',
	'enableAjaxValidation'=>false,
));

if(Acl::hasAccess('B:Export/AllDepots')):
?>
<p>Warehouse: <?php echo CHtml::dropDownList('wid', '', Org::dptList(), array('empty' => 'Select One', 'class' => 'required')); ?> Begin Location: <?php
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'bsid',
				'sourceUrl' => array('receival/blSuggest'),
				'value' => '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'autoFocus' => false,
				),
				'htmlOptions' => array(
					'size' => '10',
				),
		));
?></p>
<?php
else:
echo '<input type="hidden" name="wid" value="'.Yii::app()->user->org.'" />';
endif; ?>
<p>Barcode: <input id="scan" type="text" size="30" name="barcode" /></p>
<input id="btn" type="submit" value="Save" style="display:none;" />
<?php $this->endWidget(); ?>
<div id="result" style="margin: 10px 0; border: 1px solid;padding:20px; font-weight: bold; font-size: 32px;">
</div>
<audio id="sound" src=""></audio>
</div>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('select#wid', panel).on('change', function(){
		$('select#bsid', panel).load('receival/bsopt/'+$(this).val()+'?type=60');
		$('input#scan', panel).focus();
	}).focus();
	$('form#scan-form', panel).data('custom_success', function(r){
		$('#result', panel).prepend($('<p>'+r.msg+'</p>').css('color', r.color).fadeIn());
		$('#sound', panel).attr('src', 'site/voice/'+r.sound);
		$('#sound', panel)[0].play();
		$('#btn', panel).attr('disabled', false);
		return true;
	}).on('submit', function(){
		$('input#scan', panel).focus();
	});
	$('input#scan', panel).on('focus', function(){
		$(this).select();
	});

	$('#bsid', panel).on('autocompletecreate', function(){
		$(this).data('src', $(this).autocomplete('option', 'source'));
	}).on('focus', function(){
		$(this).autocomplete({source : $(this).data('src')+'?wid='+$('#wid', panel).val()}).autocomplete('search', $(this).val());
	});
});
</script>