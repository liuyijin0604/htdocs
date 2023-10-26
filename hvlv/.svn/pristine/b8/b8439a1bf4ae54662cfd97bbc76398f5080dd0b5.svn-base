<?php 
class ParcelScanService extends Service
{
   public static function getImPacelScanLog($p)
   {
        $shipmentScans = ShipmentScan::model()->with("process","user")->findAll(["condition"=>"pid=:pid","params"=>[":pid"=>$p->id],"order"=>"t.id desc"]);
        $provide = [];
        foreach ($shipmentScans as $key => $shipmentScan)
        {
            if(!empty($shipmentScan->process))
            {
                $provide[]  = [$shipmentScan->process->barcode,$shipmentScan->pno,ShipmentScan::$types[$shipmentScan->type],$shipmentScan->scan_time,$shipmentScan->user->fname];
            }else
            {
                $provide[]  = ["unknow barcode",$shipmentScan->pno,ShipmentScan::$types[$shipmentScan->type],$shipmentScan->scan_time,$shipmentScan->user->fname];
            }
        }
        return $provide;
   }

   public static function getGatepassSign($model)
   {
        $str = "";
        $gatepassInfo=[];
        $rs=GatepassShipment::model()->findAll('fid=:fid AND parent_id !=0 ORDER BY parent_id',array(':fid'=>$model->id));
        foreach ($rs as $r){
            $gatepassInfo[$r->parent_id][]=$r;
        }
        if(empty($gatepassInfo)){
            $str.= "<h2>Not GatePass Information Yet!</h2>";
        }else{
           foreach ($gatepassInfo as $parent_id =>$rs)
           {
               $gp=GatepassCourier::model()->findByPk($parent_id);
               $str.= "<table>";
               $str.= "<tr><th colspe='2'>GatePass Infomation</th></tr>";
               $str.= "<tr><td>Pick Up Rego:".$gp->driver_rego."</td></tr>";
               $str.= "<tr><td>Pick Up Type:".$gp->getPickUpType()."</td></tr>";
               $str.= "<tr><td>Pick Up Courier:".$gp->getCourierName()."</td></tr>";
               $str.= "<tr><td>Pick Up Name:".$gp->driver_name."</td></tr>";
               if(!empty($gp->driverLicense))
               {
                 $str.= "<tr><td>DriverLicense:</td></tr><tr><td><img src='".$gp->driverLicense->driver_license."' style='width:15em;height:auto;'/></td></tr>";
               }
               $str.= "<tr><td>Signature:</td></tr><tr><td><img src='".$gp->mdata['gatepass_signature']."'/></td></tr>";
               $str.=  "<tr><td><a href='".Yii::app()->createUrl('imParcel/genGatepassDoc',array('id'=>$gp->id))."' target='_blank'>GatePass Document</a></td></tr>";

               $str.= "<tr><td>Packs:</td><tr>";
               foreach ($rs as $r){
                      if(!empty($r->mdata['cargo_process_id']))
                      {
                        $str.="<tr><td>".$r->connote_no.":packages:".$r->getPackages()." : ".$r->scan_time. "</td><tr></div>";
                      }else
                      {
                        $str.="<tr><td>".sprintf('%03s',$r->sno)." : ".$r->scan_time. "</td><tr></div>";
                      }
               }

               $str.= "</table>";
          }  
        }
        return $str;
   }

}
?>