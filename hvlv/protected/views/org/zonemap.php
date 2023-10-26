<h1><?=$this->t('Import Zone Map');?></h1>

<?php echo $this->renderPartial('_form_zonemap', array('model'=>$model,'url'=>'org/AjaxSaveOrgZonemap')); ?>