
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-weight-check-overview-form',
	'enableAjaxValidation'=>true,
));
?>
	<?php echo $form->errorSummary($model); ?>
    <div class="row">
        <div class="rowcol">
        <?php echo  CHtml::label('Owner','for_owner_id'); ?>
        <?php echo $form->hiddenField($model,'owner_id', array('data-ov' => $model->owner_id));
        $acname1 = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name' => $acname1,
            'sourceUrl' => array('org/ownerSuggest'),
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

        <div class="rowcol">
            <?php echo CHtml::label('Exchange Rate(AUD -> RMB)','for_cc'); ?>
            <?php echo $form->textField($model,'exchange_rate', ['size' => '8']);  ?>
        </div>

    </div>
    <div class="row" >
        <div class="row rowcol">
            <?php echo $form->labelEx($model,'channel'); ?>
            <?php echo $form->dropDownList($model, 'channel',  ExChannel::getNames(true)); ?>
        </div>

        <div class="row rowcol">
            <?php echo $form->labelEx($model,'channel_cost'); ?>
            <?php echo $form->textField($model,'channel_cost', ['size' => '8']); ?>
        </div>
        <div class="row rowcol">
            <?php echo $form->labelEx($model,'channel_weight'); ?>
            <?php echo $form->textField($model,'channel_weight', ['size' => '8']); ?>
        </div>
        <div class="row rowcol">
            <?php echo $form->labelEx($model,'unit_cost_kg'); ?>
            <?php echo $form->textField($model,'unit_cost_kg', ['size' => '8']); ?>
        </div>
        <div class="row rowcol">
            <?php echo $form->labelEx($model,'channel_qty'); ?>
            <?php echo $form->textField($model,'channel_qty', ['size' => '8']); ?>
        </div>
        <div class="row rowcol">
            <?php echo $form->labelEx($model,'invoice_revenue'); ?>
            <?php echo $form->textField($model,'invoice_revenue', ['size' => '8']); ?>
        </div>
        <div class="row rowcol">
            <?php echo $form->labelEx($model,'invoice_weight'); ?>
            <?php echo $form->textField($model,'invoice_weight', ['size' => '8']); ?>
        </div>
        <div class="row rowcol">
            <?php echo $form->labelEx($model,'charge_rate_kg'); ?>
            <?php echo $form->textField($model,'charge_rate_kg', ['size' => '8']); ?>
        </div>
        <div class="row rowcol">
            <?php echo $form->labelEx($model,'awb_qty'); ?>
            <?php echo $form->textField($model,'awb_qty', ['size' => '8']); ?>
        </div>
        <div class="row rowcol">
            <?php echo $form->labelEx($model,'qty_var'); ?>
            <?php echo $form->textField($model,'qty_var', ['size' => '8']); ?>
        </div>
        <div class="row rowcol">
            <?php echo $form->labelEx($model,'weight_var'); ?>
            <?php echo $form->textField($model,'weight_var', ['size' => '8']); ?>
        </div>
        <div class="row rowcol">
            <?php echo $form->labelEx($model,'unit_rate_var'); ?>
            <?php echo $form->textField($model,'unit_rate_var', ['size' => '12']); ?>
        </div>

    </div>



    <div class="row" >
        <div class="row rowcol">
            <?php echo $form->labelEx($model,'date'); ?>
            <?php echo $form->textField($model,'date', ['size' => '12', 'class' => 'date_input']); ?>
        </div>

        <div class="row rowcol">
            <?php echo $form->labelEx($model,'due'); ?>
            <?php echo $form->textField($model,'due', ['size' => '12', 'class' => 'date_input']); ?>
        </div>

        <div class="row rowcol">
            <?php echo $form->labelEx($model,'invoice_ref'); ?>
            <?php echo $form->textField($model,'invoice_ref', ['size' => '12']); ?>
        </div>

    </div>

    <div class="row" style="margin-top: 20px;">
        <?php echo CHtml::button('Calculate',['id' => 'btn-calculate']); ?>
        <?php echo CHtml::submitButton('Update',['id' => 'btn-update']) . '&nbsp'; ?>
        <?php
        if ( isset($model->mdata['sync_xero']) && $model->mdata['sync_xero'] == 1  ) {
            echo '<p style="color:#008000;">Already Sync with Xero</p>';
        } else {
            echo CHtml::submitButton('Sync Xero',['id' => 'btn-sync-xero']);
        }
        ?>
    </div>


    <div class="row">
        <?php
        $il = new ConsolWeightCheckLine('search');
        $il->unsetAttributes();
        $il->parent_id = $model->id;
        

        $this->widget('application.extensions.editablegrid.CEditableGridView', array(
            'id' => 'consol-weight-check-grid_'.$_GET["tabid"],
            'cssFile' => false,
            'dataProvider'=> $il->search('t.consol_id, t.id ASC'),
            'filter' => $il,
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
                array('header' => 'Select','name' => 'selected','class' => 'CEditableColumn','type' => 'checkbox' ,
                    'value' => '1'),
                array('header' => 'Consol.#','name' => 'consol_id', 'class' => 'CEditableColumn','type' => 'raw', 'value' => '"<a href=\"ExcoConsol/update/" . (!empty($data->consol)?$data->consol->id:"") . "\" class=\"tab_link\" title=\"" . $data->consol_id . "\">" . $data->consol_id . "</a>"', 'footer' => 'Total:'),
                array('header' => 'Date','name' => 'consol_date', 'class' => 'CEditableColumn'),
                array('header' => 'AWB NO.','name' => 'awb_no', 'class' => 'CEditableColumn'),
                array('header' => 'Invoice Ref.','name' => 'invoice_ref', 'class' => 'CEditableColumn'),
                array('header' => 'AWB Weight','name' => 'awb_weight', 'class' => 'CEditableColumn', 'footer' => $il->getTotal($il->search()->getData(), 'awb_weight')),
                array('header' => 'Invoice Weight','name' => 'invoice_weight', 'class' => 'CEditableColumn', 'footer' => $il->getTotal($il->search()->getData(), 'invoice_weight')),
                array('header' => 'Channel Weight','name' => 'channel_weight', 'class' => 'CEditableColumn', 'footer' => $il->getTotal($il->search()->getData(), 'channel_weight')),
                array('header' => 'Wt. Var', 'name' => 'weight_var', 'footer' => $il->getTotal($il->search()->getData(), 'weight_var')),
                array('header' => 'AWB Qty','name' => 'awb_qty', 'class' => 'CEditableColumn', 'footer' => $il->getTotal($il->search()->getData(), 'awb_qty')),
                array('header' => 'Channel Qty','name' => 'channel_qty', 'class' => 'CEditableColumn', 'footer' => $il->getTotal($il->search()->getData(), 'channel_qty')),
                array('header' => 'Qty Var', 'name' => 'qty_var', 'footer' => $il->getTotal($il->search()->getData(), 'qty_var')),
                array('header' => 'ACCR Clearance', 'name' => 'accr_clearance', 'footer' => $il->getTotal($il->search()->getData(), 'accr_clearance')),
                array('header' => 'Clearance','name' => 'clearance_cost', 'class' => 'CEditableColumn', 'footer' => $il->getTotal($il->search()->getData(), 'clearance_cost', false)),
                array('header' => 'Var', 'name' => 'clearance_var', 'footer' => $il->getTotal($il->search()->getData(), 'clearance_var', false)),
                array('header' => 'ACCR Delivery', 'name' => 'accr_delivery', 'footer' => $il->getTotal($il->search()->getData(), 'accr_delivery')),
                array('header' => 'Delivery','name' => 'delivery_cost', 'class' => 'CEditableColumn', 'footer' => $il->getTotal($il->search()->getData(), 'delivery_cost', false)),
                array('header' => 'Var', 'name' => 'delivery_var', 'footer' => $il->getTotal($il->search()->getData(), 'delivery_var', false)),
                array('header' => 'ACCR Duty', 'name' => 'accr_duty', 'footer' => $il->getTotal($il->search()->getData(), 'accr_duty')),
                array('header' => 'Duty','name' => 'duty', 'class' => 'CEditableColumn', 'footer' => $il->getTotal($il->search()->getData(), 'duty', false)),
                array('header' => 'Var', 'name' => 'duty_var', 'footer' => $il->getTotal($il->search()->getData(), 'duty_var', false)),
                array('header' => 'ACCR Others', 'name' => 'accr_others', 'footer' => $il->getTotal($il->search()->getData(), 'accr_others')),
                array('header' => 'Others','name' => 'others', 'class' => 'CEditableColumn', 'footer' => $il->getTotal($il->search()->getData(), 'others', false)),
                array('header' => 'Var', 'name' => 'others_var', 'footer' => $il->getTotal($il->search()->getData(), 'others_var', false)),
                array('header' => 'Invoice Revenue','name' => 'invoice_revenue','class' => 'CEditableColumn', 'footer' => $il->getTotal($il->search()->getData(), 'invoice_revenue')),
                array('header' => 'Total Cost','name' => 'ttlCost', 'htmlOptions' => ['class' => 'show-total-cost'], 'footer' => $il->getTotal($il->search()->getData(), 'ttlCost', false)),
                array('header' => 'Var', 'name' => 'cost_var', 'footer' => $il->getTotal($il->search()->getData(), 'cost_var', false)),

               // array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save} {delete}'),
            ),
        ));
        ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){

	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
    var oldExchangeRate = <?php echo  json_encode($model->exchange_rate); ?>;

	//bind reload_tab
	tab.off('reload_tab').on('reload_tab', function(){
		var t = $('.ui-tabs', panel);
		t.tabs('load', t.tabs('option','active'));
	});

    $('#btn-sync-xero',panel).click(function(e) {
        var owner_id = $('#ConsolWeightCheck_owner_id',panel).val();
        if ( owner_id <= 0 ) {
            alert('please select one owner');
            e.preventDefault();
            e.stopPropagation();
        }
    });

});
</script>