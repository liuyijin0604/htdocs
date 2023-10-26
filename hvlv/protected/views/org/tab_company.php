<div style="position:absolute; right: 20px;">
<a href="<?=Yii::app()->createUrl("org/changePassword", array("id" => $model->id));?>" class="jqm_link">Reset HCDG Password</a>
</div>
<?php echo $this->renderPartial('_form', array('model'=>$model, 'own'=>$own)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
});
</script>