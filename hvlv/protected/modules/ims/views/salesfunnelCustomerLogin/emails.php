<?php $this->widget('zii.widgets.grid.CGridView', [
	'id'=>$_GET['tabid'].'imports-email-total-grid-list1',
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 5, false, false),
	'filter'=>$model,
	'columns'=>[
		'no',
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
		[
			'class'=>'oButtonColumn',
			'template'=>'{detail}',
			'buttons'=>[
				'detail' => [
					'imageUrl'=>false,
					'visible'=>'true',
					'url' => 'Yii::app()->createUrl("ims/salesfunnelCustomerLogin/update", ["id" => $data->id])',
					'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Detail'), 'title' => '$data->no'],
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



		
