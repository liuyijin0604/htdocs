<div style="right: 20px;position: absolute;">
<a class="tab_link" href="<?=$this->createUrl('user/create');?>" title="New User"><div class="icon" style="background-position:-16px 0"></div> New User</a>
</div>
<h1><?=$this->t('Users');?></h1>
<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?>
</p>
<?php
echo  CHtml::label('start time', 'start time', array('required' => 'required'));
echo CHtml::textField($_GET["tabid"].'start_time',date("Y-m-01"), array('size' => 20,'class'=>"date_input")); 
echo  CHtml::label('end time', 'end time', array('required' => 'required'));
echo CHtml::textField($_GET["tabid"].'end_time',date("Y-m-d"), array('size' => 20,'class'=>"date_input"));
?>

<a class="export_usage_report" target="_blank" href="<?=$this->createUrl('report/exportUserUsageReport');?>"><div style="background-position:-48px -688px" class="icon"></div>Export Usage Report</a></br>

<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'user-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
            'name'=>'type',
            'value'=>'$data->getType()',
			'filter'=>CHtml::dropDownList('User[type]', $model->type, $this->t(User::$types), array('prompt'=>$this->t('All'))),
        ),
		array('name' => 'dpt_id', 'value' => '$data->getBranch()', 'filter' => CHtml::dropDownList('User[dpt_id]', $model->dpt_id, Org::dptList(), ['prompt' => $this->t('All')]),),
		'title',
		'fname',
		'lname',


		//'user',
		array(
			'name' => 'email',
			'type' => 'raw',
			'value' => 'CHtml::link($data->email,"mailto:".$data->email)',
        ),
		'phone',

		array(
            'name'=>'org_search',
            'value'=>'@$data->org->name',
        ),

		array(
            'name'=>'active',
            'value'=>'$data->getActive()',
			'filter'=>CHtml::dropDownList('User[active]', $model->active, $this->t(['1' => 'Yes', 0 => 'No']), array('prompt'=>$this->t('All'))),
        ),

        array(
            'name'=>'abf',
            'value'=>'$data->getAbfStatus()',
			'filter'=>CHtml::dropDownList('User[abf]', $model->abf, $this->t(['Yes' => 'Yes', 'No' => 'No']), array('prompt'=>$this->t('All'))),
        ),

		array(
			'class'=>'CButtonColumn',
			'template'=>'{update}',
			'buttons'=>array(
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'$data->id > 1',
					'options' => array('class' => 'tab_link grid_edit_btn', 'title'=>$this->t('Update User')),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>')
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});
	$('.search-form form', panel).submit(function(){
		$('#user-grid', panel).yiiGridView('update', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#user-grid', panel).yiiGridView('update');
	});

	$('a.export_usage_report', panel).on('mousedown', function(){
		if($("#<?=$_GET["tabid"]?>start_time").val()==""||$("#<?=$_GET["tabid"]?>start_time").val()=="")
		{
			alert("Required Start Time and End Time");
			return false;
		}

		$(this).attr('href', '<?=$this->createUrl('report/exportUserUsageReport');?>?start_time=' + $("#<?=$_GET["tabid"]?>start_time").val()+"&&end_time="+ $("#<?=$_GET["tabid"]?>end_time").val());
	});


});
</script>
