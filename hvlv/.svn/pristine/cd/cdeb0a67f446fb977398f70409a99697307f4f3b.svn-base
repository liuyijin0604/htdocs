<h3><?=$this->t('IT TASK PLAN')?></h3>
<div style="right: 200px;position: absolute;">
      <a class="jqm_link" href="<?=$this->createUrl('tlaTask/createTlaTask');?>" title="Create Task"><div class="icon" style="background-position:-16px -0px"></div>Create Task</a>
</div>
</br>
</br>
<div id="IT-task-plan-tabs">
  <ul>
 <?php
 foreach($ITTaskTabList as $key=> $tab){
    $href = strpos($tab, '/') === false? $this->createUrl('tlaTask/getITTaskPlanDetail',array('id'=>$key, 'tab'=>$tab, "tabid" => $_GET["tabid"])) : $tab;
        echo '<li><a href="'.$href.'">'.$tab.'</a></li>';
 }
 ?>
  </ul>
</div>


<script type="text/javascript">
$(function(){
    var tab = $('#<?=$_GET["tabid"];?>');
    $('#IT-task-plan-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
        myApp.ajaxifyForm(this);
    }});
});
</script>