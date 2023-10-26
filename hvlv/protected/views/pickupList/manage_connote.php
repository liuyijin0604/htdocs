<h1><?=$this->t('Add Shipment');?></h1>
<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'pl-manage-connotes-form',
	'enableAjaxValidation'=>false,
));
?>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php echo $form->dropDownList($model,'dpt_id',Org::dptList(), array('empty' => 'Select One')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'fwd_id'); ?>
		<?php echo $form->hiddenField($model,'fwd_id', array('data-ov' => $model->fwd_id));
			$acname1 = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => array('org/exAgentSuggest'),
				'value' => empty($model->agent)? '' : $model->agent->name,
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'class' => 'required',
					'size' => '30',
				),
		));
		?>

	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'created'); ?>
		<?php echo $form->textField($model,'created',array('size' => 22, 'class' => 'datetime_input')); ?>
	</div>

<div style="clear:both;"></div>
<div class="row">
<?php
$parcel = new ExParcel('search');
if(isset($_GET['ExParcel'])){
	$parcel->unsetAttributes();
	$parcel->attributes=$_GET['ExParcel'];
}
$parcel->mids = $model->getFids();
if(empty($parcel->mids)) $parcel->mids = [-1];

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'_manage_connotes-grid',
	'cssFile' => false,
	'dataProvider'=>$parcel->search(),
	'filter'=>$parcel,
	'columns'=>array(
		array('header' => '<input type="checkbox" id="chkbox_all" />', 'type' => 'raw', 'value' => '"<input type=\"checkbox\" class=\"chkbox\" name=\"connotes[]\" value=\"".$data->id."\" />"',),
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("exParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
		array('name' => 'agent_name', 'value' => 'empty($data->agent)? "" : $data->agent->shortName(2)',),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('ExParcel[status]', $parcel->status, $this->t(ExParcel::$states), array('prompt'=>$this->t('All'))),),
		'weight',
		array('name' => 'cnor_name', 'value' => 'empty($data->cnor)? "" : $data->cnor->name',),
		array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>array(
				'update' => array(
					'imageUrl'=>false,
					'url' => 'Yii::app()->createURL("exParcel/update", array("id" => $data->id))',
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->hbn'),
				),
			),
		),
	),
));
?>
</div>

	<div class="row buttons">
		<div style="float:right" id="twt"></div>
		<?php echo $form->hiddenField($model,'id'); ?>
		<?php echo CHtml::submitButton('Move'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<div class="search-form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->textArea($parcel,'mhbns',array('cols'=>40, 'rows' => 2)); ?>
		<?php echo CHtml::submitButton($this->t('Search')); ?>
		<p><small>Up to 200 numbers.</small></p>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	
	$('form#pl-manage-connotes-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		$('#<?=$_GET["tabid"];?>_manage_connotes-grid', win).yiiGridView('update');
		//win.jqmHide();
	});

	win.off('click').on('click', 'input#chkbox_all', function(r){
		$('input.chkbox:visible', win).prop('checked', $(this).is(':checked'));
	});

	$('.search-form form', win).on('submit', function(){
		$('#<?=$_GET["tabid"];?>_manage_connotes-grid', win).yiiGridView('update', {data: $('.filters input, .filters select', win).serialize() + '&' + $(this).serialize()});
		return false;
	});
});
</script>