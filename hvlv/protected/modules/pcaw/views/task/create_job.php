<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
           'Task List'=>array('task/index'),
           'Create Job',
	),
));
?>
<h2><?=$this->t('Create WmsJob');?></h2>

<?php echo $this->renderPartial('job_form', array('model'=>$model)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('form#wms-job-create-form', tab.data('panel')).on('success', function(e, r){
           var url = window.location.href.replace('task/create','task/jobUpdate/id/'+r.id);
            window.location.href = url;
         
	});
});
</script>