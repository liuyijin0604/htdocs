<div style="right: 20px;position: absolute;">
<a class="jqm_link" href="<?=$this->createUrl('import/createSystemFuel');?>" title="New System Fuel"><div class="icon" style="background-position:-16px 0"></div> New System Fuel</a> &nbsp; </div>
<h1><?=$this->t('New System Fuel');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'im-system-fuel-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true,500),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'type','value'=>'$data->getType()'),
		'from_day',
		'to_day',
        'rate',
        array('name' => 'createdUserName','value'=>'empty($data->user_id)?"System":$data->createUser->fname." ".$data->createUser->lname'),
        'created'
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	tab.unbind('reload_system_fuel_grid').bind('reload_system_fuel_grid', function(){
        $('#im-system-fuel-grid', tab.data('panel')).yiiGridView('update');
        return false;
    });
	tab.bind('onOpen', function(){
		$('#im-charge-code-grid', panel).yiiGridView('update');
	});

});
</script>
