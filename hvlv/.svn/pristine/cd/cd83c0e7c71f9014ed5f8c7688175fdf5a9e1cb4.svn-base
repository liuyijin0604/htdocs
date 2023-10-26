<h1><?=$this->t('Update Channel');?> - <?php echo $model->name; ?></h1>

<div id="exchannel-tabs">
	<ul>
 <?php
 $tabs = [
	['overview', $this->t('Overview')],
	['rules', $this->t('Rules')],
	['rates', $this->t('Rates')],
	['connotes', $this->t('Connotes')],
	['log', $this->t('Logs')],
 ];
 foreach($tabs as $tab){
	if(Acl::hasAccess($this->CaName.'/'.$tab[0])){
		$href = strpos($tab[0], '/') === false? $this->createUrl('exChannel/update',array('id'=>$model->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
	}
 }
 ?>
	</ul>
</div>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#exchannel-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>
