<h3>Sea Process Operation</h3>
<h3><?=$model->shipment->hbn?></h3>
<div class="form" style="min-height: 200px;">
    <div class="row rowcol rowleft">
        <label><b>ETD:</b></label><?=@$model->shipment->consol->etd;?>
    </div>
    <div class="row rowcol">
        <label><b>ETA:</b></label><?=@$model->shipment->consol->eta;?>
    </div>
    <div class="row rowcol rowleft">
        <?php echo CHtml::label('status','status'); ?>
        <?php echo CHtml::dropDownList('process_status',@$model->status, SeaProcess::$states,array('prompt'=>'Select')); ?>
    </div>
    <div class="row buttons">
        <?php echo CHtml::button('Update', array('class' => 'updateStatus'));?>
    </div>
   <br/>
<?php if($model->type==1){
      if($model->status==SeaProcess::STATE_WAITING_PREALERT){
            echo CHtml::button('Send PreAlert/Notice', array('class' => 'send_pre_alert','style'=> 'margin-right:5px;'));
      }else if($model->status== SeaProcess::STATE_WAITING_REQUEST_MANIFEST){
           echo CHtml::button('Send Manifest', array('class' => 'send_sea_manifest','style'=> 'margin-right:5px;'));
      }else if($model->status== SeaProcess::STATE_WAITING_DO){
           echo CHtml::button('Send DO', array('class' => 'send_sea_do','style'=> 'margin-right:5px;'));
      }else if($model->status== SeaProcess::STATE_WAITING_OUTTURN){
           echo CHtml::button('Send Sea Outturn', array('class' => 'send_sea_outturn','style'=> 'margin-right:5px;'));
      }
}
?>
<div style="border: 1px solid black; width: 90%;min-height: 50px; margin-top: 10px;padding: 5px;">
   <?=$model->getOperationStatus();?>    
</div>
</div>
<script type="text/javascript">
$(function(){
        var win = $('#jqmw_<?=$_GET["tabid"];?>');
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = $('#<?=$_GET["tabid"];?>').data('panel');
        
        tab.unbind('reload_sea_grid').bind('reload_sea_grid', function(){
		$('#<?=$_GET["tabid"];?>_sea_grid', tab.data('panel')).yiiGridView('update');
		return false;
	});
      $('.updateStatus',win).click(function(){
         var selected=$("#process_status",win).val();
         if(confirm('Are you sure to update?')){
             $.get('<?=$this->createUrl("seaProcess/updateProcess",["id"=>$model->id]);?>'+"?status="+selected,function(r){
                  if(r == 'done'){
                        myApp.notice('Done', 5000);
                    }else{
                        myApp.alert(r, false);
                    }
                      tab.trigger('reload_sea_grid');
                      win.jqmHide();
             });
         } 
       });
       
     $('.send_pre_alert', win).click(function(){
          if(confirm('Are you sure to Send Pre Alert And Notice Arrival?')){
            $(this).hide();
            $.get('<?= $this->createUrl("seaProcess/sendPreAlertAndNotice", ["id" => $model->id]); ?>', function(r){
                if(r == 'done'){
                    myApp.notice('Done', 5000);
                }else{
                    myApp.alert(r, false);
                }
                 tab.trigger('reload_sea_grid');
             });
           }
          return false;
       });
       
      $('.send_sea_manifest', win).click(function(){
          if(confirm('Are you sure to Send Sea Manifest?')){
            $(this).hide();
            $.get('<?= $this->createUrl("seaProcess/sendSeaManifest", ["id" => $model->id]); ?>', function(r){
                if(r == 'done'){
                    myApp.notice('Done', 5000);
                }else{
                    myApp.alert(r, false);
                }
                 tab.trigger('reload_sea_grid');
             });
           }
          return false;
       });
       $('.send_sea_do', win).click(function(){
          if(confirm('Are you sure to Send Sea DO?')){
            $(this).hide();
            $.get('<?= $this->createUrl("seaProcess/sendSeaDo", ["id" => $model->id]); ?>', function(r){
                if(r == 'done'){
                    myApp.notice('Done', 5000);
                }else{
                    myApp.alert(r, false);
                }
                 tab.trigger('reload_sea_grid');
             });
           }
          return false;
       });
       $('.send_sea_outturn', win).click(function(){
          if(confirm('Are you sure to Send Sea DO?')){
            $(this).hide();
            $.get('<?= $this->createUrl("seaProcess/sendSeaOutturn", ["id" => $model->id]); ?>', function(r){
                if(r == 'done'){
                    myApp.notice('Done', 5000);
                }else{
                    myApp.alert(r, false);
                }
                 tab.trigger('reload_sea_grid');
             });
           }
          return false;
       });
 
})    
</script>