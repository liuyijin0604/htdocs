<div id="consol_detail">
	<div style="right: 20px;position: absolute;">
	<!-- <a href="#" data-dropdown="#dropdown-cartage"><div style="background-position:-48px -688px" class="icon"></div> Create Cartage</a> -->
	<div id="dropdown-cartage" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
		<ul class="dropdown-menu">
			<li><a href="<?=$this->createUrl('cartage/create',  ['ref' => $model->awb, 'type' => 'send_terminal']);?>" class="tab_link" title="Create Cartage - Send">Send to terminal</a></li>
		</ul>
	</div>
	</div>

	<h1><?=$this->t('Update Consol');?> <?php echo $model->no; ?> - <i><?=$model->getStatus();?></i></h1>

	<div id="exco-consol-tabs">
	  <ul>
	 <?php
	 $tabs = array(
		array('overview', $this->t('Overview'), true),
		// array('pallets', $this->t('Pallets'), true),
		// array('edi', $this->t('EDI'), false && $model->status > 20),
		//array('confirm', $this->t('Confirmation'), $model->status < 20),
		//array('billing', $this->t('Billing'), $model->status > 20),
		// array('files', $this->t('Files'), true),
		array('log', $this->t('Logs'), true),
	 );
	 foreach($tabs as $tab){
		if(Acl::hasAccess($this->CaName.'/'.$tab[0]) && $tab[1]){
			$href = strpos($tab[0], '/') === false? $this->createUrl('szPortal/update',array('id'=>$model->id, 'tab'=>$tab[0])) : $tab[0];
			echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
		}
	 }
	 ?>
	  </ul>
	</div>
</div>
<script type="text/javascript">
$(function(){
	var panel = $("#consol_detail");
	$('#exco-consol-tabs', panel).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		//myApp.ajaxifyForm(this);
	}});
	
	$(panel).on('mousedown', '.download-menu a', function(e){
		var me = $(this);
		if(!me.data('ou')) me.data('ou', me.attr('href'));
		if($('.download-menu', panel).hasClass('alt')){
			me.attr('href', me.data('ou')+'&alt=1');
		}else{
			me.attr('href', me.data('ou'));
		}
		$('.download-menu', panel).removeClass('alt');
	}).on('keyup', function(e){
		if(e.which == 65){
			if($('.download-menu', panel).hasClass('alt')){
				$('.download-menu', panel).removeClass('alt');
			}else{
				$('.download-menu', panel).addClass('alt');	
			}
		}
	});
});
</script>
