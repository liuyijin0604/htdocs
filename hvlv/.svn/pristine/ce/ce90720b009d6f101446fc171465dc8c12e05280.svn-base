<style type="text/css">
.thisBreak
{
width:500px;
overflow: hidden;
}
</style>
<?php 
            $this->widget('zii.widgets.grid.CGridView',array(
                        'filter'=>$model,
                        'id'=>$_GET["tabid"].'_zw_storage_records',
                        'dataProvider'=>$model->search(true),
                        // 'afterAjaxUpdate'=>'function(){
                        //     $("#zw_storage_records").remove();

                        // }',
                        'template' => "{summary}\n{items}\n{pager}",
                        'columns'=>[        
                            array('name' => 'tlano'),
                            array('name' => 'putcode'),
                            ['name' => 'status', 'value' => 'ImportZwStorage::$states[$data->status]',
                            'filter'=>CHtml::dropDownList('ImportZwStorage[status]', $model->status, $this->t(ImportZwStorage::$states), ['prompt'=>$this->t('All')]),],
                            array('name' => 'org.name','filter'=>CHtml::textField('ImportZwStorage[org_name]', $model->org_name,["class"=>"form-control"])),
                            array('name' => 'store_code'),
                            array('name'=>'channel_code'),
                            array('name' => 'volume'),
                            array('name' => 'weight'),
                            array('name' => 'goods_count'),
                            array('name' => 'remark'),
                            array('header' => 'Ship Date','value'=>'@$data->mdata["shipdate"]'),
                            array('header' => 'consol','type'=>'raw','value'=>'@$data->getConsol(true)','filter'=>CHtml::textField('ImportZwStorage[consols]', $model->consols,["class"=>"form-control"])),
                             array('name' => 'created'),
                            ['class'=>'oButtonColumn',
                                'template'=>'{Parcel Labels}&nbsp;&nbsp{edit}&nbsp;&nbsp{log}',
                                'buttons'=>[
                                    'Parcel Labels' => [
                                        'url'=> 'Yii::app()->createURL("/ims/tools/downloadZWLabels", ["id" => $data->id])',
                                        'imageUrl'=>false,
                                        'visible' => 'empty($data->putcode)?false:true;',
                                        'options' => [

                                            'class' => 'grid_print_btn',  
                                        'label'=>$this->t('Parcel Labels'),
                                        'target' => '_blank'],
                                    ],
                                     'edit' => [
                                        'imageUrl'=>false,
                                        'options' => ['class' => 'grid_edit_btn jqm_link', 'label' => 'Done', 'data-win-class' => 'XL','title'=>'$data->id','value'=>'1'],
                                        'visible' => 'true',
                                        'url' => 'Yii::app()->createUrl("zwStorage/updateZWStorage", ["id" => $data->id])',
                                        'label' => 'Edit'
                                    ],
                                    'log' => [
                                        'imageUrl'=>false,
                                        'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
                                        'visible' => 'true',
                                        'url' => 'Yii::app()->createUrl("zwStorage/log", ["id" => $data->id])',
                                        'label' => 'Log'
                                    ],
                                    // 'cancel' => [
                                    //     'imageUrl'=>false,
                                    //     'options' => ['class' => 'grid_edit_btn prepare_cancel', 'label' => 'Done', 'data-win-class' => 'L','title'=>'$data->id','value'=>'1'],
                                    //     'visible' => 'empty($data->mdata["push"])?true:false',
                                    //     'url' => 'Yii::app()->createUrl("zwStorage/cancelZWStorage", ["id" => $data->id])',
                                    //     'label' => 'Cancel'
                                    // ]
                                ],
                            ],
                             array('header' => 'hbns','type'=>'raw','value'=>'"<p class=\"thisBreak\">".@$data->mdata["hbns"]."</p>"','filter'=>CHtml::textField('ImportZwStorage[hbns]', $model->hbns,["class"=>"form-control"]))
                        ],
                    ));

?> 