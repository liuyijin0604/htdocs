<h1><?=$this->t('My '.(empty($isWms)?"Imports":"Wms").' Email List') . "-" . User::getUserName($user_id);?></h1>
<br/>
<div style="right: 200px;position: absolute;">
	<a class="tab_link" href="<?=$this->createUrl('importsMail/list', ['isWms' => $isWms]);?>" title="Email Dashboard <?=$isWms?> "><div class="icon" style="background-position:-160px -288px"></div>Email Dashboard</a>
<a class="tab_link" href="<?=$this->createUrl('importsMail/myTotalEmails', ['user_id' => $user_id]);?>" title="<?=User::getUserName($user_id);?> Total Emails"><div class="icon" style="background-position:-160px -288px"></div><?=User::getUserName($user_id);?> Total Emails</a>

<a class="tab_link" href="<?=$this->createUrl('importsMail/totalEmails');?><?=!empty($isWms)?'?isWms=1':''?>" title="Total Emails"><div class="icon" style="background-position:-160px -288px"></div>Total Emails</a>

<a class="jqm2_link"  href="<?=$this->createUrl('importsMail/createEmail', ['user_id' => $user_id]);?>" title="<?=User::getUserName($user_id);?> Create Email"><div class="icon" style="background-position:-160px -288px"></div>Create Email</a>

</div>
<h4 style="float:left">Total Left：<?=$total?></h4>
<div style="width:50%;" id="import-email-overview<?=$_GET['tabid']?>">
   <?php $this->widget('zii.widgets.grid.CGridView', [
	'id' => 'importsmail-review-list-grid-list',
	'htmlOptions' => ['style' => 'width: 70%'],
	'cssFile' => false,
	'dataProvider' => $dataProvider[0],
	'filter' => $dataProvider[1],
	'columns' => [
		['name' => 'rawType', 'headerHtmlOptions' => ['style' => 'display:none'], 'filterHtmlOptions' => ['style' => 'display:none'],
			'htmlOptions' => ['style' => 'display:none'], 'type' => 'raw'],
		['name' => 'type', 'header' => 'Type'],
		['name' => 'left', 'header' => 'Left'],
		['name' => 'today_open', 'header' => 'Today Open'],
		['name' => 'today_total', 'header' => 'Today Close'],
		['name' => 'today_reply', 'header' => 'Today Reply'],
		['name' => 'today_park', 'header' => 'Today Park'],
	],
]);?>
</div>
<?php echo CHtml::link('Advanced Search', 'serach', ['class' => 'search-button']); ?>
<div class="search-form">
<?php $this->renderPartial('_search', [
	'model' => $model,
]);?>
</div><!-- search-form -->
<?php
		$log = Log::model()->find('model = "User" AND lid = :lid AND type = 3', [':lid' => $user_id]);
		if(!empty($log))
		{
			$user_create = $log->time;
		}else
		{
			$user_create = '2000-01-01 00:00:00';
		}
$ec = new CDbCriteria;
$ec->addCondition('t.create_time >= "' . $user_create . '"');
?>
<?php $this->widget('zii.widgets.grid.CGridView', [
	'id' => 'imports-email-total-grid-list-my1'.$isWms.$user_id,
	'cssFile' => false,
	'dataProvider' => $model->search(true, 30, $ec, false),
	'filter' => $model,
	'columns' => [
		'no',
		'ticket',
		['name' => 'type', 'value' => '$data->getType()', 'filter' => CHtml::dropDownList('ImportsMail[type]', $model->type, ImportsMail::$mailTypes+ImportsMail::$wmsMailTypes, ['prompt' => 'All'])],
		['name' => 'status', 'value' => '$data->getStatus()', 'filter' => CHtml::dropDownList('ImportsMail[status]', $model->status, ImportsMail::$states, ['prompt' => 'All'])],
		['name' => 'op_id', 'header' => 'Assign To', 'type' => 'raw', 'value' => '$data->getOpName()', 'filter' => CHtml::dropDownList('ImportsMail[op_id]', $model->op_id, ImportsMail::$importscs_list_id+ImportsMail::$wmscs_list_id, ['prompt' => 'All'])],
		'from_email',
		['name' => 'to_email', 'type' => 'raw', 'value' => function ($data) {
			return CHtml::tag('div', ['title' => $data->to_email], mb_substr($data->to_email, 0, 25));
		}],
		['name' => 'subject', 'type' => 'raw', 'value' => function ($data) {
			return CHtml::tag('div', ['title' => $data->subject], mb_substr($data->subject, 0, 60));
		}],
		['name' => 'plain_body', 'type' => 'raw', 'value' => function ($data) {
			return CHtml::tag('div', ['title' => strip_tags($data->plain_body)], mb_substr(strip_tags($data->plain_body), 0, 25));
		}],
		'create_time',
		['header' => 'deadline','type'=>'raw','value' => '($data->flag&ImportsMail::FLAG_PARK)>0?("<div style=\"background-color:RGB(80,171,220)\">".$data->getDeadLine()."</div>"):$data->getDeadLine()'],
		['name' => 'date', 'header' => 'Reply Time'],
    	[
    	  'class'=>'oButtonColumn',
    	  'template'=>'{update}{park}{close}{emails}',
    	  'buttons'=>[
    	    'update' => [
    	      'imageUrl'=>false,
    	      'visible'=>'true',
    	      'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->no'],
    	    ],
    	    'park'=>[
    	      'imageUrl'=>false,
    	      'visible'=>'true',
    	      'options' => ['class' => 'read_ticket grid_view_btn', 'label' => 'Park', 'data-win-class' => 'L'],
    	      'url' => 'Yii::app()->createUrl("importsMail/parkEmail", ["id" => $data->id])',
    	      'label' => 'Park'
    	    ],
    	    'close'=>[
    	      'imageUrl'=>false,
    	      'visible'=>'true',
    	      'options' => ['class' => 'close_ticket grid_view_btn', 'label' => 'Close', 'data-win-class' => 'L'],
    	      'url' => 'Yii::app()->createUrl("importsMail/closeEmail", ["id" => $data->id])',
    	      'label' => 'Close'
    	    ],
    	    'emails' => [
    	                'imageUrl'=>false,
    	                'options' => ['class' => 'tab_link grid_view_btn','title'=>'$data->ticket', 'label' => 'Log', 'data-win-class' => 'L'],
    	                'visible' => 'empty($data->ticket)?false:true',
    	                'url' => 'Yii::app()->createUrl("customerService/getEmailRelatedEmails")."?ticket=".$data->ticket',
    	                'label' => 'Related Emails',
    	            ],
    	    ],
    	],

	]]);
?>
<script>
	$(function(){
		var tab=$("#<?=$_GET['tabid']?>");
		var panel=tab.data('panel');
		$('.search-form').toggle();
		$('.search-button',panel).click(function(){
	 $('.search-form').toggle();
  return false;
		 });
		$('.search-form form',panel).submit(function(){
				$('#imports-email-total-grid-list-my1<?=$isWms.$user_id?>',panel).yiiGridView('update', {
						data: $(this).serialize()
				});
				return false;
		});
		
	   $(panel).on('click',' .read_ticket',function(event){
			event.preventDefault();
			if(confirm("Are you Confirm to Park The Ticket?")){
				$.get($(this).attr('href'),function(r){
				   if(r=='done'){
					   myApp.notice("Park!");
					   $('#imports-email-total-grid-list-my1<?=$isWms.$user_id?>',panel).yiiGridView('update');
					   $('#importsmail-review-list-grid-list',panel).yiiGridView('update');
				   }
				});
		   }
		})


		$(panel).on('click',' .close_ticket',function(event){
			event.preventDefault();
			if(confirm("Are you Confirm to Close The Ticket?")){
				$.get($(this).attr('href'),function(r){
					if(r=='done'){
						myApp.notice("Close!");
						$('#imports-email-total-grid-list1',panel).yiiGridView('update');
						$('#importsmail-review-list-grid',panel).yiiGridView('update');
					}
				});
			 }
	});

	})
</script>



