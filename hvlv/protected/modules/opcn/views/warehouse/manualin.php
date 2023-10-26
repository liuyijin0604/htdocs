<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'links' => array(
        'Manual Receive',
    ),
));
?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'scan-form',
	'enableAjaxValidation'=>false,
));
?>
<h1><?=$this->t('Manual Receive');?></h1>
<?php

$o = Org::model()->findByPk(Yii::app()->user->org);
if(sizeof($o->extra['warehouse']) > 1){
	echo '<p>Depot: ',CHtml::dropdownList('dpt_id', '', $o->getWarehouseList(), array('empty' => 'Select One', 'class' => 'required')), '</p>';
}else{
	echo CHtml::hiddenField('dpt_id', $o->extra['warehouse'][0]);
}

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'parcel-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('header' => '<input type="checkbox" id="chkbox_all" name="recs_all" />', 'type' => 'raw', 'value' => '$data->status<30? "<input class=\"chkbox\" type=\"checkbox\" name=\"labels[]\" value=\"".$data->id."\" />" : ""', 'htmlOptions' => array('align' => 'center')),
		array('name' => 'hbn', 'header' => 'AWB'),
		array('header' => 'Status', 'type'=>'raw', 'value' => 'ImParcel::getImStatus($data->status)',
			'filter'=>CHtml::dropDownList('ImParcel[status]', $model->status, $this->tarray(ImParcel::$states), array('prompt'=>$this->t('All'))),),
		
		array('name' => 'postcode', 'header' => 'Post Code',),
		array('name' => 'pkg', 'header' => 'Packages',),
		array('name' => 'weight', 'header' => 'Weight',),
		array('name' => 'dvalue', 'header' => 'Value',),
	//	array('name' => 'client', 'header' => 'Client', 'visible' => $this->hasRole('AgentManager', 'AgentOperator')),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>array(
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'$data->status < 30',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->hbn'),
				),
			),
		),
	),
));
?>

<div class="row buttons" style="margin-left: 0px;margin-right: 0px;">
	<?php echo CHtml::submitButton('Received'); ?>
</div>
<?php $this->endWidget(); ?>

<?php ob_start(); ?>

<script type="text/javascript">
$(function(){

	$('#scan-form').on('submit', function(e){
		if($('input.chkbox:checked').length == 0){
			alert('Please select parcel(s) received.');
			return false;
		}
        var dptId = parseInt($('#dpt_id').val());
        if ( isNaN(dptId) ) {
            alert('Please select one warehouse firstly.');
            return false;
        }

        if ( window.confirm('Are you sure selected parcels are received?') ) {
            return true;
        } else {
            return false;
        }

		return false;

	}).on('success', function(e,r){
		$('#parcel-grid').yiiGridView('update');
		return true;
	});

	$('#parcel-grid').yiiGridView('update');

     $('#scan-form').on('click','input#chkbox_all',function(r){
        $('input.chkbox').prop('checked', $(this).prop('checked'));
     });


});
</script>
<?php $this->registerJS(ob_get_clean(),8); ?>
