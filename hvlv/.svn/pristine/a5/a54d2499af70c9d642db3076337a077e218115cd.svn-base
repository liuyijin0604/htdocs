<div class="pane">
<h2>Create New Ticket</h2>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'shipment-crmnotes-form',
	'enableClientValidation'=>true,
	
));
$model->type=50;
$model->source=13;
?>
    <div class="row" style="font-size: 1.4em;">
       <?php  echo $form->labelEx($model,'type');?>
       <?php  echo $form->radioButtonList($model,'type', ExCrm::$types_cn,array('labelOptions'=>array('class'=>'radio_label'),'separator'=>'&nbsp;&nbsp;'));?>
    </div>
     <div class="row" style="font-size: 1.4em;">
        <?php  echo $form->labelEx($model,'source');?>
        <?php  echo $form->radioButtonList($model,'source', ExCrm::$sources,array('labelOptions'=>array('class'=>'radio_label'),'separator'=>'&nbsp;&nbsp;'));?>
    </div>
    <div class="row" id="connote_part" style="font-size: 1.4em;">
        <?php  echo CHtml::label('运单号<span class="required">*</span>(多个单号用;或者,或者断行分隔)','Connote');?>
        <?php  echo CHtml::textArea('connote_no','');?>
    </div>
    <div class="row" style="font-size: 1.4em;">
        <?php  echo $form->labelEx($model,'email',array('label'=>'电子邮件<span class="required">*</span>'));?>
        <?php  echo $form->TextField($model,'email');?>
    </div>
      <div class="row" style="font-size: 1.4em;">
        <?php  echo $form->labelEx($model,'telephone',array('label'=>'手机号码<span class="required">*</span>'));?>
        <?php  echo $form->TextField($model,'telephone');?>
    </div>
    <div class="row" style="font-size: 1.4em;">
        <?php  echo CHtml::label('Note','notes');?>
	<?php echo CHtml::textArea('notes', '', array('rows'=>4, 'cols' => 60)); ?>
    </div>
    <div class="row" style="width: 50%">
	<?php
	echo '<br />', CHtml::label($this->t('Attachments'),'uploader');
	$pphash = FileRepo::uploadHash($model, 100);
	$this->widget('application.extensions.plupload.PluploadWidget', array(
	 'config' => array(
		 'url' => $this->createUrl('filerepo/upload/'.$pphash),
		 'max_file_size' => Yii::app()->params['maxFileSize'],
		 'unique_names' => true,
		 'file_list_height' => 60,
		 'visible_header' => false,
		 'filters' => array(
			  array('title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg'),
		  ),
		 //'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
		 'language' => Yii::app()->language,
		 'max_file_number' => 5,
		 'autostart' => true,
		 'jquery_ui' => false,
		 'reset_after_upload' => true,
	 ),
	 'id' => $_GET['tabid'].'_attachments_uploader',
	));
	echo CHtml::HiddenField('ppupload', $pphash);
	?>
	</div>
    <div class="row buttons">
	<?php echo CHtml::submitButton('Create'); ?>
</div>
    <div id="ex-ticket-result" style="margin: 10px 0; border: 1px solid;padding:20px; font-weight: bold; font-size: 20px; height: 80px;">
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->
</div>
<script>
 $(function(){
     var tab=$('#<?=$_GET['tabid']?>');
     var panel=tab.data('panel');
     
     $('input[name="ExCrm[type]"]').on('change',function(){
         var  type=$('input[name="ExCrm[type]"]:checked',panel).val();
          switch(parseInt(type)){
             case 10:  //id chasing
             case 20:
             case 30:
             case 40:
             case 50:
                 $("#connote_part",panel).show();
                  break;
                      case 80:
             case 90:
             case 100:
                 $("#connote_part",panel).hide();
                 break;
         }
     });

     $('#shipment-crmnotes-form',panel).on('submit',function(){
         $("#ex-ticket-result",panel).html('');
         var  type=$('input[name="ExCrm[type]"]:checked',panel).val();
         var connote_no=$('textarea[name="connote_no"]',panel).val();
         var telephone=$('input[name="ExCrm[telephone]"]',panel).val();
         var email=$('input[name="ExCrm[email]"]',panel).val();
         if(!type){
              myApp.alert('请选择tiket的类型！');
              return false;
         }
         if(!telephone&&!email){
               myApp.alert('电子邮件跟电话至少提供一个');
              return false;
        } 
         switch(parseInt(type)){
             case 10:  //id chasing
             case 20:
             case 30:
             case 40:
             case 50:
                if(!connote_no){
                      myApp.alert('请输入运单号');
                      return false;
                }
                 break;
        }
     }).on('success',function(r,resp){
         $("#ex-ticket-result",panel).html(resp.msg);
         $('textarea[name=connote_no]',panel).val('');
         $('input[name="ExCrm[email]"]',panel).val('');
         $('input[name="ExCrm[telephone]"]',panel).val('');
     });
     
 })
 
 
</script>