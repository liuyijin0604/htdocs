<style type="text/css">
/*<![CDATA[*/
@media only screen and (max-width: 767px)  {

		/* Force table to not be like tables anymore */
		#time_grid_view table,#time_grid_view thead,#time_grid_view tbody,#time_grid_view th,#time_grid_view td,#time_grid_view tr {
			display: block;
		}

		/* Hide table headers (but not display: none;, for accessibility) */
		#time_grid_view thead tr {
			position: absolute;
			top: -9999px;
			left: -9999px;
		}
		#time_grid_view thead tr.filters{
			position:relative;
			top: 0;
			left: 0;
		}

		#time_grid_view tr { border: 1px solid #ccc; }

		#time_grid_view td {
			/* Behave  like a "row" */
			border: none;
			border-bottom: 1px solid #eee;
			position: relative;
			padding-left: 50%;
		}

		#time_grid_view td:before {
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
		#time_grid_view td:nth-of-type(1):before { content: 'datetime '; }
#time_grid_view td:nth-of-type(2):before { content: 'num'; }

	}

	.clickTheTime
	{
		font-size:1em;
	}
	/*]]>*/
</style>
<div class="content-padded">
	<div class="col-8">
				<h3>Delivery Booking Summary</h3>
	</div>
	<div class="col-2" style="padding-left: 5px;float:right">
		<?php echo CHtml::link($this->t('+'),$this->createUrl('job/delivery'),array("data-transition"=>"slide-in",'class' => 'save_btn btn btn-primary btn-block create_booking ')); ?>
	</div>
		<?php

				$columns = array(
				    		//'pickup',
							array('name'=>'datetime','type'=>'raw','value'=>'$data["datetime"]!="none"?"<a href=\"".Yii::app()->createURL("wma/job/getDeliveryRecordList")."?booking_time=".$data["datetime"]."\" class=\"clickTheTime\" title=\"".$data["datetime"]."\" data-transition=\"slide-in\" >".$data["datetime"]."</a>":"&nbsp;"','filter'=>'<input type="date" value="'.$date.'" name ="date"/>'),
							array('name'=>'num','filter'=>false,'type'=>'raw','value'=>'"<p class=\"clickTheTime\" \">".$data["num"]."</p>"')
				         );

				 $this->widget('application.extensions.booster.TbExtendedGridView',array(
				    'fixedHeader'=>true,
				    'id'=>'time_grid_view',
				    'filter'=>$dataProvider[1],
				    'type'=>'striped bordered',
				    'headerOffset'=>40,
				    'responsiveTable'=>true,
				    'dataProvider'=>$dataProvider[0],
				    'template' => "{summary}\n{items}\n{pager}",
				    'afterAjaxUpdate'=>'function(){}',
				    'columns'=>$columns,
				    )
				    
				); ?>

</div>

<script type="text/javascript">

	/*<![CDATA[*/
		jQuery(function($) {
		jQuery('#time_grid_view').yiiGridView({'ajaxUpdate':['time_grid_view'],'ajaxVar':'ajax','pagerClass':'no\x2Dclass','loadingClass':'grid\x2Dview\x2Dloading','filterClass':'filters','tableClass':'items\x20table\x20table\x2Dstriped\x20table\x2Dbordered','selectableRows':1,'enableHistory':false,'updateSelector':'\x7Bpage\x7D,\x20\x7Bsort\x7D','filterSelector':'\x7Bfilter\x7D','pageVar':'page','afterAjaxUpdate':function(){},'selectionChanged':function(id) {
						$("#"+id+" input[type=checkbox]").change();
					}});
		});
		/*]]>*/

	$('#time_grid_view').yiiGridView('update', {data: $('.filters input, .filters select').serialize() + '&' + $(this).serialize()});

</script>
