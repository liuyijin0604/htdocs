<?php
/* @var $this AirlineController */
/* @var $model Airline */

$this->breadcrumbs=array(
	'Airlines'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Airline', 'url'=>array('index')),
	array('label'=>'Create Airline', 'url'=>array('create')),
	array('label'=>'View Airline', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Airline', 'url'=>array('admin')),
);
?>

<div class="pane">
<h1>Update Airline <?php echo $model->id; ?></h1>

<div id="airline-tabs">
	<ul>
	<?php
	$tabs = [['main', $this->t('Main')]];
	$tabs[] = ['files', $this->t('Files')];
	foreach ($tabs as $tab) {
		if (Acl::hasAccess($this->CaName.'/'.$tab[0])) {
			$href = strpos($tab[0], '/') === false? $this->createUrl('airline/update', ['id' => empty($tab[2])? $model->id : $tab[2], 'tab'=>$tab[0], "tabid" => $_GET["tabid"]]) : $tab[0];
			echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
		}
	}
	?>
	</ul>
</div>
</div>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('#airline-tabs', panel).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>