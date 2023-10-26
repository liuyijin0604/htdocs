<div id="import-need-check-tabs">
	<ul>
		<?php
		$tabs = array(
			array('importother', $this->t('General')),
			array('importaf', $this->t('Priority'), 'priority'),
			array('importaf', $this->t('T & E'), 'tne'),
			array('importaf', $this->t('YuYang'), 'yuyang'),
			array('importaf', $this->t('Air Insurance'), 'insurance'),
			array('importaf', $this->t('SkyJet'), 'skyjet'),
			array('importaf', $this->t('FYN'), 'fyn'),
			array('importaf', $this->t('Master'), 'master'),
			array('importaf', $this->t('Qantas'), 'qantas'),
			array('importaf', $this->t('Menzies'), 'menzies'),
			// array('importcourier', $this->t('Fastway'), 'Fastway'),
			// array('importcourier', $this->t('Aupost'), 'AuPost'),
			// array('importcourier', $this->t('Startrack'), 'Startrack'),
			// array('importcourier', $this->t('D2z'), 'D2Z'),
		);
		foreach ($tabs as $tab) {
			if (Acl::hasAccess($this->CaName . '/' . $tab[0]) && $tab[1]) {
				$href = strpos($tab[0], '/') === false ? $this->createUrl('billing/accUpdate', array('tab' => $tab[0], 'tabid' => $_GET['tabid'])) : $tab[0];
				if (isset($tab[2])) {
					$href .= '&type=' . $tab[2];
				}
				echo '<li><a href="' . $href . '">' . $tab[1] . '</a></li>';
			}
		}
		?>
	</ul>
</div>

<script type="text/javascript">
	$(function() {
		var tab = $('#<?=$_GET["tabid"];?>');
		$('#import-need-check-tabs', tab.data('panel')).tabs({active : <?php echo empty($_GET['actab']) ? 0 : $_GET['actab'];?>, load:
			function(event, ui) {
				myApp.ajaxifyForm(this);
			}
		});
	});
</script>