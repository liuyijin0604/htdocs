<style type="text/css">
    .container
    {
        width:100%;
    }

</style>
<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
        'links' => array(
        '海拼登记入仓 New Record'
    ),
));
// $form=$this->beginWidget('CActiveForm', array(
//     'id'=>'ad_search_form',
//     'enableAjaxValidation'=>false,
//     ));
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'zws-form',
	'enableAjaxValidation'=>false,
)); ?>
		<div style="margin-left: 2em">
			
			<div class="row">
				<div class="col-12-left" style="padding-right: 5px;">
					HBNS(split by ,;)
				</div>
				<div class="col-12" style="padding-right: 5px;">
					<?php echo CHtml::textarea('ImportZwStorage[hbns]',@$model->mdata['hbns'],['class'=>'form-control',"style"=>"margin-top:1.1em;",'rows'=>'5']); ?>
				</div>
			</div>
			
		</div>
		<div class="row">
			<div class="col-6" style="padding-left: 5px;">
				<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save'), array('class' => 'save_btn btn btn-primary btn-block')); ?>
			</div>
		</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
	$(function(){
          $('#zws-form').on('success',function(e,r){
          	javascript:history.go(-1);
          });
        
    });

</script>