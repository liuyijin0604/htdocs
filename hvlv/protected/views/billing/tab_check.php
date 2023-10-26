<div id="billing-stream-line-check-tabs">
	<ul>
		<?php
		$tabs = array(
			array('courier', $this->t('Courier')),
			array('broker', $this->t('Broker')),
			array('airport', $this->t('Airport')),
		);
		foreach ($tabs as $tab) {
			if (Acl::hasAccess($this->CaName . '/' . $tab[0]) && $tab[1]) {
				$href = strpos($tab[0], '/') === false ? $this->createUrl('billing/accUpdate', array('tab' => $tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
				echo '<li><a href="' . $href . '">' . $tab[1] . '</a></li>';
			}
		}
		?>
	</ul>
</div>
<script type="text/javascript">
	$(function(){
		var tab = $('#<?=$_GET["tabid"];?>');
		$('#billing-stream-line-check-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab']) ? 0 : $_GET['actab']; ?>, load: function(event,ui){
			myApp.ajaxifyForm(this);
		}});
	});
</script>
