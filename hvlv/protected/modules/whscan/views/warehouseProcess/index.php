<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
  echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
  return;
}
?>
<center>
<h1><?=ucfirst(Yii::app()->session['scan_warehouse'])?> Warehouse Process</h1>
</center>
<div id="parcel-tabs">
  <ul class="nav nav-tabs">
 <?php
    $tabs =[];

    $tabs[] = ['check_in', $this->t('Check In'), true,false];
    //$tabs[] = ['inspection', Yii::t('whscan','Inspection'), true];
    $tabs[] = ['putaway_sort_held', Yii::t('whscan','Put in Pallet')." + ".Yii::t('whscan','Put Away'), true,false];
    $tabs[] = ['preparation', $this->t('Preparation'), true,false];
    $tabs[] = ['held_shipment_resorting', $this->t('旧货'), true,false];
    $tabs[] = ['today_held_scan_check', $this->t('Today Held Scan'), true,false];
    $tabs[] = ['gatepass_sign', $this->t('Gatepass Sign'), true,false];
    $tabs[] = ['unlink_pallet', $this->t('Unlink Pallet'), true,true];
    $tabs[] = ['unknown_shipment', $this->t('Unknown Shipment'), true,true];
    $tabs[] = ['pallet_count_update', $this->t('Pallet Count Update'), true,true];
    $tabs[] = ['scan_surplus', $this->t('Scan Surplus'), true,true];
    $tabs[] = ['surplus_list', $this->t('Surplus List'), true,true];

 foreach($tabs as $key => $tab){
    $href = strpos($tab[0], '/') === false? $this->createUrl('warehouseProcess/processPage',array('tab'=>$tab[0], "tabid" => "tab".$key)) : $tab[0];
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
    $("#loading-container").show();
  }});
});
</script>