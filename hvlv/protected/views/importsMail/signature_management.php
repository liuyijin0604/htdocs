<div class="pane">
    <div style="right: 200px;position: absolute;">
     <a class="jqm_link" href="<?=$this->createUrl('importsMail/newSignature');?>" title="New Signature"><div class="icon" style="background-position:-16px -304px"></div>New Signature</a>   
    </div>
<h3>Signature Management-<?=Yii::app()->user->name?></h3>
</br>
<h4>Avaliable Signatures:</h4>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'new-signaure-form-mail',
	'enableAjaxValidation'=>false,
)); ?>
<?php  
   $signatures= ImportsMailSignature::model()->findAll('status!=10 AND user_id=:user_id',array(':user_id'=>Yii::app()->user->id));
   $allSigs=[];
   $default_body='';
   $default_id='';
   foreach ($signatures as $sig){
       $allSigs[$sig->id]=$sig->sig_name;
       if($sig->status==1){
           $default_body=$sig->sig_body;
           $default_id=$sig->id;
       }
   }
   $model=new ImportsMailSignature;
 
   $model->sig_body=$default_body;
   echo CHtml::radioButtonList('select_signature', $default_id,$allSigs,array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));
   ?>
<div id="signature_management_panel">
<?php  
if(empty($default_id)){
    echo '<h1>Please Create A signature At first</h1>';
}else{
 $this->renderPartial('_sig_form',array('model'=>$model));   
}
?>
</div>
    <div class="row buttons">
            <?php echo CHtml::submitButton('Update'); ?>
    </div>
<?php $this->endWidget(); ?>
</div>
</div>
<script>
   $(function(){
       var tab=$('#<?=$_GET['tabid']?>');
       var panel=tab.data('panel');
       $("input[name='select_signature']",panel).on('change',function(){
           var id=$(this).val();
           var data = {};
           data['id'] = id;
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("importsMail/selectSignature",array('tabid'=>$_GET['tabid'])) ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                CKEDITOR.instances['ota-ImportsMailSignature_sig_body'].destroy(true)
                $('#signature_management_panel').html(resp);
            },
        });
       });
  })
</script>
