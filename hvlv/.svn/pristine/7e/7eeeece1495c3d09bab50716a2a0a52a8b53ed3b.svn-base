<style type="text/css">
.bluk_note_btn {
	font: bold 16px Arial;
  text-decoration: none;
  background-color: rgb(0, 153, 204);
  color: white;
  padding: 2px 6px 2px 6px;
  border-top: 1px solid #CCCCCC;
  border-right: 1px solid #333333;
  border-bottom: 1px solid #333333;
  border-left: 1px solid #CCCCCC;
  }
#right_button {
	float: right;
	padding-bottom: 5px;
}

</style>

<div id ="trackingPanel">
<h1><?=$this->t('Shipment Tracking');?></h1>
<p><?=$this->t('Separate multiple tracking numbers with the Enter key');?></p>
<form method="get" action="<?=$this->createUrl('shipment/tracking');?>" id="tracking_form" data-bit="1">
<div class="form-group">
<textarea class="form-control input-lg" wrap="hard" id="track_code" name="mhbns"></textarea>
</div>
<div class="form-group">
<input class="btn btn-primary btn-lg" type="submit" value="<?php echo $this->t('Multiple Tracking');?>">
</div>
</form>
<div id="result" style="padding: 20px 0;"></div>

<form method="get" action="<?=$this->createUrl('customerService/blukSubmitNote');?>" id="im-parcel-form">

<div id="right_button">
	<a class="bluk_note_btn">Bulk Enquiry</a>
</div>


<?php
$org = Org::model()->findByPk(Yii::app()->user->org);
$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'id'=>'im-parcel-grid',
	'fixedHeader' => true,
	'headerOffset' => 40,
	'type' => 'striped',
	'dataProvider' => $model->search(true, (empty($org->extra['pager_size'])? 5 : $org->extra['pager_size']),false,false),
	'responsiveTable' => true,
	'template' => "{summary}\n{items}\n{pager}",
	'filter'=>$model,
	'selectableRows' => 2,
	'enableSorting' => false,
	'afterAjaxUpdate' => 'js:function(id, data){ $(\'#egw0\').after(\'<div class="pull-right"><button type="button" data-toggle="modal" data-target="#modal-export" class="btn btn-default btn-sm">Export</button></div>\'); }',
	'columns' => array(
		// array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("ims/shipment/update", array("id" => $data->id))."\" class=\"ajax-link\">".$data->hbn."</a>"'),
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("ims/shipment/tracking", array("c" => $data->hbn,"tabid"=>$data->hbn))."\" class=\"tracking-modal-link\">".$data->hbn."</a>"'),
		'ref',
               'cref',
                array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList(get_class($model).'[status]', $model->status, $this->t($model->statusList()), array('prompt' => $this->t('All'), 'class' => 'form-control')),),
		array('name' => 'weight'),
		array('name' => 'cnor_name', 'value' => 'empty($data->cnor)? "" : $data->cnor->name', 'visible' => $type =='im'),
		array('name' => 'cnor_tel', 'value' => 'empty($data->cnor)? "" : $data->cnor->tel', 'visible' => $type =='im'),
		array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
		array('name' => 'cnee_tel', 'value' => 'empty($data->cnee)? "" : $data->cnee->tel',),
		'state',
		array(
			'class'=>'application.extensions.booster.TbButtonColumn',
			'template'=>'{edit} &nbsp; {find} &nbsp; {customerService} &nbsp; ',
			'header' => 'Actions',
			'buttons'=>array(
				'edit' => array(
					'visible'=>'true',
					'icon' => 'pencil',
					'url' => 'Yii::app()->createUrl("ims/shipment/update",["id" => $data->id])',
					'options' => array('class' => 'tracking-modal-link', 'label'=>$this->t('Edit'), 'title' => 'Edit'),
				),
				'find' => array(
					'visible'=>'true',
					'icon' => 'search',
					'url' => 'Yii::app()->createUrl("ims/shipment/tracking",["c" => $data->hbn,"tabid"=>$data->hbn])',
					'options' => array('class' => 'tracking-modal-link', 'label'=>$this->t('Tracking'), 'title' => 'Tracking'),
				),
				'customerService' => array(
					'visible'=>'true',
					'icon' => 'heart',
					'url' => 'Yii::app()->createUrl("ims/customerService/index",["ref" => $data->ref,"tabid"=>$data->ref])',
					'options' => array('class' => 'tracking-modal-link', 'label'=>$this->t('Customer Service'), 'title' => 'Customer Service'),
				)
			),
		),
		array(
			'id'=>'selectedShipments',
			'class'=>'CCheckBoxColumn',
		),
		// array("header" => 'submit note',"type"=>"raw","value"=>'CHtml::checkBox("submit_note[".$data->id."]", false, array("value" => "1", "label" => "Ready to submit note."))'),
	),
)
);

?>

</form>
</div>
<div class="modal fade" id="modal-tracking" tabindex="-1" role="dialog" aria-labelledby="modal-tracking-label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
	<div class="modal-content">
	  <div class="modal-body">
	  </div>
	  <div class="modal-footer">
		<button type="button" id='modal_close' class="btn btn-default" data-dismiss="modal">////<?=$this->t('Close');?></button>
	  </div>
	</div>
  </div>
</div>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#trackingPanel');
	$('#right_button').hide();
	$('body').off('click', 'a.tracking-modal-link').on('click', 'a.tracking-modal-link', function(e){
		$('#modal-tracking<?=@$_GET['tabid']?>').modal();
		$('#modal-tracking<?=@$_GET['tabid']?> .modal-body').load($(this).attr('href'));
		e.preventDefault();
	});
	$('#tracking_form').on('submit', function(){
		$('#im-parcel-grid').show();
		$('#im-parcel-grid').yiiGridView('update', {data: $('.filters input, .filters select', tab).serialize() + '&' + $(this).serialize()});
		$('#right_button').show();
		return false;
	});

	// $('#tracking_form').ajaxForm({
	// 	success: function(r) {
	// 		$('#result').stop().hide().html(r).slideDown();
	// 	},
	// 	beforeSubmit: function(a,f,o){
	// 		return $('#track_code').val() != '';
	// 	},
	// 	dataType: 'html'
	// }).data('ajaxf', true);
	$('#im-parcel-grid').hide();

	$('.bluk_note_btn').on('mousedown', function(){
		<?php $url = $this->createUrl('customerService/blukSubmitNote');?>
		var form = new FormData(document.getElementById("im-parcel-form"));
    $.ajax({
             url: '<?=$url?>',
             type: "post",
             data: form,
             processData: false,
             contentType: false,             
             success: function(r) {
             	r = JSON.parse(r);           	
             	$('#modal-tracking<?=@$_GET['tabid']?>').modal();
							// $('#modal-tracking<?=@$_GET['tabid']?> .modal-body').load($('#im-parcel-form').attr('action'));
							// $('#modal-tracking<?=@$_GET['tabid']?> .modal-body').load(r.content);
							$('#modal-tracking<?=@$_GET['tabid']?> .modal-body').empty();
							$('#modal-tracking<?=@$_GET['tabid']?> .modal-body').append(r.content);
							e.preventDefault();

	           },
             error: function() {
                 console.log('false');
             }
         });
    });

	$('#modal_close').click(function() {		
		$('#modal-tracking<?=@$_GET['tabid']?> .modal-body').empty();
	});


 // $('.bluk_note_btn').click(function() {		
	// 	$('#modal-tracking<?=@$_GET['tabid']?>').modal();
	// 	$('#modal-tracking<?=@$_GET['tabid']?> .modal-body').load($('#im-parcel-form').attr('action'));
	// 	$('#im-parcel-form').submit();
	// 	e.preventDefault();
 //    });

});


</script>
<?php $this->registerJS(ob_get_clean()); ?>