<h1><?=$this->t('Update Container Cartage ID-' . $model->id);?></h1>

<div id="container-cartage-tabs">
	<ul>
	<?php
	$tabs = array(
		array('overview', $this->t('Overview')),
		array('files', $this->t('Files')),
	);
	foreach ($tabs as $tab) {
		if (Acl::hasAccess($this->CaName . '/' . $tab[0])) {
			$href = strpos($tab[0], '/') === false? $this->createUrl('containerCartage/update', array('id' => $model->id, 'tab'=>$tab[0], 'tabid' => $_GET['tabid'])) : $tab[0];
			echo '<li><a href="' . $href . '">' . $tab[1] . '</a></li>';
		}
	}
	?>
	</ul>
</div>

<script type="text/javascript">
$(function() {
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('#container-cartage-tabs', panel).tabs({active: <?php echo empty($_GET['actab']) ? 0 : $_GET['actab']; ?>, load: function(event,ui) {
		myApp.ajaxifyForm(this);
	}});
});
</script>