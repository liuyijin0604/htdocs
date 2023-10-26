<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
		'Manifests',
	),
));
?>
<div style="margin: 5px"></div>
   <div class="ims-manifest-export">
        <a class="btn btn-default" href="<?=$this->createUrl('manifest/createShips');?>">New Manifest</a>
    </div>
<?php
$org = Org::model()->findByPk(Yii::app()->user->org);
 $this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>'task_grid_view',
    'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model->search(),
    'template' => "{summary}\n{items}\n{pager}",
    'columns'=>array(
     	array('name' => 'ref', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("manifest", array("update" => $data->id))."\" class=\"tab_link\" title=\"".$data->ref."\">".$data->ref."</a>"',),
		// array('name' => 'awb'),
        // array('header'=>'Customer','name' => 'owner_name','value' => '$data->getOwnerName()'),
		// array('name' => 'status', 'value' => '$data->getStatus()', 'filter'=>CHtml::dropDownList('CustomerConsol[status]', $model->status, $this->t($model::$states), array('prompt'=>$this->t('All'),'class'=>'form-control')),),
		// 'pol',
		// 'pod',
		array('name'=>'created','header'=>'Create Date'),
		array('header' => 'Shipments', 'value' => '$data->countLines()'),
		array('header' => 'Weight', 'value' => '$data->totWeight()'),
    ),
));
?>
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