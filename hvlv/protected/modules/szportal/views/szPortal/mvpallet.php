<h2>Move Pallets</h2>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'mvplt-form',
	'enableAjaxValidation'=>false,
));
?>
<div class="row">
<?php
$rs = Manifest::model()->findAll('type = 60 AND consol_id = :cid', [':cid' => $model->id]);
if(!empty($rs)){
	foreach($rs as $m){
		$ids = empty($m)? [] : $m->getFids();
		$c = sizeof($ids);
		$w = $m->totExShipWeight();
		if(empty($c)) continue;
		echo '<label><input type="checkbox" name="pids[]" value="'.$m->id.'"/> Pallet #'.$m->ref.' &nbsp; Count: '.$c.' Weight: '.(round($w*100)/100).'kg</label>';
	}
}

?>
</div>
<div class="row">
	<?php echo CHtml::label('Move to:',''); ?>
	<?php
	$rs = ExcoConsol::model()->findAll('pol = :pol AND poc = :poc AND id != :cid AND status < 60 AND created > DATE_SUB(NOW(), INTERVAL 14 DAY)', [':cid' => $model->id, ':pol' => $model->pol, ':poc' => $model->poc]);
	$dl = ['NEW' => 'New Consol.'];
	foreach($rs as $r){
		$dl[$r->id] = $r->no;
	}
	echo CHtml::dropDownList('cid', '', $dl, array('empty' => 'Select One'));
	?>
</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Move')); ?>
	</div>
<?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('form#mvplt-form', win).on('success', function(e, r){
		win.data('opener').trigger('reload_tab');
		win.jqmHide();
	});

});
</script>