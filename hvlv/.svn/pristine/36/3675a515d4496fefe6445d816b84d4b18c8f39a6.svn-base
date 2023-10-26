<h1><?=$this->t('Update Payment');?></h1>

<?php
if (!empty($model->mdata['exrate'])) {
	$rate_val = '';
	foreach ($model->mdata['exrate'] as $currency => $rate) {
		$rate_val .= $currency . ' ' . floatval($rate) . ' ';
	}
} else if (!empty($model->mdata['rate'])) {
	$rate_val = floatval($model->mdata['rate']);
} else {
	$rate_val = 1;
}
?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		array('name' => 'client', 'value' => $model->cust->name),
		'date',
		array('name' => 'status', 'value' => $model->getStatus()),
		array('name' => 'bank', 'value' => $model->getBank()),
		array('name' => 'amount', 'value' => $model->getCurrency().' '.$model->amount),
		array('name' => 'diff', 'value' => $model->getCurrency().' '.(!empty($model->mdata['diff']) ? $model->mdata['diff'] : '0.00')),
		array('name' => 'rate', 'value' => $rate_val),
		array('name' => 'ata', 'value' => $model->getCurrency().' '.$model->ata),
		array('name' => 'type', 'value' => $model->getType()),
		'ref',
		'note',
	),
));
?>

<?php if (Yii::app()->name != 'PEP' && $model->ata > 0) { ?>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'payment-update-form',
	'enableAjaxValidation'=>false,
)); ?>

	<!--div class="row rowcol">
		<?php echo $form->labelEx($model,'rate'); ?>
		<?php echo CHtml::textField('rate', (!empty($model->mdata['rate']) ? $model->mdata['rate'] : 1), array('size'=>12,'maxlength'=>12)); ?>
	</div-->

		<div class="row rowcol">
			<?php echo $form->labelEx($model, 'refund'); ?>
			<?php echo $form->textField($model, 'refund', array('size'=>12,'maxlength'=>12)); ?>
		</div>

		<div class="row rowcol">
			<?php echo $form->labelEx($model, 'diff'); ?>
			<?php echo $form->textField($model, 'mdata[diff]', array('size'=>12,'maxlength'=>12)); ?>
		</div>

	<!-- <div class="row">
		<?php echo CHtml::label('AUD Amount', 'AUD Amount'); ?>
		<?php echo '<span id="aud">' . number_format(round($model->amount / (!empty($model->mdata['rate']) ? $model->mdata['rate'] : 1) * 100) / 100, 2, '.', '') . '</span>'; ?>
	</div> -->

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>
<?php } ?>

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
if ($model->status < Payment::PAYMENT_STATUS_DELETED && $model->ata == 0) {
	$pays = new PayInv;
	$pays->unsetAttributes();
	if (!empty($_GET['PayInv'])) $pays->attributes = $_GET['PayInv'];
	$pays->pay_id = $model->id;

	echo '<label>Allocations</label>';
	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'alllocation-grid',
		'cssFile' => false,
		'dataProvider'=>$pays->search(),
		'filter'=>$pays,
		'columns'=>array(
			array('name' => 'inv_no', 'value' => '$data->invoice->no'),
			array('name' => 'inv_stat', 'value' => '$data->invoice->getStatus()', 'filter'=>CHtml::dropDownList('PayInv[inv_stat]', $pays->inv_stat, Invoice::$states, array('prompt'=>$this->t('All'))), ),
			array('name' => 'inv_total', 'value' => '$data->invoice->total'),
			array('header' => 'Alloc. Amount', 'name' => 'amount'),
			array(
				'class'=>'oButtonColumn',
				'template'=>'{delete}',
				'afterDelete'=>'function(){ $("#jqmw_'.$_GET["tabid"].' #outinv-grid").yiiGridView("update"); }',
				'buttons'=>array(
					'delete' => array(
						'imageUrl'=>false,
						'visible'=>'true',
						'url' => 'Yii::app()->createUrl("payment/delAlloc", ["id" => $data->pay_id, "inv" => $data->inv_id])',
						'options' => array('class' => 'grid_delete_btn delete_pay_alloc', 'label'=>$this->t('Delete')),
					),
				),
			),
		),
	));
}
if($model->status < Payment::PAYMENT_STATUS_DELETED && $model->ata > 0):
?>
<div class="form">
<?php
if ( $model->ata != $model->amount ) {
    echo '<div><a href="', $this->createUrl('payment/exportAllocs', ['id' => $model->id]), '" target="_blank"> Export Allocations</a></div>';
}

if ( $model->ata != $model->amount ) {
	$pays = new PayInv;
	$pays->unsetAttributes();
	if(!empty($_GET['PayInv'])) $pays->attributes = $_GET['PayInv'];
	$pays->pay_id = $model->id;

	echo '<label>Allocations</label>';
	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'alllocation-grid',
	'cssFile' => false,
	'dataProvider'=>$pays->search(),
	'filter'=>$pays,
	'columns'=>array(
		array('name' => 'inv_no', 'value' => '$data->invoice->no'),
		array('name' => 'inv_stat', 'value' => '$data->invoice->getStatus()', 'filter'=>CHtml::dropDownList('PayInv[inv_stat]', $pays->inv_stat, Invoice::$states, array('prompt'=>$this->t('All'))), ),
		array('name' => 'inv_total', 'value' => '$data->invoice->getCurrency()." ".$data->invoice->total'),
		array('header' => 'Alloc. Amount', 'name' => 'amount', 'value' => '$data->payment->getCurrency()." ".$data->amount / $data->exrate'),
		array('name' => 'exrate', 'value' => 'round($data->exrate * 100) / 100'),
		array('name' => 'transaction_date'),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{delete}',
			'afterDelete'=>'function(){ $("#jqmw_'.$_GET["tabid"].' #outinv-grid").yiiGridView("update"); }',
			'buttons'=>array(
				'delete' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'url' => 'Yii::app()->createUrl("payment/delAlloc", ["id" => $data->pay_id, "inv" => $data->inv_id])',
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

<div id="cral"></div>
<div id="oigc" class="row">

	<div class="row">
	<label>Outstanding Invoices</label>

	<div class="row rowcol rowleft">
		<?php echo $form->label($model, 'allocate date'); ?>
		<?php echo CHtml::textField('alloc_date', '', ['class' => 'date_input']); ?>
	</div>

<?php
$inv = new Invoice('search');
$inv->unsetAttributes();
if(!empty($_GET['Invoice'])) $inv->attributes=$_GET['Invoice'];
$inv->to_id = $model->org_id;
$currency = $model->currency;
$rate = !empty($model->mdata['rate']) ? $model->mdata['rate'] : 1;
$ec = new CDbCriteria;
$ec->condition = "status IN (2,3,7)";

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'outinv-grid',
	'cssFile' => false,
	'dataProvider'=>$inv->search(false, 0, 't.due ASC', $ec),
	'filter'=>$inv,
	'columns'=>array(
		array('name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("invoice/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"'),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('Invoice[status]', $model->status, $this->t(Invoice::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'type', 'value' => '$data->getType()', 
			'filter'=>CHtml::dropDownList('Invoice[type]', $model->type, $this->t(Invoice::$types), array('prompt'=>$this->t('All'))),),
		'due',
		array('name' => 'total', 'value' => '$data->getCurrency()." ".$data->total'),
		array('header' => 'Balance', 'value' => '$data->getBalance(' . @$currency . ',1)'),
		array('header' => 'Allocate', 'type' => 'raw', 'value' => '"<input type=\"text\" class=\"alloc\" data-tot=\"".$data->getBalance(' . @$currency . ',1)."\" name=\"alloc[".$data->id."]\" data-currency=\"".$data->getCurrency()."\" ".($data->currency!=' . @$currency . ' ? "disabled=\"disabled\"" : "")." />"'),
	),
));
?>
	</div>
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
	if ( isset($pay->invoice) ) {
		echo '<p><a href="', $this->createUrl('invoice/print', ['id' => $pay->inv_id, 'bal' => 1]), '" target="_blank">', $pay->invoice->no, '</a>: ', $pay->invoice->getCurrency()." ".AppHelper::money_format('%i', $pay->amount),' ( Total:',$pay->invoice->getCurrency()." ".AppHelper::money_format('%i',  $pay->invoice->total),' , GST:' ,$pay->invoice->getCurrency()." ".AppHelper::money_format('%i',$pay->invoice->gst) ,')', ' ', $pay->transaction_date, '</p>';
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
	echo '<div><a href="', $this->createUrl('payment/exportAllocs', ['id' => $model->id]), '" target="_blank"> Export Allocations</a></div>';

?>
</p>

<script type="text/javascript">
$(function(){
	var win = $("#jqmw_<?=$_GET['tabid'];?>");
	var cmap = <?=json_encode(Invoice::$currencies);?>;
	var invcs = [];
	var bc = 'AUD';
	var rates = <?=json_encode(@$model->mdata['exrate'])?>;
	var ata = Number('<?=$model->ata?>');
	
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
		var currency = $(this).data('currency');
		var t = Number($('#amt_'+currency).val());
		var it = Number($(this).data('tot'));
		var v = Number($(this).val());
		$('input.alloc', win).not(this).each(function(){
			var this_currency = $(this).data('currency');
			var v = Number($(this).val());
			if (this_currency == currency) {
				if(v > 0) t = Math.round((t - v) * 100) / 100;
			} else {
				if(v > 0) t = Math.round((t - v * $('#rate_'+this_currency).val()) * 100) / 100;
			}
		});
		if(v > it) v = it;
		if(t < v) v = t;
		$(this).val(v);
		$('#bal_'+currency).val(Math.max(t-v, 0));

		$('.crate').each(function() {
			$(this).parent().next().next().find('input').val(Math.max((t-v) * $(this).val() / $('#rate_'+currency).val(), 0));
		});
	}).on('dblclick', 'input.alloc', function(){
		var currency = $(this).data('currency');
		var t = Number($('#amt_'+currency).val());
		var it = Number($(this).data('tot'));
		var v = it;
		$('input.alloc', win).not(this).each(function(){
			var this_currency = $(this).data('currency');
			var v = Number($(this).val());
			if (this_currency == currency) {
				if(v > 0) t = Math.round((t - v) * 100) / 100;
			} else {
				if(v > 0) t = Math.round((t - v * $('#rate_'+this_currency).val()) * 100) / 100;
			}
		});
		if(v > it) v = it;
		if(t < v) v = t;
		$(this).val(v);
		$('#bal_'+currency).val(Math.max(t-v, 0));

		$('.crate').each(function() {
			$(this).parent().next().next().find('input').val(Math.max((t-v) * $(this).val() / $('#rate_'+currency).val(), 0));
		});
	});

	$(document).off('click','#alllocation-grid a.grid_delete_btn.delete_pay_alloc');

	$('#payment-form', win).on('success', function(){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});

	$('#payment-update-form', win).on('success', function(){
		win.data('opener').trigger('onOpen');
		$('#outinv-grid', win).yiiGridView('update');
		var amount = '<?=$model->amount?>';
		var rate = $('#rate').val();
		$('#aud').html(Math.round(amount / rate * 100) / 100);
	});

	$('#oigc', win).on('afterUpdate', function(){
		bc = cmap['<?=$model->currency?>'];
		invcs = [bc];
		$('#outinv-grid tbody tr', this).each(function(){
			var c = $($('td', this)[4]).text().substring(0,3);
			$(this).data('currency', c);
			if(!invcs.includes(c)) invcs.push(c);
		});
		$('#cral', win).empty();
		for(var i in invcs){
			if(invcs[i] == bc){
				r = '<div class="row rowcol rowleft"><label>&nbsp;</label><input type="checkbox" class="mccb basec" id="mccb_'+invcs[i]+'" value="'+invcs[i]+'" checked />'+invcs[i]+'</div><div class="row rowcol"><label>Ex. Rate</label><input type="input" class="crate" id="rate_'+invcs[i]+'" size="7" value="1" readonly /></div><div class="row rowcol"><label>Amount</label><input type="input" id="amt_'+invcs[i]+'" class="camt basec" size="10" value="'+ata+'" readonly /></div><div class="row rowcol"><label>Balance</label><input type="input" id="bal_'+invcs[i]+'" class="basebal" class="cbal" size="10" value="'+ata+'" readonly /></div>';
			}else{
				rate = '';
				if (rates && rates[invcs[i]]) {
					rate = rates[invcs[i]];
				}
				r = '<div class="row rowcol rowleft"><input type="checkbox" class="mccb" id="mccb_'+invcs[i]+'" value="'+invcs[i]+'" />'+invcs[i]+'</div><div class="row rowcol"><input type="input" class="crate" id="rate_'+invcs[i]+'" name="exrate['+invcs[i]+']" size="7" value="'+parseFloat(rate)+'" /></div><div class="row rowcol"><input type="input" class="camt" id="amt_'+invcs[i]+'" size="10" value="'+(ata*rate)+'" /></div><div class="row rowcol"><input type="input" id="bal_'+invcs[i]+'" class="cbal" size="10" value="'+(ata*rate)+'" readonly /></div>';
			}
			$('#cral', win).append(r);
		}
		$($('#cral .mccb', win)[0]).trigger('change');
	}).trigger('afterUpdate');

	$('input.camt', win).on('dblclick', function() {
		rate = $(this).parent().prev().find('input.crate').val();
		$(this).val(ata * rate);
		$(this).parent().next().find('input.cbal').val(ata * rate);

		// clear alloc
		$('.alloc', win).each(function() {
			$(this).val('');
		});

		// rollback bal
		$('.basebal', win).each(function() {
			amt = $(this).parent().prev().find('input.camt').val();
			$(this).val(amt);
		});
	}).on('change', function() {
		$(this).parent().prev().find('input.crate').val(ata / $(this).val());
		$(this).parent().next().find('input.cbal').val(ata / $(this).val());

		// clear alloc
		$('.alloc', win).each(function() {
			$(this).val('');
		});

		// rollback bal
		$('.basebal', win).each(function() {
			amt = $(this).parent().prev().find('input.camt').val();
			$(this).val(amt);
		});
	});

	$('input.crate', win).on('dblclick', function() {
		amt = $(this).parent().next().find('input.camt').val();
		$(this).val(amt / ata);
		$(this).parent().next().next().find('input.cbal').val(amt);

		// clear alloc
		$('.alloc', win).each(function() {
			$(this).val('');
		});

		// rollback bal
		$('.basebal', win).each(function() {
			amt = $(this).parent().prev().find('input.camt').val();
			$(this).val(amt);
		});
	}).on('change', function() {
		$(this).parent().next().find('input.camt').val(ata * $(this).val());
		$(this).parent().next().next().find('input.cbal').val(ata * $(this).val());

		// clear alloc
		$('.alloc', win).each(function() {
			$(this).val('');
		});

		// rollback bal
		$('.basebal', win).each(function() {
			amt = $(this).parent().prev().find('input.camt').val();
			$(this).val(amt);
		});
	});

	$('.mccb', win).on('change', function() {
		if ($(this).prop('checked') == true) {
			$('.mccb', win).not(this).each(function() {
				$(this).prop('checked', false);
			});
			var currency = $(this).val();
			$(this).parent().next().next().find('input').trigger('dblclick');

			$('.alloc', win).each(function() {
				if ($(this).data('currency') == currency) {
					$(this).prop('disabled', false);
				} else {
					$(this).prop('disabled', true);
				}
			});
		} else {
			$('.mccb', win).not(this).each(function() {
				$(this).prop('checked', false);
			});
			$('.basec', win).prop('checked', true);
			var currency = $('.basec', win).val();
			$('.basec', win).parent().next().next().find('input').trigger('dblclick');

			$('.alloc', win).each(function() {
				if ($(this).data('currency') == currency) {
					$(this).prop('disabled', false);
				} else {
					$(this).prop('disabled', true);
				}
			});
		}
	});
});
</script>