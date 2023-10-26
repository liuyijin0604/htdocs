<h1><?=$this->t('Manage Location Recommendation');?></h1>
<div id="parcel-tabs">
  <ul>
 <?php
 $tabs = array(
	array(Org::TLA_DEPARTMENT_SYDNEY, $this->t('Sydney'), true),
	array(Org::TLA_DEPARTMENT_MELBOURNE, $this->t('Melbourne'), true),
	array(Org::TLA_DEPARTMENT_BRISBANE, $this->t('Brisbane'), true)
 );
 foreach($tabs as $tab){
		$href = $this->createUrl('wmsLocation/editParcelPrioristyByDptId',array('tab'=>$tab[0],"dptId"=>$tab[0], "tabid" => $_GET["tabid"]));
		echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
 }
 ?>
  </ul>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#parcel-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>
