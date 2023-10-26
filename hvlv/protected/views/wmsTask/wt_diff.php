<h1>Startrack & TNT Weight Diff</h1>

<div id="wt-diff-tabs">
	<ul>
	<?php
	$tabs = array(
		array('tocreate', $this->t('To Create')),
		array('created', $this->t('Created')),
	);
	foreach ($tabs as $tab) {
		$href = strpos($tab[0], '/') === false ? $this->createUrl('wmsTask/wtDiff', array('tab' => $tab[0], 'tabid' => $_GET['tabid'])) : $tab[0];
		echo '<li><a href="' . $href . '">' . $tab[1] . '</a></li>';
	}
	?>
	</ul>
</div>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('#wt-diff-tabs', panel).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});

	//bind reload_tab
	tab.off('reload_tab').on('reload_tab', function(){
		var t = $('.ui-tabs', panel);
		t.tabs('load', t.tabs('option','active'));
	});

	tab.on('onOpen', function(){
		tab.trigger('reload_tab');
	});
});
</script>