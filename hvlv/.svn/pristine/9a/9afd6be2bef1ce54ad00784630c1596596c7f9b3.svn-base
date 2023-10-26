<div style="position:relative">
<div class="dropdown" style="position:absolute; right: 0;">
<a href="#" id="dropdownMenu1" data-toggle="dropdown"><span class="glyphicon glyphicon-stats"></span> Reports</a>
<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
	<li><a href="<?=Yii::app()->createUrl('ims/shipment/report', ['type' => 'dlv']);?>" target="_blank">Delivery Report</a></li>
</ul>
</div>
</div>
<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
		'Shipments',
	),
));
//echo '</div><div class="col col-md-2 col-sm-4"><a href="',$this->createUrl('shipment/create'),'"><span class="glyphicon glyphicon-plus-sign"></span> New Shipment</a></p></div></div>';
// $nop = $model->count('agent_id = :aid AND status NOT IN (100,102) AND cbwf = 1', [':aid' => Yii::app()->user->org]);
// $irnop = $model->count('agent_id = :aid AND status >= 18 AND status NOT IN (100,102) AND cbwf = 1', [':aid' => Yii::app()->user->org]);
?>
</br>
</br>
</br>
<?php
$org = Org::model()->findByPk(Yii::app()->user->org);
$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'fixedHeader' => true,
	'headerOffset' => 40,
	'type' => 'striped',
	'dataProvider' => $model->search(true, (empty($org->extra['pager_size'])? 20 : $org->extra['pager_size']),false,false),
	'responsiveTable' => true,
	'template' => "{summary}\n{items}\n{pager}",
	'filter'=>$model,
	'selectableRows' => 2,
	'enableSorting' => false,
	'bulkActions' => array(
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
					/*bootbox.confirm("Mark all as printed?", function(result) {
						$.get("shipment/markPrint?s="+values.join(","));
					});*/
				}'
				),
		),
		'checkBoxColumnConfig' => array(
			'name' => 'id'
		),
	),
	'afterAjaxUpdate' => 'js:function(id, data){ $(\'#egw0\').after(\'<div class="pull-right"><button type="button" data-toggle="modal" data-target="#modal-export" class="btn btn-default btn-sm">Export</button></div>\'); }',
	'columns' => array(
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("ims/shipment/update", array("id" => $data->id))."\"  target=\"_blank\">".$data->hbn."</a>"'),
		'ref',
               'cref',
                ['name'=>'awb_name','header'=>'awb  /  container_no','type'=>'raw','value'=>'@$data->consol->awb."&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;".@$data->consol->mdata["container_no"]','filter'=>CHtml::textfield('awb',@$model->awb_name,["class"=>"form-control"])],
                array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList(get_class($model).'[status]', $model->status, $this->t($model->statusList()), array('prompt' => $this->t('All'), 'class' => 'form-control')),),
		array('name' => 'weight'),
		array('name' => 'cnor_name', 'value' => 'empty($data->cnor)? "" : $data->cnor->name', 'visible' => $type =='im'),
		array('name' => 'cnor_tel', 'value' => 'empty($data->cnor)? "" : $data->cnor->tel', 'visible' => $type =='im'),
		array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
		array('name' => 'cnee_tel', 'value' => 'empty($data->cnee)? "" : $data->cnee->tel',),
		array('name' => 'created'),
		'state',
		array(
			'class'=>'application.extensions.booster.TbButtonColumn',
			'template'=>'{find} &nbsp;  {print} &nbsp;{remove} &nbsp; ',
			'header' => 'Actions',
			'buttons'=>array(
				'find' => array(
					'visible'=>'true',
					'icon' => 'search',
					'url' => 'Yii::app()->createUrl("ims/shipment/tracking",["c" => $data->hbn])',
					'options' => array('class' => 'tracking-modal-link', 'label'=>$this->t('Tracking'), 'title' => 'Tracking'),
				),
				// 'customerService' => array(
				// 	'visible'=>'true',
				// 	'icon' => 'heart',
				// 	'url' => 'Yii::app()->createUrl("ims/customerService/index",["ref" => $data->ref])',
				// 	'options' => array('class' => 'tracking-modal-link', 'label'=>$this->t('Customer Service'), 'title' => 'Customer Service'),
				// ),
				'print' => array(
					'visible'=>'true',
					'icon' => 'print',
					'url' => 'Yii::app()->createUrl("ims/shipment/print", ["id" => $data->id])',
					'options' => array('target' => '_blank', 'label'=>$this->t('Print'), 'title' => 'Print'),
				),
				'remove' => array(
					'visible'=>'$data->canBeDelete()',
                    'icon' => 'minus',
					'url' => '$data->id',
					'options' => array( 'data-hbn' => '$data->hbn', 'class' => 'remove-item-link', 'label'=>$this->t('cancel'), 'title' => 'Remove'),
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
			$(this).attr('action', 'export.app?' + $('.filters input, .filters select').serialize());
		}else{
			$(this).attr('action', 'export.app');
		}
		$('#ifrm').on('load', function(){
			$('#modal-export').modal('hide');
		});
	});
	$('.input-daterange').datepicker({format: "yyyy-mm-dd"});

	$(document).off('click','.remove-item-link').on('click','.remove-item-link',function(e){
        e.preventDefault();
        var id = $(this).attr('href');
        if ( confirm('Are you sure cancel the shipment?') ) {
        	if ( confirm('Are you really sure cancel the shipment? Last Chance!!!') ) {
            var data = {};
            data['id'] = id;

            $.ajax({
                type : 'POST',
                url : '<?php echo Yii::app()->createAbsoluteUrl("manifest/ajaxRemoveShipment") ;?>',
                data: data,
                dataType: 'json',
                success:function(r){
                    if ( r.success == 1 ) {
                       $('#yw0').yiiGridView('update');
                    } else {
                        var msg = '<div>' + r.msg + '</div>';
                        var elm = $(msg);
                        alert(elm);
                    }
                }
            });
          }
        }
    });

});
</script>
<?php $this->registerJS(ob_get_clean()); ?>