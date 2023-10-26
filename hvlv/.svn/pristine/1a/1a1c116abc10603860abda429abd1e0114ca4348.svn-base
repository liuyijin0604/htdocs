<h1><?=$this->t('Sort FAQs');?></h1>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'faq-form',
	'enableAjaxValidation'=>false,
)); ?>
<div class="row" style="clear:both">
<label>Category:</label>
<?php
$model=new Faq('search');
echo $model->cateList();
?>
</div>
<div id="qs" style="margin: 20px 10px;"></div>
<div id="qfs"></div>
<div class="row buttons" style="clear:both;">
	<?php echo CHtml::submitButton($this->t('Save'), array('id'=>'save_btn')); ?>
</div>
<?php $this->endWidget(); ?>
</div><!-- form -->
<script type="text/javascript">
$(function(){
var tab = $('#<?=$_GET["tabid"];?>');
var pane = tab.data('panel');

$('#qs', pane).on('mousedown', 'ol', function(){
	if($(this).data('se')) return;
	$(this).sortable({
		disableNesting: 'no-nest',
		forcePlaceholderSize: true,
		helper:	'clone',
		items: 'li',
		maxLevels: 1,
		opacity: .6,
		placeholder: 'placeholder',
		revert: 250,
		tolerance: 'pointer',
	}).data('se', true);
});

var qfs = $('#qfs', pane);
$('#Faq_cid', pane).on('change', function(){
	var c = $(this).val();
	if(c == ''){
		myApp.alert('Please select a category');
		return true;
	}

	$('#qs', pane).empty().load('<?=$this->createUrl('faq/sortlist');?>?id='+$(this).val());
});

$('#faq-form', pane).on('beforeSerialize', function(){
	qfs.empty();
	$('>li', $('#faq_list', pane)).each(function(k, v){
		qfs.append('<input type="hidden" name="sort[]" value="'+$(this).data('id')+'" />');
	});
}).on('success', function(e, r){
	tab.trigger('load');
});
});
</script>
