<h2>Release Clear Wait</h2>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'gate-pass-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
		<div class="col" style="width: 50%;">
			<div class="row rowcol rowleft">
				<?php echo $form->labelEx($model,'dpt_id'); ?>
				<?php echo $form->dropDownList($model,'dpt_id', Org::dptList(), array('empty' => 'Select One')); ?>
			</div>
			<div class="row rowcol rowleft">
				<?php echo $form->labelEx($model,'company'); ?>
				<?php echo $form->textField($model, 'company', array('size'=>30,'maxlength'=>50)); ?>
			</div>
			<div class="row rowcol">
				<?php echo $form->labelEx($model,'ref'); ?>
				<?php echo $form->textField($model, 'ref', array('size'=>20,'maxlength'=>30)); ?>
			</div>
			<div class="row rowcol">
				<?php echo CHtml::label('ST Invoice', 'st_invoice'); ?>
				<?php echo CHtml::checkBox('st_invoice', 1); ?>
			</div>
			<div class="row rowcol">
				<?php echo CHtml::label('Ignore Resorting', 'Ignore Resorting'); ?>
				<?php echo CHtml::checkBox('ignore_resorting', 0); ?>
			</div>
			<div class="row rowcol">
				<label>&nbsp;</label>
				<?php echo CHtml::submitButton($this->t('generate_gatepass'), array('class' => 'save_btn')); ?>
			</div>
			<div class="row">
				<div class="row rowcol">
					<label>&nbsp;</label>
					<?php echo CHtml::submitButton($this->t('generate_cargo_receipt&gatepass'), array('class' => 'generate_btn')); ?>
				</div>
				<div class="row rowcol">
					<label>&nbsp;</label>
					<?php echo CHtml::submitButton($this->t('only_cargo_receipt'), array('class' => 'only_cargo_receipt_btn')); ?>
				</div>
				<div class="row rowcol">
					<label>&nbsp;</label>
					<?php echo CHtml::submitButton($this->t('generate_GP&clear&manifest'), array('class' => 'generate_gatepass_manifest_btn')); ?>
				</div>
				<div class="row rowcol">
					<label>&nbsp;</label>
					<?php echo CHtml::submitButton($this->t('change_to_clear_only'), array('class' => 'change_to_clear_btn')); ?>
				</div>
			</div>

			<div class="row">
				<?php echo CHtml::hiddenField('GatePass[sig]'); ?>
				<?php echo CHtml::hiddenField('sids'); ?>
			</div>
			<div id="selected-parcels" class="grid-view" style="width:90%;">
				<input id="all-selected-parcel-ids" type="hidden" name="parcels" value="" >
				<label> <h2> Seleted Parcels </h2></label>
				<div class="summary" style="text-align: left;"><b><span class="selected-item-amount">0</span></b> Items Selected.</div>
				<table class="items">
					<thead>	
					<tr>
						<th>Connote</th>
						<th>Ref</th>
						<th>AgentId</th>
						<th>AWB</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
					</thead>
					<tbody id="body-selected-parcles">
					</tbody>
				</table>
			</div>
			<div style="clear:both; margin-bottom: 15px;"></div>
		</div>
		<div class="col" style="width: 50%;">
			<div class="row">
					<span> <h2> Available Clear Wait Shipments </h2></span>
					<div id="loadingPic"  style="width:20px;height:20px;float:left;"></div>
					<div id="shipments-view">
						<?php
						$this->renderPartial('_sub_shipments_clear_wait', array(
							'shipment_model' => $modelShipment,
							'consoleId' => 0,
							'all_clear_wait'=>false
						));
						?>
					</div>
				</div>
		</div>
	</div>

<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	function addSelectedParcel(parcel){
		// if existing do nothing
		var existing = false;
		$('#body-selected-parcles', panel).children('tr').each(function(e){
			var id = $(this).data('id');
			if ( id == parcel.id ) {
				existing = true;
				return false;
			}
		});
	   
		if ( existing )  {
			myApp.notice('parcel already selected!');
			return false;
		}

		var existingNum = $('#body-selected-parcles tr', panel).length;
		var trClassType = existingNum % 2 == 0? 'even' : 'odd';
		var parcelElements = '<tr class="' + trClassType + '" data-id="' + parcel.id + '">';
		parcelElements +=  '<td>' + parcel.hbn + '</td>';
		parcelElements +=  '<td>' + parcel.ref + '</td>';
		parcelElements +=  '<td>' + parcel.agent + '</td>';
		parcelElements +=  '<td>' + parcel.awb + '</td>';
		parcelElements +=  '<td>' + parcel.status + '</td>';
		parcelElements +=  '<td><a href="javascript:;" class="remove-selected-parcel">Remove</a></td>';
		var obj = $(parcelElements);
		$('#body-selected-parcles', panel).append(obj.fadeIn());
		$('.selected-item-amount', panel).text(existingNum+1);

	}

	function updateSelectedAmount(){
		$('.selected-item-amount', panel).text($('#body-selected-parcles tr', panel).length);
	}

	function removeSelectedParcel(pid){
		$('#body-selected-parcles', panel).children('tr').each(function(e){
			var id = $(this).data('id');
			if ( id == pid ) {
				$(this).remove();
				updateSelectedAmount();
				return false;
			}
		});
	}

	function getAwbValueByParcel(cid){
		var awbno = '';

		$('#gp-imco-consol-grid', panel).find('input[name="id"]').each(function(e){
			if ( $(this).val() == cid ) {
				awbno = $(this).parent().parent().find('td:nth-child(2)').html();
				return false;
			}
		});
		return awbno;
	}


	$('#body-selected-parcles', panel).on('click','.remove-selected-parcel',function(e){
		if ( confirm('Are you sure remove the item?') ) {
			$(this).parents('tr').remove();
			updateSelectedAmount();
		}
	});
	
	$('#gate-pass-form', panel).on('click','.select-on-check-all',function(e){
		$('#gate-pass-form input.select-on-check', panel).each(function(e){
			$(this).click();
		});
	});


	var isLock = false;
	$('#gate-pass-form', panel).on('click','.select-on-check',function(e){
		isLock = true;
		return true;
	});

	$('#GatePass_dpt_id',panel).on('change',function(){
		var thisOps = $("#ImParcel_dpt_id option",panel);
		for(var i =0;i<thisOps.length;i++)
		{
			if(thisOps[i].value==$(this).val())
			{
				$('#ImParcel_ddpt_id').val($(this).val());
				$(thisOps[i]).attr("selected",true);
				$('#gp-imco-shipments-grid', panel).yiiGridView('update', {data: $('.filters input, .filters select', panel).serialize() + '&' + $(this).serialize()});
			}
		}
		return true;
	});



	$('#gate-pass-form', panel).on('click','table tbody td',function(e){
		var selectedMe = $(this).parent().find('td:nth-child(1)').children().prop('checked');
			var child = $(this).parent().find('td:nth-child(1)').children();
			if(isLock==false)
			{
				selectedMe = !selectedMe;
			}

			if ( selectedMe ) {
				// try to add this one
				var pitem = $(this).parent();
				var data = {};
				data['id'] = child.val();
				// get console id
				var hbnCell = '<div>' +  pitem.find('td:nth-child(2)').html() + '</div>';

				var obj = $($.parseHTML(hbnCell));
				var consolItem = obj.find('input[name="consolid"]');
				var consoleId = consolItem.val();

				consolItem.remove();
				data['hbn'] = pitem.find('td:nth-child(2)').html();
				data['ref'] = pitem.find('td:nth-child(3)').html();
				data['agent'] = pitem.find('td:nth-child(5)').html();
				data['awb'] = pitem.find('td:nth-child(6)').html();
				data['status'] = pitem.find('td:nth-child(8)').html();
				addSelectedParcel(data);

			} else {
				// try to remove it
				removeSelectedParcel(child.val());
			}
			isLock = false;
	});

	$('#console-view', panel).on("click", "table tbody td", function(event){
		if ( $(this).hasClass('button-column') ) {
			// get console id
			var consoleId = parseInt( $(this).parent().children(':nth-child(1)').find('input[name="id"]').val() );
			selectAllByConsoleId(consoleId);
			return;
		}

		if ( $(this).hasClass('show-details') ) return;
		if ( !$('#loadingPic', panel).hasClass('grid-view-loading')) {
			$('#loadingPic', panel).addClass('grid-view-loading');
		}
		// get console id
		var consoleId = parseInt( $(this).parent().children(':nth-child(1)').find('input[name="id"]').val() );
		var data = {};
		data['consoleId'] = consoleId;
		$.ajax({
			type : 'GET',
			url : '<?php echo Yii::app()->createAbsoluteUrl("gatepass/prepare",array('tabid'=>$_GET['tabid'])) ;?>',
			data: data,
			dataType: 'html',
			success:function(resp){
				$('#shipments-view').html(resp);
			},
			complete:function(jqXHR, status ){
				if ( $('#loadingPic').hasClass('grid-view-loading')) {
					$('#loadingPic').removeClass('grid-view-loading');
				}
			}
		});
	});

	$('input[class="save_btn"]', panel).on('click', function(e){
		$('#cr').val(0);
		var company = $('#GatePass_company', panel).val();
		if ( company.length <= 0 ) {
			alert('Please type company name');
			$('#GatePass_company').focus();
			return false;
		}

		return confirmForm();

	});

	$('input[class="generate_btn"]', panel).on('click', function(e){
		$('#cr').val(1);
		if(confirm('Are you should to prepare gatepass while generating cargo receipt?'))
		{
			var company = $('#GatePass_company', panel).val();
			if ( company.length <= 0 ) {
				alert('Please type company name');
				$('#GatePass_company').focus();
				return false;
			}
		}else
		{
			return false;
		}
		return confirmForm();

	});

	$('input[class="only_cargo_receipt_btn"]', panel).on('click', function(e){
		$('#cr').val(1);
		if(confirm('Are you should to only generate cargo receipt?'))
		{
			var company = $('#GatePass_company', panel).val();
			if ( company.length <= 0 ) {
				alert('Please type company name');
				$('#GatePass_company').focus();
				return false;
			}
		}else
		{
			return false;
		}
		return confirmForm();

	});

	$('input[class="generate_gatepass_manifest_btn"]', panel).on('click', function(e){
		$('#cr').val(1);
		if(confirm('Are you should to prepare gatepass while manifesting?'))
		{
			var company = $('#GatePass_company', panel).val();
			if ( company.length <= 0 ) {
				alert('Please type company name');
				$('#GatePass_company').focus();
				return false;
			}
		}else
		{
			return false;
		}
		return confirmForm();

	});

	$('input[class="change_to_clear_btn"]', panel).on('click', function(e){
		$('#cr').val(1);
		if(confirm('Are you should to update them to be cleared?'))
		{
			
		}else
		{
			return false;
		}
		return confirmForm();

	});

	function confirmForm()
	{
		if ($('#body-selected-parcles tr', panel).length < 1) {
			alert('Please select at least one shipment');
			return false;
		}

		var allSelectedParcelIds = [];
		$('#body-selected-parcles tr', panel).each(function(i){
			allSelectedParcelIds.push($(this).data('id'));
		});

		$('#all-selected-parcel-ids', panel).val(allSelectedParcelIds.join(','));
	}
	
	$('form#gate-pass-form', panel).on('success', function(e, r){
		// clear all old data for next new one
		$('.selected-item-amount', panel).html('0');
		$('#body-selected-parcles', panel).empty();
	});

	$('form#gate-pass-form', panel).on('error', function(e, r){
		// clear all old data for next new one
	});
});
</script>