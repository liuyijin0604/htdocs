
<?php
	// get zone rate based on org id
	$zoneRate = new ZoneRate();
	$importChargeCode = ImportChargeCode::model()->findByPk($chgcodeid);

?>
<h1><?=$this->t('Flex Rate for '.$importChargeCode->chargecode);?></h1>
<h5>View History Version<?php echo CHtml::dropDownList("ZoneRateVersion", "", $versionList) ?></h5>
<a class="tab_link" id = "searchVersion" href=""></a>

<div class="form" style="position:relative">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'pca-chargecode-rate-form',
	'enableAjaxValidation'=>false,
));

?>

	<?php echo $form->hiddenField($model,'id'); ?>
	<?php echo CHtml::hiddenField('chargecode_id',$chgcodeid ); ?>

	<div class="row">
	<p>CBM Pricing:	<?php echo CHtml::checkbox('calculate_cbm', !empty($importChargeCode->mdata['calculate_cbm']),["id"=>"calculate_cbm"]);?>  PLT Pricing:	<?php echo CHtml::checkbox('calculate_plt', !empty($importChargeCode->mdata['calculate_plt']),["id"=>"calculate_plt"]);?></p>

	</div>

	<div class="row" >
		<div class="col" style="width: 48%; margin-right: 10px;">
			<span> <h2> Weight Range (Unit kg) </h2></span>

			<div id="zone-rate-weight-range-grid" class="grid-view editableGrid">
				<table class="items">
					<thead>
					<tr>
						<th id="zone-rate-weight-range-grid_c0">From</th><th id="zone-rate-weight-range-grid_c1">To</th><th id="zone-rate-weight-range-grid_c3">nkg</th><th class="button-column" id="zone-rate-weight-range-grid_c2">&nbsp;</th></tr>
					</thead>
					<tfoot>
					<tr><td><input style="width:100%" id="zone-rate-weight-range-grid_weight_lo" name="ZoneRate[weight_lo]" type="text" maxlength="10"></td><td><input style="width:100%" id="zone-rate-weight-range-grid_weight_hi" name="ZoneRate[weight_hi]" type="text" maxlength="10"></td><td><input style="width:100%" id="zone-rate-weight-range-grid_nkg" name="ZoneRate[nkg]" type="text" maxlength="10"></td><td><a href="" class="save_btn add_btn" title="Add">Add</a></td></tr></tfoot>
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
		<div class="col" style="width: 50%;">
			<span> <h2> Zone Rate <span id="zone-rate-weight-span" style="width:20px;height:20px;font-size: 12px;">(0kg - 0kg)</span> </h2></span>

			<div id="org-zone-rate-view">
				<div class="grid-view">
					<table class="items">
						<thead>
						<tr>
							<th id="gp-imco-consol-grid_c0">Code</th>
							<th id="gp-imco-consol-grid_c1">Name</th>
							<th id="gp-imco-consol-grid_c2">Price / Piece</th>
							<th id="gp-imco-consol-grid_c3">Price / kg</th>
							<th id="gp-imco-consol-grid_c4">Base</th>
							<th id="gp-imco-consol-grid_c4">Minimum</th>
							<th id="gp-imco-consol-grid_c5">Min_incl</th>
						</tr>
						</thead>
						<tbody></tbody>
					</table>
				  </div>
			</div>
		</div>
	</div>
	<?php $this->endWidget(); ?>

	<div class="row">
		<div class='row rowcol rowleft'>
			<p>________________________________________________</p>
		</div>
	</div>

	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'org-zone-cost-import-form',
			'enableAjaxValidation'=>false,
			'action' => $this->createUrl('org/AjaxImportZoneCost')
		));
	?>
	<div class="row">
		<div class="col rowcol">
			<div class="icon" style="background-position:-16px 0"></div>
			<a href="<?=$this->createUrl('org/exportCostRate', ['id' => $chgcodeid,'isChargecode'=>1]);?>" target="_blank">Export</a>
		</div>
		<div class="col rowcol" style="min-height:50px; margin:left: 50px">
			<div class="icon" style="background-position:-16px 0"><div class="form">
				
					<div class="row" style="margin-left:20px;">
						<?php echo $form->hiddenField($model,'id'); ?>
						<?php echo CHtml::hiddenField('chargecode_id',$chgcodeid ); ?>
						<input type="file" name="zone_cost_rate_file" id="zone_cost_rate_file" style="float:left;" />
						<a id="zone_cost_rate_import_btn" href="#">Import</a>
						<div id="import-inprogress-flag" class="" style="width: 40px; height: 40px;margin-top: 10px;"></div>
					</div>
				</div></div>

		</div>
	</div>

	<div class="row">
		<div class='row rowcol rowleft'>
			<?php echo CHtml::label('Start Date','Start Date'); ?>
			<?php echo CHtml::textField('start_date',empty($omodel)?"":$omodel->start_date, array('size' => 20,'class'=>"date_input",'id'=>'start_date'.$_GET['tabid'])); ?>
		</div>
	</div>

	<div class="row">
		<div class='row rowcol rowleft'>
		<?= CHtml::label('Criteria','criteria');?>
		<?= CHtml::dropDownList('criteria',empty($importChargeCode->mdata['criteria'])?ImportChargeCode::ETD:$importChargeCode->mdata['criteria'], ImportChargeCode::$searchCriteria); ?>
		</div>
	</div>

	<?php $this->endWidget(); ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>


</div><!-- form -->
<script type="text/javascript">
	$(document).ready(function(e){

	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	// current zone rate data
	var curZoneRateData = <?php echo json_encode($zoneRate->getZoneRateWeightRangeByArrayByChargecode($chgcodeid)); ?>;

	// set default zone rate data
	var zoneRateData = <?php echo json_encode(ZoneMap::getChargecodeZoneMap($chgcodeid));?>;
	var allZoneRateData = [];
	var allRowIndex = 0;
	var selectedRowIndex = 0;
	initZoneRateData(zoneRateData);
	appendCurZoneRateData(curZoneRateData);
	var importInProgress = false;

	function isImportInProgress(){
		return importInProgress;
	}

	function appendCurZoneRateData(rateData){

		$('#zone-rate-weight-range-grid .items tbody td.empty', win).parent().remove();
		var wtpl = $('#zone-rate-weight-range-grid .items tfoot', win);
		var row = wtpl.find('tr').clone();
		for ( var i in rateData ) {
			allRowIndex++;
			var r = wtpl.find('tr').clone();
			$('.add_btn',r).replaceWith('<a href="javascript:;" data-index="'+allRowIndex+'"class="delete_btn" title="Remove">Remove</a>');
			$('input', r).each(function(){
				var n = $(this).attr('name');
				$(this).attr('name', n+'[]');
			});
			$('#zone-rate-weight-range-grid .items tbody', win).append(r);
			r.find('#zone-rate-weight-range-grid_weight_lo').val(rateData[i]['weight_lo']);
			r.find('#zone-rate-weight-range-grid_weight_hi').val(rateData[i]['weight_hi']);
			r.find('#zone-rate-weight-range-grid_nkg').val(rateData[i]['data'][0]['nkg']);
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
			 zoneData['nkg']=0;
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
			zoneData['nkg'] = 0;
			zoneData['data'] = JSON.parse(JSON.stringify(zoneRateData));
			allZoneRateData.push(zoneData);
		}
	}

	function updateLoHi(dataIndex, lo, hi,nkg){
		// get related zone rate data
		for ( var i in allZoneRateData ) {
			var oneZone = allZoneRateData[i];
			if ( oneZone['tag'] == dataIndex ) {
				oneZone['lo'] = lo;
				oneZone['hi'] = hi;
				oneZone['nkg']=nkg;
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
			var zoneBody = $('#org-zone-rate-view tbody', win);
			zoneBody.empty();
			fillZoneRateData(oneZoneRate);
		}
	}

	function fillZoneRateData(rateData){
		var zoneBody = $('#org-zone-rate-view tbody', win);
		var index = 0;
		for (var i in rateData ) {
			var detail = rateData[i];
			var rowData = '<tr class="odd">';
			if ( index++ % 2 == 0  ) {
				rowData = '<tr class="even">';
			}
			rowData += '<td class="show-details"><input type="hidden" name="id" value="">';
			rowData += '<a href="javascript:;" class="tab_link" title="ZoneCode">' + detail['code']+ '</a></td>';
			rowData += '<td>' + detail['name']+ '</td>';
			rowData += '<td><input name="ppc[]" data-code="'+detail['code']+'" type="text" value="' + detail['ppc']+ '" style="width:50px;"></td>';
			rowData += '<td><input name="pkg[]" data-code="'+detail['code']+'"type="text" value="' + detail['pkg']+ '" style="width:50px;"></td>';
			rowData += '<td><input name="base[]" data-code="'+detail['code']+'"type="text" value="' + detail['base']+ '" style="width:50px;""></td>';
			rowData += '<td><input name="minimum[]" data-code="'+detail['code']+'"type="text" value="' + detail['minimum']+ '" style="width:50px;""></td>';
			rowData += '<td><input name="min_incl[]" data-code="'+detail['code']+'"type="text" value="' + detail['min_incl']+ '" style="width:50px;"></td></tr>';

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
			$('#zone-rate-weight-span', win).html(wrangeStr);
		}
	}


	$('form#pca-chargecode-rate-form', win).on('success', function(e, r){
	//	win.data('opener').trigger('reload_contact_grid');
		win.jqmHide();
	});


	$('#zone-rate-weight-range-grid .add_btn', win).on('click', function(e){

		e.preventDefault();


		allRowIndex++;
		$('#zone-rate-weight-range-grid .items tbody td.empty', win).parent().remove();
		var r = $(this).parents('tr').clone();
		$('.add_btn',r).replaceWith('<a href="javascript:;" data-index="'+allRowIndex+'"class="delete_btn" title="Remove">Remove</a>');
		$('input', r).each(function(){
			var n = $(this).attr('name');
			$(this).attr('name', n+'[]');
		});
		$('#zone-rate-weight-range-grid .items tbody').append(r);
		$(this).parents('tr').find('input').val('');

		addOneZoneRate(allRowIndex);

		return false;
	});

	$('#zone-rate-weight-range-grid', win).on('click','tr',function(e){
		// exclude the dummy row data
		if ( $(this).parent().is("tfoot") ) return;

		// get related zone data related tag
		var tag = $(this).find('.delete_btn').data('index');
		refreshZoneRate(tag);
		refreshSelectedWeightRangeByDest($(this));

	});

	$('#zone-rate-weight-range-grid', win).on('click','.delete_btn',function(e){
		e.preventDefault();
		if ( confirm( 'Are you sure remove the zone rate data?') ) {
			$(this).parents('tr').remove();
			return false;
		}
		return true;
	});

	$('#zone-rate-weight-range-grid', win).on('change textInput input','input[name="ZoneRate[weight_lo][]"]',function(e){
		refreshSelectedWeightRangeByDest($(this).parent().parent());
		if ( !validWeightFrom($(this)) )  $(this).focus();
	}).on('change textInput input','input[name="ZoneRate[weight_hi][]"]',function(e){
		refreshSelectedWeightRangeByDest($(this).parent().parent());
		if ( !validWeightTo($(this)) ) $(this).focus();
	});

	$('#org-zone-rate-view', win).on('change','input[type=text]',function(e){
		var bFound = false;
		var oneZoneRate = [];
		var rowIndex = 0;
		for ( var i in allZoneRateData ) {
			var oneZone = allZoneRateData[i];
			if ( oneZone['tag'] == selectedRowIndex ) {
				bFound = true;
				oneZoneRate = oneZone['data'];
				rowIndex = i;
				console.log('related zone data found');
				break;
			}
		}
		if ( bFound ) {
			// get code related data item
			var code = $(this).data('code');
			for ( var j in oneZoneRate ) {
				var oneData = oneZoneRate[j];
				if ( oneData['code'] == code ) {
					oneData[$(this).attr('name').replace(/[\[\]]+/,'')] = $(this).val();
					break;
				}
			}
		}
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
				appendCurZoneRateData(r.wranges);

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

		// update all lo and hi weight range
		$('#zone-rate-weight-range-grid', win).find('tr').each(function(e){
			var dataIndex = $(this).find('.delete_btn').data('index');
			if ( !isNaN(dataIndex) ) {
				var lo = $(this).find('#zone-rate-weight-range-grid_weight_lo').val();
				var hi = $(this).find('#zone-rate-weight-range-grid_weight_hi').val();
				var nkg=$(this).find('#zone-rate-weight-range-grid_nkg').val();
				updateLoHi(dataIndex,lo,hi,nkg);
			}
		});

		// ajax post to save them
		var orgId = $('#Org_id', win).val();
		var chgCodeId = $('#chargecode_id', win).val();

		var postdata = JSON.stringify(allZoneRateData);
		var cbm = $('#calculate_cbm:checked',win).length;
		var plt = $('#calculate_plt:checked',win).length;
		var start_date = $('#start_date<?=$_GET['tabid']?>',win).val();
		var criteria = $('#criteria',win).val();
		// var minimum_cbm = $('#minimum_cbm',win).val();

		$.ajax({
			type : 'POST',
			url : '<?php echo Yii::app()->createAbsoluteUrl("org/savePcaZonePrice") ;?>',
			dataType: 'json',
			data:{ 'oid' : orgId,'cid' : chgCodeId,'data' : postdata, 'calculate_cbm' : cbm, 'calculate_plt' : plt,'minimum_cbm':0,'start_date':start_date,'criteria':criteria},
			success:function(resp){
				if ( resp.success == 1 ) {
					myApp.notice('flex rate by zone saved successfully!', 5000);
				} else {
					myApp.alert(resp.msg);
				}
			}
		});

	});

	$("#ZoneRateVersion",win).on("change",function(e)
	{
		if($(this).val()!=""&&$(this).val()!="0")
		{
			$('#searchVersion',win).attr('href','<?=$this->createUrl('import/viewChargeCodeRateHistory',['id'=>$chgcodeid]);?>?version=' + $(this).val());
			$('#searchVersion',win).attr('title','of:<?=$importChargeCode->chargecode?>version=' + $(this).val());
			$('#searchVersion',win).click();
		}
	});


});
</script>