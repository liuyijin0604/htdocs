<h1><?=$this->t('Update Billing Payment');?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		array('name' => 'client', 'value' => $model->cust->name),
		'date',
		array('name' => 'status', 'value' => $model->getStatus()),
		array('name' => 'bank', 'value' => $model->getBank()),
		array('name' => 'amount', 'value' => $model->getCurrency().' '.$model->amount),
		array('name' => 'diff', 'value' => $model->getCurrency().' '.(!empty($model->mdata['diff']) ? $model->mdata['diff'] : '0.00')),
		array('name' => 'ata', 'value' => $model->getCurrency().' '.$model->ata),
		array('name' => 'type', 'value' => $model->getType()),
		'ref',
		'note',
	),
));
?>
<br />
<h3>Attachment</h3>
<div class="row">
          <?php
          $fr = new FileRepo('search');
          $fr->unsetAttributes();
          if (empty($_GET['FileRepo'])) {
              $fr->status = 20;
              $fr->type = 15;
          } else {
              $fr->attributes = $_GET['FileRepo'];
              if (empty($fr->type))
                  $fr->type = 80;
          }
          $fr->fid = $model->id;
          $mf = Acl::hasAccess('B:org/manageFile');

          $this->widget('zii.widgets.grid.CGridView', array(
              'id' => 'file-credit-note-grid',
              'summaryText' => '',
              'dataProvider' => $fr->search(),
              'filter' => $fr,
              'columns' => array(
                  array('name' => 'name', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->baseUrl."/filerepo/".$data->hash."/".$data->name."\" target=\"_blank\">".$data->name."</a>"'),
                  array(
                      'name' => 'size',
                      'value' => '$data->formatSize()',
                      'filter' => false,
                  ),
                  'date',
              ),
          ));
          ?>
</div>
<div class="row">

            <?php

echo '<br />', CHtml::label($this->t('Upload Files'),'uploader');
$pphash = FileRepo::uploadHash($model, 15);
$this->widget('application.extensions.plupload.PluploadWidget', array(
 'config' => array(
	 'url' => $this->createUrl('filerepo/upload/'.$pphash),
	 'max_file_size' => Yii::app()->params['maxFileSize'],
	 'unique_names' => true,
	 'file_list_height' => 60,
	 'visible_header' => false,
	 'filters' => array(
		  array('title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg'),
	  ),
	 //'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
	 'language' => Yii::app()->language,
	 'max_file_number' => 2,
	 'autostart' => true,
	 'jquery_ui' => false,
	 'reset_after_upload' => true,
 ),
 'callbacks' => array(
	 'FileUploaded' => 'function(up,file,response){$("#'.$_GET["tabid"].'").trigger("reload_excofile_grid");$("#file-credit-note-grid").yiiGridView.update("file-credit-note-grid");}',
 ),
 'id' => $_GET['tabid'].'_excofile2_uploader',
));
echo CHtml::HiddenField('ppupload', $pphash);
?>
   
</div>
<p>
<?php
if($model->status < Payment::PAYMENT_STATUS_DELETED && $model->ata > 0):
?>
<div class="form">
<?php
if ( $model->ata != $model->amount ) {
    echo '<div><a href="', $this->createUrl('paymentBilling/exportAllocs', ['id' => $model->id]), '" target="_blank"> Export Allocations</a></div>';
}

if ( $model->ata != $model->amount ) {
	$pays = new PayBill;
	$pays->unsetAttributes();
	if(!empty($_GET['PayBill'])) $pays->attributes = $_GET['PayBill'];
	$pays->pay_id = $model->id;

	echo '<label>Allocations</label>';
	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'alllocation-grid',
	'cssFile' => false,
	'dataProvider'=>$pays->search(),
	'filter'=>$pays,
	'columns'=>array(
		array('name' => 'billing_no', 'value' => '$data->billing->no'),
		array('name' => 'billing_stat', 'value' => '$data->billing->getStatus()', 'filter'=>CHtml::dropDownList('PayBill[billing_stat]', $pays->billing_stat, Billing::$states, array('prompt'=>$this->t('All'))), ),
		array('name' => 'billing_total', 'value' => '$data->billing->getCurrency()." ".$data->billing->total'),
		array('header' => 'Alloc. Amount', 'name' => 'amount', 'value' => '$data->payment->getCurrency()." ".$data->amount'),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{delete}',
			'afterDelete'=>'function(){ $("#jqmw_'.$_GET["tabid"].' #outbilling-grid").yiiGridView("update"); }',
			'buttons'=>array(
				'delete' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'url' => 'Yii::app()->createUrl("payment/delAlloc", ["id" => $data->pay_id, "billing" => $data->bill_id])',
					'options' => array('class' => 'grid_delete_btn delete_pay_alloc', 'label'=>$this->t('Delete')),
				),
			),
		),
	),
));
}
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'payment-form',
	'enableAjaxValidation'=>false,
)); ?>
	<div class="row">
	<label>Outstanding Billings</label>
<?php
$billing = new Billing('search');
$billing->unsetAttributes();
if(!empty($_GET['Billing'])) $billing->attributes=$_GET['Billing'];
$billing->org_id = $model->org_id;
$ec = new CDbCriteria;
$ec->condition = "status IN (2,3)";

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'outbilling-grid',
	'cssFile' => false,
	'dataProvider'=>$billing->search(false, 0, 't.due ASC', $ec),
	'filter'=>$billing,
	'columns'=>array(
		'billing_cref',
		'no',
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('Billing[status]', $model->status, $this->t(Billing::$states), array('prompt'=>$this->t('All'))),),
		'due',
		array('name' => 'total', 'value' => '$data->getCurrency()." ".$data->total'),
		array('header' => 'Balance', 'value' => '$data->getBalance()'),
		array('header' => 'Allocate', 'type' => 'raw', 'value' => '"<input type=\"text\" class=\"alloc\" data-tot=\"".$data->getBalance()."\" name=\"alloc[".$data->id."]\" />"'),
	),
));
?>
	</div>
	<div class="row">
	<?php echo $form->labelEx($model,'note'); ?>
	<?php echo $form->textArea($model,'note',array('rows'=>3, 'cols'=>40)); ?>
	</div>
<!--
	<div class="row">
		<label><?php echo CHtml::checkbox('post');?> Post Receipt</label>
	</div>
	-->
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Save')); ?>
		<?php echo CHtml::submitButton($this->t('Delete'), ['class' => 'btn']); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<?php
endif;
echo '<br/><hr/>';
foreach($model->pays as $pay){
	if ( isset($pay->billing) ) {
		echo '<p><a href="', $this->createUrl('billing/print', ['id' => $pay->bill_id, 'bal' => 1]), '" target="_blank">', $pay->billing->no, '</a>: ', $pay->payment->getCurrency()." ".AppHelper::money_format('%i', $pay->amount),' ( Total:',$pay->billing->getCurrency()." ".AppHelper::money_format('%i',  $pay->billing->total),' , GST:' ,$pay->billing->getCurrency()." ".AppHelper::money_format('%i',$pay->billing->gst) ,')', '</p>';
	}
}
if($model->status == Payment::PAYMENT_STATUS_PENDING){
	if(Acl::hasAccess("B:payment/approveCreditNote")) echo CHtml::submitButton($this->t('Post'), ['class' => 'btn']), ' &nbsp;';
		echo CHtml::submitButton($this->t('Delete'), ['class' => 'btn']);
}elseif($model->status == Payment::PAYMENT_STATUS_POSTED){
	echo CHtml::submitButton($this->t('Delete'), ['class' => 'btn']);
}elseif($model->status == Payment::PAYMENT_STATUS_DELETED){
	// does not support undelete again
	//echo CHtml::submitButton($this->t('Undelete'), ['class' => 'btn']);
}
	echo '<div><a href="', $this->createUrl('paymentBilling/exportAllocs', ['id' => $model->id]), '" target="_blank"> Export Allocations</a></div>';

?>
</p>

<script type="text/javascript">
$(function(){
	var win = $("#jqmw_<?=$_GET['tabid'];?>");
	
	$('input.btn', win).click(function(){
		var act = $(this).val();
		if(window.confirm('Are you sure to '+act+'?')){
			$.get('<?php echo $this->createUrl("payment/btn",["id" => $model->id]);?>?act='+act, function(){
				win.data('opener').trigger('onOpen');
				win.jqmHide();
			});
		}
		return false;
	});

	$(win).on('change', 'input.alloc', function(){
		var t = Number(<?=$model->ata;?>);
		var it = Number($(this).data('tot'));
		var v = $(this).val();

		$('input.alloc', win).not(this).each(function(){
			var v = Number($(this).val());
			if(v > 0) t = Math.round((t - v) * 100) / 100;
		});

		if(v > it) v = it;
		if(t < v) v = t;

		$(this).val(v);
	}).on('dblclick', 'input.alloc', function(){
		var t = Number(<?=$model->ata;?>);
		var it = Number($(this).data('tot'));
		$(this).val('');
		$('input.alloc', win).each(function(){
			var v = Number($(this).val());
			if(v > 0) t = Math.round((t - v) * 100) / 100;
		});
		if(t <= 0) return;
		if(t >= it) $(this).val(it);
		else $(this).val(t);
	});
	

	$(document).off('click','#alllocation-grid a.grid_delete_btn.delete_pay_alloc');

	$('#payment-form', win).on('success', function(){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});
});
</script>