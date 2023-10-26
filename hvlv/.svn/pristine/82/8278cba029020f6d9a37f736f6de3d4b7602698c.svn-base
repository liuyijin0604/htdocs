<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'wms-item-form',
	'enableAjaxValidation'=>false,
)); ?>
<?php
$fn = ['pl' => 'Plt Code', 'gn' => 'Product', 'cq' => 'Carton', 'uq' => 'Qty', 'ex' => 'Expiry', 'bn' => 'Batch #', 'nt' => 'Notes'];
$f = [];
switch($model->task->type){
	case 1010: //Pallets In
	case 1020: //Bulk In
	case 1030: //Container Unload
	$f = ['pl', 'gn', 'cq', 'uq', 'ex', 'bn', 'nt'];
	break;
	case 2030: //Container Load
	case 2040: //Pack PMC
	case 3010: //Pick Pallet
	case 3020: //Pick Carton
	case 3030: //Pick Unit
	$f = ['pl'];
	break;
	case 3210:
	break;
	case 2110: //pick up
	break;
	case 2120: //delivery
	break;
	case 4010: //Stock Take
	case 4030: //Stock Discard
	break;
}

foreach($f as $k){
	echo '<div class="row"><label>'.$fn[$k].'</label>';
	if($k == 'gn'){
		echo '<input type="hidden" name="mdata[gi]" value="'.(empty($model->mdata['gi'])? '' : $model->mdata['gi']).'" />';
	}elseif($k == 'sn'){
		echo '<input type="hidden" name="mdata[si]" value="'.(empty($model->mdata['si'])? '' : $model->mdata['si']).'" />';
	}elseif($k == 'pl'){
		if(empty($model->mdata['pl']) && !empty($model->mdata['pli'])){
			$l = WmsLocation::model()->findByPk($model->mdata['pli']);
			if($l) $model->mdata['pl'] = $l->code;
		}
	}
	$v = empty($model->mdata[$k])? '' : $model->mdata[$k];
	echo '<input type="text" class="in_'.$k.'" name="mdata['.$k.']" value="'.$v.'" size="40" />';
	if ($k == 'pl') {
		$l = WmsLocation::model()->find('code = :code', array(':code' => $model->mdata['pl']));
		echo ' <a target="_blank" href="' . $this->createUrl('wmsLocation/label', ['id' => $l->id]) . '"><div class="icon" style="background-position:-128px -576px"></div> Print Label</a>';
	}
	echo '</div>';
}

?>
<div class="row buttons">
	<?php echo CHtml::submitButton($this->t('Save')); ?>
</div>
<?php $this->endWidget(); ?>
</div><!-- form -->
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('#wms-item-form', win).on('success', function(){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});

	$(win).on('focus', 'input.in_gn', function(){
		var me = $(this);
		if(me.data('acinit') == 1) return;
		me.autocomplete({
			'source': 'wmsProd/suggest?oid=<?=$model->task->job->org_id;?>',
			'showAnim': 'fold',
			'minLength': 2,
			'delay': 200,
			'select': function(event, ui){
				$(this).val(ui.item.label).prevAll("input[type=hidden]").val(ui.item.value).data("ov",ui.item.value);
				return false;
			},
			'response': function(evt, ui){
				if(ui.content.length == 1){
					ui.item = ui.content[0];
					$(this).val(ui.label).prevAll("input[type=hidden]").val(ui.value).data("ov",ui.value);
				}else if(ui.content.length == 0){
					$(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov"));
				}
				return false;
			},
		}).data('acinit', 1);
	});
});
</script>