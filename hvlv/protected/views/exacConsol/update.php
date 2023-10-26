<h1><?=$this->t('Update Consol');?> <?php echo $model->no; ?> - <i><?=$model->getStatus();?></i></h1>

<div id="exac-consol-tabs">
  <ul>
 <?php
 $tabs = array(
	array('overview', $this->t('Overview')),
	array('pallets', $this->t('Pallets')),
	array('esm', $this->t('ESM')),
	array('log', $this->t('Logs')),
 );
 foreach($tabs as $tab){
	if(Acl::hasAccess($this->CaName.'/'.$tab[0])){
		$href = strpos($tab[0], '/') === false? $this->createUrl('exacConsol/update',array('id'=>$model->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
	}
 }
 ?>
  </ul>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#exac-consol-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>
