<h1><?=$this->t('Update Organisation').' - '.$model->id;?></h1>
<div id="org-tabs">
  <ul>
 <?php
 $tabs = array(
	array('company', $this->t('Company')),
	array('contacts', $this->t('Contacts')),
	array('files', $this->t('Files')),
	array('rates', $this->t('Rates')),
	array('barcode', $this->t('Barcodes')),
     array('edi', $this->t('EDI')),
     array('wms_rates', $this->t('WMS Rates')),
    // array('cgrates', $this->t('Consumables Rates')),
      array('org_pricing', $this->t('Org Pricing Note')),
     array('log', $this->t('Log')),
 );
 foreach($tabs as $tab){
	if(Acl::hasAccess($this->CaName.'/'.$tab[0])){
		$href = strpos($tab[0], '/') === false? $this->createUrl('org/update',array('id'=>$model->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
	}
 }
 ?>
  </ul>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	
	$('#org-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>