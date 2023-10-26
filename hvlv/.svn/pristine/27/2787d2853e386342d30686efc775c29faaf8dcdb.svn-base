<style type="text/css">
/*<![CDATA[*/
@media only screen and (max-width: 767px)  {

		/* Force table to not be like tables anymore */
		#job_grid_view table,#job_grid_view thead,#job_grid_view tbody,#job_grid_view th,#job_grid_view td,#job_grid_view tr {
			display: block;
		}

		/* Hide table headers (but not display: none;, for accessibility) */
		#job_grid_view thead tr {
			position: absolute;
			top: -9999px;
			left: -9999px;
		}
		#job_grid_view thead tr.filters{
			position:relative;
			top: 0;
			left: 0;
		}

		#job_grid_view tr { border: 1px solid #ccc; }

		#job_grid_view td {
			/* Behave  like a "row" */
			border: none;
			border-bottom: 1px solid #eee;
			position: relative;
			padding-left: 50%;
		}

		#job_grid_view td:before {
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
		#job_grid_view td:nth-of-type(1):before { content: 'No '; }
#job_grid_view td:nth-of-type(2):before { content: 'Rego '; }
#job_grid_view td:nth-of-type(3):before { content: 'Mobile '; }
#job_grid_view td:nth-of-type(4):before { content: 'Company Name'; }
#job_grid_view td:nth-of-type(5):before { content: 'Name'; }
#job_grid_view td:nth-of-type(6):before { content: 'Booking Time '; }
#job_grid_view td:nth-of-type(7):before { content: 'Type '; }
#job_grid_view td:nth-of-type(8):before { content: 'Note '; }
#job_grid_view td:nth-of-type(9):before { content: 'Plt '; }
#job_grid_view td:nth-of-type(10):before { content: 'Pkg '; }
#job_grid_view td:nth-of-type(11):before { content: 'operation'; }

	}
/*]]>*/
</style>

<div class="content-padded">
	<div class="col-8">
				<h3>Delivery Record/Booking</h3>
	</div>
	<div class="col-2" style="padding-left: 5px;float:right">
		<?php echo CHtml::link($this->t('+'),$this->createUrl('job/delivery'),array("data-transition"=>"slide-in",'class' => 'save_btn btn btn-primary btn-block create_booking ')); ?>
	</div>

<?php

$columns = array(
				    		//'pickup',
							array('name'=>'no'),
							array('name'=>'rego'),
							array('name'=>'mobile','type'=>'raw','value'=>'empty($data->mobile)?"&nbsp;":$data->mobile'),
							array('header'=>'Company Name','filter'=>false,'type'=>'raw','value'=>'empty(@$data->mdata["company_name"])?"&nbsp;":@$data->mdata["company_name"]'),
							array('name'=>'name','filter'=>false),
							array('name'=>'booking_time','filter'=>false,'type'=>'raw'),
							array('header'=>'Booking Type','type'=>'raw','filter'=>false,'value'=>'@$data->getBookingType();'),
							array('name'=>'note','filter'=>false,'type'=>'raw','value'=>'empty($data->note)?"&nbsp;":$data->note'),
				         	array('name'=>'plt','filter'=>false,'type'=>'raw','value'=>'empty($data->plt)?"&nbsp;":$data->plt'),
				         	array('name'=>'pkg','filter'=>false,'type'=>'raw','value'=>'empty($data->pkg)?"&nbsp;":$data->pkg'),
				         	[
				         		'header'=>"operation",'class'=>'oButtonColumn',
								'template'=>'{confirm}&nbsp;',
								'buttons'=>[
									'confirm' => [
										'url'=>' Yii::app()->createURL("wma/job/confirmDeliveryRecord")."?id=".$data->id',
										'imageUrl'=>false,
										'visible'=>'$data->status==DeliveryRecord::NEWSTATE?true:false',
										'options' => ['class' => 'save_btn btn btn-green btn-block', 'label'=>$this->t('Operation'),"data-transition"=>"slide-in", 'title' => '$data->id'],
									]
								],
							]
				         );


 $this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>'job_grid_view',
    'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model->search(true,30,'ASC'),
    'template' => "{summary}\n{items}\n{pager}",
    'afterAjaxUpdate'=>'function(){hideNull();}',
    'columns'=>$columns,
    )); ?>
</div>

<script type="text/javascript">

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

	/*<![CDATA[*/
	jQuery(function($) {
	jQuery('#job_grid_view').yiiGridView({'ajaxUpdate':['job_grid_view'],'ajaxVar':'ajax','pagerClass':'no\x2Dclass','loadingClass':'grid\x2Dview\x2Dloading','filterClass':'filters','tableClass':'items\x20table\x20table\x2Dstriped\x20table\x2Dbordered','selectableRows':1,'enableHistory':false,'updateSelector':'\x7Bpage\x7D,\x20\x7Bsort\x7D','filterSelector':'\x7Bfilter\x7D','pageVar':'DeliveryRecord_page','afterAjaxUpdate':function(){hideNull();},'selectionChanged':function(id) {
					$("#"+id+" input[type=checkbox]").change();
				}});
	});
	/*]]>*/

	if($('#job_grid_view')!=undefined)
	{
		$('#job_grid_view').yiiGridView('update', {data: $('.filters input, .filters select').serialize() + '&' + $(this).serialize()});
	}
	
	hideNull();

</script>
