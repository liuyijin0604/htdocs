<div style="right: 20px;position: absolute;">
<a href="#" data-dropdown="#wms-chargecode-dropdown"><div style="background-position:-48px -688px" class="icon"></div> Manage Charge Code</a>
<div id="wms-chargecode-dropdown" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createUrl('wmsOrg/wmsChargecodeMng');?>" class="tab_link" title="WMS Weight Charge Code">Weight Charge Code</a></li>
		<li><a href="<?=$this->createUrl('wmsOrg/localChargecodeMng');?>" class="tab_link" title="WMS Local Charge Code">Local Charge Code</a></li>
		<li><a href="<?=$this->createUrl('wmsOrg/intlChargecodeMng');?>" class="tab_link" title="WMS Intl Charge Code">Int'l Charge Code</a></li>
	</ul>
</div>
</div>
<h1><?=$this->t('Manage Wms Organisation');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'org-wms-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true,30,$criteria),
	'filter'=>$model,
	'columns'=>array(
		'id',
		array(
            'name'=>'type',
            'value'=>'$data->getType()',
			'filter'=>CHtml::dropDownList('Org[type]', $model->type, $this->t(Org::$types), array('prompt'=>$this->t('All'))),
        ),
		array(
			'name' => 'code',
			'type' => 'raw',
			'value' => '"<a class=\"tab_link\" href=\"org/update/".$data->id."\" title=\"Org-".$data->code."\">".$data->code."</a>"',
		),
		'name',
		'phone',
		array(
            'name'=>'status',
            'value'=>'$data->getStatus()',
			'filter'=>CHtml::dropDownList('Org[status]', $model->status, $this->t(Org::$states), array('prompt'=>$this->t('All'))),
        ),
	     array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>array
			(
				'update' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '"Org-".$data->code'),
				),
			),
		),
	),
)); ?>
