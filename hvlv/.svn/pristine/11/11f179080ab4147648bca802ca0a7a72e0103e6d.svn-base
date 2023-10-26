
<div class="form" style="position:relative">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'pca-chargecode-rate-form',
	'enableAjaxValidation'=>false,
));

?>

	<div class="row">
	</div>

	<div class="row" >
		<div class="col" style="width: 100%; margin-right: 10px;">
			<div id="parcel-priority-range-grid<?=$_GET['dptId']?>" class="grid-view editableGrid">
				<?php
					echo CHtml::hiddenField('dptId',$_GET['dptId']);
				?>
				<table class="items">
					<thead>
					<tr>
						<th id="parcel-priority-range-grid_c0">Parcel Type</th><th id="parcel-priority-range-grid_c3">Oversize</th><th id="parcel-priority-range-grid_c1">Area Type</th><th id="parcel-priority-range-grid_c3">Priority</th><th class="button-column" id="parcel-priority-range-grid_c2">&nbsp;</th></tr>
					</thead>
					<tfoot>
					<tr>
						<td>
							<?php

								foreach ([2=>'B2C',4=>'B2B',8=>'FBA',16=>'HELD'] as $key => $value)
								{
									echo CHtml::checkbox('ParcelPriority[parcel_type]',0,array('class'=>'upload_consol','id'=>"parcel-priority-grid_parcel_type",'value'=>$key)),$value;
								}

							?>

						</td>
						<td>
							<?php

								foreach ([1=>'YES',2=>'NO'] as $key => $value)
								{
									echo CHtml::checkbox('ParcelPriority[is_oversize]',0,array('class'=>'upload_consol','id'=>"parcel-priority-grid_is_oversize",'value'=>$key)),$value;
								}

							?>

						</td>
						<td>
							<?php

								foreach ([2=>'Rack',4=>'Ground',8=>'Held Area'] as $key => $value)
								{
									echo CHtml::checkbox('ParcelPriority[area_type]',0,array('class'=>'upload_consol','id'=>"parcel-priority-grid_area_type",'value'=>$key)),$value;
								}

							?>

						</td>
						<td>
							<?php

								foreach ([2=>'None',4=>'Level 1',8=>'Level 2',16=>'Level 3',32=>'Level 4'] as $key => $value)
								{
									echo CHtml::checkbox('ParcelPriority[priority]',0,array('class'=>'upload_consol','id'=>"parcel-priority-grid_priority",'value'=>$key)),$value;
								}

							?>

						</td>

						

						<td><a href="" class="save_btn add_btn" title="Add">Add</a></td></tr></tfoot>
					<tbody>
					<tr><td colspan="5" class="empty"><span class="empty">No results found.</span></td></tr>
					</tbody>
				</table>

			</div>
<?php
/*
			$this->widget('application.extensions.editablegrid.CEditableGridView', array(
			'id'=>'parcel-priority-range-grid',
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
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t( 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
</div>

<?php $this->renderPartial('_recommendation',["recommendation"=>$recommendation]) ?>

<script type="text/javascript">
	$(document).ready(function(e){
	var tab = $('#<?=$_GET["tabid"];?>');
	var win = tab.data('panel');
	var gridId = 'parcel-priority-range-grid<?=$_GET['dptId']?>';
	// current zone rate data
	var parcelPrioritysData = <?php echo json_encode($parcelPrioritys); ?>;

	var allParcelPrioritysRateData = [];
	var allRowIndex = 0;
	var selectedRowIndex = 0;
	appendParcelPrioritysData(parcelPrioritysData);
	var importInProgress = false;

	function isImportInProgress(){
		return importInProgress;
	}

	function appendParcelPrioritysData(rateData){
		$('#'+gridId+' .items tbody td.empty', win).parent().remove();
		var wtpl = $('#'+gridId+' .items tfoot', win);
		var row = wtpl.find('tr').clone();
		for ( var i in rateData ) {
			allRowIndex++;
			var r = wtpl.find('tr').clone();
			$('.add_btn',r).replaceWith('<a href="javascript:;" data-index="'+allRowIndex+'"class="delete_btn" title="Remove">Remove</a>');
			$('input', r).each(function(){
				var n = $(this).attr('name');
				$(this).attr('name', n+'[]');
			});
			// console.log(rateData[i]);
			$('#'+gridId+' .items tbody', win).append(r);
			r.find("input[name='ParcelPriority[parcel_type][]']").each(function(j){
				if((rateData[i]['parcel_type']&$(this).val())>0)
				{
					$(this).attr('checked',true);
				}
			});

			r.find("input[name='ParcelPriority[area_type][]']").each(function(j){
				if((rateData[i]['area_type']&$(this).val())>0)
				{
					$(this).attr('checked',true);
				}
			});


			r.find("input[name='ParcelPriority[is_oversize][]']").each(function(j){
				if((rateData[i]['is_oversize']&$(this).val())>0)
				{
					$(this).attr('checked',true);
				}
			});

			r.find("input[name='ParcelPriority[priority][]']").each(function(j){
				if((rateData[i]['priority']&$(this).val())>0)
				{
					$(this).attr('checked',true);
				}
			});


			addOneParcelPrioritysByData(allRowIndex,rateData[i]['data']);
		}
	}

	 function addOneParcelPrioritysByData(tag,rateData){
		 var bFound = false;
		 for ( var i in allParcelPrioritysRateData ) {
			 var oneZone = allParcelPrioritysRateData[i];
			 if ( oneZone['tag'] == tag ) {
				 bFound = true;
			 }
		 }
		 if ( !bFound ) {
			 var zoneData = {};
			 zoneData['tag'] = tag;
			 zoneData['parcel_type'] = 0;
			 zoneData['area_type'] = 0;
			 zoneData['is_oversize']=0;
			 zoneData['priority']=0;
			 allParcelPrioritysRateData.push(zoneData);
		 }
	 }

	 function addOneParcelPrioritysRate(tag)
	 {
		// check existing or not
		// if not existing just create a new one
		var bFound = false;
		for ( var i in allParcelPrioritysRateData ) {
			var oneZone = allParcelPrioritysRateData[i];
			if ( oneZone['tag'] == tag ) {
				bFound = true;
			}
		}
		if ( !bFound ) {
			var zoneData = {};
			zoneData['tag'] = tag;
			zoneData['parcel_type'] = 0;
			zoneData['area_type'] = 0;
			zoneData['is_oversize']=0;
			zoneData['priority']=0;
			allParcelPrioritysRateData.push(zoneData);
		}
	}

	function updateData(dataIndex,parcel_type,area_type,is_oversize,priority)
	{
		// get related zone rate data
		for ( var i in allParcelPrioritysRateData ) {
			var oneZone = allParcelPrioritysRateData[i];
			if ( oneZone['tag'] == dataIndex ) {
				allParcelPrioritysRateData[i]['parcel_type'] = parcel_type;
				allParcelPrioritysRateData[i]['area_type'] = area_type;
				allParcelPrioritysRateData[i]['is_oversize']=is_oversize;
				allParcelPrioritysRateData[i]['priority']=priority;
				break;
			}
		}
	}




	$('form#pca-chargecode-rate-form', win).on('success', function(e, r){
	//	win.data('opener').trigger('reload_contact_grid');
		win.jqmHide();
	});


	$('#'+gridId+' .add_btn', win).on('click', function(e){

		e.preventDefault();


		allRowIndex++;
		$('#'+gridId+' .items tbody td.empty', win).parent().remove();
		var r = $(this).parents('tr').clone();
		$('.add_btn',r).replaceWith('<a href="javascript:;" data-index="'+allRowIndex+'"class="delete_btn" title="Remove">Remove</a>');
		$('input', r).each(function(){
			var n = $(this).attr('name');
			$(this).attr('name', n+'[]');
		});
		$('#'+gridId+' .items tbody').append(r);

		addOneParcelPrioritysRate(allRowIndex);

		return false;
	});

	$('#'+gridId, win).on('click','tr',function(e){
		// exclude the dummy row data
		if ( $(this).parent().is("tfoot") ) return;

		// get related zone data related tag
		var tag = $(this).find('.delete_btn').data('index');
	});

	$('#'+gridId, win).on('click','.delete_btn',function(e){
		e.preventDefault();
		if ( confirm( 'Are you sure remove the priority data?') ) {
			$(this).parents('tr').remove();
			return false;
		}
		return true;
	});


	$('input[type="submit"]',win).click(function(e){
		//alert('callme');
		e.preventDefault();
		// update all lo and hi weight range
		$('#'+gridId, win).find('tr').each(function(e){
			var dataIndex = $(this).find('.delete_btn').data('index');
			if ( !isNaN(dataIndex) ) {
				var parcel_type = 0;
				var area_type = 0;
				var is_oversize = 0;
				var priority = 0;

				$(this).find("input[name='ParcelPriority[parcel_type][]']:checked").each(function(i){
					parcel_type += parseInt($(this).val());
				});

				$(this).find("input[name='ParcelPriority[area_type][]']:checked").each(function(i){
					area_type += parseInt($(this).val());
				});


				$(this).find("input[name='ParcelPriority[is_oversize][]']:checked").each(function(i){
					is_oversize += parseInt($(this).val());
				});

				$(this).find("input[name='ParcelPriority[priority][]']:checked").each(function(i){
					priority +=parseInt($(this).val());
				});



				updateData(dataIndex,parcel_type,area_type,is_oversize,priority);
			}
		});

		var postdata = JSON.stringify(allParcelPrioritysRateData);
		// ajax post to save them
		var dptId = $('#dptId', $('#'+gridId)).val();
		$.ajax({
			type : 'POST',
			url : '<?php echo Yii::app()->createAbsoluteUrl("wmsLocation/saveParcelPriority") ;?>',
			dataType: 'json',
			data:{ 'dptId' : dptId,'data' : postdata},
			success:function(resp){
				if ( resp.success == 1 ) {
					myApp.notice('flex rate saved successfully!', 5000);
				} else {
					myApp.alert(resp.msg);
				}
			}
		});

	});
});
</script>