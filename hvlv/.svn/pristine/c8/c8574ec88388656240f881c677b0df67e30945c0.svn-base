<h1><?=$this->t('Update WMS Org');?> <?php echo $model->name; ?></h1>

<div id="wmsprod-tabs">
  <ul>
 <?php
 $tabs = array(
	array('overview', $this->t('Overview')),
	array('storage', $this->t('Stock')),
        array('product', $this->t('Product')),
	array('rate', $this->t('Rate Manage')),
        array('billing', $this->t('Billing')),
	array('log', $this->t('Logs')),
 );
 foreach($tabs as $tab){
	if(Acl::hasAccess($this->CaName.'/'.$tab[0])){
		if($tab[0] == 'overview'){
			$href = strpos($tab[0], '/') === false? $this->createUrl('top/wmsOrg/update',array('id'=>$model->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		}
		else{
			$href = strpos($tab[0], '/') === false? $this->createUrl('wmsOrg/update',array('id'=>$model->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		}
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

