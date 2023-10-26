<div style="position: absolute; right: 260px;">
	<div class="col rowcol">
	<div class="icon" style="background-position:-16px 0"></div><a href="<?=$this->createUrl('org/exportCostRate', ['id' => $model->id]);?>" target="_blank">Export</a>
	</div>
	<div class="col rowcol" style="min-height:50px; margin:left: 50px">
		<div class="icon" style="background-position:-16px 0"><div class="form">
				<?php
				$form=$this->beginWidget('CActiveForm', array(
					'id'=>'org-zone-cost-import-form',
					'enableAjaxValidation'=>false,
					'action' => $this->createUrl('org/AjaxImportZoneCost')
				));
				?>
				<div class="row" style="margin-left:20px;">
					<?php echo $form->hiddenField($model,'id'); ?>
					<input type="file" name="zone_cost_rate_file" id="zone_cost_rate_file" style="float:left;" />
					<a id="zone_cost_rate_import_btn" href="#">Import</a>
					<div id="import-inprogress-flag" class="" style="width: 40px; height: 40px;margin-top: 10px;"></div>
				</div>
				<?php $this->endWidget(); ?>
			</div></div>

	</div>
</div>
<h1><?=$this->t($model->name.' Rates');?></h1>


<div class="form" style="position:relative">

<?php
	// get zone rate based on org id
	$zoneRate = new ZoneRate();
?>

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'org-zone-rate-form',
		'enableAjaxValidation'=>false,
	));
	?>
	<?php echo CHtml::hiddenField('rate_id',$model->id ); ?>
	<div class="row rowleft rowcol">
	<label>Servicing Warehouse:</label>
	<?php echo CHtml::dropDownList('mdata[ddpt_id]', @$model->mdata['ddpt_id'], Org::dptList(25), ['empty' => 'Select One']);?>
	</div>
	<div class="row rowcol">
	<label>MLIDs:</label>
	<?php echo CHtml::textField('mdata[mlids]', @$model->mdata['mlids']);?>
	</div>
	<div class="row rowcol">
	<label>Fuel(0-1):</label>
	<?php echo CHtml::numberField('mdata[fuel]', @$model->mdata['fuel'],["style"=>"width:5em;"]);?>
	</div>
	<div class="row rowcol">
	<label>CBM Pricing:</label>
	<?php echo CHtml::checkbox('mdata[cbm_pricing]', !empty($model->mdata['cbm_pricing']));?>
	</div>
	<div class="row rowcol">
	<label>PLT Pricing:</label>
	<?php echo CHtml::checkbox('mdata[plt_pricing]', !empty($model->mdata['plt_pricing']));?>
	</div>


	<div class="row rowcol">
	<label>Can DG:</label>
	<?php echo CHtml::checkbox('mdata[can_dg]', !empty($model->mdata['can_dg']));?>
	</div>

	<div class="row rowcol">
	<label>Dimension Requiring:</label>
	<?php echo CHtml::checkbox('mdata[dimension_requiring]', !empty($model->mdata['dimension_requiring']));?>
	</div>

	<div class="row rowcol">
	<label>Business Type:</label>
	<?php echo CHtml::dropDownList('mdata[business_type]', @$model->mdata['business_type'],[0=>"All",1=>'B2B',2=>'B2C']);?>
	</div>

	<?php if($model->type ==90):?>
	<div class="row rowcol">
		<label>Minimum CBM:</label>
	<?php echo CHtml::textField('mdata[minimum_cbm]', @$model->mdata['minimum_cbm']);?>
	</div>
	</br>
	<div class="xpanel-body-item row rowcol rowleft">
				<div class="row rowcol rowcol-left divBorder">
						<?php echo CHtml::label('CBM Courier Only','CBM Courier Only',["style"=>"font-weight:bold;"]); ?>
						<div class="row">
							<div class="row rowcol rowcol-left">
								<?php echo CHtml::label('fuel surcharge','fuel_surcharge'); ?>
								<?php echo CHtml::textField('mdata[truck_delivery][fuel_surcharge]',isset($model->mdata['truck_delivery']['fuel_surcharge'])?	$model->mdata['truck_delivery']['fuel_surcharge']:0,array('style'=>"width:8em;"))?>(0-0.15)<br>
	
								<?php echo CHtml::label('express ','express'); ?>
								<?php echo CHtml::textField('mdata[truck_delivery][express]',isset($model->mdata['truck_delivery']['express'])?$model->mdata[	'truck_delivery']['express']:0,array('style'=>"width:8em;"))?>times<br>
	
								<?php echo CHtml::label('Extra express','Extra-express'); ?>
								<?php echo CHtml::textField('mdata[truck_delivery][extra_express]',isset($model->mdata['truck_delivery']['extra_express'])?$model->mdata['truck_delivery']['extra_express']:0,array('style'=>"width:8em;"))?>times<br>
	
							</div>
							<div class="row rowcol rowcol-left">
								<?php echo CHtml::label('Extra Distance','Extra_Distance'); ?>
								<?php echo CHtml::textField('mdata[truck_delivery][extra_distance]',isset($model->mdata['truck_delivery']['extra_distance'])?	$model->mdata['truck_delivery']['extra_distance']:0,array('style'=>'width:8em;'))?>KM<br>
								<?php echo CHtml::label('Extra Distance Fee','Extra_Distance_Fee'); ?>
								<?php echo CHtml::textField('mdata[truck_delivery][extra_distance_fee]',isset($model->mdata['truck_delivery']['extra_distance_fee'])?$model->mdata['truck_delivery']['extra_distance_fee']:0,array('style'=>'width:4em;'))?>AUD/
								<?php echo CHtml::textField('mdata[truck_delivery][extra_distance_limit]',isset($model->mdata['truck_delivery']['extra_distance_limit'])?$model->mdata['truck_delivery']['extra_distance_limit']:0,array('style'=>'width:4em;'))?>KM<br>
							</div>
	
							<div class="row rowcol rowcol-left">
								<?php echo CHtml::label('Extra Width','Extra_Width'); ?>
								<?php echo CHtml::textField('mdata[truck_delivery][extra_width]',isset($model->mdata['truck_delivery']['extra_width'])?$model->mdata['truck_delivery']['extra_width']:0,array('style'=>'width:8em;'))?>CM<br>
	
								<?php echo CHtml::label('Extra Width Fee','Extra_Width_Fee'); ?>
								<?php echo CHtml::textField('mdata[truck_delivery][extra_width_fee]',isset($model->mdata['truck_delivery']['extra_width_fee']	)?$model->mdata['truck_delivery']['extra_width_fee']:0,array('style'=>'width:8em;'))?>AUD/CBM<br>
							</div>
	
	
						</div>
				</div>
	</div>
    <?php endif;?>
	<div class="row" >
		<div class="col" style="width: 40%; margin-right: 10px;">
			<span> <h2> Weight Range (Unit kg) </h2></span>

			<div id="zone-rate-weight-range-grid" class="grid-view editableGrid">
				<table class="items">
					<thead>
					<tr>
						<th><a class="sort-link">From</a></th><th><a class="sort-link" >To</a></th><th class="button-column">&nbsp;</th></tr>
					</thead>
					<tfoot>
					<tr><td><input style="width:100%" id="zone-rate-weight-range-grid_weight_lo" name="ZoneRate[weight_lo]" type="text" maxlength="10"></td><td><input style="width:100%" id="zone-rate-weight-range-grid_weight_hi" name="ZoneRate[weight_hi]" type="text" maxlength="10"></td><td><a href="" class="save_btn add_btn" title="Add">Add</a></td></tr></tfoot>
					<tbody>
					<tr><td colspan="3" class="empty"><span class="empty">No results found.</span></td></tr>
					</tbody>
				</table>

			</div>
<?php
/*
			$this->widget('application.extensions.editablegrid.CEditableGridView', array(
			'id'=>'zone-rate-weight-range-grid',
			'cssFile' => false,
			'dataProvider' => $zoneRate->getZoneRateWeightRange(-1),
			'formUrl' => $this->createUrl('invoice/linesGrid', array('id'=>empty($model->id)? 0 : $model->id)),
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
			array('header' => 'From','name' => 'weight_lo', 'class' => 'CEditableColumn'),
			array('header' => 'To','name' => 'weight_hi', 'class' => 'CEditableColumn'),
			array('class'=>'CEditableButtonColumn', 'template' => '{save}')
			),
			));
*/
 ?>

		</div>
		<div class="col" style="width: 54%;">
			<h2> Zone Rate <span id="zone-rate-weight-span" style="font-size: 12px;">(0kg - 0kg)</span> &nbsp; &nbsp; <a class="copyData" href="#" style="font-size:12px;font-weight:normal;">Copy All</a></h2>
			<div id="org-zone-rate-view">
				<div class="grid-view">
					<table class="items">
						<thead>
						<tr>
							<th>Code</th>
							<th>Name</th>
							<th>Price/Piece</th>
							<th>Price/kg</th>
							<th>Base</th>
							<th>Minimum</th>
							<th>Min Incl</th>
							<th>nKg</th>
						</tr>
						</thead>
						<tbody></tbody>
					</table>
				  </div>
			</div>
		</div>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
$(document).ready(function(e){

	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	// current weight ranges
	var weightRanges = <?php echo json_encode($zoneRate->getZoneRateWeightRangeByArray($model->id)); ?>;

	// set default zone cost rate
	var zoneCostRateData = <?php echo json_encode(ZoneMap::getOrgZoneMap($model->org_id,$model->zone_id) ); ?>;

	var allZoneRateData = [];
	var allRowIndex = 0;
	var selectedRowIndex = 0;
	var importInProgress = false;

	initZoneRateData(zoneCostRateData);
	appendWerightRanges(weightRanges);

	function isImportInProgress(){
		return importInProgress;
	}

	function appendWerightRanges(rateData){

		$('#zone-rate-weight-range-grid .items tbody td.empty',win).parent().remove();
		var wtpl = $('#zone-rate-weight-range-grid .items tfoot',win);
		var row = wtpl.find('tr').clone();
		for ( var i in rateData ) {
			allRowIndex++;
			var r = wtpl.find('tr').clone();
			$('.add_btn',r).replaceWith('<a href="javascript:;" data-index="'+allRowIndex+'"class="delete_btn" title="Remove">Remove</a>');
			$('input', r).each(function(){
				var n = $(this).attr('name');
				$(this).attr('name', n+'[]');
			});
			$('#zone-rate-weight-range-grid .items tbody',win).append(r);
			r.find('#zone-rate-weight-range-grid_weight_lo').val(rateData[i]['weight_lo']);
			r.find('#zone-rate-weight-range-grid_weight_hi').val(rateData[i]['weight_hi']);
			addOneZoneRateByData(allRowIndex,rateData[i]['data']);
		}
	}

	 function addOneZoneRateByData(tag,rateData){
		 var bFound = false;
		 for ( var i in allZoneRateData ) {
			 var oneZone = allZoneRateData[i];
			 if ( oneZone['tag'] == tag ) {
				 bFound = true;
			 }
		 }
		 if ( !bFound ) {
			 var zoneData = {};
			 zoneData['tag'] = tag;
			 zoneData['lo'] = 0;
			 zoneData['hi'] = 0;
			 zoneData['data'] = rateData;
			 allZoneRateData.push(zoneData);
		 }
	 }

	 function addOneZoneRate(tag){
		// check existing or not
		// if not existing just create a new one
		var bFound = false;
		for ( var i in allZoneRateData ) {
			var oneZone = allZoneRateData[i];
			if ( oneZone['tag'] == tag ) {
				bFound = true;
			}
		}
		if ( !bFound ) {
			var zoneData = {};
			zoneData['tag'] = tag;
			zoneData['lo'] = 0;
			zoneData['hi'] = 0;
			zoneData['data'] = JSON.parse(JSON.stringify(zoneCostRateData));
			allZoneRateData.push(zoneData);
		}
	}

	function updateLoHi(dataIndex, lo, hi){
		// get related zone rate data
		for ( var i in allZoneRateData ) {
			var oneZone = allZoneRateData[i];
			if ( oneZone['tag'] == dataIndex ) {
				oneZone['lo'] = lo;
				oneZone['hi'] = hi;
				break;
			}
		}
	}

	function refreshZoneRate(tag){
		// get related zone rate data
		var bFound = false;
		var oneZoneRate = [];
		for ( var i in allZoneRateData ) {
			var oneZone = allZoneRateData[i];
			if ( oneZone['tag'] == tag ) {
				bFound = true;
				oneZoneRate = oneZone['data'];
				break;
			}
		}
		if ( bFound ) {
			selectedRowIndex = tag;
			var zoneBody = $('#org-zone-rate-view tbody',win);
			zoneBody.empty();
			fillZoneRateData(oneZoneRate);
		}
	}

	function fillZoneRateData(rateData){
		var zoneBody = $('#org-zone-rate-view tbody',win);
		var index = 0;
		for (var i in rateData ) {
			var detail = rateData[i];
			var rowData = '<tr class="odd">';
			if ( index++ % 2 == 0  ) {
				rowData = '<tr class="even">';
			}
			rowData += '<td class="show-details"><input type="hidden" name="id" value="">';
			rowData += '<a href="zoneMap/list?oid=<?=$model->org_id;?>&zid=<?=$model->zone_id;?>&z1='+detail['code']+'" class="tab_link" title="'+detail['code']+'">' + detail['code']+ '</a></td>';
			rowData += '<td>' + detail['name']+ '</td>';
			rowData += '<td><input name="ppc[]" data-code="'+detail['code']+'" type="text" value="' + detail['ppc']+ '"></td>';
			rowData += '<td><input name="pkg[]" data-code="'+detail['code']+'"type="text" value="' + detail['pkg']+ '"></td>';
			rowData += '<td><input name="base[]" data-code="'+detail['code']+'"type="text" value="' + detail['base']+ '"></td>';
			rowData += '<td><input name="minimum[]" data-code="'+detail['code']+'"type="text" value="' + detail['minimum']+ '"></td>';
			rowData += '<td><input name="min_incl[]" data-code="'+detail['code']+'"type="text" value="' + detail['min_incl']+ '"></td>';
			rowData += '<td><input name="nkg[]" data-code="'+detail['code']+'"type="text" value="' + detail['nkg']+ '"></td></tr>';

			zoneBody.append(rowData);
		}
	}

	function initZoneRateData(rateData){
		fillZoneRateData(rateData);
	}

	function validWeightFrom($fromWeight){
		var lo =  parseFloat($fromWeight.val());
		var hi =  parseFloat($fromWeight.parent().parent().find('#zone-rate-weight-range-grid_weight_hi').val());
		if ( !isNaN(lo) && !isNaN(hi) ) {
			if ( lo > hi ) {
				alert( "low weight must be less than high weight");
				return false;
			}
		}
		return true;
	}

	function validWeightTo($toWeight){
		var hi =  parseFloat($toWeight.val());
		var lo =  parseFloat($toWeight.parent().parent().find('#zone-rate-weight-range-grid_weight_lo').val());
		if ( !isNaN(lo) && !isNaN(hi) ) {
			if ( lo > hi ) {
				alert( "low weight must be less than high weight");
				return false;
			}
		}
		return true;
	}

	function refreshSelectedWeightRangeByDest(destRange){
		if ( typeof destRange != 'undefined' ) {
			var lo = destRange.find('#zone-rate-weight-range-grid_weight_lo').val();
			var hi = destRange.find('#zone-rate-weight-range-grid_weight_hi').val();
			var wrangeStr = '(' + lo + 'kg - ' + hi + 'kg)';
			$('#zone-rate-weight-span',win).html(wrangeStr);
		}
	}

	function updateRowItemData(value, code,key){
		//console.log('update data for :' + value + ' ,' +  code  + ' , ' + key);
		// save related price by piece
		var bFound = false;
		var oneZoneRate = [];
		var rowIndex = 0;
		for ( var i in allZoneRateData ) {
			var oneZone = allZoneRateData[i];
			if ( oneZone['tag'] == selectedRowIndex ) {
				bFound = true;
				oneZoneRate = oneZone['data'];
				rowIndex = i;
				// console.log('related zone data found');
				break;
			}
		}
		if ( bFound ) {
			// get code related data item
			for ( var j in oneZoneRate ) {
				var oneData = oneZoneRate[j];
				if ( oneData['code'] == code ) {
					oneData[key] = value;
					break;
				}
			}
		}
	}


	$('form#org-zone-rate-form', win).on('success', function(e, r){
		win.jqmHide();
	});


	$('#zone-rate-weight-range-grid .add_btn',win).on('click', function(e){
		if ( isImportInProgress() ) return;
		e.preventDefault();
		allRowIndex++;
		$('#zone-rate-weight-range-grid .items tbody td.empty',win).parent().remove();
		var r = $(this).parents('tr').clone();
		$('.add_btn',r).replaceWith('<a href="javascript:;" data-index="'+allRowIndex+'"class="delete_btn" title="Remove">Remove</a>');
		$('input', r).each(function(){
			var n = $(this).attr('name');
			$(this).attr('name', n+'[]');
		});
		$('#zone-rate-weight-range-grid .items tbody',win).append(r);
		$(this).parents('tr').find('input').val('');

		addOneZoneRate(allRowIndex);

		return false;
	});

	$('#zone-rate-weight-range-grid',win).on('click','tr',function(e){
		if ( isImportInProgress() ) return;
		// exclude the dummy row data
		if ( $(this).parent().is("tfoot") ) return;

		// get related zone data related tag
		var tag = $(this).find('.delete_btn').data('index');
		refreshZoneRate(tag);
		refreshSelectedWeightRangeByDest($(this));

	});

	$('#zone-rate-weight-range-grid',win).on('click','.delete_btn',function(e){
		if ( isImportInProgress() ) return;
		e.preventDefault();
		if ( confirm( 'Are you sure remove the zone rate data?') ) {
			$(this).parents('tr').remove();
			return false;
		}
		return true;
	});

	$('#zone-rate-weight-range-grid',win).on('change textInput input','input[name="ZoneRate[weight_lo][]"]',function(e){
		if ( isImportInProgress() ) return;
		refreshSelectedWeightRangeByDest($(this).parent().parent());
		if ( !validWeightFrom($(this)) )  $(this).focus();
	});

	$('#zone-rate-weight-range-grid',win).on('change textInput input','input[name="ZoneRate[weight_hi][]"]',function(e){
		if ( isImportInProgress() ) return;
		refreshSelectedWeightRangeByDest($(this).parent().parent());
		if ( !validWeightTo($(this)) ) $(this).focus();
	});

	$('#org-zone-rate-view',win).on('change textInput input','input[name="ppc[]"]',function(e){
		if ( isImportInProgress() ) return;
		updateRowItemData($(this).val(),$(this).data('code'),'ppc');
	});

	$('#org-zone-rate-view',win).on('change textInput input','input[name="pkg[]"]',function(e){
		if ( isImportInProgress() ) return;
		updateRowItemData($(this).val(),$(this).data('code'),'pkg');
	});

		$('#org-zone-rate-view',win).on('change textInput input','input[name="minimum[]"]',function(e){
			if ( isImportInProgress() ) return;
			updateRowItemData($(this).val(),$(this).data('code'),'minimum');
		});

		$('#org-zone-rate-view',win).on('change textInput input','input[name="min_incl[]"]',function(e){
			if ( isImportInProgress() ) return;
			updateRowItemData($(this).val(),$(this).data('code'),'min_incl');
		});

		$('#org-zone-rate-view',win).on('change textInput input','input[name="base[]"]',function(e){
			if ( isImportInProgress() ) return;
			updateRowItemData($(this).val(),$(this).data('code'),'base');
		});
		$('#org-zone-rate-view',win).on('change textInput input','input[name="nkg[]"]',function(e){
			if ( isImportInProgress() ) return;
			updateRowItemData($(this).val(),$(this).data('code'),'nkg');
		});

		$('#zone_cost_rate_import_btn',win).click(function(e){
			if ( isImportInProgress() ) return;

			$('#import-inprogress-flag',win).addClass('grid-view-loading');
			e.preventDefault();
			e.stopPropagation();
			importInProgress = true;
			$('form#org-zone-cost-import-form',win).submit();
		});


		$('form#org-zone-cost-import-form', win).on('error', function(e,r) {
			importInProgress = false;
		});

		$('form#org-zone-cost-import-form', win).data('custom_success', function(r){

			if ( r.done === true ) {
				// refresh current data
				allZoneRateData = [];
				allRowIndex = 0;
				selectedRowIndex = 0;
				$('#org-zone-rate-view tbody',win).empty();
				$('#zone-rate-weight-range-grid .items tbody',win).empty();
				initZoneRateData(r.zcostrate);
				appendWerightRanges(r.wranges);

				alert('Import successfully!');
			} else {
				alert(r.msg);
			}
			$('#import-inprogress-flag',win).removeClass('grid-view-loading');

			importInProgress = false;

			return true;
		});

	$('input[type="submit"]',win).click(function(e){
		//alert('callme');
		e.preventDefault();

		if ( isImportInProgress() ) return;

		// update all lo and hi weight range
		$('#zone-rate-weight-range-grid',win).find('tr').each(function(e){
			var dataIndex = $(this).find('.delete_btn').data('index');
			if ( !isNaN(dataIndex) ) {
				var lo = $(this).find('#zone-rate-weight-range-grid_weight_lo').val();
				var hi = $(this).find('#zone-rate-weight-range-grid_weight_hi').val();
				updateLoHi(dataIndex,lo,hi);
			}
		});

		// ajax post to save them
		var rateId = $('#rate_id',win).val();
		var ddpt = $('#mdata_ddpt_id',win).val();
		var cbm = $('#mdata_cbm_pricing:checked',win).length;
		var plt = $('#mdata_plt_pricing:checked',win).length;
		var dg = $('#mdata_can_dg:checked',win).length;
		var dr = $('#mdata_dimension_requiring:checked',win).length;
		var bsstype = $('#mdata_business_type',win).val();
		var mlids = $('#mdata_mlids',win).val();
		var fuel = $('#mdata_fuel',win).val();
		var minimum_cbm = $('#mdata_minimum_cbm',win).val();
		var truck_delivery_fuel_surcharge = $('#mdata_truck_delivery_fuel_surcharge',win).val();
		var truck_delivery_express = $('#mdata_truck_delivery_express',win).val();
		var truck_delivery_extra_express = $('#mdata_truck_delivery_extra_express',win).val();
		var truck_delivery_extra_distance = $('#mdata_truck_delivery_extra_distance',win).val();
		var truck_delivery_extra_distance_fee = $('#mdata_truck_delivery_extra_distance_fee',win).val();
		var truck_delivery_extra_distance_limit = $('#mdata_truck_delivery_extra_distance_limit',win).val();
		var truck_delivery_extra_width = $('#mdata_truck_delivery_extra_width',win).val();
		var truck_delivery_extra_width_fee = $('#mdata_truck_delivery_extra_width_fee',win).val();
		var cbm_data = {'truck_delivery':{'fuel_surcharge':truck_delivery_fuel_surcharge,'express':truck_delivery_express,'extra_express':truck_delivery_extra_express,'extra_distance':truck_delivery_extra_distance,'extra_distance_fee':truck_delivery_extra_distance_fee,'extra_distance_limit':truck_delivery_extra_distance_limit,'extra_width':truck_delivery_extra_width,'extra_width_fee':truck_delivery_extra_width_fee}}
		$.ajax({
			type : 'POST',
			url : '<?php echo Yii::app()->createAbsoluteUrl("org/saveZonePrice") ;?>',
			dataType: 'json',
			data:{'rid' : rateId, 'ddpt': ddpt, 'cbm': cbm,'plt': plt, 'mlids': mlids,'fuel':fuel,'can_dg':dg,'dimension_requiring':dr,'cbm_data':cbm_data,'minimum_cbm':minimum_cbm,'bsstype':bsstype, 'data' : JSON.stringify(allZoneRateData)},
			success:function(resp){
				if ( resp.success == 1 ) {
					myApp.notice('flex rate by zone saved successfully!', 5000);
				} else {
					myApp.alert(resp.msg);
				}
			}
		});

	});


	$('#org-zone-rate-view', win).on('paste','tbody input[type=text]',function(e){
		if (window.clipboardData && window.clipboardData.getData){
			pastedText = window.clipboardData.getData('Text');
		} else if (e.originalEvent.clipboardData && e.originalEvent.clipboardData.getData) {
			pastedText = e.originalEvent.clipboardData.getData('text/plain');
		}

		var ppos = -1;
		var me = $(this);
		var cells = pastedText.trim().split(/[\t\n\r]+/);
		$('#org-zone-rate-view tbody input[type=text]', win).each(function(i){
			if($(this).is(me)) ppos = 0;
			if(ppos >= cells.length) return false;
			if(ppos > -1) $(this).val(cells[ppos++]).trigger('change');
		});
		
		return false;
	});

	$('a.copyData', win).on('click', function(){
		var input = $('<textarea style="width:1px; height:1px;border:none;"></textarea>');
		var rs = [];
		$('#org-zone-rate-view tbody tr', win).each(function(i){
			var c = [];
			$('input[type=text]', this).each(function(){
				c.push($(this).val());
			});
			rs.push(c.join("\t"));
		});
		win.append(input);
		input.val(rs.join("\n")).select();
		document.execCommand("copy");
		input.remove();
		window.alert('Copied');
	});
});
</script>