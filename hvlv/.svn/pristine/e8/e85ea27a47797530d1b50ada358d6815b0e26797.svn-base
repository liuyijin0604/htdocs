<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
  echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
  return;
}
?>
<center>
<h1><?=ucfirst(Yii::app()->session['scan_warehouse'])?> Check In Dashboard</h1>
</center>
<div id="parcel-tabs">
  <ul class="nav nav-tabs">
 <?php
    $tabs =[];

    $tabs[] = ['check_in_dash', $this->t('Check In Dash'), true,false];
    //$tabs[] = ['inspection', Yii::t('whscan','Inspection'), true];
    $tabs[] = ['coming_tmrw', $this->t('Coming TMRW'), true,false];
    $tabs[] = ['arriving_today', $this->t('Arriving Today'), true,false];

 foreach($tabs as $key => $tab){
    $href = strpos($tab[0], '/') === false? $this->createUrl('warehouseProcess/checkinPage',array('tab'=>$tab[0], "tabid" => "tab".$key, "width" =>$_GET['width'])) : $tab[0];
    echo '<li><a href="'.$href.'" class="active" style="'.($tab[3]?"background-color:rgb(20,72,126);color:#FFF":"").'">'.$tab[1].'</a></li>';
 }
 ?>
  </ul>
</div>
<script type="text/javascript">
$(function(){
  $('#parcel-tabs').tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
    // posApp.ajaxifyForm(this);
    $("#loading-container").hide();
  },beforeLoad: function(event,ui){
  }});
});
</script>