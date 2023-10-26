<h1><?=$this->t('Postcodes');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', [
	'id'=>'postcode-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>[
		'postcode',
		'suburb',
		['name' => 'state', 'filter'=>CHtml::dropDownList('Postcode[state]', $model->state, Postcode::stateList(), ['prompt'=>$this->t('All')])],
		['name' => 'country', 'filter'=>CHtml::dropDownList('Postcode[country]', $model->country, Postcode::countryList(), ['prompt'=>$this->t('All')])],
		['header' => 'Map', 'type' => 'raw', 'value' => '"<a href=\"https://maps.google.com/?q=".$data->suburb."+".$data->state."+".$data->postcode.(empty($data->lat)? "" : "&ll=".$data->lat.",".$data->lon)."&z=16\" target=\"_blank\">Show</a> &#8599"'],
	],
]); ?>
<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});
	$('.search-form form', panel).submit(function(){
		$.fn.yiiGridView.update('postcode-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#postcode-grid', panel).yiiGridView('update');
	});
});
</script>
