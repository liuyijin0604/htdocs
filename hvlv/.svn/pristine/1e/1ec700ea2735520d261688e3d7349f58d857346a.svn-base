<h1><?=$this->t('Scan Outturn');?> <?php echo $model->id; ?></h1>

<div id="outturn-tabs">
  <ul>
 <?php
 $tabs = array(
	array('scan', $this->t('Scan')),
	array('parcel', $this->t('Parcel')),
 );
 foreach($tabs as $tab){
	if(Acl::hasAccess($this->CaName.'/'.$tab[0])){
		$href = strpos($tab[0], '/') === false? $this->createUrl('outturn/update',array('id'=>$model->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
	}
 }
 ?>
  </ul>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#outturn-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
	
});
</script>