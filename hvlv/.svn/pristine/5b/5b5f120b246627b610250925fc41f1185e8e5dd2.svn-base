<div style="right: 20px;position: absolute;">
<a class="jqm_link" href="<?=$this->createUrl('/zoneMap/update', ['org_id' => $model->org_id, 'zone_id' => $model->zone_id]);?>"><div class="icon" style="background-position:-16px 0"></div> Update</a>
</div>
<h1><?=$model->org->name.':'.$model->zone_id.' '.$this->t('Zone Maps');?></h1>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'zone-map-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'ajaxUrl' => Yii::app()->request->url,
	'filter'=>$model,
	'columns'=>array(
		// 'zone_id',
		'z1',
		'z2',
		'zone_name',
		'pc_lo',
		'pc_hi',
		'suburb',
		'state',
	),
)); ?>

<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});
	$('.search-form form', panel).submit(function(){
		$.fn.yiiGridView.update('zone-map-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#zone-map-grid', panel).yiiGridView('update');
	});
});
</script>
