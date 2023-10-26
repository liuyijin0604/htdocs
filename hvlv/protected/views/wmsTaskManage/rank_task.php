<h1><?=$operator->op->name?></h1>
<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'wms-task-rank-form',
	'enableAjaxValidation' => false,
)); ?>
<div class="row" style="clear:both">
<div style="min-width: 100%; float: left;">
<?php
echo $operator->treeList(array('id' => 'active_list', 'class' => 'connectedSortable', 'style' => 'min-height: 500px; border: 1px #0c0 solid; padding: 5px 5px 5px 40px; font-size: 20px'));
?>
</div>
</div>
<div id="fvs"></div>
<div class="row buttons" style="clear:both">
	<?php echo CHtml::submitButton($this->t('Save'), array('id'=>'save_btn')); ?>
</div>
<?php $this->endWidget(); ?>
</div><!-- form -->
<script type="text/javascript">
$(function(){
var tab = $('#jqmw_<?=$_GET["tabid"];?>');
var pane = tab.data('panel');
$("#active_list, #inactive_list", pane).nestedSortable({
	disableNesting: 'no-nest',
	forcePlaceholderSize: true,
	helper:	'clone',
	items: 'li',
	maxLevels: 1,
	opacity: .6,
	placeholder: 'placeholder',
	revert: 250,
	tabSize: 25,
	tolerance: 'pointer',
	connectWith: '.connectedSortable',
});
var err = false;
var fvs = $('#fvs', pane);
var serialList = function(l,n,p){
	$('>li',l).each(function(k, v){
		if($(this).data('edit') == true) {
			err = true;
			myApp.alert('Please finish editing first!');
		}
		id = $(this).attr('id') || 'new_'+p+'_'+k;
		fvs.append('<input type="hidden" name="'+n+'['+k+']" value="'+id+'" />');
		if($('>ol', this).length > 0){
			serialList($('>ol', this), n, id);
		}
	});
}
$('#wms-task-rank-form', pane).on('beforeSerialize', function(){
	err = false;
	fvs.empty();
	serialList($('#active_list', pane), 'active', 0);
	serialList($('#inactive_list', pane), 'inactive', 0);
	if(!err) $('#save_btn', pane).attr('disabled', true);
	return !err;
}).on('success', function(e, r){
	tab.trigger('load');
});
});
</script>
