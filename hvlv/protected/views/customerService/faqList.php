<h3>Manage Faq List</h3>
<style>
    .cloumn_red{
        background-color:pink;
    }  
    .column_direct{
        color: green;
        font-weight: bold;
    }
</style>

<?php 
	
	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
	'id'=>$_GET["tabid"].'_cs_faq_grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 30,false,true),
	'filter'=>$model,
	'afterAjaxUpdate'=>'function(){initFunctions();}',
	'columns'=>[		
		array('name' => 'content', 'type' => 'raw',),
		array('name' => 'frontend_content', 'type' => 'raw',),
		['name' => 'status','value' => 'CsFaq::$state[$data->status]'],
		['class'=>'oButtonColumn',
			'template'=>'{active}&nbsp{inactive}&nbsp;{log}',
			'buttons'=>[
				'active' => [
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'inactive', 'label'=>$this->t('Inactive'), 'title' => '$data->id'],
				],
				'inactive' => [
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'active', 'label'=>$this->t('Active'), 'title' => '$data->id'],
				],
				'log' => [
					'imageUrl'=>false,
					'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("cargoProcess/log", ["id" => $data->id])',
					'label' => 'Log'
				],
			],
		]
	],
]);


; ?>

<div class="form">
<?php 
$url = $this->createUrl('customerService/addFaq');
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'cs_add_faq_form',
	'enableAjaxValidation'=>false,
	'action'=> $url)
	);
?>

        <div class="row">
                <?php echo CHtml::label('content','content'); ?>
                <?php echo CHtml::textField('faq[content]','',["class"=>"form-control"]); ?>
        </div>
        <br>
        <div class="row">
                <?php echo CHtml::label('frontend_content','frontend_content'); ?>
                <?php echo CHtml::textField('faq[frontend_content]','',["class"=>"form-control"]); ?>
        </div>
        <br>
        <div class="row">
                 <?php echo CHtml::submitButton('add',array("class"=>"form-control update","style"=>"width:250px;")); ?>
        </div>


<script type="text/javascript">
		var fawin = $('#jqmw_<?=$_GET["tabid"];?>');
		var fatab = $('#<?=$_GET["tabid"];?>');
		var fapanel = $('#<?=$_GET["tabid"];?>').data('panel');
		$(function(){
			fatab.unbind('reload<?=$_GET["tabid"]?>_cs_faq_grid').bind('reload<?=$_GET["tabid"]?>_cs_faq_grid', function(){
				$('#<?=$_GET["tabid"]?>_cs_faq_grid',fawin).yiiGridView('update');
				return false;
			});

				 $('.update',fawin).on('click',function(){
					var form = new FormData(document.getElementById("cs_add_faq_form"));
					 $.ajax({
					            url: '<?=Yii::app()->createURL("customerService/addFaq")?>',
					            type: "post",
					            data: form,
					            processData: false,
					            contentType: false,
					            success: function(r) {
					                if(r=='done')
					                 {
										myApp.notice('Done', 5000);
									 }else
									 {
										myApp.alert(r, false);   
						             }
						             fatab.trigger('reload<?=$_GET["tabid"]?>_cs_faq_grid');
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });			
				return false;
			});
			initFunctions();
		});

			function initFunctions()
			{
				  $('.active',fawin).on('click',function(){
				 	let id = $(this).attr("title");
					 $.ajax({
					            url: '<?=Yii::app()->createURL("customerService/inactiveFaq")?>?type=inactive&&id='+id,
					            type: "get",
					            processData: false,
					            contentType: false,
					            success: function(r) {
					                if(r=='done')
					                 {
										myApp.notice('Done', 5000);
									 }else
									 {
										myApp.alert(r, false);   
						             }
						             fatab.trigger('reload<?=$_GET["tabid"]?>_cs_faq_grid');
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });			
						return false;
					});

						 $('.inactive',fawin).on('click',function(){
						 	let id = $(this).attr("title");
							 $.ajax({
							            url: '<?=Yii::app()->createURL("customerService/inactiveFaq")?>?type=active&&id='+id,
							            type: "get",
							            processData: false,
							            contentType: false,
							            success: function(r) {
							                if(r=='done')
							                 {
												myApp.notice('Done', 5000);
											 }else
											 {
												myApp.alert(r, false);   
								             }
								             fatab.trigger('reload<?=$_GET["tabid"]?>_cs_faq_grid');
								         },
							            error: function(e) {
							                console.log(e);
							            }
							        });			
						return false;
					});
			}

</script>

 <?php $this->endWidget();?>


		