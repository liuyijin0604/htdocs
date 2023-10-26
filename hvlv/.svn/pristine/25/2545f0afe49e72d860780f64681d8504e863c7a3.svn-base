<h1><?=$this->t('Warehouse Group Management');?></h1>


<h1><?=$this->t('Rule');?></h1>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'warehouse-group-rule-grid',
	'cssFile' => false,
	'dataProvider'=>$rule->search(),
	'filter'=>$rule,
	'columns'=>array(
		['name' => 'dpt_id','value'=>'$data->branch->name'],
		['header' => 'Time','value'=>'$data->from."-".$data->to'],
		['header' => 'Agents','type'=>'raw','value'=>'$data->getAgentNames(true)'],
		['header' => 'With Other Agent','value'=>'(!empty($data->other_agent)?"YES":"")'],
		['name' => 'group'],
		['name' => 'maximum'],
		['name' => 'status','value'=>'(!empty($data->status)?"INACTIVE":"ACTIVE")'],
		array(
			'class'=>'CButtonColumn',
			'template'=>'{update}',
			'buttons'=>array(
				'update' => array(
					'url'=>'Yii::app()->createURL("warehouseGroup/updateRule")."?id=".$data->id',
					'imageUrl'=>false,
					'options' => array('class' => 'jqm_link grid_edit_btn', 'title'=>$this->t('Update Rule')),
				),
			),
		)
	),
)); ?>




<h1><?=$this->t('Warehouse User Group List');?></h1>
<p>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'warehouse-group-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		['header' => 'depot','value'=>'$data->user->branch->name'],
		['name' => 'username','value'=>'$data->user->name'],
		['name' => 'group'],
		['name' => 'leader','value'=>'!empty($data->leader)?"YES":""'],
		array(
			'class'=>'CButtonColumn',
			'template'=>'{update}',
			'buttons'=>array(
				'update' => array(
					'url'=>'Yii::app()->createURL("user/update")."?id=".$data->user_id',
					'imageUrl'=>false,
					'visible'=>'$data->user_id > 1',
					'options' => array('class' => 'tab_link grid_edit_btn', 'title'=>$this->t('Update User')),
				),
			),
		)
	),
)); ?>


<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>')
	var panel = tab.data('panel');
	$(function() {
		var tab = $('#<?= $_GET["tabid"]; ?>');

		tab.unbind('reload_group_rule_grid').bind('reload_group_rule_grid', function() {
			$('#warehouse-group-rule-grid', tab.data('panel')).yiiGridView('update');
			return false;
		});

	})
});
</script>
