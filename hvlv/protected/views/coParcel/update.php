<?php
if(!empty($model->consol_id)){
	echo '<div style="float:right">Consol: <a title="'.$model->consol->no.'" class="tab_link" href="'.$this->createUrl('locoConsol/update', array('id' => $model->consol_id)).'">'.$model->consol->no.'</a></div>';
}
?>
<h1><?=$this->t('Update Courier Shipment');?> <?php echo $model->hbn; ?> - <i><?=$model->getStatus();?></i></h1>
<div id="coparcel-tabs">
  <ul>
 <?php
 $tabs = array(
     array('overview', $this->t('Overview')),
     array('tracking', $this->t('Tracking')),
     array('log', $this->t('Logs')),
     array('crm', $this->t('CRM')),
 );
 foreach($tabs as $tab){
	if(Acl::hasAccess($this->CaName.'/'.$tab[0])){
		$href = strpos($tab[0], '/') === false? $this->createUrl('coParcel/update',array('id'=>$model->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
	}
 }
 ?>
  </ul>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#coparcel-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>
