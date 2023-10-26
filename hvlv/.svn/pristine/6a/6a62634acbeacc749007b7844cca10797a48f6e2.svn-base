<h3>Si Reconcile Detail: Invoice--------<?=$model->parent->inv_no?></h3>
<div id="si-reconcile-tabs">
  <ul>
 <?php
 $tabs = $model->getAllDpmt();
 foreach($tabs as $key=> $tab){
	$href = strpos($key, '/') === false? $this->createUrl('siReconcile/getReconcileDetail',array('id'=>$model->id, 'tab'=>$key, "tabid" => $_GET["tabid"])) : $key;
		echo '<li><a href="'.$href.'">'.$tab.'</a></li>';
 }
 ?>
  </ul>
</div>


<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#si-reconcile-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>