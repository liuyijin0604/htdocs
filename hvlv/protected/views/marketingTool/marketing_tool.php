<style type="text/css">
label.left {
	float: left;
	min-width: 70px;
}
</style>

<div id="<?=$_GET['tabid']?>marketing_tool_tabs">
  <ul>
	<li><a href="#<?=$_GET["tabid"];?>_address_list">Address List</a></li>
	<li><a href="#<?=$_GET["tabid"];?>_email_list">Email List</a></li>
  </ul>

  <div id="<?=$_GET["tabid"];?>_address_list" class="pane">
	<div style="right: 20px;position: absolute;">
		<a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('marketingTool/editAddress');?>" title="New Address"><div class="icon" style="background-position:-16px 0"></div>New Address</a>  
		<a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('marketingTool/importExcelAddress');?>" title="New Address"><div class="icon" style="background-position:-16px 0"></div>Import  Excel</a>  
		<a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('marketingTool/emailMaxLimitation');?>" title="Marketing Tool Settings"><div class="icon" style="background-position:-16px 0"></div>Marketing Tool Settings</a>  
	</div>


  
  <h1>Address List</h1>

	<?php
	$objMarketingAddress = new MarketingAddress('search');
	$objMarketingAddress->status = 1;
	if(isset($_GET['MarketingAddress'])){
		$objMarketingAddress->setAttributes($_GET['MarketingAddress']);
	}

	

	$this->widget('zii.widgets.grid.CGridView', [
		'id'=>$_GET["tabid"].'_address-grid',
		'cssFile' => false,
		'dataProvider'=>$objMarketingAddress->search(),
		'filter'=>$objMarketingAddress,
		'columns'=>[
			'company',
			'country',
			'contact',
			'email',
			'tel',
			['name' => 'imp', 'value' => 'MarketingAddress::listYesNo[$data->imp]', 'filter'=>CHtml::dropDownList('MarketingAddress[imp]', $objMarketingAddress->imp, $this->t([''=>'All']+MarketingAddress::listYesNo)),],
			['name' => 'tpl', 'value' => 'MarketingAddress::listYesNo[$data->tpl]', 'filter'=>CHtml::dropDownList('MarketingAddress[tpl]', $objMarketingAddress->tpl, $this->t([''=>'All']+MarketingAddress::listYesNo)),],
			['name' => 'tld', 'value' => 'MarketingAddress::listYesNo[$data->tld]', 'filter'=>CHtml::dropDownList('MarketingAddress[tld]', $objMarketingAddress->tld, $this->t([''=>'All']+MarketingAddress::listYesNo)),],
			['name' => 'status', 'value' => 'MarketingAddress::listStatus[$data->status]', 'filter'=>CHtml::dropDownList('MarketingAddress[status]', $objMarketingAddress->status, $this->t([''=>'All']+MarketingAddress::listStatus)),],
			'group',
			[
				'class' => 'oButtonColumn',
				'template' => '{update}',
				'buttons' => [
					'update' => [
						'imageUrl' => false,
						'visible' => 'true',
						'url' => 'Yii::app()->createUrl("marketingTool/editAddress", ["id" => $data->id])',
						'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Update'), 'title' => '$data->id'),
					],
				],
			],
		],
	]);
	?>
  </div>


  <div id="<?=$_GET["tabid"];?>_email_list" class="pane">
	<div style="right: 20px;position: absolute;">
		<a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('marketingTool/editEmail');?>" title="New Email"><div class="icon" style="background-position:-16px 0"></div>New Email</a>  
		<a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('marketingTool/sendingHistory');?>" title="Sending History"><div class="icon" style="background-position:-16px 0"></div>Sending History</a>  
	</div>

	<h1>Email List</h1>
	<?php
		$objMarketingEmail = new MarketingEmail('search');
		if(isset($_GET['MarketingEmail'])){
			$objMarketingEmail->setAttributes($_GET['MarketingEmail']);
		}

		

		$this->widget('zii.widgets.grid.CGridView', [
			'id'=>$_GET["tabid"].'_email-grid',
			'cssFile' => false,
			'dataProvider'=>$objMarketingEmail->search(),
			'filter'=>$objMarketingEmail,
			'columns'=>[
				'subject',
				// 'html',
				'date',
				['name' => 'status', 'value' => 'MarketingEmail::listStatus[$data->status]', 'filter'=>CHtml::dropDownList('MarketingEmail[status]', $objMarketingEmail->status, $this->t([''=>'All']+MarketingEmail::listStatus))],
				[
					'class' => 'oButtonColumn',
					'template' => '{Edit}{Group}{Send}{Schedule}', 
					//'template' => '{Edit}{Schedule}',
					'buttons' => [
						'Edit' => [
							'imageUrl' => false,
							'visible' => 'true',
							'url' => 'Yii::app()->createUrl("marketingTool/editEmail", ["id" => $data->id])',
							'options' => array('class' => 'jqm_link grid_edit_btn','data-win-class'=>'L','label' => $this->t('Update'), 'title' => '$data->id'),
						],
						'Group' => [
							'imageUrl' => false,
							'visible' => 'true',
							'url' => 'Yii::app()->createUrl("marketingTool/selectGroup", ["id" => $data->id])',
							'options' => array('class' => 'jqm_link grid_edit_btn','data-win-class'=>'L','label' => $this->t('Update'), 'title' => '$data->id'),
						],
						'Send' => [
							'imageUrl' => false,
							'visible' => 'true',
							'options' => ['class' => 'grid_edit_btn', 'label' => $this->t('Update'), 'title' => '$data->id', 'onclick'=>'"funcSend($data->id)"'],
						],
						'Schedule' => [
							'imageUrl' => false,
							'visible' => 'true',
							'url' => 'Yii::app()->createUrl("marketingTool/scheduleEmail", ["id" => $data->id])',
							'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Update'), 'title' => '$data->id'),
						],
					],
				],
			],
		]);
		?>

  </div>

</div>

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	var tab = $('#<?=$_GET["tabid"];?>');
	var tabs = $('#<?=$_GET["tabid"];?>marketing_tool_tabs').tabs();
	
	var ckDestroy = function(){
		if(CKEDITOR){
			for(i in CKEDITOR.instances){
				if($('#'+i, tabs).length > 0) CKEDITOR.instances[i].destroy(true);
			}
		}
	};
	
	tab.on('close', ckDestroy);

	win.on('close', ckDestroy);
	
});


function funcSend(numId){
	if(confirm("Do you want to send?") ){
        setTimeout(() => {
			var listData = new FormData();
            listData.append("id",numId);

            htmlobj = $.ajax({
                type:"POST",
                url: "/marketingTool/sendEmail",
                data: listData,
				contentType: false,
                processData: false,
                async: false
            });
            obj = JSON.parse(htmlobj.responseText);
            if(obj.isSuccess == false){
				alert("Unsuccessful: You havn't select a group.")
            }
			else{
				alert("Email has been sent.")
			}
        }, 0);
	}
}

</script>