<h3>View Mail <?php echo $model->no."--".$model->getMapStatus() ?>--(Ticket Status:<?=$model->getStatus()?>)</h3>
<br/>
<p>
<?php
if(!empty($next))
{
	echo CHtml::link("Next",$this->createUrl("importsMail/update",["id"=>$next]),["class"=>"tab_link change_email","style"=>"float:right;font-size:2em;","title"=>$nextNo]);
}else
{
	echo "<span style='float:right;line-height:2.5em;'>Next</span>";
}
?>
<span style="float:right;font-size:2em;">&nbsp;/&nbsp;</span>
<?php
if(!empty($previous))
{
echo CHtml::link("Previous",$this->createUrl("importsMail/update",["id"=>$previous]),["class"=>"tab_link change_email","style"=>"float:right;font-size:2em;","title"=>$previousNo]);
}else
{
	echo "<span style='float:right;line-height:2.5em;'>Previous</span>";
}
?>
</p>
<br/>
<br/>
<div id="import-email-list-tabs">
  <ul>
 <?php
 $tabs = array(
	array('overview', $this->t('Overview'), true),
	array('reply', $this->t('Reply'), empty($model->reply_mails)?false:true),
     	array('log', $this->t('Log'), true),
	
 );
 foreach($tabs as $tab){
     if(!$tab[2])         continue;
	if(Acl::hasAccess($this->CaName.'/'.$tab[0]) && $tab[1]){
		$href = strpos($tab[0], '/') === false? $this->createUrl('importsMail/update',array('id'=>$model->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
	}
 }
 ?>
  </ul>
</div>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#import-email-list-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
	$('.change_email',tab.data('panel')).on('click',function(){
		tab.trigger('doClose');
	});
});
</script>