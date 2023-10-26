<h1><?=$this->t('Update Invoice');?> <?php echo $model->no; ?></h1>


<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		array('name' => 'to_name', 'value' => @$model->cust->name),
		'date',
		'due',
		array('name' => 'status', 'value' => $model->getStatus()),
		array('name' => 'type', 'value' => $model->getType()),
		array('name' => 'total', 'value' => $model->getCurrency().' '.$model->total),
	),
));
?>
<br />
<p>
<?php
if(empty($model->consol_id))
{
	echo "Link to consol:&nbsp;".CHtml::textField('consolNo', '', array('class'=>'form-control')).CHtml::submitButton($this->t('Update Consol'), ['class' => 'link_consol'])."</br>"."</br>";
}

if($model->status == 1){
	if(Acl::hasAccess("D:Invoice/pending_paid"))
	{
		echo CHtml::submitButton($this->t('Paid Pending'), ['class' => 'btn']), ' &nbsp;';
	}
	echo CHtml::submitButton($this->t('Post'), ['class' => 'btn']), ' &nbsp;',
		CHtml::submitButton($this->t('Cancel'), ['class' => 'btn']),' &nbsp;';
}elseif(in_array($model->status, [2,3,7])){
	if(Acl::hasAccess("D:Invoice/pending_paid"))
	{
		echo CHtml::submitButton($this->t('Paid Pending'), ['class' => 'btn']), ' &nbsp;';
	}
	echo CHtml::submitButton($this->t('Wechat'), ['class' => 'btn']), ' &nbsp;',
	CHtml::submitButton($this->t('Alipay'), ['class' => 'btn']), ' &nbsp;';
	if(Yii::app()->name!="TLA")
	{
		echo CHtml::submitButton($this->t('Poli'), ['class' => 'btn']), ' &nbsp;';
	}
	echo CHtml::submitButton($this->t('Paypal'), ['class' => 'btn']), ' &nbsp;';
	echo CHtml::textField('url', '', array('size'=>30,'maxlength'=>30,'class'=>'form-control')), ' &nbsp;';
	if(in_array($model->type, [20, 100]) && User::model()->findByPk(Yii::app()->user->id)->type <= 10){
		echo CHtml::submitButton($this->t('Reissue'), ['class' => 'btn']);
	}
//    if(empty($model->isInvoiceClosed()))
//	echo CHtml::submitButton($this->t('Delete'), ['class' => 'btn']);
}elseif($model->status == 10){
    if(empty($model->isInvoiceClosed())){
	echo CHtml::submitButton($this->t('Undelete'), ['class' => 'btn']);
	if(in_array($model->type, [20, 100]) && $model->mayReissue()){
		echo CHtml::submitButton($this->t('Reissue'), ['class' => 'btn']);
	}
    }
}elseif($model->status == Invoice::INVOICE_STATUS_HOLDING){
   echo CHtml::submitButton($this->t('Pending'), ['class' => 'btn']), ' &nbsp;';
}
if (($model->status == 1 && empty($model->isInvoiceClosed())) || yii::app()->user->grp == 0) {
	echo CHtml::submitButton($this->t('Delete'), ['class' => 'btn']);
}
?>
</p>

<script type="text/javascript">
$(function(){

	var win = $("#jqmw_<?=$_GET['tabid'];?>");
	var panel = $("#<?=$_GET['tabid'];?>").data('panel');
	
	$('input.btn', win).click(function(){
		var act = $(this).val();
		if(window.confirm('Are you sure to '+act+'?')){
			if ($.inArray( act, ['Wechat', 'Alipay', 'Poli', 'Paypal'] ) < 0) {
				$.get('<?php echo $this->createUrl("invoice/btn",["id" => $model->id]);?>?act='+act, function(){
					win.data('opener').trigger('onOpen');
					win.jqmHide();
				});
			} else {
				$.get('<?php echo $this->createUrl("invoice/makesupay",["id" => $model->id]);?>?act='+act, function(e){
					e = JSON.parse(e);
					$('#url').val(e.url);
				});
			}
		}
	});

	$('.link_consol', win).click(function(){
		var act = $(this).val();
		if(window.confirm('Are you sure to link consol?')){
			$.get('<?php echo $this->createUrl("invoice/linkConsol",["id" => $model->id]);?>?consolNo='+$("#consolNo").val(), function(r){
					myApp.alert(r,false);
					$('#invoice-grid', panel).yiiGridView('update');
				});
		}
	});
});
</script>