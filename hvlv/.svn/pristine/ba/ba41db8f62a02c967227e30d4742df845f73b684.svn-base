<div style="right: 20px;position: absolute;">


    <div class="icon" style="background-position:-16px 0"></div>
    <a class="tab_link" data-win-class="XL" href="<?=$this->createUrl('edi/createAWB');?>" title="New EDI AWB">New AWB</a> &nbsp;
    <a class="jqm_link" href="<?=$this->createUrl('edi/export');?>" title="Export"><div class="icon" style="background-position:-16px 0"></div> Export Current Search</a>


</div>

<h1><?=$this->t('EDI AWB');?></h1>

<p>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'edi-awb-list-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("edi/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"',),
        'awb',
        array('name' => 'owner', 'value' => 'empty($data->owner)? "" : $data->owner->name',),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('EdiAwbConsol[status]', $model->status, $this->t($model::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'dpt_id', 'value' => '$data->depot->name', 
			'filter'=>CHtml::dropDownList('EdiAwbConsol[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), array('prompt'=>$this->t('All'))),),
        'created',
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}{delete}',//{view}
			'buttons'=>array
			(
				'view' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->no'),
				),
                'delete' => array(
                    'imageUrl'=>false,
                    'options' => array('class' => 'grid_delete_btn', 'label'=>$this->t('Remove EDI AWB')),
                    'url' => 'Yii::app()->createURL("edi/removeAwb", array("id" => $data->id))',
                ),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	tab.bind('onOpen', function(){
		$('#edi-awb-list-grid', panel).yiiGridView('update');
	});
});
</script>
