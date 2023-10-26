<h1><?=$this->t('Stock');?> - <?php echo $model->prod->name; ?></h1>

<div id="wms-stock-tabs" style="min-height: 400px">
  <ul>
 <?php
 $tabs = array(
	array('location', $this->t('Location'), true),
	array('ledger', $this->t('Ledger'), true),
	array('ledger2', $this->t('Ledger 2'), true),
 );
 foreach($tabs as $tab){
	if(Acl::hasAccess($this->CaName.'/'.$tab[0]) && $tab[1]){
		$href = strpos($tab[0], '/') === false? $this->createUrl('wmsStock/view',array('id'=>$model->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
	}
 }
 ?>
  </ul>
</div>
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('#wms-stock-tabs', win).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>
