<h3>财务的票件-<?=Yii::app()->user->name?></h3>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
                'id'=>$_GET["tabid"].'_crm-grid',
                'cssFile' => false,
                'dataProvider'=> $model->search(),
                'filter' => $model,
                'columns'=>array(
                     array('name'=>'no','type'=>'raw','value'=>'"<a href=\"".Yii::app()->createUrl("exCrm/update",array("id"=>$data->id))."\"  class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"'),
                     array('name'=>'the_main_ticket','header'=>'Main Ticket','value'=>'@$data->main_ticket->no'),
                     array('name'=>'comp_port','value'=>'$data->getCompPort()','filter'=>CHtml::dropDownList('ExCrm[comp_port]',$model->comp_port, ExChannel::getPocs(true), array('prompt'=>$this->t('All')))),
                     array('header'=>'Port Issue Process','value'=>'$data->getPortIssueProcess()'),
                    array('name' => 'shipnos','type'=>'raw' ,'value' => '$data->getShipNos(2)'),
                     array('header'=>'Bank Name','value'=>'@$data->crm_comp->bank_name'),
                     array('header'=>'BSB','value'=>'@$data->crm_comp->bsb'),
                     array('header'=>'Account User Name','value'=>'@$data->crm_comp->account_name'),
                     array('header'=>'Account Number','value'=>'@$data->crm_comp->account_number'),
                     array('header'=>'Approved Value','value'=>'@$data->crm_comp->approve_ammount.(array(1=>"AUD",3=>"RMB",0=>"")[@intval($data->crm_comp->mdata[approve_currency])])'),
                     array('name' => 'agent', 'value'=>'$data->getAgentName()'),
                     array('name'=>'type','value'=>'$data->getTypecn()','filter'=>CHtml::dropDownList('ExCrm[type]', $model->type, array( 30=>'理赔', 35=>'口岸索赔'), array('prompt'=>$this->t('All')))),
                     array('name'=>'status','type' => 'raw','value'=>'$data->getStatus()','filter'=>CHtml::dropDownList('ExCrm[status]', $model->status, array( 50 => '财务处理中',90 => '关掉',100=>'取消') , array('prompt'=>$this->t('All')))),
                     array('name' =>'last_update_time','header' => 'Last Update Time'),
                     array(
                        'class'=>'oButtonColumn',
                        'template'=>'{view}{finish}',
                        'buttons'=>array(
                               'view' => array(
                                       'url'=>'Yii::app()->createURL("ExCrm/update",array("id"=>$data->id))',
                                       'imageUrl'=>false,
                                       'options' => array('class' => 'tab_link grid_view_btn','title'=>'$data->no'),
                                        ),
                                 'finish' => array(
                                         'imageUrl'=>false,
                                         'options' => array('class' => 'finish_ticket grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'),
                                         'visible' => 'true',
                                          'url' => 'Yii::app()->createUrl("crm/closeCrm", ["id" => $data->id])',
                                          'label' => 'Finish'
                             ),
                                ),
                        ),
                )
        ));
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	tab.bind('onOpen', function(){
		$('#<?=$_GET["tabid"]?>_crm-grid', panel).yiiGridView('update');
	});
        
       $('body').on('click','.finish_ticket',function(event){
           console.log(1);
            event.preventDefault();
            if(confirm("Are you Confirm to Close The Ticket?")){
                $.get($(this).attr('href'),function(e){
                });
           }
        })
	
	$('form#shipment-crmnotes-form', panel).on({'success': function(e,r){
			$('#<?=$_GET["tabid"]?>_crm-grid', panel).yiiGridView('update');
		},
		'reset': true
	}
	);
});
</script>