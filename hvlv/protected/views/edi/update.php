<h1><?=$this->t('Update EDI AWB');?> <?php echo $model->no; ?> - <i><?=$model->getStatus();?></i></h1>

<div id="edi-awb-tabs">
  <ul>
 <?php
 $tabs = array(
	array('overview', $this->t('Overview'), true),
	array('log', $this->t('Logs'), true),
 );
 foreach($tabs as $tab){
	if(Acl::hasAccess($this->CaName.'/'.$tab[0]) && $tab[1]){
		$href = strpos($tab[0], '/') === false? $this->createUrl('edi/update',array('id'=>$model->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
	}
 }
 ?>
  </ul>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#edi-awb-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>
