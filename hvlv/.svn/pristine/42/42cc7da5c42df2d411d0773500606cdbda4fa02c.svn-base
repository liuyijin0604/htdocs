<div id="recscan-tabs">
  <ul>
 <?php
 $tabs = array(
	array('export', $this->t('Export Receival')),
	array('batch', $this->t('Batch Receival')),
	array('report', $this->t('Receival Report')),
     array('scanw', $this->t('Scan and Weight'))
 );
 foreach($tabs as $tab){
	if(Acl::hasAccess($this->CaName.'/'.$tab[0])){
		$href = strpos($tab[0], '/') === false? $this->createUrl('receival/scan',array('tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
	}
 }
 ?>
  </ul>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#recscan-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET["actab"])? 0 : $_GET["actab"]; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>