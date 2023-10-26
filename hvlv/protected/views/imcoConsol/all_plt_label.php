<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'all-pltLabel-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => ['class' => 'ifrm-form', 'target' => '_blank'],
)); ?>
	
	<div class="row">
		<input type="text" name="id" id="id" value="<?php echo $model->id;?>" style="display: none;" />
	</div>
	<div class="row">
		<label>Date(日期)</label>
		<?php echo CHtml::textField('date', date("Y-m-d"), array('size' => 12, 'id' => 'date', 'name' => 'date', 'class' => 'date_input')); ?>
	</div>
	<div class="row">
		<?php
		if($model->service == ImcoConsol::AIRCONSOL){
			echo '
			<label>AWB Nunber</label>
			<input type="text" value="'.substr($model->awb, -5).'" name="awb" size="15" />';
		}
		else{
			echo '
			<label>Cont No.(后五位柜号)</label>
			<input type="text" value="'.substr($model->container_no, -5).'" name="cont_no" size="15" />';
		}
		?>		
	</div>
	<div class="row">
		<label>Notes/(拆柜人大写字母)</label>
		<input type="text" name="notes" size="15" />
	</div>
	<div class="row">
		<?php echo CHtml::checkbox('previous_label', false) . 'previous label';?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Generate')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('form#all-pltLabel-form', win).on('submit', function(r){
		setTimeout(function(){
			win.data('opener').trigger('onOpen');
			win.jqmHide();
		}, 1e3);
	});

});
</script>