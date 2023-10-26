<h2><?=Yii::t('whscan','Put in Pallet')." and ".Yii::t('whscan','Put Away')?></h2>

<?php echo $this->renderPartial('loading_list', array('url'=>"warehouseProcess/getPutawaySortHeldList","dptId"=>$dptId)); ?>