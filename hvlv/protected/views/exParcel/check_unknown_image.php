<div class="pane">
    <div class="form">
        <div class="row buttons" >
            <?php echo CHtml::submitButton('Update status', array('class' => 'update_status')); ?>
        </div>
        <?php
        $this->widget('zii.widgets.grid.CGridView', array(
            'id' => "ex-image-grid_" . $_GET['tabid'],
            'dataProvider' => $model->search(),
            'filter' => $model,
            'columns' => array(
                'hbn',
                array('name'=>'agent_id','htmlOptions'=>array('width'=>'80px'),'filterHtmlOptions'=>array('style'=>'width:80px')),
                'date',
                'pdf_number',
                array('name' => 'islinked', 'value' => '$data->getLinkedStatus()', 'header' => 'Status', 'filter' => CHtml::dropDownList('ExImage[islinked]', $model->islinked, Array(0 => 'Unidentified', 1 => 'Linked', 2 => 'Shipment UnCreated'), array('prompt' => 'All'))),
                array('name'=>'id','headerHtmlOptions' => array('style' => 'display:none'),'filterHtmlOptions' => array('style' => 'display:none'),
                      'htmlOptions' => array('style' => 'display:none')),
                array(
                    'header' => 'Image',
                    'type' => 'raw',
                    'value' => 'CHtml::image($data->get_url_id($data->id),"", array("width"=>50, "height"=>30 ,"class"=>"the_image" ))'),
                array(
                    'class' => 'oButtonColumn',
                    'template' => '{bind}',
                    'buttons' => array(
                        'bind' => array(
                            'url' => 'Yii::app()->createUrl("exParcel/ecreate",array("id"=>$data->id))',
                            'options' => array('class' => 'jqm_link grid_edit_btn','style'=>'display:none;'),
                          ),

                       ),
                     'filterHtmlOptions' => array('style' => 'display:none'),
                        'headerHtmlOptions' => array('style' => 'display:none'),
                          'htmlOptions' => array('style' => 'display:none')
                ),
            ),
        ))
        ?>
    </div>
</div>
<script>
    $(function(){
        var tab=$('#<?=$_GET["tabid"]?>');
        var panel=tab.data('panel');
        var panel=tab.data('panel');
        tab.unbind('reload_image_grid').bind('reload_image_grid',function(){
            $('#ex-image-grid_<?=$_GET['tabid']?>',panel).yiiGridView('update');
            return false;
        });
        $('.update_status').on('click',function(){
            $.get('<?=Yii::app()->createURL('exParcel/toCheck')?>',function(r){
                myApp.notice('Update Successfully');
                tab.trigger('reload_image_grid');
                return false;
            });
       });

       $('body').off('click').on('click','img.the_image',function(){
              $(this).parent().next().find('a').trigger('click');
       });
       
    });
</script>