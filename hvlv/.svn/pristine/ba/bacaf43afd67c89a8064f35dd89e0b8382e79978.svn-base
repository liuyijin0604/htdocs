<style type="text/css">
/*<![CDATA[*/
/*@media only screen and (max-width: 767px)  {

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
		#job_grid_view td:nth-of-type(1):before { content: 'Rego '; }
		#job_grid_view td:nth-of-type(2):before { content: 'Pallet '; }
		#job_grid_view td:nth-of-type(3):before { content: 'Note '; }
		#job_grid_view td:nth-of-type(4):before { content: 'Status '; }
		#job_grid_view td:nth-of-type(5):before { content: 'detail'; }
		#job_grid_view td:nth-of-type(6):before { content: 'Time '; }

	}*/
/*]]>*/
</style>

<div class="content-padded">
	<h3>Delivery Record</h3>

<?php

$columns = array(
    		//'pickup',
			array('name'=>'rego'),
         	array('name'=>'plt'),
         	array('name'=>'note'),
         	array('name'=>'status'),
         	array('header'=>'detail','value'=>''),
         	array('name'=>'created'));


	// $columns[]= [
 //         		'header'=>'Operation',
 //         		'class'=>'oButtonColumn',
	// 			'template'=>'{Sign}&nbsp;{Receipt}&nbsp;{Price}',
	// 			'buttons'=>[
	// 				'Sign' => [
	// 					'url'=>' Yii::app()->createURL("dplatform/job/signPage",["id"=>$data->id])',
	// 					'imageUrl'=>false,
	// 					'visible'=>'@$data->mdata["driverDeliveryStatus"]==CargoProcess::DELIVERIED||$data->status>CargoProcess::WAITINGAGENTDELIVERY?false:true',
	// 					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
	// 				],
	// 				'Receipt' => [
	// 					'url'=>' Yii::app()->createURL("dplatform/job/viewCargoReceipt",["id"=>$data->id])',
	// 					'imageUrl'=>false,
	// 					'visible'=>'@$data->mdata["driverDeliveryStatus"]==CargoProcess::DELIVERIED||$data->status>CargoProcess::WAITINGAGENTDELIVERY?true:false',
	// 					'options' => ['class' => 'jqm_link grid_view_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
	// 				],
	// 				'Price' => [
	// 					'url'=>' Yii::app()->createURL("dplatform/invoice/viewCargoInvoice")."?id=".$data->id',
	// 					'imageUrl'=>false,
	// 					'visible'=>'false',
	// 					'options' => ['class' => 'jqm_link grid_view_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
	// 				],
	// 			],
	// 		];

 $this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>'job_grid_view',
    'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model->search(),
    'template' => "{summary}\n{items}\n{pager}",
    'afterAjaxUpdate'=>'function(){initButtons();}',
    'columns'=>$columns,
    ),
    
); ?>
</div>

<script type="text/javascript">


</script>
