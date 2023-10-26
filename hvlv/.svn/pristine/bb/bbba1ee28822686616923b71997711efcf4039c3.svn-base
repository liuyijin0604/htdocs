<style type="text/css">
.thisBreak
{
width:160px;
overflow: hidden;
}
</style>
<?php 
            $this->widget('application.extensions.booster.TbExtendedGridView',array(
                        'fixedHeader'=>true,
                        'id'=>'zw_storage_records',
                        'type'=>'striped bordered',
                        'headerOffset'=>40,
                        'responsiveTable'=>true,
                        'dataProvider'=>$model->search(true),
                        'template' => "{summary}\n{items}\n{pager}",
                        'columns'=>[        
                            array('name' => 'tlano'),
                            array('name' => 'putcode'),
                            array('name' => 'store_code'),
                            array('name' => 'volume'),
                            array('name' => 'weight'),
                            array('name' => 'goods_count'),
                            array('name' => 'remark'),
                            array('header' => 'consol','type'=>'raw','value'=>'@$data->getConsol(true,true)'),
                            ['class'=>'oButtonColumn',
                                'template'=>'{Parcel Labels}&nbsp;&nbsp;{edit}&nbsp;&nbsp;{cancel}',
                                'buttons'=>[
                                    'Parcel Labels' => [
                    'url'=> 'Yii::app()->createURL("/ims/tools/downloadZWLabels", ["id" => $data->id])',
                    'imageUrl'=>false,
                    'visible' => 'empty($data->putcode)?false:true;',
                    'options' => ['class' => 'tab_link grid_print_btn',  'label'=>$this->t('Parcel Labels'),'target' => '_blank'],
                ],
                                     'edit' => [
                                        'imageUrl'=>false,
                                        'options' => ['class' => 'grid_edit_btn prepare_done', 'label' => 'Done', 'data-win-class' => 'L','title'=>'$data->id','value'=>'1'],
                                        'visible' => 'true',
                                        'url' => 'empty($data->mdata["push"])?Yii::app()->createUrl("/ims/tools/updateZWStorage", ["id" => $data->id]):Yii::app()->createUrl("/ims/tools/updateZWStorageHbns", ["id" => $data->id])',
                                        'label' => 'Edit'
                                    ],
                                    'cancel' => [
                                        'imageUrl'=>false,
                                        'options' => ['class' => 'grid_edit_btn prepare_cancel', 'label' => 'Done', 'data-win-class' => 'L','title'=>'$data->id','value'=>'1'],
                                        'visible' => 'empty($data->mdata["push"])?true:false',
                                        'url' => 'Yii::app()->createUrl("/ims/tools/cancelZWStorage", ["id" => $data->id])',
                                        'label' => 'Cancel'
                                    ]
                                ],
                            ],
                              array('header' => 'hbns','type'=>'raw','value'=>'"<p class=\"thisBreak\">".@$data->mdata["hbns"]."</p>"','filter'=>CHtml::textField('ImportZwStorage[hbns]', $model->hbns,["class"=>"form-control"]))
                        ],
                    ));

?> 