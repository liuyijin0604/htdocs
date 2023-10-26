<div class="form">
<div style="right: 20px;text-align:right;position:absolute;">
<?php if (in_array($model->type, [1010, 1020])) { ?>
	<a href="<?=$this->createUrl('wmsTask/print', array('id' => $model->id, 't' => 'ticket'));?>" target="_blank"><div style="background-position: -128px -576px" class="icon"></div> Stockin Ticket</a>
<?php } ?>
</div>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'wms-task-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model->job, 'cust'); ?>
		<?php echo '<b>' . $model->job->customer->name . '</b>'; ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'ref'); ?>
<?php echo $form->textField($model,'ref',['size' => 25]); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('AWBN', 'awbn'); ?>
		<?php echo CHtml::textField('meta[awbn]', @$model->mdata['awbn'], ['size' => 25]); ?>
	</div>

	<?php if (!empty($model->batch)) { ?>
		<div class="row rowcol">
			<?php echo CHtml::label('Batch', 'batch'); ?>
			<?php echo '<b>' . $model->batch->getNo() . ' / ' . $model->batch->box_number . '</b>'; ?>
		</div>
	<?php } ?>

	<?php if (!empty($model->mdata['ct'])) { ?>
		<div class="row rowcol">
			<?php echo CHtml::label('Bulk Task no.', 'ct'); ?>
			<?php echo CHtml::textField('ct', @end($model->mdata['ct']), array('readOnly' => 'readOnly')); ?>
		</div>
	<?php } ?>

	<?php if (!empty($model->mdata['intaskid'])) { ?>
		<div class="row rowcol">
			<?php echo CHtml::label('In Task', 'in'); ?>
			<?php
				$no = WmsTask::model()->findByPk($model->mdata['intaskid'])->getNo();
				echo '<a class="tab_link" href="/wmsTask/update/' . $model->mdata['intaskid'] . '" title="' . $no . '">' . $no . '</a>';
			?>
		</div>
	<?php } ?>

	<?php if (!empty($model->mdata['outtaskid'])) { ?>
		<div class="row rowcol">
			<?php echo CHtml::label('Out Task', 'out'); ?>
			<?php
				foreach ($model->mdata['outtaskid'] as $id) {
					$no = WmsTask::model()->findByPk($id)->getNo();
					echo '<a class="tab_link" href="/wmsTask/update/' . $id . '" title="' . $no . '">' . $no . '</a>&nbsp;&nbsp;';
				}
			?>
		</div>
	<?php } ?>

	<?php if (!empty($model->mdata['restock'])) { ?>
		<div class="row rowcol">
			<?php echo CHtml::label('Cancel Restock Task', 'restock'); ?>
			<?php
				$no = WmsTask::model()->findByPk($model->mdata['restock'])->getNo();
				echo '<a class="tab_link" href="/wmsTask/update/' . $model->mdata['restock'] . '" title="' . $no . '">' . $no . '</a>&nbsp;&nbsp;';
			?>
		</div>
	<?php } ?>

	<?php
	$origin = WmsTask::model()->find('JSON_VALUE(meta, "$.restock") = :id', [':id' => $model->id]);
	if ($origin) { ?>
		<div class="row rowcol">
			<?php echo CHtml::label('Cancelled Task', 'origin'); ?>
			<?php
				echo '<a class="tab_link" href="/wmsTask/update/' . $origin->id . '" title="' . $origin->no . '">' . $origin->no . '</a>&nbsp;&nbsp;';
			?>
		</div>
	<?php } ?>

	<?php if (!empty($model->edijobs)) { ?>
		<div class="row rowcol">
			<?php echo CHtml::label('Edi Job', 'edijob'); ?>
			<?php echo $model->getEdiJob(); ?>
		</div>
	<?php } ?>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php if ($model->type == 6010) $states = WmsTask::$states_adhoc;
		else $states = WmsTask::$states; ?>
		<?php echo $form->dropDownList($model, 'status', $this->t($states), array('empty' => $this->t('Select One'))); ?>
	</div>

	<?php if ($model->type != 6010 && in_array($model->job->customer->id, Org::$easyships)) { ?>
	<div class="row rowcol">
		<?php echo CHtml::label('Easyship Status', 'easy_shipstatus'); ?>
		<?php echo $model->getStatus(); ?>
	</div>
	<?php } ?>

	<div class="row rowcol">
		<?php if (empty($model->dpt_id)) {
			$user = User::model()->findByPk(Yii::app()->user->id);
			if (!empty($user) && in_array($user->dpt_id, array_keys(Org::dptList3PL()))) {
				$model->dpt_id = $user->dpt_id;
			} else {
				$model->dpt_id = 106;
			}
		} ?>

		<?php echo $form->labelEx($model, 'dpt_id'); ?>

		<?php if ($model->isNewRecord) {
			echo $form->dropDownList($model, 'dpt_id', Org::dptList3PL(), ['empty' => 'Select One', 'disabled' => 'disabled']);
		} else {
			echo $form->dropDownList($model, 'dpt_id', Org::dptList3PL(), ['empty' => 'Select One', 'disabled' => 'disabled']);
		} ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'op_id'); ?>
	<?php
	if ($model->type == WmsTask::TYPE_ADHOC_TASK) {
		$model->op_id = $model->op_id ? $model->op_id : Yii::app()->user->id;
		$op_name = User::model()->findByPk(Yii::app()->user->id)->getName();
	} else {
		$op_name = '';
	} ?>
		<?php echo $form->hiddenField($model,'op_id');
			$acname = empty($_GET["tabid"])? 'op_ac' : $_GET["tabid"].'_op_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('user/suggest'),
				'value' => empty($model->op) ? $op_name : $model->op->getName(),
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
				),
		));
		?>
	</div>

	<?php if (in_array($model->type, [2030, 3010])) { ?>
		<div class="row rowcol">
			<?php echo CHtml::checkbox('mdata[pcae_arrange]', !empty($model->mdata['pcae_arrange'])), $this->t(' <b>PCAE Arrange</b>'); ?>
		</div>
	<?php } ?>

	<?php if ($model->type == WmsTask::TYPE_ADHOC_TASK) { ?>
	<div class="row rowcol">
	<?php echo CHtml::label('Process', 'mdata[proc_id]'); ?>
		<?php echo $form->hiddenField($model,'mdata[proc_id]');
			$acname = empty($_GET["tabid"])? 'proc_ac' : $_GET["tabid"].'_proc_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('user/suggest'),
				'value' => empty($model->mdata['proc_id']) ? '' : User::model()->findByPk($model->mdata['proc_id'])->getName(),
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
				),
		));
		?>
	</div>

	<div class="row rowcol">
	<?php echo CHtml::label('QA', 'mdata[qa_id]'); ?>
		<?php echo $form->hiddenField($model,'mdata[qa_id]');
			$acname = empty($_GET["tabid"])? 'qa_ac' : $_GET["tabid"].'_qa_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('user/suggest'),
				'value' => empty($model->mdata['qa_id']) ? '' : User::model()->findByPk($model->mdata['qa_id'])->getName(),
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
				),
		));
		?>
	</div>
	<?php } ?>

	<div class="row rowcol rowleft">
	<?php echo $form->labelEx($model,'due_time'); ?>
<?php echo $form->textField($model,'due_time',['class' => 'datetime_input', 'id' => $_GET['tabid'].'_dt_due']); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'schd_time'); ?>
<?php echo $form->textField($model,'schd_time',['class' => 'datetime_input', 'id' => $_GET['tabid'].'_dt_schd']); ?>
	</div>

	<div class="row rowcol rowleft">
	<?php echo $form->labelEx($model,'start_time'); ?>
<?php echo $form->textField($model,'start_time',['class' => 'datetime_input', 'id' => $_GET['tabid'].'_dt_start']); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'compl_time'); ?>
<?php echo $form->textField($model,'compl_time',['class' => 'datetime_input', 'id' => $_GET['tabid'].'_dt_comp']); ?>
	</div>

<?php
switch($model->type){
	case 1010: //Pallets In
	case 1020: //Bulk In
	case 1030: //Container Unload
		if($model->type == 1010) echo $this->renderPartial('_form_extra_1010', array('model'=>$model));
		if($model->type == 1030) echo $this->renderPartial('_form_extra_1030', array('model'=>$model));
		echo $this->renderPartial('_form_extra_10', array('model'=>$model));
	break;
	case 2030: //Container Load
	case 2040: //Pack PMC
	case 3010: //Pick Pallet
	case 3020: //Pick Carton
	case 3030: //Pick Unit
	case 5010: //CG Delivery
		if($model->type == 2030) echo $this->renderPartial('_form_extra_2030', array('model'=>$model));
		if ($model->type == 2040) echo $this->renderPartial('_form_extra_2040', array('model' => $model));
		echo $this->renderPartial('_form_extra_30', array('model'=>$model));
	break;
	case 4010: //Stock Take
	case 4030: //Stock Discard
	break;
	case 7010:
		echo $this->renderPartial('_form_extra_70', array('model' => $model));
	break;
}
?>

	
	<div class="row rowcol rowleft">
		<label>Instruction</label>
		<?php echo CHtml::textArea('meta[note]', @$model->mdata['note'], array('style' => 'width:400px; height:100px;')); ?>
	</div>
	<?php if ($model->dpt_id == 106) { ?>
		<div class="row rowcol rowleft">
			<label>FBA ID</label>
			<?php echo CHtml::textField('meta[fba_id]',$model->mdata['fba_id']?$model->mdata['fba_id']:''); ?>
		</div>
		<div class="row rowcol">
			<label>FBA PO</label>
			<?php echo CHtml::textField('meta[fba_po]',@$model->mdata['fba_po']?$model->mdata['fba_po']:''); ?>
		</div>
		<div class="row rowcol rowleft">
			<label>Name of Items Action</label>
			<?php echo CHtml::dropDownList('itemsaction[option]', '', WmsTask::$itemsAction_Options,array('multiple' => 'multiple', 'seize' => 3,'style' => 'width:300px; height:100px;')); ?>
		</div>
		<div class="row rowcol">
			<label>&nbspNum of Items Action</label>
			<?php echo " x ".CHtml::textField('meta[numItemsAction]',$model->mdata['numItemsAction']?$model->mdata['numItemsAction']:'0'); ?>
		</div>
		<div class="row rowcol">
			<label>&nbspItems Fee</label>
			<?php echo " = ".CHtml::textField('meta[totalItemsActionFee]',@$model->mdata['totalItemsActionFee']); ?>
		</div>
		<div class="row rowcol">
			<label>&nbspItems Action Name</label>
			<?php echo CHtml::textField('meta[ItemsActionName]',@$model->mdata['ItemsActionName']); ?>
		</div>	
		<div class="row rowcol rowleft">
			<label>Name of Pallet Action</label>
			<?php echo CHtml::dropDownList('palletsaction[option]', '', WmsTask::$palletsAction_Options,array('multiple' => 'multiple', 'seize' => 3,'style' => 'width:300px; height:100px;')); ?>
		</div>
		<div class="row rowcol">
			<label>&nbspNum of Pallet Action</label>
			<?php echo " x ".CHtml::textField('meta[numPalletsAction]',$model->mdata['numPalletsAction']?$model->mdata['numPalletsAction']:'0'); ?>
		</div>
		<div class="row rowcol">
			<label>&nbspPallets Fee</label>
			<?php echo " = ".CHtml::textField('meta[totalPalletsActionFee]',@$model->mdata['totalPalletsActionFee']); ?>
		</div>
		<div class="row rowcol">
			<label>&nbspPallets Action Name</label>
			<?php echo CHtml::textField('meta[PalletsActionName]',@$model->mdata['PalletsActionName']); ?>
		</div>	
	<?php } ?>
	<?php if (in_array(Yii::app()->user->id, WmsTask::$op) || (isset(Yii::app()->user->grp) && Yii::app()->user->grp == 0)) { ?>
		<div class="row">
			<?php echo CHtml::label('No WMS Invoice', 'noinv'); ?>
			<?php echo CHtml::checkbox('noinv', $model->bwf & 4); ?>
		</div>
	<?php } ?>
	<div class="row buttons">
		<?php echo CHtml::hiddenField('meta_items'); ?>
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save'), ['name' => 'act_btn']); ?>
		<?php if (!$model->isNewRecord && $model->status == 10 && in_array($model->job->org_id, Org::$easyships)) {
			echo CHtml::submitButton('Short Release', ['name' => 'act_btn']);
		} ?>
		<?php if (!$model->isNewRecord && $model->type == 1010 && ($model->bwf & 512) > 0 && empty($model->mdata['confirmed_diff'])) {
			echo CHtml::submitButton('Confirm Diff', ['name' => 'act_btn']);
		} ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
$('#itemsaction_option').dblclick(function() 
{ 
    var selectedValue = parseInt(jQuery(this).val());
	var enterValue = parseInt($("#meta_numItemsAction").val());

    switch(selectedValue){
        case 10:
			var text= "Wrapping Combine Carton";
			var floatFee= 0.2*enterValue;
            break;
        case 20:
            var text= "Combine Carton";
			var floatFee= 0.2*enterValue;
            break;
		case 30:
            var text= "Pick Accessories from Carton";
			var floatFee= 0.2*enterValue;
            break;
		case 40:
        	var text= "FBA label";
			var floatFee= 1.0*enterValue;
            break;
		case 50:
        	var text= "Check Carton";
			var floatFee= 0.8*enterValue;
        break;
        default:
            alert("catch default");
            break;
    }
	var textarea = $("#meta_note").val();
	textarea = textarea +text+ '\n';
	$("#meta_note").val(textarea);

	var textFee = Number($("#meta_totalItemsActionFee").val());
	textFee = textFee+floatFee;
	$("#meta_totalItemsActionFee").val(textFee.toFixed(2));
});
</script>
<script type="text/javascript">
$('#palletsaction_option').dblclick(function() 
{ 
    var selectedValue = parseInt(jQuery(this).val());
	var enterValue = parseInt($("#meta_numPalletsAction").val());

    switch(selectedValue){
        case 10:
			var text= "Palletize";
			var floatFee= 25.0*enterValue;
            break;
        case 20:
            var text= "Wrap";
			var floatFee= 5.0*enterValue;
            break;
        default:
            alert("catch default");
            break;
    }
	var textarea = $("#meta_note").val();
	textarea = textarea +text+ '\n';
	$("#meta_note").val(textarea);

	var textFee = Number($("#meta_totalPalletsActionFee").val());
	textFee = textFee+floatFee;
	$("#meta_totalPalletsActionFee").val(textFee.toFixed(2));
});
</script>
<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	var t = $('#mtsk_itms', panel);
	
	for(var i = 0; i < 3; i++) t.trigger('addLine');

	$('#wms-task-form', panel).on({
		'success': function(e, r){
			<?php if ($model->isNewRecord) { ?>
			myApp.tabs.CreateTab({
				title: 'T-'+r.id,
				url: tab.data('url').replace(/\/create.+/, '/update/'+r.id),
			});
			tab.trigger('close');
			<?php } else { ?>
			tab.trigger('reload_tab');
			<?php } ?>
			$('tbody input', t).prop('disabled', false);
		},
		'error': function(e, r){
			<?php if ($model->isNewRecord) { ?>
			myApp.tabs.CreateTab({
				title: 'T-'+r.id,
				url: tab.data('url').replace(/\/create.+/,'/update/'+r.id),
			});
			tab.trigger('close');
			<?php } else { ?>
			tab.trigger('reload_tab');
			<?php } ?>
			$('tbody input', t).prop('disabled', false);
		}
	}).on('beforeSerialize', function(){
		var ia = [];
		$('tbody tr', t).each(function(){
			var o = {};
			$('input', this).each(function(){
				o[$(this).attr('name')] = $(this).val();
			});
			ia.push(o);
		});
		$('#meta_items', this).val(JSON.stringify(ia));
		$('tbody input', t).prop('disabled', true);
		return true;
	});
	var item_data = <?=json_encode($model->getItemsArray());?>;
	var e = item_data.length - $('tbody tr', t).length;
	if(e > 0) for(var c = 0; c < e; c++) t.trigger('addLine');
	$('tbody tr', t).each(function(i){
		for(p in item_data[i]){
			$('input.in_'+p, this).val(item_data[i][p]);
			if (p === 'sn') {
				if (!item_data[i]['si'] || item_data[i]['si'] == 0) {
					$('input.in_'+p, this).css('background-color', 'rgba(255,0,0,0.5)');
				} else if (item_data[i]['meb']) {
					$('input.in_'+p, this).css('background-color', 'rgba(255, 96, 0, 0.5)');
				}
			}
		}
	});
	t.trigger('calcTot');

	if ($('#mdata_pi_cargo', panel).prop('checked')) {
		$('#div_cargo', panel).show();
	} else {
		$('#div_cargo', panel).hide();
	}
	$('#mdata_pi_cargo', panel).off('click').on('click', function() {
		if ($('#mdata_pi_cargo', panel).prop('checked')) {
			$('#div_cargo', panel).show();
		} else {
			$('#div_cargo', panel).hide();
		}
	});

	if ($('#mdata_simple_in', panel).prop('checked')) {
		$('#div_simple', panel).show();
	} else {
		$('#div_simple', panel).hide();
	}
	$('#mdata_simple_in', panel).off('click').on('click', function() {
		if ($('#mdata_simple_in', panel).prop('checked')) {
			$('#div_simple', panel).show();
		} else {
			$('#div_simple', panel).hide();
		}
	});

	$(panel).on('click', '#wms-task-form .ajax_link', function() {
		$(this).prev().remove();
		$(this).remove();
	});
});
</script>
