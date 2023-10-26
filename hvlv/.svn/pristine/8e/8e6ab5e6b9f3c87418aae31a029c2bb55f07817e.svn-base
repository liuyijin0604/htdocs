<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
          'Org List',
	),
));
?>
<?php if(Yii::app()->user->grp<=60):?>
<div style="right: 200px;position: absolute;">
<?php if(Yii::app()->user->org==1427||Yii::app()->user->grp<=0):?>
<a class="btn btn-default" href="<?=$this->createUrl('accounts/chargecodeList');?>" title="New ChargeCode."><div class="icon" style="background-position:-16px 0"></div>ChargeCodes</a>
<?php endif;?>
<a class="btn btn-default" href="<?=$this->createUrl('accounts/create');?>" title="New Org."><div class="icon" style="background-position:-16px 0"></div> New Org.</a>
</div>
<?php endif;?>
<h1><?=$this->t('Manage Organisation');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?>
</p>
<?php $this->widget('application.extensions.booster.TbExtendedGridView', array(
	'id'=>'org-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		array(
            'name'=>'type',
            'value'=>'$data->getType()',
			'filter'=>CHtml::dropDownList('Org[type]', $model->type, array(30=>'Client'), array('prompt'=>$this->t('All'),'class'=>'form-control')),
        ),
		array(
			'name' => 'code',
			'type' => 'raw',
			'value' => '"<a class=\"tab_link ajax-link\" href=\"update/id/".$data->id."\" title=\"Org-".$data->code."\">".$data->code."</a>"',
		),
		'name',
		'phone',
		array(
            'name'=>'status',
            'value'=>'$data->getStatus()',
			'filter'=>CHtml::dropDownList('Org[status]', $model->status, $this->t(Org::$states), array('prompt'=>$this->t('All'),'class'=>'form-control')),
        ),
		
		array(
			'class'=>'application.extensions.booster.TbButtonColumn',
			'template'=>'{update}',
			'buttons'=>array
			(
				'update' => array(
					'imageUrl'=>false,
                                        'icon' => 'glyphicon glyphicon-edit',
				),
			),
		),
	),
)); ?>
<?php ob_start(); ?>
<script type="text/javascript">
    $(function(){
        
    })
</script>
<?php $this->registerJS(ob_get_clean(),8); ?>
