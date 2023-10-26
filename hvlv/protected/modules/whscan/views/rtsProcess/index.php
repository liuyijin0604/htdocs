<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
  echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
  return;
}
?>
<center>
<h1><?=ucfirst(Yii::app()->session['scan_warehouse'])?> RTS Process</h1>
</center>
<div id="parcel-tabs">
  <ul class="nav nav-tabs">
 <?php
    $tabs =[];

    $tabs[] = ['rts_scan', $this->t('Put Away RTS'), true,-1];
    $tabs[] = ['rts_resend', $this->t('RTS Resend'), true,ShipmentRtsRecordConfirm::RTS_WAITING_RESEND];
    $tabs[] = ['rts_unknown', $this->t('RTS Unknown'), true,ShipmentRtsRecord::UNKNOWN];
    $tabs[] = ['rts_discard', $this->t('RTS Waiting Discard'), true,ShipmentRtsRecord::WAITING_UNKNOWN_DISCARD];
    $tabs[] = ['rts_putaway', $this->t('Change RTS Location'), true,-1];

 foreach($tabs as $key => $tab){
  $str = "";
  if($tab[3]>=0)
  {
    if($provide[$tab[3]]['today_left']>0)
    {
      $str = "(<font style='font-size:1em;font-weight:bold;color:#14487E'>".$provide[$tab[3]]['today_left']."</font>)";
    }else
    {
      $str = "(".$provide[$tab[3]]['today_left'].")";
    }
  }
    $href = strpos($tab[0], '/') === false? $this->createUrl('rtsProcess/processPage',array('tab'=>$tab[0], "tabid" => "tab".$key)) : $tab[0];
    echo '<li><a href="'.$href.'" class="active" >'.$tab[1].$str.'</a></li>';
 }
 ?>
  </ul>
</div>
<script type="text/javascript">
$(function(){
  $('#parcel-tabs').tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
    // posApp.ajaxifyForm(this);
  }});
});
</script>