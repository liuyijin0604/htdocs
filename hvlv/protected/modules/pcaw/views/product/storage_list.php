<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
           'Stock List',
	),
));
?>
<h2><?=$this->t('Manage Stock');?></h2>
<div class="row">
	<?php if (in_array(Yii::app()->user->org, [1,2025])) { ?>
	<div class="col-xs-3">
		<span class="glyphicon glyphicon-stop" style="color: red; font-size: 20px;">0-33%</span>&nbsp;
		<span class="glyphicon glyphicon-stop" style="color: orange; font-size: 20px;">34-66%</span>&nbsp;
		<span class="glyphicon glyphicon-stop" style="color: green; font-size: 20px;">67-100%</span>&nbsp;
		<span class="glyphicon glyphicon-stop" style="color: black; font-size: 20px;">Unknown</span>&nbsp;
	</div>
	<?php } ?>
	<div class="col-xs-9">
		<div class="dropdown" style="display: inline-block; margin-right: 1em">
			<button class="btn btn-default dropdown-toggle" style="width: 10em" type="button" id="menu1" data-toggle="dropdown">Export <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
				<li role="presentation"><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('product/exportStock');?>" target="_blank">Current Search</a></li>
				<li role="presentation"><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('product/exportStockWithLocation');?>" target="_blank">Whole Stock With Loc</a></li>
				<li role="presentation"><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('product/exportStockExpiry')?>" target="_blank">Expiry Report</a></li>
			</ul>
		</div>
	</div>
</div>
<!-- <div class="row">
	<div class="col-xs-2">
    <form action="wmsProd/storage/id/<?=Yii::app()->user->org?>">
      <button class="btn btn-default" type="submit">Sync Stocks</button>
    </form>
	</div>
</div> -->
<?php
if (in_array(Yii::app()->user->org, [Org::ORGID_3PL_COBAYER])) {
	$ec = new CDbCriteria;
	$ec->addCondition('qty > 0');
}
if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
	$ec = new CDbCriteria;
	$ec->addCondition('org_id = ' . Yii::app()->session['org_id']);
}
$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'id'=>'wms-org-stock-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 30, $ec),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'cust_name', 'value' => '$data->customer->name', 'visible' => sizeof(User::getOrgIds()) > 1 ? true : false),
		array('name' => 'prod_name', 'value' => 'empty($data->prod)? "" : $data->prod->name'),
		array('name' => 'prod_ean', 'type' => 'raw', 'value' => 'empty($data->prod)? "" : "<a href=\"".Yii::app()->createUrl("pcaw/product/update", ["id" => $data->prod_id])."\" class=\"tab_link ajax-link\" title=\"Product ".$data->prod->name."\">".$data->prod->ean."</a>"'),
		array('name'=>'prod_sku','value'=>'$data->prod->getSku(Yii::app()->session["org_id"])','filter' => CHtml::textField('WmsStock[prod_sku]', $model->prod_sku, ['class' => 'form-control'])),
		array('name' => 'qty', 'value' => '$data->getQty()'),
		'qty_res',
		array('name' => 'expiry', 'type' => 'raw', 'value' => '$data->showExpiry()'),
		//Author:Nero Date:2021/6/27 Description: Show branch of stock
		array('name' => 'dpt_id', 'value' => '$data->getBranch()', 'filter'=>CHtml::dropDownList('WmsStock[dpt_id]', $model->dpt_id, Org::dptList3PL(), ['prompt'=>$this->t('All'),'class' => 'form-control']),),
		//array('name' => 'dpt_id', 'type' => 'raw', 
		//'value' => function($model){
			//return (($model->dpt_id)==106 ? "SYD":"MEL");
		//}
		//,'filter' => CHtml::textField('WareHouse[name]', $model->dpt_id, ['disabled' => 'disabled','class' => 'form-control'])
		//,),
		//'dpt_id',
		array('name' => 'updated', 'value' => 'date("Y-m-d H:i", strtotime($data->updated))'),
//		array(
//			'class'=>'oButtonColumn',
//			'template'=>'{view}',
//			'buttons'=>array
//			(
//				'view' => array(
//					'imageUrl'=>false,
//                                        'url'=>'Yii::app()->createUrl("wmsStock/view", ["id" => $data->id])',
//					'options' => array('class' => 'jqm_link grid_view_btn', 'data-win-class' => 'L'),
//				),
//			),
//		),
	),
)); ?>

<script type="text/javascript">
$(function(){
       $('a.export_search').on('mousedown', function(){
		var q = $('.filters input, .filters select').serialize()+'&'+$('.search-form form').serialize();
		$(this).attr('href', $(this).data('baseurl') + '?' + q);
	});
});
</script>