<h1><?=$this->t('Messages');?></h1>
<div id="message-tabs">

  <ul class="nav nav-tabs" id="messageTab">
 <?php
 $tabs = array(
	array('inbox', $this->t('Inbox'),'','active'),
	array('send', $this->t('Send'),'',''),
 );
 foreach($tabs as $tab){
	if(Acl::hasAccess($this->CaName.'/'.$tab[0])){
		$href = strpos($tab[0], '/') === false? $this->createUrl('message/index',array('tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		echo '<li class="'.$tab[3].'"><a data-toggle="tab" href="'.$href.'" '.$tab[2].'>'.$tab[1].'</a></li>';
	}
 }
 ?>
  </ul>
</div>
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('#message-tabs').tabs({load: function(event,ui){
		return true;
	}});
});
</script>