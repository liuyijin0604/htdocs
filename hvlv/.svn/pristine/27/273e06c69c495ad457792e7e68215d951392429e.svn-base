<div style="right: 20px;position: absolute;">
<a href="<?=$this->createUrl('cartage/create',  ['jobid' => $model->id, 'type' => 'send_terminal']);?>" class="jqm_link" id="cartage_btn" data-win-class="XXL" title="Create Cartage - Send"><div style="background-position:-48px -688px" class="icon"></div> Create Cartage</a>
</div>

<h1><?=$this->t('Update Job');?> <?php echo $model->no; ?> - <i><?=$model->getStatus();?></i></h1>

<div id="edi-job-tabs">
  <ul>
 <?php
 $tabs = array(
	array('overview', $this->t('Overview'), true),
	array('log', $this->t('Logs'), true),
     array('attachements', $this->t('Attachements'), true),
 );
 foreach($tabs as $tab){
	if(Acl::hasAccess($this->CaName.'/'.$tab[0]) && $tab[1]){
		$href = strpos($tab[0], '/') === false? $this->createUrl('ediJob/update',array('id'=>$model->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
	}
 }
 ?>
  </ul>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var pane = tab.data('panel');
	$('#edi-job-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});

	if ($(pane).width() <= 800) {
		$('#cartage_btn', pane).attr('data-win-class', 'XL');
	}
});
</script>
