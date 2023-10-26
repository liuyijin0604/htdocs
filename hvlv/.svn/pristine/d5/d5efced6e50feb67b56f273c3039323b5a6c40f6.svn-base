<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
          'Org List'=>array('accounts/index'),
          'Create',
	),
));
?>
<h1><?=$this->t('Create Organisation');?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'own'=>$own,'user'=>$user)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');

	$('form#org-form', tab.data('panel')).on('success', function(e, r){
           var url = window.location.href.replace('accounts/create','accounts/update/id/'+r.id);
            window.location.href = url;
         
	});
});
</script>