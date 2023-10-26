<style type="text/css">
label.left {
	float: left;
	min-width: 70px;
}
.width_100{
	width: 100px;
	display: inline-block;
}

.width_545{
	width: 545px;
}


</style>

<div class="grid-view width_545">
	<table class="items">
		<thead>
			<tr>
				<th>Depot</th>
				<th>New</th>
				<th>Waitting</th>
				<th>Active</th>
				<th>Used</th>
				<th>Canceled</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$dic=[
			'Sydney'=>[
				PriceEnquiry::status_new =>0,
				PriceEnquiry::status_wait_quo =>0,
				PriceEnquiry::status_active =>0,
				PriceEnquiry::status_cannot_do => 0,
				PriceEnquiry::status_inactive =>0,
				PriceEnquiry::status_canceled =>0,
			],
			'Melbourne'=>[
				PriceEnquiry::status_new =>0,
				PriceEnquiry::status_wait_quo =>0,
				PriceEnquiry::status_active =>0,
				PriceEnquiry::status_cannot_do => 0,
				PriceEnquiry::status_inactive =>0,
				PriceEnquiry::status_canceled =>0,
			],
			'Brisbane'=>[
				PriceEnquiry::status_new =>0,
				PriceEnquiry::status_wait_quo =>0,
				PriceEnquiry::status_active =>0,
				PriceEnquiry::status_cannot_do => 0,
				PriceEnquiry::status_inactive =>0,
				PriceEnquiry::status_canceled =>0,
			],
			'Perth'=>[
				PriceEnquiry::status_new =>0,
				PriceEnquiry::status_wait_quo =>0,
				PriceEnquiry::status_active =>0,
				PriceEnquiry::status_cannot_do => 0,
				PriceEnquiry::status_inactive =>0,
				PriceEnquiry::status_canceled =>0,
			],
		];

		$listPriceEnquiry = PriceEnquiry::model()->findAll();
		foreach($listPriceEnquiry as $objPriceEnquiry){
			$dic[$objPriceEnquiry->depot][$objPriceEnquiry->status]++;
		}

		foreach ($dic as $strDepot => $listStatus){
			echo '<tr><td>'.$strDepot.'</td>';
			foreach ($listStatus as $num){
				echo '<td>'.$num.'</td>';
			}
			echo '</tr>';

		}
		?>

		</tbody>
	</table>

</div>


	<div style="right: 20px;position: absolute;">
		<a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('priceEnquiry/edit');?>" title="New Enquiry"><div class="icon" style="background-position:-16px 0"></div>New Enquiry</a>  
	</div>


  <br/>  <br/>  <br/>
  <h1>Price Enquiry</h1>

	<?php
	$objPriceEnquiry = new PriceEnquiry('search');

	if(isset($_GET['PriceEnquiry'])){
		$objPriceEnquiry->setAttributes($_GET['PriceEnquiry']);
	}

	


	$this->widget('zii.widgets.grid.CGridView', [
		'id'=>$_GET["tabid"].'_price_enquiry_grid',
		'cssFile' => false,
		'dataProvider'=>$objPriceEnquiry->search(),
		'filter'=>$objPriceEnquiry,
		'columns'=>[
			'date',
			['header' => 'Quotation No.', 'name' => 'code'],
			'address',
			'suburb',
			'postcode',
			['header' => 'Total Weight', 'name' => 'weight'],
			['header' => 'Total Quantity', 'name' => 'quantity'],
			['header' => 'Org', 'name' => 'org_id'],
			['header' => 'Enquiry From', 'name' => 'name'],
			['header' => 'Tel', 'name' => 'tel'],
			['header' => 'Email', 'name' => 'email'],
			['header' => 'Customer Notes', 'name' => 'note'],
			'paid_by',
			['header' => 'Memo', 'name' => 'memo'],
			'price',
			['name' => 'depot', 'value' => 'PriceEnquiry::listDepot[$data->depot]', 'filter'=>CHtml::dropDownList('PriceEnquiry[depot]', $objPriceEnquiry->depot, $this->t([''=>'All']+PriceEnquiry::listDepot)),],
			['name' => 'status', 'value' => 'PriceEnquiry::listStatus[$data->status]', 'filter'=>CHtml::dropDownList('PriceEnquiry[status]', $objPriceEnquiry->status, $this->t([''=>'All']+PriceEnquiry::listStatus)),],
			[
				'class' => 'oButtonColumn',
				'template' => '{update}',
				'buttons' => [
					'update' => [
						'imageUrl' => false,
						'visible' => 'true',
						'url' => 'Yii::app()->createUrl("priceEnquiry/edit", ["id" => $data->id])',
						'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Update'), 'title' => '$data->id'),
					],
				],
			],
		],
	]);
	?>




</div>