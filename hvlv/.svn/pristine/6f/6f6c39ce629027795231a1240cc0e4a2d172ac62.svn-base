<h1><?=$this->t('Dashboard');?></h1>

<div id="dash-ex-tabs">
	<ul>
	<?php
	$tabs = [
		['dashboard_main', $this->t('Main')],
		['dashboard_report', $this->t('Report')],
		['dashboard_overdue', $this->t('Over Due')],
	];
	foreach ($tabs as $tab) {
		if (Acl::hasAccess($this->CaName . '/' . $tab[0])) {
			$href = strpos($tab[0], '/') === false? $this->createUrl('wmsTask/dashEX', ['tab' => $tab[0], 'tabid' => $_GET['tabid']]) : $tab[0];
			echo '<li><a href="' . $href . '">' . $tab[1] . '</a></li>';
		}
	}
	?>
	</ul>
</div>
<script type="text/javascript">
	$(function(){
		var tab = $('#<?=$_GET["tabid"];?>');
		$('#dash-ex-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab']) ? 0 : $_GET['actab']; ?>, load: function(event,ui){
			myApp.ajaxifyForm(this);
		}});
	});
</script>