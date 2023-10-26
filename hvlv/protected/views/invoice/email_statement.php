<h1><?=$this->t('Receivables Report');?></h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'email-statement-form',
	'enableAjaxValidation' => false,
	'action' => $this->createUrl('invoice/emailStatments'),
)); ?>
	
	<div class="row">
	<?php
	$model = new Invoice('search');
	$model->unsetAttributes();
	if(!empty($_GET['Invoice']))
		$model->attributes=$_GET['Invoice'];

	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'email-statement-grid',
	'cssFile' => false,
	'dataProvider'=> $model->statementSearch(),
	'filter'=>$model,
	'columns'=>array(
		array('header' => '<input type="checkbox" id="chkbox_all" name="recs_all" checked />', 'type' => 'raw', 'value' => 'empty($data->cust->contactList(1))? "No Email" : "<input class=\"chkbox\" type=\"checkbox\" name=\"ids[]\" value=\"".$data->to_id."\" checked />"', 'htmlOptions' => array('align' => 'center')),
		array('name' => 'to_name', 'type' => 'raw', 'value' => '"<a href=\"org/update/".$data->to_id."\" title=\"Update Org\" class=\"tab_link\">".$data->cust->shortName(3)."</a>"'),
		array('name' => 'dpt_id', 'value' => '$data->getBranch()', 
			'filter'=>CHtml::dropDownList('Invoice[dpt_id]', $model->dpt_id, Org::dptList(), array('prompt'=>$this->t('All'))),),
		array('name' => 'total', 'value' => '$data->getCurrency()." ".$data->statementTotal(false)'),
		array('name' => 'date'),
	),
)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Email')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	win.off('click').on('click', 'input#chkbox_all', function(r){
		$('input.chkbox', win).prop('checked', $(this).is(':checked'));
	});
});
</script>