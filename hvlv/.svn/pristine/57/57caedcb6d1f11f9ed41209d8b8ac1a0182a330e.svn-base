<h1><?=$this->t((empty($isWms)?'Imports':'WMS').' Mail Management');?></h1>
<div style="right: 200px;position: absolute;">
<a class="jqm_link"  href="<?=$this->createUrl("customerService/addTicket")?>" title="Add Ticket" ><span class="icon"></span>Add Ticket</a>
<?php if (Acl::hasAccess("B:importsMail/filterManage")):?>
<a class="tab_link" href="<?=$this->createUrl('importsMail/filterManage');?><?=!empty($isWms)?'?isWms=1':''?>" title="Filter Management"><div class="icon" style="background-position:-128px -32px"></div>Filter Management</a>
<?php endif;?>
<a class="tab_link" href="<?=$this->createUrl('importsMail/myEmails');?><?=!empty($isWms)?'?isWms=1':''?>" title="My Emails"><div class="icon" style="background-position:-240px -288px"></div>My Emails</a>
<a class="tab_link" href="<?=$this->createUrl('importsMail/totalEmails');?><?=!empty($isWms)?'?isWms=1':''?>" title="Total Emails"><div class="icon" style="background-position:-160px -288px"></div>Total Emails</a>
<a class="tab_link" href="<?=$this->createUrl('importsMail/mySignature');?>" title="My Signature"><div class="icon" style="background-position:-16px -304px"></div>My Signature</a>
<?php if (Acl::hasAccess("B:importsMail/viewReport")):?>
<a class="tab_link" href="<?=$this->createUrl('importsMail/mailListDailyReport');?><?=!empty($isWms)?'?isWms=1':''?>" title="Mail Report"><div class="icon" style="background-position:-240px -288px"></div>Mail Report</a>
<?php endif;?>
</div>
<br/>
<h4>统计： <?=$total?>票</h4>
<div style="width:50%" id="import-email-overview">
	 <?php  $this->widget('zii.widgets.grid.CGridView', [
		'id'=>$_GET['tabid'].'importsmail-review-list-grid',
		'htmlOptions'=>['style'=>'width: 70%'],
		'afterAjaxUpdate'=>'function(r,s){$("#list_total_summary").html($(s).find("#list_total_summary").html());}',
		'cssFile' => false,
		'dataProvider'=>$dataProvider[0],
		'filter'=>$dataProvider[1],
		'columns'=>[
			['name'=>'rawType','headerHtmlOptions' => ['style' => 'display:none'],'filterHtmlOptions' => ['style' => 'display:none'],
				'htmlOptions' => ['style' => 'display:none'],'type'=>'raw'],
			['name'=>'type','header'=>'Type'],
			['name'=>'new', 'header'=>'New'],
			[ 'name'=>'allocated',  'header'=>'Allocated']
		],
	 ]);

	 ?>
</div>
<div class="row">
		<div style="clear:both;"></div>
		<div  class="col">
				<div id="list_total_summary">
				<p></p>
				<table class="chart">
					 <tr><th>Name</th><th>Yesterday Left</th><th>New</th><th>Park</th><th>Close</th><th>Replied</th><th>Unfinished</th><th>Finished %</th><th>MTD %</th></tr>
						<?php foreach ($result as $user_id=> $r) {
		if($user_id == 666) continue;
		$name=ucfirst(User::getUserName($user_id));
		$today_left=intval(@$r['today_left']);
		$today_open=intval(@$r['today_open']);
		$today_park=intval(@$r['today_park']);
		$today_reply=intval(@$r['today_reply_only']+@$r['today_reply_park']);
		$today_close_only=intval(@$r['today_close_only']);
		$yesterdayData = @$r['yesterdayData'];
		$finishPercent=@$r['finishPercent'];
		$monthlyFinishPercent = @$r['monthlyFinishPercent'];
		$finishPercentStr = number_format($finishPercent*100, 2, '.', '').'%';
		$monthlyFinishPercentStr = number_format($monthlyFinishPercent*100, 2, '.', '').'%';

		if($finishPercent<0.8)
		{
			$finishPercentStr = "<font color='red'>".$finishPercentStr."</font>";
		}

		if($monthlyFinishPercent<0.8)
		{
			$monthlyFinishPercentStr = "<font color='red'>".$monthlyFinishPercentStr."</font>";
		}

		echo '<tr><td class="user_name" data-id="'.$user_id.'">'.$name.'</td><td>'.$yesterdayData.'</td><td>'.$today_open.'</td><td>'.$today_park.'</td><td>'.$today_close_only.'</td><td>'.$today_reply.'</td><td>'.$today_left.'</td><td>'.$finishPercentStr.'</td><td>'.$monthlyFinishPercentStr.'</td></tr>';
	}?>
				</table>
		</div>
				</div>
		<div class="col">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</div>
		<div class="col">
			<div id="list_feedback_summary">
				<p></p>
				<table class="chart">
					<tr>
						<th>Name</th>
						<th>Overall</th>
						<th>Overall Count</th>
						<th>MTD</th>
						<th>MTD Count</th>
					</tr>
					<?php 
						if (!empty($feedback)) {
							foreach($feedback as $uid => $r) {
								$name = $r['name'];
								$overall = $r['overall_rating'];
								$overallCount = $r['overall_count'];
								$mtd = $r['mtd_rating'];
								$mtdCount = $r['mtd_count'];
								echo '<tr><td class="user_name" data-id="'.$uid.'">'.$name.'</td><td><a href="'.Yii::app()->createUrl("importsMail/feedbackDetails", ["id" => $uid]).'" class="tab_link" title="Mail Rating">'.number_format($overall, 1, '.', ',').'</a></td><td>'.$overallCount.'</td><td>'.number_format($mtd, 1, '.', ',').'</td><td>'.$mtdCount.'</td></tr>';
							}
						}
					?>
				</table>
			</div>
		</div>
</div>
	<div style="clear:both;"></div>
<?php echo CHtml::link('Advanced Search', 'serach', ['class'=>'search-button']); ?>
<div class="search-form">
<?php $this->renderPartial('_search', [
	'model'=>$model,
]); ?>
</div><!-- search-form -->
<?php $this->widget('zii.widgets.grid.CGridView', [
	'id'=>$_GET['tabid'].'imports-email-total-grid-list1',
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 30, false, false),
	'filter'=>$model,
	'columns'=>[
		'no',
		'ticket',
		['name'=>'type', 'value'=>'$data->getType()','filter'=>CHtml::dropDownList('ImportsMail[type]', $model->type, empty($isWms)?ImportsMail::$mailTypes:ImportsMail::$wmsMailTypes, ['prompt'=>'All'])],
		['name'=>'status','value' => '$data->getStatus()','filter'=>CHtml::dropDownList('ImportsMail[status]', $model->status, ImportsMail::$states, ['prompt'=>'All'])],
		['name'=>'op_id','header'=>'Assign To', 'type' => 'raw', 'value'=>'$data->getOpName()','filter'=>CHtml::dropDownList('ImportsMail[op_id]', $model->op_id, empty($isWms)?ImportsMail::$importscs_list_id:ImportsMail::$wmscs_list_id, ['prompt'=>'All']) ],
		'from_email',
		['name'=>'to_email','type'=>'raw','value'=>function ($data) {
			return CHtml::tag('div', ['title'=>$data->to_email], substr($data->to_email, 0, 25));
		}],
		['name'=>'subject','type'=>'raw','value'=>function ($data) {
			return CHtml::tag('div', ['title'=>$data->subject], mb_substr($data->subject, 0, 60));
		}],
		['name'=>'plain_body','type'=>'raw','value'=>function ($data) {
			return CHtml::tag('div', ['title'=>strip_tags($data->plain_body)], mb_substr(strip_tags($data->plain_body), 0, 25));
		}],
		'create_time',
		['header' => 'deadline','type'=>'raw','value' => '($data->flag&ImportsMail::FLAG_PARK)>0?("<div style=\"background-color:RGB(80,171,220)\">".$data->getDeadLine()."</div>"):$data->getDeadLine()'],
		['name'=>'date', 'header'=>'Reply Time'],
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
					'options' => ['class' => 'park_ticket grid_view_btn', 'label' => 'Read', 'data-win-class' => 'L'],
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
<script type="text/javascript">
$(function(){
	var tab=$("#<?=$_GET['tabid']?>");
	var panel=tab.data('panel');
	$('.search-form').toggle();
	$('.search-button',panel).click(function(){
		$('.search-form').toggle();
		return false;
	});
				 
	$(panel).on('click',' .park_ticket',function(event){
		event.preventDefault();
		if(confirm("Are you Confirm to Read The Ticket?")){
			$.get($(this).attr('href'),function(r){
				if(r=='done'){
					myApp.notice("Park!");
					$('#imports-email-total-grid-list1',panel).yiiGridView('update');
					$('#importsmail-review-list-grid',panel).yiiGridView('update');
				}
			});
		}
	});

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

	$('.search-form form',panel).submit(function(){
		$('#imports-email-total-grid-list1',panel).yiiGridView('update', {
			data: $(this).serialize()
		});
		return false;
	});
				
	$('#list_total_summary',panel).on("click", "table tbody td.user_name", function(event){
		var user_id=Number($(this).data('id'));
		if(user_id> 0){
			myApp.tabs.CreateTab({
				title: user_id + ' Email List',
				url: "ImportsMail/MyEmails?user_id="+user_id+"<?=!empty($isWms)?'&&isWms=1':''?>",
				bg: false
			});
		}
		return false;
	});
});
</script>



		
