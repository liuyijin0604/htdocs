<?php
$this->breadcrumbs=array(
	'Manifests'=>array('index'),
	$this->t('List'),
);
?>

<h1><?=$this->t('Manifests');?></h1>
<?php //echo '<div style="margin-top: -26px; margin-left: 120px;"><a class="jqm_link" href="manifest/create"><div style="background-position:-16px 0" class="icon"></div>'.$this->t('Upload Manifest').'</a></div>';?>
<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'manifest-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'ref',
		array('header' => 'File', 'type' => 'raw', 'value' => '"<a href=\"".$data->getFileLink()."\" target=\"_blank\">".$data->getFileName()."</a>"',),
		array('name' => 'type', 'value' => '$data->getType()', 
			'filter'=>CHtml::dropDownList('Manifest[type]', $model->type, $this->t(Manifest::$types), array('prompt'=>$this->t('All'))),),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('Manifest[status]', $model->status, $this->t(Manifest::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'fwd_name', 'value' => 'empty($data->fwd_id)? "" : $data->owner->name', ),
		array('name' => 'dpt_id', 'value' => '$data->getDptName()', 
			'filter'=>CHtml::dropDownList('Manifest[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), array('prompt'=>$this->t('All'))),),
		array('name' => 'by_id', 'value' => 'empty($data->by_id)? "" : $data->creator->name', ),
		'created',
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view} {export}',
			'buttons'=>array
			(
				'view' => array(
					'url' => 'Yii::app()->createURL("invoice/print", array("id" => $data->invoice->id))',
					'imageUrl'=>false,
					'label' => 'Invoice',
					'options' => array('class' => 'grid_view_btn', 'target' => '_blank'),
					'visible' => '!empty($data->invoice->id)'
				),
				'export' => array(
					'url' => '"#".$data->id',
					'imageUrl' => false,
					'label' => $this->t('Export'),
					'options' => array('class' => 'grid_file_btn export_btn', 'data-dropdown' => '#'.$_GET["tabid"].'-dropdown-1'),
					'visible' => 'in_array($data->type, [10, 110, 210])'
				),
			),
		),
	),
)); ?>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createUrl('manifest/export', array('id'=>0));?>" target="_blank">Manifest</a></li>
		<li><a href="<?=$this->createUrl('manifest/label', array('id'=>0, 'size'=> 'A4'));?>" target="_blank">Label A4</a></li>
		<li><a href="<?=$this->createUrl('manifest/label', array('id'=>0, 'size'=> 'A6'));?>" target="_blank">Label A6</a></li>
		<li><a href="<?=$this->createUrl('manifest/label', array('id'=>0, 'size'=> 'A6', 'type' => 'eparcel'));?>" target="_blank">eParcel A6</a></li>
	</ul>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});
	$('.search-form form', panel).submit(function(){
		$.fn.yiiGridView.update('manifest-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	panel.on('click', 'a.export_btn', function(){
		var id = $(this).attr('href').substring(1);
		$('#<?=$_GET["tabid"];?>-dropdown-1 a', panel).each(function(){
			$(this).attr('href', $(this).attr('href').replace(/\/(\d+)\.app/mg, '/'+id+'.app'));
		});
	});
	tab.on('onOpen', function(){
		$('#manifest-grid', panel).yiiGridView('update');
	});
});
</script>
