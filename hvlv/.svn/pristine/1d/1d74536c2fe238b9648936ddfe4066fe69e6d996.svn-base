<h3>New GatePass List</h3>
<div style="right: 200px;position: absolute;">
    <a  href="<?=$this->createUrl('gatepass/exportCurrentSearch');?>" title="Export Current Search" target="_blank" class="export_search" ><div class="icon" style="background-position:-128px -32px"></div>Export Current Search</a>
    <a  href="<?=$this->createUrl('gapsig/site/index');?>" title="Filter Management" target="_blank"><div class="icon" style="background-position:-128px -32px"></div>GatePass App</a>
</div>
<br/>
<?php $this->widget('zii.widgets.grid.CGridView',array(
    'id'=>'gatepass_shipment_signature',
    'cssFile' => false,
    'filter'=>$model,
    'dataProvider'=>$model->search(),
    'columns'=>array(
        'no',
        array('name'=>'courier_id','value'=>'$data->getCourierName()',
            'filter'=>CHtml::dropDownList("GatepassCourier[courier_id]",empty($model->mdata['ocourier_id'])?$model->courier_id:$model->mdata['ocourier_id'], GatepassCourier::$gatepass_courier,array('prompt'=>'All','class'=>'form-control'))
            ),
        'driver_name',
        'driver_rego',
        'ref',
        'create_time',
        array('header'=>'Total Packs','value'=>'$data->getTotalPacks()'),
        array('header' => 'Depot', 'value' => '$data->getDptName()'), 
            // 'filter'=>CHtml::dropDownList('warehouse', @$model->mdata['warehouse'], Org::dptList(), array('prompt'=>$this->t('All'))),),
        array('header'=>'User','value'=>'$data->getUserName()'),
        array(
            'class'=>'oButtonColumn',
            'template'=>'{print}',
            'buttons'=>array(
                'print' => array(
	                  'visible'=>'true',
		           'icon' => 'grid_print_btn',
                           'url' => 'Yii::app()->createUrl("imParcel/genGatepassDoc",array("id"=>$data->id))',
		           'options' => array('label'=>$this->t('print'),'class'=>'grid_print_btn','target'=>'_blank', 'title' => 'print'),
		            ),
            ),
        )
    ),
));

?> 

<script type="text/javascript">
$(function(){
    var tab = $('#<?=$_GET["tabid"];?>');
    var panel = tab.data('panel');

    $('a.export_search', panel).on('mousedown', function(){
        var q = $('.filters input, .filters select', panel).serialize();
        $(this).attr('href', $(this).attr('href') + '?' + q);
    });


});
</script>