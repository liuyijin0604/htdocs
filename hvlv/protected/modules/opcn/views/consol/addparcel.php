<h1><?=$this->t('Add Parcel');?></h1>

<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-addparcel-form',
	'enableAjaxValidation'=>false,
));
?>
	<div class="row">
	<?php
	echo CHtml::label('Shipments','recs');
	$m = new ImParcel('search');
	if(isset($_GET['ImParcel'])) $m->attributes=$_GET['ImParcel'];
	$m->type = 10;
	$m->status = 25;
	$m->odpt_id = $model->dpt_id;
	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'con-man-grid',
	'cssFile' => false,
	'dataProvider' => $m->search(100),
	'filter' => $m,
	'summaryText' => false,
	'columns'=>array(
		array('header' => '<input type="checkbox" id="chkbox_all" name="recs_all" checked="checked" />', 'type' => 'raw', 'value' => '"<input class=\"chkbox\" type=\"checkbox\" name=\"recs[]\" value=\"".$data->id."\" checked />"', 'htmlOptions' => array('align' => 'center')),
		array('header' => 'AWB', 'name' => 'hbn'),
		array('header' => 'Client', 'name' => 'client'),
		array('header' => 'Goods', 'name' => 'goods'),
		array('header' => 'Packages', 'name' => 'pkg'),
		array('header' => 'Value', 'name' => 'dvalue'),
		array('header' => 'Weight', 'name' => 'weight'),
		array('header' => 'State', 'name' => 'state'),
		array('header' => 'Post Code', 'name' => 'postcode'),
		array('header' => 'Created', 'name' => 'created'),
	),
)); ?>
</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Add'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET['tabid'];?>');
	
	$('form#consol-addparcel-form', win).on('success', function(r){
		win.data('opener').trigger('update-parcels-grid');
		win.jqmHide();
	});
	
	win.off('click').on('click', 'input#chkbox_all', function(r){
		$('input.chkbox', win).attr('checked', this.checked);
	});
});
</script>