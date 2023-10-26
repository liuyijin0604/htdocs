<?php 
if (preg_match('/iphone/i', $_SERVER["HTTP_USER_AGENT"]) || preg_match('/android/i', $_SERVER["HTTP_USER_AGENT"])) {
	$client = 'mobile';
} else {
	$client = 'desktop';
}
?>
<div style="right: 20px;position: absolute;">
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-cartage"><div style="background-position:-48px -688px" class="icon"></div> Create Cartage</a>
<div id="<?=$_GET["tabid"];?>-dropdown-cartage" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createUrl('cartage/create',  ['ref' => $model->awb, 'type' => 'pick_terminal']);?>" class="tab_link" title="Create Cartage - Pick">Pick up from terminal</a></li>
		<li><a href="<?=$this->createUrl('cartage/create',  ['ref' => $model->awb, 'type' => 'pick_customer']);?>" class="tab_link" title="Create Cartage - Pick">Pick up from customer</a></li>
	</ul>
</div>
</div>

<h1><?=$this->t('Update Consol');?> <?php echo $shadow->no; ?> - <i><?=$shadow->getStatus();?></i></h1>

<div id="imco-consol-tabs">
  <ul>
 <?php
 $tabs = array(
	array('overview', $this->t('Overview'), true),
	array('acr', $this->t('ACR'), $model->status < 100),
	array('aout', $this->t('AOUT'), $model->status < 100),
	array('scan_check', $this->t('Scan Check'), $model->status < 100),
	array('delivery', $this->t('Delivery'), $model->status < 100),
	array('old_billing', $this->t('old_billing'), $model->status < 100),
	array('billing', $this->t('Billing'), $model->status < 100),
        array('files', $this->t('Files')),
        array('orgs', $this->t('Orgs'),true),
	array('log', $this->t('Logs'), true),
 );
 foreach($tabs as $tab){
	if(Acl::hasAccess($this->CaName.'/'.$tab[0]) && $tab[1]){
		if ($tab[0] == 'overview') {
			$href = strpos($tab[0], '/') === false? $this->createUrl('imcoConsol/update',array('id'=>$model->id,'shadow_id'=>$shadow->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"], "client" => $client)) : $tab[0];
		} else {
			$href = strpos($tab[0], '/') === false? $this->createUrl('imcoConsol/update',array('id'=>$model->id,'shadow_id'=>$shadow->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
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
	$('#imco-consol-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>
