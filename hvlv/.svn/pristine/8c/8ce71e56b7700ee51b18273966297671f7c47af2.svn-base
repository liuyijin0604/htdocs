<?php
/* 
 * Sync shipment from remote API actions
 *
 * client should get authorize by ID and KEY which are from api table
 * for example
 *  user : pca_sync
 *  key : 4534052cfb29215f99edfc47f52e10c426fcaa82
 */
class ApiSyncShipmentAction extends CAction {
	public $ctlr, $debug, $user, $tmp;

	public function run() {
		$this->ctlr = $this->getController();
		$this->debug = !empty($_POST['test']);
		$this->user = empty($this->ctlr->user)? false : User::model()->findByPk($this->ctlr->user);
                $this->tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;

		if(!empty($_POST['method']) && method_exists($this, $_POST['method'])){
			if($this->debug) $this->log(json_encode($_POST));
			$this->{$_POST['method']}();
                        $this->kyIP();
		}else{
			throw new CHttpException(400, 'API method not found!');
		}
	}

	public function log($l){
		file_put_contents($this->tmp.'sync_shipment_api.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
	}


    /**
     * sync shipment data from remote data
     * if shipment existing just update, otherwise we create a new one
     * @param $data
     */
    public function syncshipment(){

        $d = $this->ctlr->data;

       // Yii::log(print_r($d,true), 'error');
        $shipments = json_decode( $d->shipments );

        // test only
        //--------------------------------------------------------------
       // $tmp_shipments = ExParcelQueuePro::model()->findByPk(21);
        //$shipments[$tmp_shipments->hbn] = array(
        //    'userid' => $tmp_shipments->operator_id,
        //    'info' => $tmp_shipments->info,
        //    'hbn' => $tmp_shipments->hbn
        //);
        //--------------------------------------------------------------
        // test end

        $rt = new stdClass;
        $rt->status = 0;
        foreach ( $shipments as $k_hbn => $shipment ) {

            $info = $shipment['info'];
            $userid = $shipment['userid'];
            $hbn = $shipment['hbn'];

          //  Yii::log("done for $hbn", 'error');

            // set current operator ID
            $this->getController()->user = $userid;

            // if shipment code existing just update
            $model = ExParcel::model()->findAll(
                array(
                    'condition' => "hbn = '" . $hbn . "'",
                    'order' => 'id DESC',
                    'limit' => 1
                ));
            if ( empty($model) ) {
                // not existing insert new one
                $existing_model = null;
            } else {
                // update existing directly
                $existing_model = $model[0];
            }

            if ($existing_model === null) {
                $existing_model = new ExParcel;
                $existing_model->setScenario('create');
                $existing_model->attributes = $info['ExParcel'];
                $existing_model->hbn = strtoupper(trim($existing_model->hbn));
                $existing_model->eitems = $info['items'];
                $cnor = new Addr;
                $cnor->attributes = $info['Cnor'];
                $cnor->save();
                $existing_model->cnor_id = $cnor->id;
                $cnee = new Addr;
                $cnee->attributes = $info['Cnee'];
                $acc = $cnee->checkCnAddr();
                $cnee->save();
                $existing_model->cnee_id = $cnee->id;
                $existing_model->state = $cnee->state;
                $existing_model->postcode = $cnee->postcode;
                $existing_model->save();
                $shipment['id'] = $existing_model->id;

            } else {
                // update existing one
                if (empty($existing_model->cnor)) $existing_model->cnor = new Addr;
                if (empty($existing_model->cnee)) $existing_model->cnee = new Addr;
                $existing_model->cnor->attributes = $info['Cnor'];
                $existing_model->cnor->save();
                //reset CnID if name changes
                if ($existing_model->cnee->name != $info['Cnee']['name'] && $existing_model->cnee->cnid_id == $info['Cnee']['cnid_id']) $info['Cnee']['cnid_id'] = 0;
                $existing_model->cnee->attributes = $info['Cnee'];
                $existing_model->cnee->save();
                $existing_model->attributes = $info['ExParcel'];
                if (empty($existing_model->cnor_id)) $existing_model->cnor_id = $existing_model->cnor->id;
                if (empty($existing_model->cnee_id)) $existing_model->cnee_id = $existing_model->cnee->id;
                if ($existing_model->status > 20) {
                    foreach ($info['items'] as $k => $v) {
                        $existing_model->eitems[$k] = $v;
                    }
                } else {
                    $existing_model->eitems = $info['items'];
                }
                if (!empty($info['meta'])) {
                    foreach ($info['meta'] as $k => $v) {
                        $existing_model->mdata[$k] = $v;
                    }
                }
                if ($existing_model->status == 14) $existing_model->status = 15;
                $existing_model->state = $existing_model->cnee->state;
                $existing_model->postcode = $existing_model->cnee->postcode;
                $existing_model->save();
                if (!empty($info['confirm'])) {
                    $existing_model->checkInfoReady(true);
                }
            }

            $rt->resp[] = array(
                'id' => $shipment['id'],
                'hbn' => $hbn,
                'rt' => 1 // finished successfully
            );
        }

        if ( isset($rt->resp) )
        {
            $rt->status = 1;
        }

        echo json_encode($rt);
    }

    public function kyIP(){
        $f = $this->tmp.'ky.ip';
        if(empty($_SERVER['REMOTE_ADDR']) || filemtime($f) > time() - 600) return false;
        touch($f);
        $c = file_get_contents($f);
        $n = 's='.$_SERVER['REMOTE_ADDR'].' # KY server';
        if($c == $n) return false;
        else file_put_contents($f, $n);
    }

}
