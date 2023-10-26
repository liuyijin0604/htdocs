<?php
echo '<div class="row"><div class="col col-md-10 col-sm-8">';
$this->widget('zii.widgets.CBreadcrumbs', [
	'links' => [
		'Shipments',
	],
]);
echo '</div><div class="col col-md-2 col-sm-4"><a href="',$this->createUrl('shipment/create'),'"><span class="glyphicon glyphicon-plus-sign"></span> New Shipment</a></p></div></div>';
$noid = $model->with('cnee')->count('agent_id = :aid AND status > 8 AND status < 18 AND cnee.cnid_id = 0', [':aid' => Yii::app()->user->org]);
$nop = $model->count('agent_id = :aid AND status NOT IN (100,102) AND cbwf = 1', [':aid' => Yii::app()->user->org]);
$irnop = $model->count('agent_id = :aid AND status >= 18 AND status NOT IN (100,102) AND cbwf = 1', [':aid' => Yii::app()->user->org]);
?>
<div>
<?php if ($noid > 0): ?>
<a href="<?=Yii::app()->createUrl('pos/shipment/report', ['type' => 'noid']);?>" target="_blank"><span class="glyphicon glyphicon-menu-right"></span> Waiting ID <span class="badge"><?=$noid;?></span></a> &nbsp;
<?php endif; ?>
<?php if ($nop > 0): ?>
<div class="dropdown" style="display:inline">
<a href="#" id="aLabel" data-toggle="dropdown"><span class="glyphicon glyphicon-menu-right"></span> Not Printed <span class="badge"><?=$nop;?></span></a>
<ul class="dropdown-menu" aria-labelledby="dLabel">
	<li><a href="<?=Yii::app()->createUrl('pos/shipment/report', ['type' => 'nop']);?>" target="_blank">Report</a></li>
	<li><a class="confirm_link" href="<?=substr(Yii::app()->createUrl('pos/shipment/bulkprint/nop/all_connotes_'.date('Ymd').'.pdf'), 0, -5);?>" target="_blank">Print All</a></li>
	<?php if ($irnop > 0): ?>
	<li><a class="confirm_link" href="<?=substr(Yii::app()->createUrl('pos/shipment/bulkprint/irnop/ready_connotes_'.date('Ymd').'.pdf'), 0, -5);?>" target="_blank">Print Info Ready <span class="badge"><?=$irnop;?></span></a></li>
	<?php endif; ?>
</ul>
</div>
<?php endif; ?>
</div>
<?php
$org = Org::model()->findByPk(Yii::app()->user->org);
$this->widget(
	'application.extensions.booster.TbExtendedGridView',
	[
		'fixedHeader' => true,
		'headerOffset' => 40,
		'type' => 'striped',
		'dataProvider' => $model->search(true, empty($org->extra['pager_size'])? 20 : $org->extra['pager_size']),
		'responsiveTable' => true,
		'template' => "{summary}\n{items}\n{pager}",
		'filter'=>$model,
		'selectableRows' => 2,
		'enableSorting' => false,
		'bulkActions' => [
			'align' => 'left',
			'actionButtons' => [
				[
					'id' => 'bulk-print',
					'buttonType' => 'button',
					'context' => 'primary',
					'size' => 'small',
					'label' => 'Bulk Print',
					'click' => 'js:function(values){
					window.open("bulkPrint/"+values.join(",")+"/bulk.pdf");
					/*bootbox.confirm("Mark all as printed?", function(result) {
						$.get("shipment/markPrint?s="+values.join(","));
					});*/
				}'
				],
			],
			'checkBoxColumnConfig' => [
				'name' => 'id'
			],
		],
		'afterAjaxUpdate' => (Yii::app()->user->grp == 80)? 'js:function(id, data){ $(\'#egw0\').after(\'<div class="pull-right"><button type="button" data-toggle="modal" data-target="#modal-export" class="btn btn-default btn-sm">Export</button></div>\'); }' : 'js:function(){return}',
		'columns' => [
			['name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("pos/shipment/update", array("id" => $data->id))."\" class=\"ajax-link\">".$data->hbn."</a>"'],
			['name' => 'status', 'value' => '$data->getStatus()',
				'filter'=>CHtml::dropDownList(get_class($model).'[status]', $model->status, $this->t($model->statusList()), ['prompt' => $this->t('All'), 'class' => 'form-control']),],
			['name' => 'weight'],
			['name' => 'cnor_name', 'value' => 'empty($data->cnor)? "" : $data->cnor->name', 'visible' => $type =='ex'],
			['name' => 'cnor_tel', 'value' => 'empty($data->cnor)? "" : $data->cnor->tel', 'visible' => $type =='ex'],
			['name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',],
			['name' => 'cnee_tel', 'value' => 'empty($data->cnee)? "" : $data->cnee->tel',],
			'state',
			['name' => 'created'],
			['header' => 'Printed', 'value' => '$data->cbwf & 1 > 0? "N" : "Y"', 'filter' => CHtml::dropDownList(get_class($model).'[cbwf]', $model->cbwf, $this->t([1 => 'N', 0 => 'Y']), ['prompt' => $this->t('All'), 'class' => 'form-control'])],
			[
				'class'=>'application.extensions.booster.TbButtonColumn',
				'template'=>'{find} &nbsp; {print} &nbsp; {upid}',
				'header' => 'Actions',
				'buttons'=>[
					'find' => [
						'visible'=>'true',
						'icon' => 'search',
						'url' => 'Yii::app()->createUrl("pos/shipment/tracking",["c" => $data->hbn])',
						'options' => ['class' => 'tracking-modal-link', 'label'=>$this->t('Tracking'), 'title' => 'Tracking'],
					],
					'print' => [
						'visible'=>'true',
						'icon' => 'print',
						'url' => 'substr(Yii::app()->createUrl("pos/shipment/print", ["id" => $data->id]),0,-5)."/".$data->hbn.".pdf"',
						'options' => ['target' => '_blank', 'class' => 'grid_print_btn', 'label'=>$this->t('Print'), 'title' => 'Print'],
					],
					'upid' => [
						'visible'=>'empty($data->cnee->cnid_id)',
						'icon' => 'upload',
						'url' => 'Yii::app()->createUrl("pos/shipment/upid", ["id" => $data->id])',
						'options' => ['target' => '_blank', 'class' => 'tracking-modal-link', 'label'=>$this->t('Upload ID'), 'title' => 'Upload ID'],
					],
				],
			],
		],
	]
);

?>
<!-- Tracking Modal -->
<div class="modal fade" id="modal-tracking" tabindex="-1" role="dialog" aria-labelledby="modal-tracking-label" aria-hidden="true">
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

<?php if (Yii::app()->user->grp == 80): ?>
<!-- Export Modal -->
<div class="modal fade" id="modal-export" tabindex="-1" role="dialog" aria-labelledby="modal-export-label" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">Export</h4>
	  </div>
	  <form id="export-form" target="ifrm" method="post" data-ajaxf="2">
	  <div class="modal-body">
		<div class="form-group">
			<label><?=$this->t('Date Range');?></label>
			<div class="input-daterange input-group" id="datepicker">
			<input type="text" class="input-lg form-control" name="start" value="<?=date('Y-m-d', strtotime('-7 day'));?>" />
			<span class="input-group-addon">to</span>
			<input type="text" class="input-lg form-control" name="end" value="<?=date('Y-m-d');?>" />
			</div>
		</div>
		<div class="form-group">
			<label><input id="ucsc" type="checkbox" name="sc" value="1" checked />
		<?=$this->t('Use Current Search Conditions');?></label>
		</div>
	  </div>
	  <div class="modal-footer">
		<button type="submit" class="btn btn-primary"><?=$this->t('Export');?></button>
	  </div>
	  </form>
	</div>
  </div>
</div>
<?php endif; ?>
<iframe id="ifrm" name="ifrm" style="display:none"></iframe> 
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('body').off('click', 'a.tracking-modal-link').on('click', 'a.tracking-modal-link', function(e){
		$('#modal-tracking').modal();
		$('#modal-tracking .modal-body').load($(this).attr('href'));
		e.preventDefault();
	});

	$('#egw0').after('<div class="pull-right"><button type="button" data-toggle="modal" data-target="#modal-export" class="btn btn-default btn-sm">Export</button></div>');

	$('#export-form').on('submit', function(e){
		if($('#ucsc:checked').length > 0){
			$(this).attr('action', 'export.html?' + $('.filters input, .filters select').serialize());
		}else{
			$(this).attr('action', 'export.html');
		}
		$('#ifrm').on('load', function(){
			$('#modal-export').modal('hide');
		});
	});
	$('.input-daterange').datepicker({format: "yyyy-mm-dd"});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>