
<div style="right: 200px;position: absolute;">
<a class="tab_link" href="<?=$this->createUrl('importsMail/myEmails');?>" title="My Emails"><div class="icon" style="background-position:-240px -288px"></div>My Emails</a>

<a class="tab_link" href="<?=$this->createUrl('importsMail/mySignature');?>" title="My Signature"><div class="icon" style="background-position:-16px -304px"></div>My Signature</a>
<?php if (Acl::hasAccess("B:importsMail/viewReport")):?>
<a class="tab_link" href="<?=$this->createUrl('importsMail/mailListDailyReport');?>" title="Mail Report"><div class="icon" style="background-position:-240px -288px"></div>Mail Report</a>
<?php endif;?>
</div>
<br/>
	<div style="clear:both;"></div>
<?php echo CHtml::link('Advanced Search', 'serach', ['class'=>'search-button']); ?>
<div class="search-form">
<?php $this->renderPartial('_search', [
	'model'=>$model,
]); ?>
</div><!-- search-form -->
<?php $this->widget('zii.widgets.grid.CGridView', [
	'id'=>'imports-email-total-grid-list1',
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 30, false, false),
	'filter'=>$model,
	'columns'=>[
		'no',
		['name'=>'type', 'value'=>'$data->getType()','filter'=>CHtml::dropDownList('ImportsMail[type]', $model->type, ImportsMail::$mailTypes, ['prompt'=>'All'])],
		['name'=>'status', 'value'=>'$data->getStatus()','filter'=>CHtml::dropDownList('ImportsMail[status]', $model->status, ImportsMail::$states, ['prompt'=>'All'])],
		['name'=>'op_id','header'=>'Assign To', 'type' => 'raw', 'value'=>'$data->getOpName()','filter'=>CHtml::dropDownList('ImportsMail[op_id]', $model->op_id, ImportsMail::$importscs_list_id, ['prompt'=>'All']) ],
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
		['name'=>'date', 'header'=>'Reply Time'],
		[
			'class'=>'oButtonColumn',
			'template'=>'{update}{read}{close}',
			'buttons'=>[
				'update' => [
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->no'],
				],
				'read'=>[
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'read_ticket grid_view_btn', 'label' => 'Read', 'data-win-class' => 'L'],
					'url' => 'Yii::app()->createUrl("importsMail/readEmail", ["id" => $data->id])',
					'label' => 'Read'
				],
				'close'=>[
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'close_ticket grid_view_btn', 'label' => 'Close', 'data-win-class' => 'L'],
					'url' => 'Yii::app()->createUrl("importsMail/closeEmail", ["id" => $data->id])',
					'label' => 'Close'
				]
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
				 
				$(panel).on('click',' .read_ticket',function(event){
						event.preventDefault();
						if(confirm("Are you Confirm to Read The Ticket?")){
								$.get($(this).attr('href'),function(r){
									 if(r=='done'){
											 myApp.notice("Read!");
											$('#imports-email-total-grid-list1',panel).yiiGridView('update');
											 $('#importsmail-review-list-grid',panel).yiiGridView('update');
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
				})
				$('.search-form form',panel).submit(function(){
								$('#imports-email-total-grid-list1',panel).yiiGridView('update', {
												data: $(this).serialize()
								});
								return false;
				});
				
		 $('#list_total_summary',panel).on("click", "table tbody td.user_name", function(event){
//        var columnIndex=$(this).index();
				var user_id=parseInt($(this).parent().children(':nth-child(1)').html());
				if(user_id<=0) return false;
					myApp.tabs.CreateTab({
					 title: user_id + ' Email List',
			url: "ImportsMail/MyEmails?user_id="+user_id,
			bg:  false
		});
								return false;
		 });
		})
</script>



		
