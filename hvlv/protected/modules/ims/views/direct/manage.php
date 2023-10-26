<?php
echo '<div class="row"><div class="col col-md-10 col-sm-8">';
$this->widget('zii.widgets.CBreadcrumbs', array(
	'links' => array(
		'Direct Orders',
	),
));
echo '</div><div class="col col-md-2 col-sm-4"><a href="',$this->createUrl('direct/create'),'"><span class="glyphicon glyphicon-plus-sign"></span> New Order</a></p></div></div>';
$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'fixedHeader' => true,
	'headerOffset' => 40,
	'type' => 'striped',
	'dataProvider' => $model->search(true, 20),
	'responsiveTable' => true,
	'template' => "{summary}\n{items}\n{pager}",
	'filter'=>$model,
	'selectableRows' => 2,
	'enableSorting' => false,
	/*'bulkActions' => array(
		'align' => 'left',
		'actionButtons' => array(
			array(
				'id' => 'bulk-print',
				'buttonType' => 'button',
				'context' => 'primary',
				'size' => 'small',
				'label' => 'Bulk Print',
				'click' => 'js:function(values){
					window.open("bulkPrint/"+values.join(",")+"/bulk.pdf");
				}'
				),
		),
		'checkBoxColumnConfig' => array(
			'name' => 'id'
		),
	),*/
	'afterAjaxUpdate' => 'js:function(id, data){ $(\'#egw0\').after(\'<div class="pull-right"><button type="button" data-toggle="modal" data-target="#modal-export" class="btn btn-default btn-sm">Export</button></div>\'); }',
	'columns' => array(
		array('header' => 'Order #', 'name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("pos/direct/update", array("id" => $data->id))."\" class=\"ajax-link\">".$data->hbn."</a>"'),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList(get_class($model).'[status]', $model->status, $this->t($model::$states), array('prompt' => $this->t('All'), 'class' => 'form-control')),),
		array('name' => 'cnor_name', 'value' => 'empty($data->cnor)? "" : $data->cnor->name', 'visible' => $type =='ex'),
		array('name' => 'cnor_tel', 'value' => 'empty($data->cnor)? "" : $data->cnor->tel', 'visible' => $type =='ex'),
		array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
		array('name' => 'cnee_tel', 'value' => 'empty($data->cnee)? "" : $data->cnee->tel',),
		'state',
		array(
			'class'=>'application.extensions.booster.TbButtonColumn',
			'template'=>'{find}',
			'header' => 'Actions',
			'buttons'=>array(
				'find' => array(
					'visible'=>'true',
					'icon' => 'search',
					'url' => 'Yii::app()->createUrl("pos/shipment/tracking",["c" => $data->hbn])',
					'options' => array('class' => 'tracking-modal-link', 'label'=>$this->t('Update'), 'title' => 'Update'),
				),
			),
		),
	),
)
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