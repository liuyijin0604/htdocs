<?php
$this->widget('zii.widgets.grid.CGridView', array(
                'id'=>$_GET["tabid"].'_crm-grid',
                'cssFile' => false,
                'dataProvider'=> $model->search(),
                'filter' => $model,
                'columns'=>array(
                     array('name'=>'no','type'=>'raw','value'=>'"<a href=\"".Yii::app()->createUrl("exCrm/update",array("id"=>$data->id))."\"  class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"'),
                     array('name' => 'shipnos','type'=>'raw' ,'value' => '$data->getShipNos(2)'),
                     array('name' => 'agent', 'value'=>'$data->getAgentName()'),
                     array('name' => 'consol_no','type'=>'raw', 'value'=>'$data->getConsolNos(3)'),
                     array('name' => 'customer_name','value'=>'$data->getCustomerName()'),
                    array('name' => 'sender_name','value'=>'$data->getShipperName()'),
                     array('name'=>'opener','value'=>'$data->getOpenUser()'),
                     array('name'=>'currenter','header'=>'Current Operator','value'=>'$data->getUser()'),
                     array('name'=>'assigner','value'=>'$data->getAssignUser()'),
                     array('name'=>'type','value'=>'$data->getTypecn()','filter'=>CHtml::dropDownList('ExCrm[type]', $model->type, ExCrm::$types_cn, array('prompt'=>$this->t('All')))),
                     array('name'=>'source', 'value'=>'$data->getSource()', 'filter'=>CHtml::dropDownList('ExCrm[source]', $model->source, ExCrm::$sources , array('prompt'=>$this->t('All')))),
                     'email',
                     'telephone',
                     array('header' => 'Description', 'type' => 'raw',   'value'=>function($data){return CHtml::tag('div', array('title'=>@$data->mdata['desc'],),substr(@$data->mdata['desc'],0,18));},),
                     array('header' => 'Last Note', 'type' => 'raw',   'value'=>function($data){return CHtml::tag('div', array('title'=>$data->getLastNote(),),substr($data->getLastNote(),0,18));},),
                     array('name'=>'status','type' => 'raw','value'=>'$data->getStatus()','filter'=>CHtml::dropDownList('ExCrm[status]', $model->status, ExCrm::$states , array('prompt'=>$this->t('All')))),
                     array('header'=>'extra Info','type'=>'raw','value'=>'$data->getExtraInfo()','filter'=>CHtml::dropDownList('ExCrm[flag]',$model->flag,$this->t(ExCrm::$flags),array('prompt'=>'All'))),
                     array('name'=>'create_time','header' => 'Create Time'),
                     array('name' =>'last_update_time','header' => 'Last Update Time'),
                     array(
                        'class'=>'oButtonColumn',
                        'template'=>'{update}{log}',
                        'buttons'=>array(
                                'update' => array(
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

