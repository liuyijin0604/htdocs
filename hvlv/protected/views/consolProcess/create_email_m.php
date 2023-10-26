<h1><?=$this->t('Create Email');?></h1>

<?php echo $this->renderPartial('//customProcess/_form_email_m', array('model'=>$model,'noAttachments'=>true)); ?>