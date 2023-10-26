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
		'Reports',
	),
));
?>
<h2>Pickup List</h2>
<?php
$org = Org::model()->findByPk(Yii::app()->user->org);
$model = new PickupList('search');
$model->fwd_id = Yii::app()->user->org;
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
		array('name' => 'ref', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("pos/reports/view", array("id" => $data->id, "t" => "pl"))."\" class=\"view-modal-link\">".$data->ref."</a>"'),
		array('name' => 'created', 'value' => 'substr($data->created,0,10)'),
		array('header' => 'Packs', 'value' => '$data->countLines();'),
		array('header' => 'Weight', 'value' => '$data->totWeight();'),
		array(
			'class'=>'application.extensions.booster.TbButtonColumn',
			'template'=>'{view} &nbsp; {export}',
			'header' => 'Actions',
			'buttons'=>array(
				'view' => array(
					'visible'=>'true',
					'icon' => 'search',
					'url' => 'Yii::app()->createUrl("pos/reports/view",["id" => $data->id, "t" => "pl"])',
					'options' => array('class' => 'view-modal-link', 'label'=>$this->t('View'), 'title' => 'View'),
				),
				'export' => array(
					'visible'=>'true',
					'icon' => 'export',
					'url' => 'Yii::app()->createUrl("pos/reports/export",["id" => $data->id, "t" => "pl"])',
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