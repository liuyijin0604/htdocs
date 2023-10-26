<h1><?=$this->t('Location');?> - <?php echo $model->code; ?></h1>

<div id="wms-location-tabs" style="min-height: 400px">
	<ul>
	<?php
	$tabs = array(
		array('content', $this->t('Content'), true),
		array('ledger', $this->t('Ledger'), true),
		array('log', $this->t('Log'), true),
 	);
 	foreach($tabs as $tab){
		if(Acl::hasAccess($this->CaName.'/'.$tab[0]) && $tab[1]){
			$href = strpos($tab[0], '/') === false? $this->createUrl('wmsLocation/view',array('id'=>$model->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
			echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
		}
	 }
	?>

	</ul>
</div>
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('#wms-location-tabs', win).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>