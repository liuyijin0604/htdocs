<?php
/* @var $this CartageController */
/* @var $model Cartage */

$this->breadcrumbs=array(
	'Cartages'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Cartage', 'url'=>array('index')),
	array('label'=>'Create Cartage', 'url'=>array('create')),
	array('label'=>'View Cartage', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Cartage', 'url'=>array('admin')),
);
?>

<div class="pane">
	<h1>Update Cartage <?php echo $model->id . ' ' . $model->getJobAwb(); ?></h1>

	<div id="cartage-tabs">
		<ul>
			<?php
			$tabs = [['overview', $this->t('Overview')]];
			$tabs[] = ['files', $this->t('Files')];
			foreach ($tabs as $tab) {
				$href = strpos($tab[0], '/') === false ? $this->createUrl('cartage/update', ['id' => empty($tab[2])? $model->id : $tab[2], 'tab' => $tab[0], 'tabid' => $_GET['tabid'], 'actab' => @$_GET['actab']]) : $tab[0];
				echo '<li><a href="' . $href . '">' . $tab[1] . '</a></li>';
			}
			?>
		</ul>
	</div>
</div>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('#cartage-tabs', panel).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
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