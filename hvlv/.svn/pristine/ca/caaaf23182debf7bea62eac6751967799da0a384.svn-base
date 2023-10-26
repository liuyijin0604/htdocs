<div style="position: absolute; left: 200px;">
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-2"><div style="background-position:-48px -688px" class="icon"></div> Export</a>
<div id="<?=$_GET["tabid"];?>-dropdown-2" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="#" data-baseurl="<?=$this->createUrl('pickupList/exportAll', array('type' => 'search'));?>" target="_blank" class="export_search">Current Search</a></li>
	</ul>
</div>
<a class="jqm_link" href="<?=$this->createUrl('pickupList/create');?>"><div style="background-position:-16px 0" class="icon"></div> Manual Create</a> &nbsp; <a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div class="icon" style="background-position:-48px -688px"></div> Reports</a>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a class="jqm_link" href="<?=$this->createUrl('pickupList/report', ['type' => 'det']);?>">Detailed Report</a></li>
		<li><a class="jqm_link" href="<?=$this->createUrl('pickupList/report', ['type' => 'bal']);?>">Balance Report</a></li>
        <li><a class="jqm_link" href="<?=$this->createUrl('pickupList/report', ['type' => 'top']);?>">Top Report</a></li>
        <li><a class="jqm_link" href="<?=$this->createUrl('pickupList/report', ['type' => 'drv']);?>">Driver Report</a></li>
	</ul>
</div>
</div>

<h1><?=$this->t('Pickups/Receipts');?></h1>
<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
));
?>
</div>

<?php
$form=$this->beginWidget('CActiveForm', array(
    'id'=>'receipt-list-form',
    'action' => $this->createUrl('pickuplist/listupdate'),
    'enableAjaxValidation'=>false,
));
?>
<?php 
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'shipment-receipt-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'created', 'value' => 'substr($data->created,0,10)'),
        array('header'=>'Finish?','type'=>'html', 'value'=>'($data->checkFinish())? CHtml::tag("div",array("class"=>"icon")) : CHtml::tag( "div",array("class"=>"icon", "style"=>"background-position:-96px 0px"))',),
		array('header' => 'Receive', 'type'=>'raw', 'value' => '(!empty($data->mdata["cc_status"]) && $data->mdata["cc_status"] == 10)? "<span class=\"warn\">需确认入库</span>" : "" '),
		array('name' => 'fwd_name', 'value' => 'empty($data->fwd_id)? "" : $data->owner->shortName(2)." (".$data->owner->code.")"'),
		'ref',
		array('header' => 'Packs', 'value' => '$data->countLines();'),
		array('header' => 'Weight', 'value' => '$data->totWeight();'),
		// array('header' => 'Real Packs', 'type' => 'raw', 'value' => '"<div id=\"pkgs_div_".$data->id."\"><input type=\"text\" name=\"pkgs[".$data->id."]\" id=\"pkgs_".$data->id."\" value=\"".(!empty($data->mdata["pkgs"])?$data->mdata["pkgs"]:"")."\" size=\"10\" disabled autocomplete=\"off\" /></div>"'),
		// array('header' => 'Cash For Parcels', 'type' => 'raw', 'value' => '"<div id=\"total_div_".$data->id."\"><input type=\"text\" name=\"total[".$data->id."]\" id=\"total_".$data->id."\" value=\"".(!empty($data->mdata["total"])?$data->mdata["total"]:"")."\" size=\"10\" disabled autocomplete=\"off\" /></div>"'),
		array('name' => 'dpt_id', 'value' => '$data->getBranch()', 
			'filter'=>CHtml::dropDownList('PickupList[dpt_id]', $model->dpt_id, Org::dptList(), array('prompt'=>$this->t('All'))),),
		//array('name' => 'status', 'value' => '$data->getStatus()', 'filter'=>CHtml::dropDownList('PickupList[status]', $model->status, $model::$states, array('prompt'=>$this->t('All'))),),
		//array('header' => 'Total', 'value' => 'empty($data->mdata["total"])? "" : $data->mdata["total"]'),
		array('name' => 'by_id', 'value' => 'empty($data->by_id)? "" : $data->creator->name', ),
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
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '"Receipt ".$data->ref'),
				),
			),
		),
	),
)); ?>
<?php $this->endWidget(); ?>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});

	var resetFilters = function(){
		$('.search-form form', panel).trigger('reset');
		$('#shipment-receipt-grid', panel).yiiGridView('update', {data: 'ExParcel=reset'});
	};

	$('.search-form form', panel).on('submit', function(){
		$('#shipment-receipt-grid', panel).yiiGridView('update', {data: $('.filters input, .filters select', panel).serialize() + '&' + $(this).serialize()});
		return false;
	}).find('.reset_btn').click(resetFilters);

	$('input[id^=pkgs]', panel).on('keyup', function(e) {
		var keyCode = e.charCode || e.keyCode;
		if (keyCode == 13) {
			var id = $(this).attr('id').match(/(\d+)/);
			if (id) {
				$('form#receipt-list-form', panel).submit();
			}
		}
	});

	$('input[id^=total]', panel).on('keyup', function(e) {
		var keyCode = e.charCode || e.keyCode;
		if (keyCode == 13) {
			var id = $(this).attr('id').match(/(\d+)/);
			if (id) {
				$('form#receipt-list-form', panel).submit();
			}
		}
	});

	$('div[id^=pkgs]', panel).on('dblclick', function() {
		if ($(this).find('input[id^=pkgs]').attr('disabled')) {
			$(this).find('input[id^=pkgs]').removeAttr('disabled');
			$(this).find('input[id^=pkgs]').focus();
		} else {
			$(this).find('input[id^=pkgs]').attr('disabled', 'disabled');
		}
	});

	$('div[id^=total]', panel).on('dblclick', function() {
		if ($(this).find('input[id^=total]').attr('disabled')) {
			$(this).find('input[id^=total]').removeAttr('disabled');
			$(this).find('input[id^=total]').focus();
		} else {
			$(this).find('input[id^=total]').attr('disabled', 'disabled');
		}
	});

	$('form#receipt-list-form', panel).on('success', function() {
		$('form#receipt-list-form', panel).find('input[id^=pkgs]').each(function() {
			$(this).attr('disabled', 'disabled');
		});
		$('form#receipt-list-form', panel).find('input[id^=total]').each(function() {
			$(this).attr('disabled', 'disabled');
		});
	});

	tab.bind('onOpen', function(){
		$('#shipment-receipt-grid', panel).yiiGridView('update', {
			success: function() {
				$('input[id^=pkgs]', panel).off('keyup').on('keyup', function(e) {
					var keyCode = e.charCode || e.keyCode;
					if (keyCode == 13) {
						var id = $(this).attr('id').match(/(\d+)/);
						if (id) {
							$('form#receipt-list-form', panel).submit();
						}
					}
				});

				$('input[id^=total]', panel).off('keyup').on('keyup', function(e) {
					var keyCode = e.charCode || e.keyCode;
					if (keyCode == 13) {
						var id = $(this).attr('id').match(/(\d+)/);
						if (id) {
							$('form#receipt-list-form', panel).submit();
						}
					}
				});

				$('div[id^=pkgs]', panel).off('dblclick').on('dblclick', function() {
					if ($(this).find('input[id^=pkgs]').attr('disabled')) {
						$(this).find('input[id^=pkgs]').removeAttr('disabled');
						$(this).find('input[id^=pkgs]').focus();
					} else {
						$(this).find('input[id^=pkgs]').attr('disabled', 'disabled');
					}
				});

				$('div[id^=total]', panel).off('dblclick').on('dblclick', function() {
					if ($(this).find('input[id^=total]').attr('disabled')) {
						$(this).find('input[id^=total]').removeAttr('disabled');
						$(this).find('input[id^=total]').focus();
					} else {
						$(this).find('input[id^=total]').attr('disabled', 'disabled');
					}
				});

				$('form#receipt-list-form', panel).off('success').on('success', function() {
					$('form#receipt-list-form', panel).find('input[id^=pkgs]').each(function() {
						$(this).attr('disabled', 'disabled');
					});
					$('form#receipt-list-form', panel).find('input[id^=total]').each(function() {
						$(this).attr('disabled', 'disabled');
					});
				});
			}
		});
	});

	$('a.export_search', panel).on('mousedown', function(){
		var q = $('.filters input, .filters select', panel).serialize();
		$(this).attr('href', $(this).data('baseurl') + '&' + q);
	});
});
</script>
