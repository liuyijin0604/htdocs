<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
           'Product List',
	),
));
?>
<h2><?=$this->t('Manage Products');?></h2>
<div class="row">
	<div class="col-xs-12">
		<div class="dropdown" style="display: inline-block; margin-right: 1em">
			<button class="btn btn-default dropdown-toggle" style="width: 10em" type="button" id="menu1" data-toggle="dropdown">Create
			<span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
				<li role="presentation"><a role="menuitem" tabindex="-1" class="ajax-link" href="<?=$this->createUrl('product/create');?>">Create Product</a></li>
				<li role="presentation"><a role="menuitem" tabindex="-1" class="ajax-link" href="<?=$this->createUrl('product/createKit');?>">Create Product Kits/Packages/Bundles</a></li>
			</ul>
		</div>
		<div class="dropdown" style="display: inline-block; margin-right: 1em">
			<button class="btn btn-default dropdown-toggle" style="width: 10em" type="button" id="menu2" data-toggle="dropdown">Import
			<span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu" aria-labelledby="menu2">
				<li role="presentation"><a role="menuitem" tabindex="-1" class="tracking-modal-link" href="<?=$this->createUrl('product/import');?>">Import Products</a></li>
				<li role="presentation"><a role="menuitem" tabindex="-1" class="tracking-modal-link" href="<?=$this->createUrl('product/importKit');?>">Import Product Kits/Packages/Bundles</a></li>
			</ul>
		</div>
	</div>
	<!-- <div class="col-xs-2">
    <form action="wmsProd/prod/id/<?=Yii::app()->user->org?>">
      <button class="btn btn-default" type="submit">Sync Products</button>
    </form>
	</div> -->
</div>

<?php $this->widget('application.extensions.booster.TbExtendedGridView', array(
	'id'=>'wms-prod-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'type', 'value' => '$data->getType()', 
			'filter'=>CHtml::dropDownList('WmsProd[type]', $model->type, $this->t([10 => 'Physical Product', 50 => 'Kit/Set']), array('prompt'=>$this->t('All'),'class'=>'form-control')),),
		'ean',
		'name',
		'name_zh',
		'brand',
		// array('name'=>'sku','value'=>'$data->getSku(Yii::app()->session["org_id"])','filter'=>CHtml::textField('WmsProd[sku]', $model->sku, ['class' => 'form-control'])),
		array('name'=>'sku','value'=>'$data->getSku(!empty(Yii::app()->session["org_id"]) ? Yii::app()->session["org_id"] : Yii::app()->user->org)','filter'=>CHtml::textField('WmsProd[sku]', $model->sku, ['class' => 'form-control'])),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('WmsProd[status]', $model->status, $this->t(WmsProd::$states), array('prompt'=>$this->t('All'),'class'=>'form-control')),),
		array(
			'class'=>'application.extensions.booster.TbButtonColumn',
			'template'=>'{update}{update2} &nbsp; {print}{print_horizontal}',
			'header' => 'Actions',
			'buttons'=>array
			(
				'view' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'$data->type == 10',
					'options' => array('class' => 'tab_link ajax-link', 'label'=>$this->t('Update'), 'title' => 'Product'),
				),
				'update2' => array(
					'imageUrl'=>false,
					'visible'=>'$data->type == 50',
					'options' => array('class' => 'tab_link ajax-link', 'label'=>$this->t('Update'), 'title' => 'Product'),
					'url' => 'Yii::app()->createUrl("pcaw/product/updateKit", ["id" => $data->id])',
					'label' => '<i class="glyphicon glyphicon-pencil"></i>',
				),
				'print' => array(
					'imageUrl' => false,
					'visible' => 'Yii::app()->user->org != 1882 ? true : false',
					'icon' => 'print',
					'url' => 'Yii::app()->createUrl("pcaw/product/print", ["id" => $data->id])',
					'options' => array('target' => '_blank', 'class' => 'grid_print_btn', 'title' => 'Print SKU'),
				),
				'print_horizontal' => array(
					'imageUrl' => false,
					'visible' => 'Yii::app()->user->org == 1882 ? true : false',
					'icon' => 'print',
					'url' => 'Yii::app()->createUrl("pcaw/product/printHorizontal", ["id" => $data->id])',
					'options' => array('target' => '_blank', 'class' => 'grid_print_btn', 'title' => 'Print SKU'),
				),
			),
		),
	),
)); ?>
</div>

<!-- Tracking Modal -->
<div class="modal fade" id="modal-tracking" tabindex="-1" role="dialog" aria-labelledby="modal-tracking-label" aria-hidden="true">
  <div class="modal-dialog ">
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
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});
	$('.search-form form', panel).submit(function(){
		$.fn.yiiGridView.update('wms-prod-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#wms-prod-grid', panel).yiiGridView('update');
	});
        $('body').off('click', 'a.tracking-modal-link').on('click', 'a.tracking-modal-link', function(e){
		$('#modal-tracking').modal();
		$('#modal-tracking .modal-body').load($(this).attr('href'));
		e.preventDefault();
	});

});
</script>
<?php $this->registerJS(ob_get_clean()); ?>