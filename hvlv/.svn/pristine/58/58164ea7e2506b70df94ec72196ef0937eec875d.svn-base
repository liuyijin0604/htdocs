<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'org-form',
	'enableAjaxValidation'=>false,
)); ?>

<?php 
    $dpt_id=106;
    if (!empty(Yii::app()->user->dpt_id)){
        $dpt_id=Yii::app()->user->dpt_id;
    }

?>
<div class="form">
<div class="row  rowcol rowleft">
    <?php echo CHtml::label('From:','wms_from_date');
     echo CHtml::textField('wms_from_date',empty($model->extra['wms_from_date'])?'':$model->extra['wms_from_date'],array('class'=>'date_input','id'=>'wms_from_date'.$_GET['tabid']));
      ?>
</div>
<div class="row rowcol ">
    <?php echo CHtml::label('To:','wms_to_date');
     echo CHtml::textField('wms_to_date',empty($_GET['to_date'])?date("Y-m-d"):$_GET['to_date'],array('class'=>'date_input','id'=>'wms_to_date'.$_GET['tabid']));
      ?>
</div>
<div class="row rowleft">
    <?php echo CHtml::label('Invoice Type:','invoice_type');
            echo CHtml::radioButtonList('wms_invoice_type', 'wms', array('wms' => 'WMS', 'storage' => 'Storage'), array('separator' => '&nbsp;&nbsp;', 'labelOptions' => array('style' => 'display:inline')));
      ?>
</div>
<div class="row rowleft">
    <?php echo CHtml::label('Branch:','branch');
            echo CHtml::radioButtonList('dpt_id', $dpt_id, array('106' => 'SYD', '218' => 'MEL','530'=>'BNE'), array('separator' => '&nbsp;&nbsp;', 'labelOptions' => array('style' => 'display:inline')));
      ?>
</div>
    
    <div class="row" >
       <?php echo CHtml::submitButton('Create');?>
    </div>
</div>

<?php $this->endWidget(); ?>
<script>
    $(function(){
      var tab=$("#<?=$_GET['tabid']?>");
      var panel=tab.data('panel');
      	$('#storage_charge_type', panel).next().on('dblclick', function(){
		if(window.confirm('Are you sure to override Charge type?')){
			$(this).prev().attr('disabled', false);
		}
	});
    });
</script>