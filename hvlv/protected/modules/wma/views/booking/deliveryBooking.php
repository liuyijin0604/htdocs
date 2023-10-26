<style type="text/css">
	@media only screen and (max-width: 767px)  {

		/* Force table to not be like tables anymore */
		#db_grid_view table,#db_grid_view thead,#db_grid_view tbody,#db_grid_view th,#db_grid_view td,#db_grid_view tr {
			display: block;
		}

		/* Hide table headers (but not display: none;, for accessibility) */
		#db_grid_view thead tr {
			position: absolute;
			top: -9999px;
			left: -9999px;
		}
		#db_grid_view thead tr.filters{
			position:relative;
			top: 0;
			left: 0;
		}

		#db_grid_view tr { border: 1px solid #ccc; }

		#db_grid_view td {
			/* Behave  like a "row" */
			border: none;
			border-bottom: 1px solid #eee;
			position: relative;
			padding-left: 50%;
		}

		#db_grid_view td:before {
			/* Now like a table header */
			position: absolute;
			/* Top/left values mimic padding */
			top: 6px;
			left: 6px;
			width: 45%;
			padding-right: 10px;
			white-space: nowrap;
		}
		.grid-view .button-column {
			text-align: left;
			width:auto;
		}
		/*
		Label the data
		*/
		#db_grid_view td:nth-of-type(1):before { content: 'No '; }
#db_grid_view td:nth-of-type(2):before { content: 'Rego '; }
#db_grid_view td:nth-of-type(3):before { content: 'Mobile '; }
#db_grid_view td:nth-of-type(4):before { content: 'Company Name'; }
#db_grid_view td:nth-of-type(5):before { content: 'Name '; }
#db_grid_view td:nth-of-type(6):before { content: 'Booking Type'; }
#db_grid_view td:nth-of-type(7):before { content: 'Booking Time '; }
#db_grid_view td:nth-of-type(8):before { content: 'Note '; }
#db_grid_view td:nth-of-type(9):before { content: 'Plt '; }
#db_grid_view td:nth-of-type(10):before { content: 'Pkg '; }
#db_grid_view td:nth-of-type(11):before { content: 'operation'; }

	}
</style>
<style type="text/css">
	.table-striped
	{
		width: 100%;
	}
	.create_booking
	{
		font-size: 2em;
		width:100%;
		height: 100%;
	}
</style>

<div class="content-padded" id="delivery-booking">
	<div class="row">
			<div class="col-8">
				<h3>Delivery Booking</h3>
			</div>
			<div class="col-3" style="padding-left: 5px;float:right">
				<?php echo CHtml::link($this->t('+'),$this->createUrl('booking/createBooking'),array("data-transition"=>"slide-in",'class' => 'save_btn btn btn-primary btn-block create_booking ')); ?>
			</div>
	</div>

	<div class="row">
		<div class="col-6">
			<?php echo CHtml::link($this->t('advanced search'),'#',array('id' => 'adSearch')); ?>
		</div>
	</div>

	<div>
		<?php

				$columns = array(
				    		//'pickup',
							array('name'=>'no'),
							array('name'=>'rego'),
							array('name'=>'mobile','type'=>'raw','value'=>'empty($data->mobile)?"&nbsp;":$data->mobile'),
							array('header'=>'Company Name','filter'=>false,'type'=>'raw','value'=>'empty(@$data->mdata["company_name"])?"&nbsp;":@$data->mdata["company_name"]'),
							array('name'=>'name'),
							array('header'=>'Booking Type','type'=>'raw','filter'=>false,'value'=>'@$data->getBookingType();'),
							array('name'=>'booking_time','type'=>'raw','filter'=>'<input type ="hidden" name="DeliveryRecord[uuid]" value="'.$model->uuid.'" id ="dbuuid"/><input type="date" name="DeliveryRecord[booking_time]" value='.@$model->booking_time.' >'),
				         	array('name'=>'note','type'=>'raw','value'=>'empty($data->note)?"&nbsp;":$data->note','filter'=>false),
				         	array('name'=>'plt','filter'=>false,'type'=>'raw','value'=>'empty($data->plt)?"&nbsp;":$data->plt'),
				         	array('name'=>'pkg','filter'=>false,'type'=>'raw','value'=>'empty($data->pkg)?"&nbsp;":$data->pkg'),
				         	[
				         		'header'=>"operation",'class'=>'oButtonColumn',
								'template'=>'{edit}&nbsp;{Waiting Position}&nbsp;{cancel}',
								'buttons'=>[
									'edit' => [
										'url'=>'Yii::app()->createURL("wma/booking/editBooking")."?id=".$data->id."&&uuid=".$data->uuid',
										'imageUrl'=>false,
										'visible'=>'true',
										'options' => ['class' => 'edit_book save_btn btn btn-green btn-block', 'label'=>$this->t('Operation'),"data-transition"=>"slide-in", 'title' => '$data->id','id'=>'edit_book'],
									],
									'Waiting Position' => [
										'url'=>' Yii::app()->createURL("wma/booking/checkList")."?id=".$data->id',
										'imageUrl'=>false,
										'visible'=>'true',
										'options' => ['class' => 'check_list btn btn-green btn-block', 'label'=>$this->t('Operation'),"data-transition"=>"slide-in", 'title' => '$data->id','value'=>'$data->id','id'=>'check_list'],
									],
									'cancel' => [
										'imageUrl'=>false,
										'options' => ['class' => 'cancel_booking btn btn-red btn-block ', 'label' => 'Log', 'data-win-class' => 'L','value'=>'$data->id','id'=>'cancel_booking'],
										'visible' => 'true',
										'url' => 'Yii::app()->createUrl("wma/booking/cancelBooking")."?id=".$data->id',
										'label' => 'cancel'
									],
								],
							]
				         );



				 $this->widget('application.extensions.booster.TbExtendedGridView',array(
				    'fixedHeader'=>true,
				    'id'=>'db_grid_view',
				    'filter'=>$model,
				    'type'=>'striped bordered',
				    'headerOffset'=>40,
				    'responsiveTable'=>true,
				    'dataProvider'=>$model->search(),
				    'template' => "{summary}\n{items}\n{pager}",
				    'afterAjaxUpdate'=>'function(){initButtons();hideNull();}',
				    'columns'=>$columns,

				    )
				    
				); ?>
	</div>
	<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'sign-form',
		'enableAjaxValidation' => false,
	)); ?>
		

	<?php $this->endWidget(); ?>

	</div>
</div>

<script type="text/javascript">
	var pcadbAdStatus = 0;
	function initButtons()
	{
		$('.cancel_booking').on('touchend',function()
		{
			if(confirm("Are you sure to cancel this booking?"))
			{
				let id = $(this).attr("value");
				let uuid = $('#dbuuid').val();
				$.ajax({
					url: "<?=$this->createUrl('booking/cancelBooking');?>"+"?id="+id+"&&uuid="+uuid,
					dataType: 'json',
					type: "get",
					data: [],
					processData: false,
					contentType: false,
					success: function(r){
						if(r.done)
						{
							$('#db_grid_view').yiiGridView('update', {data: $('.filters input, .filters select').serialize() + '&' + $(this).serialize()});
						}
					},
					error: function(r, e){alert(e);}
				});
			}
			return false;
		});

		$('.check_list').on('touchend',function()
		{
			checkList(this);
			return false;
		});

		function checkList(thisElement)
		{
			let id = $(thisElement).attr("value");
				let uuid = $('#dbuuid').val();
				$.ajax({
					url: "<?=$this->createUrl('booking/checkList');?>"+"?id="+id+"&&uuid="+uuid,
					dataType: 'json',
					type: "get",
					data: [],
					processData: false,
					contentType: false,
					success: function(r){
						if(r.code)
						{
							alert(r.data);
						}
					},
					error: function(r, e){alert(e);}
				});
		}

		$('#db_grid_view a').click(function(){
		var btn = this;  
		var event = document.createEvent('Events');
		event.initEvent('touchend', true, true); 
		btn.dispatchEvent(event); 
		});

		if(document.body.clientWidth<=767)
		{
			$('.filters')[0].style.display="none";
		}

	}
	
	/*<![CDATA[*/
	jQuery(function($) {
	jQuery('#db_grid_view').yiiGridView({'ajaxUpdate':['db_grid_view'],'ajaxVar':'ajax','pagerClass':'no\x2Dclass','loadingClass':'grid\x2Dview\x2Dloading','filterClass':'filters','tableClass':'items\x20table\x20table\x2Dstriped\x20table\x2Dbordered','selectableRows':1,'enableHistory':false,'updateSelector':'\x7Bpage\x7D,\x20\x7Bsort\x7D','filterSelector':'\x7Bfilter\x7D','pageVar':'DeliveryRecord_page','afterAjaxUpdate':function(){initButtons();hideNull();},'selectionChanged':function(id) {
					$("#"+id+" input[type=checkbox]").change();
				}});
	});
	/*]]>*/

	$(window).on("load", function() {
		var pcadbAdStatus = 0;
		if (!window.localStorage) 
		{
		    alert('This browser does NOT support');
		}else
		{
			$('#dbuuid').val(pcadbApp.uuid());
			$('#db_grid_view').yiiGridView('update', {data: $('.filters input, .filters select').serialize() + '&' + $(this).serialize()});
		}

		// $('#adSearch').on('touchend',function()
		// {
		// 	if(pcadbAdStatus==0)
		// 	{
		// 		$('#db_grid_view thead tr.filters').css("width","100%");
		// 		$('#db_grid_view thead tr.filters').show();
		// 		$('#DeliveryRecord_no').focus();
		// 		pcadbAdStatus = 1;
		// 	}else
		// 	{
		// 		$('#db_grid_view thead tr.filters').css("width","100%");
		// 		$('#db_grid_view thead tr.filters').hide();
		// 		$('#DeliveryRecord_no').focus();
		// 		pcadbAdStatus = 0;
		// 	}
		// });
	});


	$('#adSearch').on('touchend',function()
	{
		if(pcadbAdStatus==0)
		{
			$('#db_grid_view thead tr.filters').css("width","100%");
			$('#db_grid_view thead tr.filters').show();
			$('#DeliveryRecord_no').focus();
			pcadbAdStatus = 1;
		}else
		{
			$('#db_grid_view thead tr.filters').css("width","100%");
			$('#db_grid_view thead tr.filters').hide();
			$('#DeliveryRecord_no').focus();
			pcadbAdStatus = 0;
		}
		return false;
	});

	if($('#dbuuid')!=undefined)
	{
		$('#dbuuid').val(pcadbApp.uuid());
		$('#db_grid_view').yiiGridView('update', {data: $('.filters input, .filters select').serialize() + '&' + $(this).serialize()});
	}

	if($('#db_grid_view')!=undefined)
	{
		$('#db_grid_view').yiiGridView('update', {data: $('.filters input, .filters select').serialize() + '&' + $(this).serialize()});
	}

	function hideNull()
	{
		if($('.filters td .filter-container')!=undefined)
		{
			let containers = $('.filters td .filter-container');
			for (var i = 0; i <= containers.length; i++) 
			{
				if(containers[i]!=undefined && containers[i].innerHTML == "&nbsp;")
				{
					console.log("123");
					containers[i].parentNode.style.display="none";
				}
			}
		}
	}

	hideNull();


	$('#delivery-booking a').click(function(){
		var btn = this;  
		var event = document.createEvent('Events');
		event.initEvent('touchend', true, true); 
		btn.dispatchEvent(event); 
	});

	pcadbApp.initLink();

</script>
