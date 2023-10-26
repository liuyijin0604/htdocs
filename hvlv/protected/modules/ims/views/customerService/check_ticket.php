<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
		'Shipments' => array('customerService/index'),
           'Check Ticket',
	),
));
?>
<h2>Check Ticket&nbsp;<?=$ticket?></h2>
<br>
 <?php $this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>'task_grid_view',
    'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model->search(),
    'template' => "{summary}\n{items}\n{pager}",
    'columns'=>array(
    	["name"=>"Shipment.ref"],
    	["name"=>"faq_answer","filter"=>false],
    	["name"=>"s_note",'type'=>'raw',"filter"=>false,'value'=>'"<a href=\"".Yii::app()->createURL("ims/customerService/checkDetail", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->id."\" target=\"_blank\">".CHtml::tag("div", ["title"=>strip_tags($data->s_note)], mb_substr(strip_tags($data->s_note), 0, 25))."</a>"'],
    	["name"=>"answer_date","filter"=>false],
    	["name"=>"process_type","filter"=>false,'value'=>'ShipmentQuestion::$processTypes[$data->process_type]'],
    	["name"=>"c_read","type"=>"raw","filter"=>false,"value"=> '"<a href=\"".Yii::app()->createURL("ims/customerService/csRead", array("id" => $data->id))."\" label=\"Log\" data-win-class=\"L\" class=\"jqm_link grid_view_btn\" title=\"".ShipmentQuestion::$readTypes[$data->c_read]."\" >".ShipmentQuestion::$readTypes[$data->c_read]."</a>"'],

    ),
    
));
?>
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
</div>

 <?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('body').off('click', 'a.tracking-modal-link').on('click', 'a.tracking-modal-link', function(e){
		$('#modal-tracking').modal();
		$('#modal-tracking .modal-body').load($(this).attr('href'));
		e.preventDefault();
	});

});
</script>
<?php $this->registerJS(ob_get_clean()); ?>