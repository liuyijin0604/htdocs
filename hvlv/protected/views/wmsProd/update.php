<h1><?=$this->t('Update Product');?> <?php echo $model->name; ?></h1>

<div id="wmsprod-tabs">
  <ul>
 <?php
 if ($model->type == 50) {
	 $tabs = array(
		array('overview', $this->t('Overview')),
		array('package', $this->t('Package')),
		array('org', $this->t('Customer')),
		array('kit', $this->t('Kit')),
		array('photos', $this->t('Photos')),
		array('log', $this->t('Logs')),
	 );
 } else if ($model->type != 20) {
	 $tabs = array(
		array('overview', $this->t('Overview')),
		array('package', $this->t('Package')),
		array('org', $this->t('Customer')),
		array('hscode', $this->t('HS Code')),
		array('photos', $this->t('Photos')),
		array('log', $this->t('Logs')),
	 );
 } else {
	 $tabs = array(
		array('overview', $this->t('Overview')),
		array('log', $this->t('Logs')),
	 );
 }
 foreach($tabs as $tab){
	if(Acl::hasAccess($this->CaName.'/'.$tab[0])){
		$href = strpos($tab[0], '/') === false? $this->createUrl('wmsProd/update',array('id'=>$model->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
	}
 }
 ?>
  </ul>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#wmsprod-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>