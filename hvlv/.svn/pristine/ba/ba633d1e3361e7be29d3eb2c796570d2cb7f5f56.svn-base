<h1><?=$this->t('3PL Dashboard');?> </h1>

<div id="dash-3pl-tabs">
	<ul>
		<?php
		$tabs = array(
			array('dash_3pl_adhoc', $this->t('Adhoc'), true),
			array('dash_3pl_kpi', $this->t('KPI')),
		);
		foreach ($tabs as $tab) {
			if (Acl::hasAccess($this->CaName . '/' . $tab[0]) && $tab[1]) {
				$href = strpos($tab[0], '/') === false ? $this->createUrl('wmsTask/dash3PL', array('tab' => $tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
				echo '<li><a href="' . $href . '">' . $tab[1] . '</a></li>';
			}
		}
		?>
	</ul>
</div>
<script type="text/javascript">
	$(function(){
		var tab = $('#<?=$_GET["tabid"];?>');
		$('#dash-3pl-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab']) ? 0 : $_GET['actab']; ?>, load: function(event,ui){
			myApp.ajaxifyForm(this);
		}});
	});
</script>