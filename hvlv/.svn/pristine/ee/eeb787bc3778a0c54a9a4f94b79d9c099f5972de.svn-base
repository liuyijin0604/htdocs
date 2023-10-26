<?php
$this->widget('zii.widgets.CBreadcrumbs', [
    'homeLink'=>CHtml::link('Home', ['site/index']),
    'links' => [
        'RTS Process',
    ],
]);
?>
<center>
<h1>RTS Process</h1>
</center>
<div id="parcel-tabs">
  <ul class="nav nav-tabs">
 <?php
    $tabs =[];
    $tabs[] = ['rts_received', $this->t('RTS Received'), true];
    $tabs[] = ['rts_waiting_resend', $this->t('RTS Waiting TLA Resend'), true];
    $tabs[] = ['rts_done', $this->t('RTS Done'), true];
    $tabs[] = ['rts_discarded', $this->t('RTS Discarded'), true];

 foreach($tabs as $key => $tab){
    $href = strpos($tab[0], '/') === false? $this->createUrl('rtsProcess/processPage',array('tab'=>$tab[0], "tabid" => "tab".$key)) : $tab[0];
    echo '<li><a href="'.$href.'" class="active">'.$tab[1].'</a></li>';
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