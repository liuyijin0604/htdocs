<?php 
class TrackingService extends Service
{
    private $cts = null;
    /**
     this service is moved from the gatepass createStorageInvoice function
     *
     * @param  [type] &$shipment [description]
     * @param  [type] $mid       [description]
     * @return [type]            [description]
     */
    public function handlingTrackingInfo($trackingInfo)
    {
        $ref = $trackingInfo->ref;
        $trackingData = $trackingInfo->data;
        $p = ImParcel::model()->find("ref = :ref",[":ref"=>$ref]);
        $trans = $p->trans;
        $r = end($trans);
        $this->cts = &$r;
        $toll = [];
        $auspost = [];
        if (in_array($r->org_id, [Org::ORGID_COURIER_ALLIED])) {
            $toll[] = $r;
            $trans = Yii::app()->db->beginTransaction();
                try {
                    $this->_upImTrack($r, $this->_prepTollEvents($trackingData));
                    $trans->commit();
                } catch (Exception $ex) {
                    $trans->rollback();
                    throw $ex;
                }
        }

        if (in_array($r->org_id, [Org::ORGID_COURIER_AUPOST])) {
            $auspost[] = $r;
            $trans = Yii::app()->db->beginTransaction();
                try {
                    $this->_upImTrack($r, $this->_prepAupostEvents($trackingData));
                    $trans->commit();
                } catch (Exception $ex) {
                    $trans->rollback();
                    throw $ex;
                }
        }
        return true;
    }

    public function getShipmentNeedTracking()
    {
        $getTime = date('Y-m-d H:i:s',strtotime('-4 hours'));
        $rs = Tranship::model()->with(['shipment'])->findAll(['condition'=>'t.status IN (11,19) AND org_id IN (:eizId) AND time < :getTime','params'=>[":eizId"=>Org::ORGID_COURIER_ALLIED,":getTime"=>$getTime],"order"=>'t.id desc']);
        $data = [];
        foreach ($rs as $key => $r)
        {
           $data[] = [$r->connote,$r->shipment->postcode];
        }
        return $data;
    }

    private function  _prepTollEvents($data){
        $finalData = [];
        foreach($data as $key => $d){
            $thisData = [];
            $thisData[0] = $d[1];
            $thisData[1] = $d[0];
            $thisData[2] = $d[2];
            if(preg_match('/delivered/i', $thisData[1]))
            {
                $this->cts->status = 99;
                $this->cts->mdata['inTransit'] = 0;
                $thisData['delivered'] = 1;
            }
            if(preg_match('/(Pick)|(Transit)|(IN DEPOT)/i', $thisData[1]))
            {
                $this->cts->mdata['inTransit'] = 1;
            }
            $finalData[] = $thisData;
        }
        return $finalData;
    }

    private function _prepAupostEvents($data){
        $finalData = [];
        $amp = ['/by Australia Post/', '/Shipping information/', '/Australia Post facility/', '/With Australia Post/'];
        $amr = ['', 'Courier dispatch information', 'delivery depot', 'With driver'];
        foreach($data as $key => $d){
            $thisData = [];
            $d[1] = preg_replace($amp, $amr, $d[1]);//|Customer enquiry lodged
            $thisData[0] = $d[1];
            $thisData[1] = $d[0];
            $thisData[2] = $d[2];
            if (preg_match('/^(Delivered|Returned|Returning to sender)/i', $thisData[1]) && !empty($this->cts)) {
                $this->cts->status = 99;
                $thisData['delivered'] = 1;
            }
            $finalData[] = $thisData;
        }
        return $finalData;
    }


    public function _upImTrack($r, $data=null)
    {
        $this->cts = &$r;
        foreach ($data as $key => $t) {
            if (empty($t[1]) || empty($t[0])) {
                continue;
            }
            if (empty($r->shipment)) {
                continue;
            }
            
            if($r->status > 90&&!empty($t['delivered']))
            {
                $r->shipment->addTracking(90, $t[1], $t[2], $t[0], $r->id);
            }else
            {
                $r->shipment->addTracking(70, $t[1], $t[2], $t[0], $r->id);
            }
        }

        if(empty($data))
        {
            return;
        }

        //update status
        if ($r->status > 90) {
            if ($r->shipment->status<80 && in_array($r->shipment->status, [42,60,70])) {
                $r->shipment->status = 90;
                $r->shipment->update(['status']);
            }
        } elseif ((($r->shipment->status == 60)||($r->shipment->status == 42)) && ($r->shipment->scan_no == 0) && $r->shipment->scanAfter8h()&&!empty($r->mdata['inTransit'])) {
            $r->shipment->status = 70;
            $r->shipment->update(['status']);
        } else {
            // in case local delivery parcel
            // if tranship status existing
            // which means parcel is in courier
            if (!empty($data)) {
                if ($r->shipment->status < 70 && !in_array($r->shipment->status, [50,55,57,58]) && ($r->shipment->cbwf & 32) == 0 && empty($r->shipment->mdata['direct_courier']) && !in_array($r->shipment->agent_id, [1427, 1656, 1689])&& ($r->shipment->scan_no < $r->shipment->pkg)&& $r->shipment->scanAfter8h()&&!empty($r->mdata['inTransit'])) {
                    $r->shipment->status = 70;
                    $r->shipment->update(['status']);
                }
            }
        }

        $r->time = date('Y-m-d H:i:s');
        $r->save();
    }

    

}
?>