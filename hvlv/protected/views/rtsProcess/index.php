<center>
<h1>Import RTS</h1>
</center>
</br>
<h4 style="float:left">Total Left：</h4>
<div style="width:50%;" id="rts-process-overview<?=$_GET['tabid']?>">
   <?php $this->widget('zii.widgets.grid.CGridView', [
    'id'=>'rts_process_list_grid'.$_GET['tabid'],
    'htmlOptions'=>['style'=>'width: 70%'],
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],
    'filter'=>$dataProvider[1],
    'columns'=>[
        ['name'=>'status','headerHtmlOptions' => ['style' => 'display:none'],'filterHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions' => ['style' => 'display:none'],'type'=>'raw'],
        ['name'=>'status','value'=>'ShipmentRtsRecord::$opProcessType[$data["status"]]'],
        ['name'=>'today_left','header'=>'today_left']
    ],
   ]); ?>
</div>
</br>

<h1>Scan for Checking</h1>
<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'scan-form',
        'action'=>$this->createUrl("rtsProcess/checkRtsStatus"),
        'enableAjaxValidation'=>false,
    ));
    ?>
    <p>Barcode: <input id="scan" type="text" size="30" name="barcode" /></p>
    <input type="hidden" name="gpfound" value="0">
    <?php $this->endWidget(); ?>
    <div id="result" style="display:none;margin: 20px; border: 1px solid;padding:30px 40px; font-weight: bold; font-size: 1em;">
    </div>
    <audio id="sound" src=""></audio>
</div>
</br>
<div id="parcel-tabs">
  <ul class="nav nav-tabs">
 <?php
    $tabs =[];

    $tabs[] = ['rts_unknown', $this->t('Unknown RTS'), true,1,ShipmentRtsRecord::UNKNOWN];
    $tabs[] = ['rtc_list', $this->t('RTS/RTC List'), true,1,ShipmentRtsRecord::KNOWN];
    $tabs[] = ['rtc_record', $this->t('RTC Record'), true,1,ShipmentRtsRecordConfirm::RTC_WAITING];
    $tabs[] = ['rts_resend', $this->t('RTS Waiting Resend'), true,0];
    $tabs[] = ['rts_done', $this->t('RTS Done List'), true,0];
    $tabs[] = ['rts_waiting_discard', $this->t('RTS Waiting Discard'), true,0];
    $tabs[] = ['rts_discarded', $this->t('RTS Discarded List'), true,0];
    $tabs[] = ['rts_wrong_courier', $this->t('RTS Wrong Courier List'), true,0];

 foreach($tabs as $key => $tab){
   $color = "";
   if($tab[3]==1&&$provide[$tab[4]]['today_left']>0)
   {
      $color ='<span style="color:red" >*</span>';
   }
    $href = strpos($tab[0], '/') === false? $this->createUrl('rtsProcess/processPage',array('tab'=>$tab[0], "tabid" => "tab".$key)) : $tab[0];
    echo '<li><a href="'.$href.'" class="active" >'.$tab[1].$color.'</a></li>';
 }
 ?>
  </ul>
</div>
<script type="text/javascript">
$(function(){
  var tab = $('#<?=$_GET["tabid"];?>');
  var panel = $('#<?=$_GET["tabid"];?>').data('panel');
  $('#parcel-tabs',panel).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
    // posApp.ajaxifyForm(this);
  }});

  $('form#scan-form', panel).data('custom_success', function(r){
             $('#result', panel).html(r.msg).css('color', r.color).fadeIn(100, function(){
              //  alert(r.msg);
            });

            return true;
        }).on('submit', function(){
            $('input#scan', panel).focus();
        });

});
</script>