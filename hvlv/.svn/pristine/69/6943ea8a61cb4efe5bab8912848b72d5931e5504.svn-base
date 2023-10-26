<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
          'Org List'=>array('accounts/orgList'),
          'chargeCode',
	),
));
?>
<div style="right: 20px;position: absolute;">
<a class="jqm_link" href="<?=$this->createUrl('accounts/createchargecode');?>" title="New Charge Code"><div class="icon" style="background-position:-16px 0"></div> New Charge Code</a> &nbsp; </div>
<h1><?=$this->t('Import Charge Code');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'im-charge-code-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'chargecode',
        array('name' => 'owner_name', 'value' => 'empty($data->owner)? "" : $data->owner->name'),
        'note',
		'created',
        array('name' => 'status', 'value' => '$data->getStatus()',
            'filter'=>CHtml::dropDownList('ImportChargeCode[status]', $model->status, $this->t(ImportChargeCode::$states), array('prompt'=>$this->t('All')))),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>array
			(
				'update' => array(
					'imageUrl'=>false,
                                        'url'=>'Yii::app()->createUrl("accounts",array("updateChargecode"=>"$data->id"))',
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->chargecode'),
				),
			),
		),
	),
)); ?>

