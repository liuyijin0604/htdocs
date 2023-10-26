<?php Yii::app()->clientScript->registerCss('mycss',"#myModal".$_GET['tabid']."{z-index:3; display: none; position: absolute; top:100px; right:50px; width: 400px; max-width: 800px;}
		 .top {
		 color: #aaaaaa;
		font-size: 28px;
		font-weight: bold;
	 
}
 ")?>
<h1><?=$this->t('Update Export Shipment');?> <?php echo $model->hbn; ?> - <i><?=$model->getStatus();?></i></h1>

<div id="exparcel-tabs">
	<ul>
 <?php
 $tabs = array(
	array('overview', $this->t('Overview')),
	array('tracking', $this->t('Tracking')),
//	array('billing', $this->t('Billing')),
	array('log', $this->t('Logs')),
		array('crm', $this->t('CRM'))
 );
 foreach($tabs as $tab){
	if(Acl::hasAccess($this->CaName.'/'.$tab[0])){
		$href = strpos($tab[0], '/') === false? $this->createUrl('exParcel/update',array('id'=>$model->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
	}
 }
 ?>
	</ul>
</div>
<?php
if($this->findhbn($model->hbn)){ 
	echo '<div id="myModal'.$_GET['tabid'].'" class="modal" class >
			 <span class="top" id="close'.$_GET['tabid'].'">&times;</span>
			 <span class="top" id="rotate'.$_GET['tabid'].'">&#8635</span>
			 <a class="jqm_link" data-win-class="XL" href="'.Yii::app()->createURL('exParcel/ecreate',array('id'=>$this->geteid($model->hbn))).'"><div style="background-postion:-48px -688px" class="icon"></div>Link</a> &nbsp;
			 <span class="agent_id">Agent:'.$this->getAgent($model->hbn).'</span>
                         <img src="'.$this->get_url_hbn($model->hbn).'" id='.'mypic' .$_GET['tabid'].' style="width:700px; height:500px;">
			 </div>';
}elseif(!empty($model->mdata['client_entry'])){
	$cltent = $model->mdata['client_entry'];
	if(!empty($cltent['items']['g'])){
		$gs = [];
		foreach($cltent['items']['g'] as $gi => $g){
			$gs[] = $cltent['items']['q'][$gi].' &times; '.$g;
		}
		echo '<div id="myModal'.$_GET['tabid'].'" class="modal" class >
			 <span class="top" id="close'.$_GET['tabid'].'">&times;</span><p style="background: rgba(255,255,255,0.7); padding: 20px; font-size: 1.2em;"><b>Client Entry:</b><br />'.implode('<br />', $gs).'</p></div>';
	}
} else {
	echo '<a style="display:none" class="jqm_link" data-win-class="XL" id="myclick'.$_GET['tabid'].'" href="'.Yii::app()->createURL('exParcel/image',array('agent_id'=>$model->agent_id,'fhbn'=>$model->hbn)).'"></a> &nbsp;';
}
?>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#exparcel-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>
