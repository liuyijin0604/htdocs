<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
          'Org List'=>array('accounts/index'),
          'Update -'.$model->id
	),
));
?>
<h2><?=$this->t('Update Organisation').' - '.$model->id;?></h2>
<?php echo $this->renderPartial('_form', array('model'=>$model, 'own'=>$own,'user'=>$user)); ?>


<script type="text/javascript">
$(function(){

});
</script>