<h3>GatePass Information:</h3>
<?php
  $gatepassInfo=[];
  $rs=GatepassShipment::model()->findAll('fid=:fid AND parent_id !=0 ORDER BY parent_id',array(':fid'=>$model->id));
  foreach ($rs as $r){
      $gatepassInfo[$r->parent_id][]=$r;
  }
  if(empty($gatepassInfo)){
      echo "<h2>Not GatePass Information Yet!</h2>";
  }else{
     foreach ($gatepassInfo as $parent_id =>$rs){
     $gp=GatepassCourier::model()->findByPk($parent_id);
     echo "<div class='row'><h4 style='display:inline-block;'>Pick Up Rego:</h4>".$gp->driver_rego;
     echo "<div class='row'><h4 style='display:inline-block;'>Pick Up Type:</h4>".$gp->getPickUpType();
     echo "<div class='row'><h4 style='display:inline-block;'>Pick Up Courier:</h4>".$gp->getCourierName()
     . "<div class='row'><h4 style='display:inline-block;'>Pick Up Name:</h4> ".$gp->driver_name."&nbsp;&nbsp;&nbsp;<h4 style='display:inline-block;'>Pick Up time:</h4>".$gp->create_time."</div>";
     if(!empty($gp->driverLicense))
     {
       echo "<div class='row'><h4>DriverLicense:</h4><img src='".$gp->driverLicense->driver_license."' style='width:15em;height:auto;'/></div>";
     }
     echo "<div class='row'><h4>Signature:</h4><img src='".$gp->mdata['gatepass_signature']."'/></div>";
     echo  "<a href='".Yii::app()->createUrl('imParcel/genGatepassDoc',array('id'=>$gp->id))."' target='_blank'>GatePass Document</a>";
     echo "<div class='row'><h4>Packs:</h4></div>";
     foreach ($rs as $r){
            if(!empty($r->mdata['cargo_process_id']))
            {
              echo "<div class='row'>".$r->connote_no.":packages:".$r->getPackages()." : ".$r->scan_time. "</div>";
            }else
            {
              echo "<div class='row'>".sprintf('%03s',$r->sno)." : ".$r->scan_time. "</div>";
            }
     }
     echo "<br/>";
     echo "<hr/>";
  }  
  }

?>
<br />
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('#fetchTracking', panel).on('success', function(e, r){
		$('#<?=$_GET["tabid"];?>_parcel-tracking-grid', panel).yiiGridView('update');
	});
});
</script>