<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'links' => array(
		'Tools' => array('tools/index'),
		'Products',
	),
));
?>
<h2>Products List</h2>
<?php

$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'fixedHeader' => true,
	'headerOffset' => 40,
	'id' => 'prod_list',
	'type' => 'striped',
	'dataProvider' => $model->search(true, empty($org->extra['pager_size'])? 20 : $org->extra['pager_size']),
	'responsiveTable' => true,
	'template' => "{summary}\n{items}\n{pager}",
	'filter'=>$model,
	'selectableRows' => 2,
	'enableSorting' => false,
	'columns' => array(
		'sn',
		array('name' => 'sku', 'value' => '$data->getSku()'),
		array('name' => 'name', 'value' => '$data->getName()'),
		array('name' => 'price', 'value' => '$data->getPrice()'),
		array(
			'class'=>'application.extensions.booster.TbButtonColumn',
			'template'=>'{update}',
			'header' => 'Actions',
			'buttons'=>array(
				'update' => array(
					'visible'=>'true',
					'icon' => 'edit',
					'url' => 'Yii::app()->createUrl("pos/tools/prodUp",["id" => $data->id])',
					'options' => array('class' => 'modal-link', 'label'=>$this->t('Update'), 'title' => 'Update'),
				),
			),
		),
	),
)
);

?>
<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modal-label" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-body">
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?=$this->t('Close');?></button>
	  </div>
	</div>
  </div>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('body').off('click', 'a.modal-link').on('click', 'a.modal-link', function(e){
		$('#modal').modal();
		$('#modal .modal-body').load($(this).attr('href'));
		e.preventDefault();
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>