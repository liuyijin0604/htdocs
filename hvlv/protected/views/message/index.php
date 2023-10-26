<h1><?=$this->t('Messages');?></h1>
<div id="message-tabs" style="height: 800px">
  <ul>
 <?php
 $tabs = array(
	array('inbox', $this->t('Inbox')),
	array('send', $this->t('Send')),
 );
 foreach($tabs as $tab){
	if(Acl::hasAccess($this->CaName.'/'.$tab[0])){
		$href = strpos($tab[0], '/') === false? $this->createUrl('message/index',array('tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
	}
 }
 ?>
  </ul>
</div>
<script type="text/javascript">
$(function(){
	var win = $('#jqmw2_<?=$_GET["tabid"];?>');
	$('#message-tabs', win).tabs({load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>