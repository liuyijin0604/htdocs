<h1><?=$this->t('Product Output to driver');?></h1>

<?php echo $this->renderPartial('_out_form', array('model'=>$model,'inventories' => $inventories)); ?>