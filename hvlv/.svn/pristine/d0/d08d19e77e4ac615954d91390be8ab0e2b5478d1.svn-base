<div style="position: absolute; right: 120px;">
    <a href="<?=$this->createUrl('deliveryRecord/create');?>" class="jqm_link" title="New GP"><div style="background-position:-16px 0" class="icon"></div> New delivery booking</a>

    <a href="<?=$this->createUrl('deliveryRecord/list',["status"=>10]);?>" class="tab_link" title="DR CancelList"><div style="background-position:-16px 0" class="icon"></div>Cancel list</a>
    <a href="<?=$this->createUrl('deliveryRecord/list',["status"=>9]);?>" class="tab_link" title="DR DoneList"><div style="background-position:-16px 0" class="icon"></div>Done list</a>
</div>

<div class="row">
		<div style="width:50%" id="new-state-view<?=$_GET['tabid']?>">
		   <?php $this->widget('zii.widgets.grid.CGridView', [
		    'id'=>'new-state-list-grid'.$_GET['tabid'],
		    'htmlOptions'=>['style'=>'width: 70%'],
		    'cssFile' => false,
		    'dataProvider'=>$dataProvider[0],
		    'filter'=>$dataProvider[1],
		    'columns'=>[
		        ['name'=>'status','headerHtmlOptions' => ['style' => 'display:none'],'filterHtmlOptions' => ['style' => 'display:none'],
		            'htmlOptions' => ['style' => 'display:none'],'type'=>'raw'],
		        ['name'=>'status','value'=>'DeliveryRecord::$states[$data["status"]]'],
		        'number',
		        ['name'=>'Today','header'=>'Today'],
		        ['name'=>'day2','header'=>'2 days'],
		        ['name'=>'day3','header'=>'>=3 days','cssClassExpression' => '$data>0? "cloumn_red_1" : ""'],
		    ],
		   ]); ?>
		</div>
</div>


 <div id="delivery-record-list-view">
      <?php
         $this->renderPartial('record_list', [
            'model' => $model
         ]);
    ?>
  </div>


<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	tab.bind('onOpen', function(){
		$('#<?=$_GET["tabid"];?>delivery-record-grid', panel).yiiGridView('update');
	});
});
</script>
