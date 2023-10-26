<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
           'Product List'=>array('product/goods'),
            'Create',
	),
));
?>
<h2><?=$this->t('Create WmsProd');?></h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');

	$('form#wms-prod-form', tab.data('panel')).on('success', function(e, r){
           var url = window.location.href.replace('product/create','product/update/id/'+r.id);
            window.location.href = url;
         
	});
});
</script>