<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'payment-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<div class="row rowcol rowleft">
		<?php if($model->type==Payment::PAYMENT_TYPE_CREDIT_NOTE):?>
		<?php 
			$checkUrl = "org/ownerSuggest";
			echo $form->labelEx($model,'org_id');
		?>
		<?php else:?>
			<?php $checkUrl = "org/supplierSuggest";?>
			<label for="Payment_org_id" class="required" aria-required="true">Courier <span class="required" aria-required="true">*</span></label>
		<?php endif;?>
		<?php echo $form->hiddenField($model,'org_id', array('data-ov' => $model->org_id));
			$acname1 = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => array($checkUrl),
				'value' => '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]).trigger("change"); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'size' => '30',
				),
		));
		?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'date'); ?>
	<?php echo $form->textField($model,'date', ['class' => 'date_input']); ?>
	<?php echo $form->hiddenField($model,'type', ['class' => 'date_input']); ?>
	</div>
	<div class="row rowcol rowleft">
	<?php echo $form->labelEx($model,'currency'); ?>
	<?php $model->currency = 1; ?>
	<?php echo $form->dropDownList($model, 'currency', $this->t(Invoice::$currencies)); ?>
	</div>
	<div class="row rowcol">
	<?php echo CHtml::label('Consol No.', 'mdata[consol]'); ?>
	<?php echo $form->textField($model, 'mdata[consol]'); ?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'rate'); ?>
		<?php echo CHtml::textField('rate', 1, array('size' => 12, 'maxLength' => 12)); ?>
	</div>
        <div class="row rowcol">
            <?php echo CHtml::label('Department','department'); ?>
	    <?php 
             $department=User::getDeparts();
             if(empty($department)&&Acl::hasAccess("B:payment/approveCreditNote")) $department= Invoice::$dpmts;
             echo CHtml::dropDownList('deparment','',$this->t($department),array('id'=>'department_id')); ?>
        </div>
      <div class="row">
	<?php echo $form->labelEx($model,'ref'); ?>
<?php echo $form->textField($model,'ref',array('size'=>30,'maxlength'=>100)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->label($model, 'allocate date'); ?>
		<?php echo CHtml::textField('alloc_date', '', ['class' => 'date_input']); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->label($model, 'From Invoice Numbers'); ?>
		<?php echo CHtml::textField('from_invoice_id', ''); ?>
		<?php echo CHtml::hiddenField('pass', ''); ?>
	</div>

<!--        <div class="row rowcol">
	<?php echo $form->labelEx($model,'amount'); ?>
        <?php echo $form->textField($model,'amount',array('size'=>12,'maxlength'=>12)); ?>
	</div>-->

        <div class="row">
	<label>Credit Note Detail</label>
	<?php
        $il = new CreditLine('search');
	$il->pid = empty($model->id)? 0 : $model->id;
        
        
	$this->widget('application.extensions.editablegrid.CEditableGridView', array(
		'id'=>'the_invline-grid',
		'cssFile' => false,
		'dataProvider'=>$il->search(),
		'formUrl' => $this->createUrl('payment/linesGrid', array('id'=>empty($model->id)? 0 : $model->id)),
		'summaryText' => '',
		'afterSave' => "function(r){
			if(r.done == true){
				myApp.notice(r.msg, 5000);
			}else{
				myApp.alert(r.msg, false);
			}
			return r.done;
		}",
		'columns'=>array(
		        array('header' => 'Description','name' => 'description', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 40,'class'=>'change-event']),
			array('header' => 'Qty','name' => 'qty', 'class' => 'CEditableColumn','inputOptions' => ['size' => 5,'class'=>'change-event']),
			array('header' => 'Rate','name' => 'rate', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5,'class'=>'change-event']),
                        array('header' => 'Tax Rate', 'name' => 'tax','class' => 'CEditableColumn','type' => 'list',
                    'filter'=> Invoice::$InvoiceRevenueTaxRate,'inputOptions' => ['class'=>'change-event']),

			array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save} {delete}'),
		),
	));
	?>
        <div><b>Total:</b><span id="the_total_value" style="font-weight: bold;"></span></div>
        <div><input type="hidden" name="the_total_value" id="input_the_total_value"/></div>
	</div>
  
	<div class="row">
		<?php if($model->type==Payment::PAYMENT_TYPE_COURIER_CN):?>
		<label>Outstanding Courier Invoice For Si Reconcile</label>
				<?php
			$si = new SiReconcile('search');
			$this->widget('zii.widgets.grid.CGridView', array(
				'id'=>'outinv-grid',
				'cssFile' => false,
				'dataProvider'=>$si->search(),
				'filter'=>$si,
				'columns'=>array(
					['name' => 'inv_no', 'value' => '@$data->parent->inv_no'],
					['name'=>'org_id'],
					['name' => 'status','type'=>'raw', 'value' => '$data->getFullStatus()', 'filter' => SiReconcile::$states],
					['header' => 'error type','value' => '$data->getSubErrorType()'],
					['name' => 'confirm_status','type'=>'raw', 'value' => '$data->getFullConfirmStatus()','filter'=>$si->type==SiReconcile::TYPE_MANUAL?SiReconcile::$broker_confirmed_status:SiReconcile::$search_confirmed_status],
					['name'=>'total'],
					['name'=>'total_gst'],
					['name'=>'total_ex_gst'],
					['name' => 'inv_date', 'value' => '@$data->parent->inv_date'],
					['name'=>'create']
				),
			));
			?>
		<?php else:?>
		<label>Outstanding Invoices</label>
		<?php
			$inv = new Invoice('search');
			if(empty($_GET['invoice_to_id'])){
				$inv->to_id = -1;
			}else{
				$inv->unsetAttributes();
				$rate = isset($_GET['Invoice']['rate']) ? $_GET['Invoice']['rate'] : 1;
				unset($_GET['Invoice']['rate']);
			        if(!empty($_GET['dpt_id'])) $inv->dpmt=$_GET['dpt_id'];
			        $inv->to_id=$_GET['invoice_to_id'];
			        if(!empty($_GET['Invoice'])){
			            $inv->attributes=$_GET['Invoice'];
			        }	
			}
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
						'filter'=>CHtml::dropDownList('Invoice[status]', $model->status, $this->t($model::$states), array('prompt'=>$this->t('All'))),),
					array('name' => 'type', 'value' => '$data->getType()', 
						'filter'=>CHtml::dropDownList('Invoice[type]', $model->type, $this->t($model::$types), array('prompt'=>$this->t('All'))),),
					'due',
					array('name' => 'total', 'value' => '$data->getCurrency()." ".$data->total'),
					array('header' => 'Balance', 'value' => '$data->getBalance(' . @$_GET['invoice_currency_'] . ',' . @$rate . ')'),
					array('header' => 'Allocate', 'type' => 'raw', 'value' => '"<input type=\"text\" class=\"alloc\" data-tot=\"".$data->getBalance(' . @$_GET['invoice_currency_'] . ',' . @$rate . ')."\" name=\"alloc[".$data->id."]\" />"'),
				),
			));
			?>
		<?php endif;?>

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
	 'FileUploaded' => 'function(up,file,response){$("#'.$_GET["tabid"].'").trigger("reload_excofile_grid");}',
 ),
 'id' => $_GET['tabid'].'_excofile_uploader',
));
echo CHtml::HiddenField('ppupload', $pphash);
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
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $("#jqmw_<?=$_GET['tabid'];?>");
	
	win.on('searchInvoice', function(){
		$.fn.yiiGridView.update('outinv-grid', {
			data: {'invoice_to_id': $('#Payment_org_id', win).val(), 'invoice_currency_': $('#Payment_currency', win).val(),'dpt_id':$('#department_id').val(), 'Invoice[rate]' : $('#rate', win).val() }
		});
               refresh();
               return false;
	});

	$('#Payment_currency, #Payment_org_id, #department_id, #rate', win).on('change', function(){
		win.trigger('searchInvoice');
	});

	$('#payment-form', win).data('custom_success', function(data){
        if(data.done === true){
        	win.data('opener').trigger('onOpen');
			win.jqmHide();
			myApp.notice(data.msg, 5000);
		}else{

			if(data.msg=="confirmGen")
			{
				if(confirm("The from invoice "+data.invoice+" already generate credit note "+data.credit_note+", are you sure to generate this credit note?"))
				{
					$('#pass',win).val("1");
					$('#payment-form', win).submit();
				}else
				{
					$('#pass',win).val("");
				}
			}else
			{
				$('#pass',win).val("");
				myApp.alert(data.msg, false);
			}
		}
		$('input[type=submit]', win).attr('disabled', false);
        return false;
    });

         valid();
        function refresh(){
             var total=refreshInvoiceTotal();
             $("#the_total_value").html(total);
             $("#input_the_total_value").val(total);
             valid();
        }
         
      function valid(){
	$(win).off('change').on('change', 'input.alloc', function(){
		var t = Number($('#input_the_total_value', win).val());
                console.log(t);
		var it = Number($(this).data('tot'));
		var v = $(this).val();

		$('input.alloc', win).not(this).each(function(){
			var v = Number($(this).val());
			if(v > 0) t = Math.round((t - v) * 100) / 100;
		});

		if(v > it) v = it;
		if(t < v) v = t;

		$(this).val(v);
	}).off('dblclick').on('dblclick', 'input.alloc', function(){
		var t = Number($('#input_the_total_value', win).val());
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
    }
	  
        $('#the_invline-grid .add_btn').on('click', function(){
		$('#the_invline-grid .items tbody td.empty').parent().remove();
		var r = $(this).parents('tr').clone();
		$('.add_btn', r).remove();
		$('input', r).each(function(){
			var n = $(this).attr('name');
			$(this).attr('name', n+'[]');
		});
        $('select', r).each(function(){
            var n = $(this).attr('name');
            $(this).attr('name', n+'[]');
        });
		$('#the_invline-grid .items tbody').append(r);
		$(this).parents('tr').find('input').val('');
            refresh();
        $('select[name="CreditLine[tax][]"]').off('change').on('change',function(){
               refresh();
        });
  	return false;
	});
      $('#the_invline-grid').on('input','.change-event',function(){
           refresh();
        $('select[name="CreditLine[tax][]"]').change(function(){
              refresh();
        });
    });
       function refreshInvoiceTotal(){
		var r = $('#the_invline-grid .items tbody tr',win);
		var totRevenue = 0;
		$(r).each(function(){
			var qty = $(this).find("input[name='CreditLine[qty][]']").val();
			if ( typeof qty == 'undefined' ) {
				qty = $(this).find("input[name='CreditLine[qty]']").val();
			}
			var rate = $(this).find('input[name="CreditLine[rate][]"]').val();
			if ( typeof rate == 'undefined' ) {
				rate = $(this).find('input[name="CreditLine[rate]"]').val();
			}

			var costAmount = $(this).find('select[name="CreditLine[tax][]"]').val();
			if ( typeof costAmount == 'undefined' ) {
				costAmount = $(this).find('select[name="CreditLine[tax]"]').val();
			}
                        
			qty = parseFloat(qty);
			rate = parseFloat(rate);
			if ( qty > 0 && rate > 0 ) {
				totRevenue += qty * rate;
                                if(costAmount=="OUTPUT"){
                                    totRevenue += qty * rate*0.1;
                                 }
			}

		});
		return totRevenue;
	}

});
</script>