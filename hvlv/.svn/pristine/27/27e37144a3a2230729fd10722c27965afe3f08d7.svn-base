<h1><?=$this->t('Check Courier Labels');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?>
</p>

<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">

</div><!-- search-form -->

<?php

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'org-grid',
	'cssFile' => false,
	'dataProvider'=>$dataprovider,
	'filter'=>$filtersForm,
	'columns'=>array(
		'fid',
		'prefix',
		array(
            'name'=>'name',
            'value'=>'($data->org->name)',
        ),

		array(
            'name'=>'Rest of Labels',
            'value'=>'$data->getRestofLabels()',
        ),
        array(
            'header'=>'Date',
            'value'=>'$data->getDate()',
        ),
		
		

		// array(
		// 	'class'=>'oButtonColumn',
		// 	'template'=>'{view}{update}',
		// 	'buttons'=>array
		// 	(
		// 		'view' => array(
		// 			'imageUrl'=>false,
		// 			'options' => array('class' => 'jqm_link grid_view_btn'),
		// 		),
		// 		'update' => array(
		// 			'imageUrl'=>false,
		// 			'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '"Org-".$data->model'),
		// 		),
		// 		/*'reset' => array(
		// 			'imageUrl' => false,
		// 			'url' => 'Yii::app()->createUrl("org/changePassword", array("id" => $data->id))',
		// 			'label' => 'Reset Password',
		// 			'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Reset Password')),
		// 		)*/
		// 	),
		// ),
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
