<div class="form">
<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'batch-form',
	'enableAjaxValidation'=>false,
	'action' => $this->createUrl('receival/batch'),
));
if(Acl::hasAccess('B:Export/AllDepots')):
?>
<p>Warehouse: <?php echo CHtml::dropDownList('wid', '', Org::dptList(), array('empty' => 'Select One', 'class' => 'required', 'id' => 'batch_wid')); ?> Begin Location: <?php
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'bsid',
				'id' => 'batch_bsid',
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
?><small>*Use location to check in parcel from different origin</small></p>
<?php
else:
echo '<input type="hidden" name="wid" value="'.Yii::app()->user->org.'" />';
endif; ?>
<div class="row">
	<label for="manifest">Manifest - <small>.csv/.xls/.xlsx File</small></label>
	<input type="file" name="manifest" id="manifest" />
</div>
<p><input id="batch_btn" type="submit" value="Submit" /></p>
<?php $this->endWidget(); ?>
</div>
<div id="batch_result" style="margin: 10px 0; border: 1px solid;padding:20px; font-weight: bold; font-size: 20px;">
</div>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('form#batch-form', panel).data('custom_success', function(r){
		$('#batch_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
		$('#batch_btn', panel).attr('disabled', false);
		return true;
	});

	$('#batch_bsid', panel).on('autocompletecreate', function(){
		$(this).data('src', $(this).autocomplete('option', 'source'));
	}).on('focus', function(){
		$(this).autocomplete({source : $(this).data('src')+'?wid='+$('#batch_wid', panel).val()}).autocomplete('search', $(this).val());
	});
});
</script>