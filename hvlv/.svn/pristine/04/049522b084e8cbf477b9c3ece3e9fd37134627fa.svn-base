<head>
    <style>
    .req_do_green {
        background-color: RGB(125,191,9);        
    }

    .req_do_red {
        background-color: red;
    }

    .req_do_blue {
        background-color: RGB(40,91,168);
    }

    a.req_do_block {
        color: white;
        margin-left: 5px;
        padding: 0.2em;
        margin: 0.5em;
    }
    </style>

</head>
<h1>Sales Funnel Requirement - Submitted Customer Submission</h1>

<!-- <div style="right: 20px;position: absolute;">
    <a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('salesFunnel/createQuestionAnswersForm');?>" title="New question and Answers"><div class="icon" style="background-position:-16px 0"></div>New question and Answers</a>  
</div> -->
</br></br></br>

<?php $this->widget('zii.widgets.grid.CGridView', [
	'id'=>'submission-grid-list1',
	'cssFile' => false,
	'dataProvider'=>$submissionModel->search(true, 30, false, false),
	'filter'=>$submissionModel,
	'columns'=>[
		'id',
        'company_name',
        'first_name',
        'last_name',
        'email',
        'contact_number',
        // 'status',
        ['name'=>'status', 'value'=>'$data->showStatusType()', 'filter'=>CHtml::dropDownList('SalesfunnelRequirementsSubmission[status]', $submissionModel->status, SalesfunnelRequirementsSubmission::$statusType )],
        ["name"=>'customer_type',"type"=>"raw","value"=>'CHtml::dropDownList("customer_type{$data->id}", $data->customer_type,SalesfunnelRequirementsSubmission::$customerType, array("prompt"=>"SELECT","onchange"=>"return quickCustomerTypeChange($data->id);"))', 'filter'=>CHtml::dropDownList('SalesfunnelRequirementsSubmission[customer_type]', $submissionModel->customer_type, SalesfunnelRequirementsSubmission::$customerType,['prompt'=>'All'])],
        ['header'=>'Process Status','type'=>'raw','value'=>'$data->getProcessStatus()'],//'filter'=>CHtml::dropDownList('Consol[sea_process_status]', $consol->sea_process_status, $this->t(ConsolProcess::$seaStates), ['prompt'=>$this->t('All')]),],

        ['name'=>'forwarder_shippingagent', 'value'=>'$data->showForwarderAgent()', 'filter'=>CHtml::dropDownList('SalesfunnelRequirementsSubmission[forwarder_shippingagent]', $submissionModel->forwarder_shippingagent, SalesfunnelRequirementsSubmission::$forwarderAgent,['prompt'=>'All'])],
        ['name'=>'category', 'type'=>'raw', 'value'=>'$data->showServiceType()'],
        [
        'class'=>'oButtonColumn',
        'template'=>'{operate}',
        'buttons'=>[
            
            'operate' => [
                'url'=>'Yii::app()->createURL("salesFunnel/showIndividualRequirement",array("id"=>$data->id))',
                'imageUrl'=>false,
                'visible'=>'true',
                'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->email.$data->id', 'data-win-class' => 'L'],
                 ],
            ],
        ],
        [
        'class'=>'oButtonColumn',
        'template'=>'{Create Email}{Related Emails}',
        'buttons'=>[            
            'Create Email' => [
                'url'=>'Yii::app()->createURL("salesFunnel/createEmail",array("id"=>$data->id))',
                'imageUrl'=>false,
                'visible'=>'true',
                'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->email.$data->id', 'data-win-class' => 'L'],
                 ],
            'Related Emails' => [
                'url'=>'Yii::app()->createURL("salesFunnel/getRelatedEmails",array("id"=>$data->id))',
                'imageUrl'=>false,
                'visible'=>'true',
                'options' => ['class' => 'jqm_link grid_view_btn', 'label'=>$this->t('Update'), 'title' => '$data->email.$data->id', 'data-win-class' => 'L'],
                 ],
            ],
        ]
		
	   ]
	]);
?>


<script type="text/javascript">
    var tab222222 = $('#<?=$_GET["tabid"];?>');
    tab222222.unbind('reload_submission_grid').bind('reload_submission_grid', function(){
            $('#submission-grid-list1', tab222222.data('panel')).yiiGridView('update');
            return false;
        });
    function quickCustomerTypeChange(submissionId)
    {
            if(confirm("Are you sure to change customer Type ?"))
            {        
               var customerType= $("#customer_type"+submissionId).children('option:selected').val();
               // alert(customerType+" "+submissionId);                         
               $.ajax({
                       url: '<?=$this->createUrl("salesFunnel/changeCustType")?>',
                       type: "post",
                       data: {"customer_type":customerType,"id":submissionId},
                        processData: true,
                        contentType: 'application/x-www-form-urlencoded',
                       success: function(r) {
                           if(r=='done')
                            {
                               //alert(nextQuestion+"   "+id);
                               myApp.notice('Done', 5000);
                            }else
                            {
                               myApp.alert(r, false);   
                            }
                            tab222222.trigger('reload_submission_grid');
                        },
                       error: function(e) {
                           console.log(e);
                       }
                   });     
            }
    }

</script>