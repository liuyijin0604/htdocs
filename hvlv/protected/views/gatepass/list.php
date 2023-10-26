<div style="position: absolute; left: 120px;">
<!--     <a href="<?=$this->createUrl('gatepass/create');?>" class="tab_link" title="New GP"><div style="background-position:-16px 0" class="icon"></div> New Gate Pass</a> -->
    <a href="<?=$this->createUrl('gatepass/prepare');?>" class="tab_link" title="Prepare GP"><div style="background-position:-16px 0" class="icon"></div> Prepare Gate Pass</a>
    <a href="<?=$this->createUrl('gatepass/check');?>" class="tab_link" title="Check GP"><div style="background-position:-16px 0" class="icon"></div> Check Gate Pass</a>
    <a href="<?=$this->createUrl('gatepass/details');?>" class="tab_link" title="GatePass Details"><div style="background-position:-16px 0" class="icon"></div> Show More By HBN</a>
    <!-- <a href="<?=$this->createUrl('gatepass/importManifest');?>" class="jqm_link" title="Import Gatepass For Manifest"><div style="background-position:-16px 0" class="icon"></div> Import Gatepass For Manifest</a>
    <a href="<?=$this->createUrl('gatepass/importCargoReceiptForGP');?>" class="jqm_link" title="Import Cargo Receipt For GP"><div style="background-position:-16px 0" class="icon"></div> Import Cargo Receipt For GP</a> -->
	<a href="<?=$this->createUrl('gatepass/importFileForGP');?>" class="jqm_link" title="Import File For GP"><div style="background-position:-16px 0" class="icon"></div> Import File For GP</a>
	<a href="<?=$this->createUrl('gatepass/releaseClearWait');?>" class="tab_link" title="Release Clear Wait"><div style="background-position:-16px 0" class="icon"></div> Release Clear Wait</a>
</div>
<h1><?=$this->t('Gate Pass');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'gate-pass-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'dpt_id', 'value' => '$data->getDptName()', 
			'filter'=>CHtml::dropDownList('GatePass[dpt_id]', $model->dpt_id, Org::dptList(), array('prompt'=>$this->t('All'))),),
		array('name' => 'company', 'value' => '$data->company'),
		array('name' => 'id'),
		'ref',
		'created',
        array('name' => 'status', 'value' => '$data->getStatus()',
            'filter'=>CHtml::dropDownList('GatePass[status]', $model->status, $model->statusList(), array('prompt'=>$this->t('All'))),),
		array('name' => 'by_id', 'value' => 'empty($data->creator)? "" : $data->creator->name'),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view}{confirm}{export}',
			'buttons'=>array(
				'view' => array(
					'imageUrl'=>false,
					'url'=>'Yii::app()->createUrl("gatepass/print", ["id" => $data->id])',
					'options' => array('class' => 'grid_view_btn', 'target' => '_blank'),
				),
                'confirm' => array(
                    'imageUrl'=>false,
                    'visible'=> '$data->status == 60',
                    'url'=>'Yii::app()->createUrl("gatepass/confirm", ["id" => $data->id])',
                    'options' => array('class' => 'jqm_link grid_view_btn'),
                ),
                'export' => [
						'imageUrl'=>false,
						'options' => ['class' => 'grid_print_btn',"target"=>"_blank"],
						'url' => 'Yii::app()->createUrl("gatepass/export", ["id" => $data->id])',
						'label' => 'export',
				],
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	tab.bind('onOpen', function(){
		$('#gate-pass-grid', panel).yiiGridView('update');
	});
});
</script>
