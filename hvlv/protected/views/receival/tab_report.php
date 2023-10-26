<div class="form">
<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'report-form',
	'enableAjaxValidation'=>false,
	'action' => $this->createUrl('receival/report'),
	'htmlOptions'=>array(
		'class' => 'ifrm-form',
		'target' => $_GET["tabid"].'_ifrm',
	),
));
if(Acl::hasAccess('B:Export/AllDepots')):
?>
<p>Warehouse: <?php echo CHtml::dropDownList('wid', '', Org::dptList(), array('empty' => 'Select One', 'class' => 'required')); ?></p>
<?php
else:
echo '<input type="hidden" name="wid" value="'.Yii::app()->user->org.'" />';
endif; ?>
<p>Date: <input id="receival_report_date" type="text" size="15" name="date" class="date_input" value="<?=date('Y-m-d');?>" /> <input type="submit" value="Submit" /></p>
<?php $this->endWidget(); ?>
</div>
<iframe name="<?=$_GET["tabid"];?>_ifrm" id="<?=$_GET["tabid"];?>_ifrm" src="" border="0" style="display:none;">
</iframe>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('form#report-form', tab.data('panel')).validate();
});
</script>