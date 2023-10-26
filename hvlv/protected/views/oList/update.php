<h1><?=$this->t('Update '.$model->name.' List');?></h1>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'olist-form',
	'enableAjaxValidation'=>false,
)); ?>
<div class="row" style="clear:both">
<div style="min-width:300px; float: left;">
<h2>Active Items</h2>
<?php
echo $model->treeList(array('id'=>"active_list", 'class'=>"connectedSortable", 'style'=>"min-height:200px;border: 1px #0c0 solid; padding: 5px 5px 5px 25px;"));
?>
<p>New Item: <input id="new_item" type="text" size="25" /> <input id="add_item" type="button" value="Add" /></p>
</div>
<div style="min-width:300px; float: left;margin-left: 15px;">
<h2>Inactive Items</h2>
<ol id="inactive_list" class="connectedSortable" style="min-height:200px;border: 1px #c00 dashed; padding: 5px 5px 5px 25px;">
<?php
foreach(oList::subList($model->id,0) as $p){
	echo '<li id='.$p->id.'><span>'.$p->item.'</span></li>';
}
?>
</ol>
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
var tab = $('#<?=$_GET["tabid"];?>');
var pane = tab.data('panel');
$("#active_list, #inactive_list", pane).nestedSortable({
	disableNesting: 'no-nest',
	forcePlaceholderSize: true,
	helper:	'clone',
	items: 'li',
	maxLevels: <?=$model->mlvl;?>,
	opacity: .6,
	placeholder: 'placeholder',
	revert: 250,
	tabSize: 25,
	tolerance: 'pointer',
	connectWith: '.connectedSortable',
});
var chkItem = function(v, o){
	if(v == ''){
		myApp.alert('Item name can not be empty!');
		return false;
	}else{
		var m = false
		$('#active_list li', pane).not(o).each(function(){
			if($(this).text() == v){
				m = true;
				return false;
			}
		});
		if(m){
			myApp.alert('Item name already exist!');
			return false;
		}
	}
	return true;
};
$('#new_item', pane).keydown(function(e){
	var code = (e.keyCode ? e.keyCode : e.which);
	if(code == 13) { //Enter keycode
		$('#add_item', pane).trigger('click');
		return false;
	}
});
$('#add_item', pane).click(function(){
	var v = $('#new_item', pane).val();
	if(chkItem(v)){
		$('#active_list', pane).append('<li class="new"><span>'+v+'</span></li>').sortable('refresh');
		$(this).val('');
	}
	return false;
});
$('#active_list', pane).on('dblclick', 'li', function(){
	if($(this).data('edit') == true) return;
	var inp = $('<input type="text" value="'+$(this).text()+'" />').keydown(function(e){
		var code = (e.keyCode ? e.keyCode : e.which);
		if(code == 13){
			var v = $(this).val();
			if(chkItem(v, $(this))) $(this).parent().text(v).parent().data('edit', false);
			return false;
		}
	});
	$(this).data('edit', true).find('span').empty().append(inp);
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
		fvs.append('<input type="hidden" name="'+n+'['+p+']['+id+']" value="'+$('>span', this).text()+'" />');
		if($('>ol', this).length > 0){
			serialList($('>ol', this), n, id);
		}
	});
}
$('#olist-form', pane).on('beforeSerialize', function(){
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
