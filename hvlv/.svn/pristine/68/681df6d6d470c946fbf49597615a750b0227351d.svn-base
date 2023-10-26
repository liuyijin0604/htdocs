
<h1><?=$this->t('Aupost ELMS Consols');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'elms-consol-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        array('name' => 'no','type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("elmsConsol/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"'),
        array('name'=>'owner_name', 'type' => 'raw','value'=>'empty($data->owner_id)?"":$data->owner->name'),
        array('name'=>'awb', 'header' => 'MS#', 'value' => '$data->awb'),
        array('name' => 'status', 'value' => '$data->getStatus()',
            'filter'=>CHtml::dropDownList('ElmsConsol[status]', $model->status, $this->t(ElmsConsol::$states), array('prompt'=>$this->t('All'))),),
        array('name' => 'pol', 'filter'=>CHtml::dropDownList('ElmsConsol[pol]', $model->pol, $this->t(AppHelper::setting2List('pods')), array('prompt'=>$this->t('All'))),),
        array('name' => 'pod', 'filter'=>CHtml::dropDownList('ElmsConsol[pod]', $model->pod, $this->t(AppHelper::setting2List('pols')), array('prompt'=>$this->t('All'))),),
        'eta',
        array(
            'class'=>'oButtonColumn',
            'template'=>'{update}',
            'buttons'=>array(
                'update' => array(
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->no'),
                ),
            ),
        ),
    ),
)); ?>
<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        $('.search-button', panel).click(function(){
            $('.search-form', panel).toggle();
            return false;
        });
        $('.search-form form', panel).submit(function(){
            $.fn.yiiGridView.update('elms-consol-grid', {
                data: $(this).serialize()
            });
            return false;
        });
        tab.bind('onOpen', function(){
            $('#elms-consol-grid', panel).yiiGridView('update');
        });
    });
</script>
