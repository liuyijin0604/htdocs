<h3>分配给我的票-<?=Yii::app()->user->name?></h3>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
                'id'=>$_GET["tabid"].'_crm-grid',
                'cssFile' => false,
                'dataProvider'=> $model->search(),
                'filter' => $model,
                'columns'=>array(
                     array('name'=>'no','type'=>'raw','value'=>'"<a href=\"".Yii::app()->createUrl("exCrm/update",array("id"=>$data->id))."\"  class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"'),
                     array('name' => 'shipnos','type'=>'raw' ,'value' => '$data->getShipNos(2)'),
                     array('name' => 'consol_no','type'=>'raw', 'value'=>'$data->getConsolNos(3)'),
                     array('name' => 'agent', 'value'=>'$data->getAgentName()'),
                     array('name' => 'customer_name','value'=>'$data->getCustomerName()'),
                     array('name' => 'sender_name','value'=>'$data->getShipperName()'),
                     array('name'=>'type','value'=>'$data->getTypecn()','filter'=>CHtml::dropDownList('ExCrm[type]', $model->type, ExCrm::$types_cn, array('prompt'=>$this->t('All')))),
                     array('name'=>'source', 'value'=>'$data->getSource()', 'filter'=>CHtml::dropDownList('ExCrm[source]', $model->source, ExCrm::$sources , array('prompt'=>$this->t('All')))),
                     'email',
                     'telephone',
                     array('header'=>'Description','value'=>'@$data->mdata["desc"]'),
                     array('header' => 'Last Note','value' => '$data->getLastNote()'),
                     array('name'=>'status','type' => 'raw','value'=>'$data->getStatus()','filter'=>CHtml::dropDownList('ExCrm[status]', $model->status, ExCrm::$states , array('prompt'=>$this->t('All')))),       
                     array('name'=>'create_time','header' => 'Create Time'),
                     array(
                        'class'=>'oButtonColumn',
                        'template'=>'{view}{log}',
                        'buttons'=>array(
                               'view' => array(
                                       'url'=>'Yii::app()->createURL("ExCrm/update",array("id"=>$data->id))',
                                       'imageUrl'=>false,
                                       'options' => array('class' => 'tab_link grid_view_btn','title'=>'$data->no'),
                                        ),
                                 'log' => array(
                                         'imageUrl'=>false,
                                         'options' => array('class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'),
                                         'visible' => 'true',
                                          'url' => 'Yii::app()->createUrl("exCrm/log", ["id" => $data->id])',
                                          'label' => 'Log'
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
	
	$('form#shipment-crmnotes-form', panel).on({'success': function(e,r){
			$('#<?=$_GET["tabid"]?>_crm-grid', panel).yiiGridView('update');
		},
		'reset': true
	}
	);
});
</script>