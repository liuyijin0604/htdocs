<h3>Tickets-<?=Yii::app()->user->name?></h3>
<div style="right: 200px;position: absolute;">
<a class="tab_link" href="<?=$this->createUrl('exCrm/create');?>" title="New Ticket"><div class="icon" style="background-position:-16px 0"></div>创建新的票</a>
<a class="tab_link" href="<?=$this->createUrl('exCrm/myTickets');?>" title="My Tickets"><div class="icon" style="background-position:-16px 0"></div>指定给我的票</a>
<a class="tab_link" href="<?=$this->createUrl('exCrm/financeTickets');?>" title="Finance Tickets"><div class="icon" style="background-position:-16px 0"></div>财务的票件</a>
</div>
<br/>
<h4>统计： <?=$total?>票</h4>
<div style="width:50%" id="custom-client-view">
   <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'excrm-client-list-grid',
        'htmlOptions'=>array('style'=>'width: 70%'),
        'cssFile' => false,
        'dataProvider'=>$dataProvider[0],
        'filter'=>$dataProvider[1],
        'columns'=>array(
            array('name'=>'rawType','headerHtmlOptions' => array('style' => 'display:none'),'filterHtmlOptions' => array('style' => 'display:none'),
                'htmlOptions' => array('style' => 'display:none'),'type'=>'raw'),
            array('name'=>'type','header'=>'类型'),
            array('name'=>'new', 'header'=>'新的'),
            array( 'name'=>'op',  'header'=>'客服处理中'),
            array('name'=>'manager', 'header'=>'经理处理中'),
            array('name'=>'finance',  'header'=>'财务处理中'),
        ),
    )); ?>
</div>
<div class="form">
    <div class="row">
   <?php  echo CHtml::button('Show All', array('class' => 'show_all'));?>
    </div>
</div>
<div id="excrm_list_grid_view">
    <?php $this->renderPartial('_sub_crm_list',array('model'=>$model));?>
</div>


<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
       $('#excrm-client-list-grid',panel).on("click", "table tbody td", function(event){
        // get console id
        var type = parseInt($(this).parent().children(':nth-child(1)').html());
        var data = {};
        data['type'] = type;
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("exCrm/ticketList",array('tabid'=>$_GET['tabid'])) ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
               $('#excrm_list_grid_view',panel).html($(resp).find('#excrm_list_grid_view'));
            },
        });
    });
      $('.show_all',panel).on('click',function(event){
        var data = {};
        data['type'] = 0;
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("exCrm/ticketList",array('tabid'=>$_GET['tabid'])) ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                 $('#excrm_list_grid_view',panel).html($(resp).find('#excrm_list_grid_view'));
            },
        });
    });

	tab.bind('onOpen', function(){
		$('#<?=$_GET["tabid"]?>_crm-grid', panel).yiiGridView('update');
	});
	
	$('form#shipment-crmnotes-form', panel).on({'success': function(e,r){
			$('#<?=$_GET["tabid"]?>_crm-grid', panel).yiiGridView('update');
		},
		'reset': true
	}
	);
});
</script>