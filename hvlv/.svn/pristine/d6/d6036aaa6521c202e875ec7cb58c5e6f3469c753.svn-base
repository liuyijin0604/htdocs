<style type="text/css">
label.left {
	float: left;
	min-width: 70px;
}
</style>


	<div>
		<a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('priceEnquiry/excel');?>" title="New Enquiry" style="float:right"><div class="icon" style="background-position:-160px 0"></div>&nbsp&nbspEnquiry By Excel</a> 
		<a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('priceEnquiry/edit');?>" title="New Enquiry" style="float:right"><div class="icon" style="background-position:-16px 0"></div>&nbsp&nbspFull Enquiry&nbsp&nbsp</a> 
		<a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('priceEnquiry/quickEnquary');?>" title="Quick Enquiry" style="float:right"><div class="icon" style="background-position:-32px 0"></div>*&nbsp&nbspQuick Enquiry&nbsp&nbsp</a>
	</div>


  
  <h1>Price Enquiry</h1>

	<?php
	$objPriceEnquiry = new PriceEnquiry('search');

	if(isset($_GET['PriceEnquiry'])){
		$objPriceEnquiry->setAttributes($_GET['PriceEnquiry']);
	}

	
	$objPriceEnquiry->setAttributes(['org_id'=>Yii::app()->user->org]);


	$this->widget('zii.widgets.grid.CGridView', [
		'id'=>'price_enquiry_grid',
		'cssFile' => false,
		'dataProvider'=>$objPriceEnquiry->search(),
		'filter'=>$objPriceEnquiry,
		'columns'=>[
			'date',
			['header' => 'Quotation No.', 'name' => 'code','type'=>'raw','value' => '$data->funcPE()'],
			'address',
			'suburb',
			'postcode',
			['header' => 'Total Weight', 'name' => 'weight'],
			['header' => 'Total Quantity', 'name' => 'quantity'],
			['header' => 'Enquiry From', 'name' => 'name'],
			['header' => 'Tel', 'name' => 'tel'],
			['header' => 'Email', 'name' => 'email'],
			['header' => 'Customer Notes', 'name' => 'note'],
			'paid_by',
			['header' => 'Memo', 'name' => 'memo'],
			'price',
			['name' => 'status', 'value' => 'PriceEnquiry::listStatus[$data->status]', 'filter'=>CHtml::dropDownList('PriceEnquiry[status]', $objPriceEnquiry->status, $this->t([''=>'All']+PriceEnquiry::listStatus)),],
			[
				'class' => 'oButtonColumn',
				'template' => '{View}',
				'buttons' => [
					'View' => [
						'imageUrl' => false,
						'visible' => 'true',
						'url' => 'Yii::app()->createUrl("ims/priceEnquiry/view", ["id" => $data->id])',
						'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => $this->t('View'), 'title' => '$data->id'),
					],
				],
			],
		],
	]);
	?>




</div>

