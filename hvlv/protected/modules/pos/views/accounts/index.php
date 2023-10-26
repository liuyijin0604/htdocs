<style type="text/css">
.modal-wide {
  width: 80%;
}
.modal-wide .modal-body {
  overflow-y: auto;
}
</style>
<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'links' => array(
		'Accounts',
	),
));
?>
<h2>Invoices</h2>
<?php
$org = Org::model()->findByPk(Yii::app()->user->org);
$model = new Invoice('search');
$model->to_id = Yii::app()->user->org;

$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'fixedHeader' => true,
	'headerOffset' => 40,
	'type' => 'striped',
	'dataProvider' => $model->search(true, empty($org->extra['pager_size'])? 20 : $org->extra['pager_size']),
	'responsiveTable' => true,
	'template' => "{summary}\n{items}\n{pager}",
	'filter'=>$model,
	'selectableRows' => 2,
	'enableSorting' => false,
	'columns' => array(
		array('name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("pos/accounts/export", array("id" => $data->id))."\" target=\"_blank\">".$data->no."</a>"'),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('Invoice[status]', $model->status, $this->t($model::$states), array('prompt'=>$this->t('All'), 'class' => 'form-control')),),
		'date',
		'due',
		array(
			'class'=>'application.extensions.booster.TbButtonColumn',
			'template'=>'{print} &nbsp; {export}',
			'header' => 'Actions',
			'buttons'=>array(
				'print' => array(
					'visible'=>'true',
					'icon' => 'print',
					'url' => 'Yii::app()->createUrl("pos/accounts/export",["id" => $data->id])',
					'options' => array('label'=>$this->t('PDF'), 'title' => 'PDF', 'target' => '_blank'),
				),
				'export' => array(
					'visible'=>'true',
					'icon' => 'file',
					'url' => 'Yii::app()->createUrl("pos/accounts/export",["id" => $data->id, "xls" => "1"])',
					'options' => array('label'=>$this->t('Export'), 'title' => 'Export', 'target' => '_blank'),
				),
			),
		),
	),
)
);
?>

<!-- View Modal -->
<div class="modal fade" id="modal-view" tabindex="-1" role="dialog" aria-labelledby="modal-view-label" aria-hidden="true">
  <div class="modal-dialog modal-wide">
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
	$('body').off('click', 'a.view-modal-link').on('click', 'a.view-modal-link', function(e){
		$('#modal-view').modal();
		$('#modal-view .modal-body').load($(this).attr('href'));
		e.preventDefault();
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>