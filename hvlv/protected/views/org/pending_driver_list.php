<div style="right: 20px;position: absolute;">
    <a class="org_export_search" href="#" data-baseurl="<?=$this->createUrl('org/export', ['type' => 'search']);?>" target="_blank"><div class="icon" style="background-position:-16px 0"></div>Export Current Search</a>
</div>

<div style="right: 200px;position: absolute;">
<a class="tab_link" href="<?=$this->createUrl('org/create');?>" title="New Org."><div class="icon" style="background-position:-16px 0"></div> New Org.</a>
</div>

<div style="right: 300px;position: absolute;">
    <a class="tab_link" href="<?=$this->createUrl('org/syncxero');?>" title="New Org."><div class="icon" style="background-position:-16px 0"></div>Sync Xero</a>
</div>

<h1><?=$this->t('Manage Organisation');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?>
</p>

<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'org-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 30, false, true),
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
            'name'=>'cargo_driver_type',
            'value'=>'$data->getCargoDriverType()',
			'filter'=>CHtml::dropDownList('Org[cargo_driver_type]', $model->cargo_driver_type, $this->t(Org::$cdts), array('prompt'=>$this->t('All'))),
        ),
		/*
		array(
			'name'=>'email',
			'type' => 'raw',
			'value' => 'CHtml::link($data->email, "mailto:".$data->email)',
		),
		array(
			'name' => 'by',
			'value' => '$data->getOwner()',
        ),
		'address',
		'suburb',
		'postcode',
		'country',
		'fax',
		'desc',
		'notes',
		'meta',
		'by',
		*/
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view}{update}',
			'buttons'=>array
			(
				'view' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'update' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '"Org-".$data->code'),
				),
				/*'reset' => array(
					'imageUrl' => false,
					'url' => 'Yii::app()->createUrl("org/changePassword", array("id" => $data->id))',
					'label' => 'Reset Password',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Reset Password')),
				)*/
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form').toggle();
		return false;
	});
	$('.search-form form', panel).submit(function(){
		$('#org-grid', panel).yiiGridView('update', {
			data: $(this).serialize()
		});
		return false;
	});

    $('a.org_export_search', panel).on('mousedown', function(){
        var q = $('.filters input, .filters select', panel).serialize();
        $(this).attr('href', $(this).data('baseurl') + '&' + q);
    });

	tab.bind('onOpen', function(){
		$('#org-grid', panel).yiiGridView('update');
	});
});
</script>
