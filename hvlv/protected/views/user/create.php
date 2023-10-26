<h1><?=$this->t('Create User');?></h1>

<?php
if(isset($type)&&$type=="driverUser")
{
 echo $this->renderPartial('_form', array('model'=>$model,'org'=>$org,'type'=>'driverUser')); 
}else
{

 echo $this->renderPartial('_form', array('model'=>$model)); 
}
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#user-form', tab.data('panel')).data('reset', true);
});
</script>