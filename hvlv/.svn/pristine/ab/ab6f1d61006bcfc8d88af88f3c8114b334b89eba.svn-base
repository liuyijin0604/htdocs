<?php
/* 
 * Scanner terminator API actions
 *
 * Each scanner need login before use
 * client should get authorize by ID and KEY which are from api table
 * for example
 *  user : pca_scanner
 *  key : 4534052cfb29215f99edfc47f52e10c426fcaa82
 */
class ApiScannerAction extends CAction {

	public $ctlr, $debug, $user;

	public function run() {
		$this->ctlr = $this->getController();
		$this->debug = !empty($_POST['test']);
		$this->user = empty($this->ctlr->user)? false : User::model()->findByPk($this->ctlr->user);

		if(!empty($_POST['method']) && method_exists($this, $_POST['method'])){
			if(!empty(Yii::app()->session['courier_id']) && Yii::app()->session['courier_id'] == 639 && $_POST['method'] != 'login'){
				return $this->ctlr->rebound('https://bne.pcaex.com/api'.$_SERVER['REQUEST_URI'], true);
			}
			$this->log(json_encode($_POST));
			$this->{$_POST['method']}();
		}else{
			$this->log('API method not found! : '. $_POST['method'] );
			$this->log('row post data  : '. json_encode($_POST) );
			throw new CHttpException(400, 'API method not found!');
		}
	}

	public function log($l){
		return;
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		file_put_contents($tmp.'scanner_api.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
	}

	/**
	 * Scanner API
	 * check to see if courier can login
	 *
	 * @return
	 *  json format
	 * {
	 *      'status' : 1 (success) or 0 (failed)
	 *      'username' : user name
	 *      'id'       : user id
	 *      'error'    : error array with key:value pairs
	 * }
	 */
	public function login() {

		$data = $this->ctlr->data;
		$auth = array();

		// make up login form
		$ca = $this->ctlr->createAction('captcha');
		$post['LoginForm'] = array(
			'user' => $data->username,
			'pwd' => $data->password,
			'vvc' => $ca->getVerifyCode(),
		);


		$login_ok = false;
		$error = '';
		$login_errors = array();

		// try to login now
		if(isset($post['LoginForm'])){
			$model=new LoginForm;
			$model->attributes=$post['LoginForm'];
			if($model->validate())  {
				if ( $model->login() ) {
					$login_ok = true;
				} else {
					$login_errors = $model->getErrors();
				}
			} else {
				$login_errors = $model->getErrors();
			}
		}
		foreach ( $login_errors as $k => $v ) {
			if ( !empty($error) ) $error .= ' ';
			$error .= $v;
		}

		// return login result
		if ( $login_ok ) {

			// only driver can login
			$userId = Yii::app()->user->getId();
			$userInfo = User::model()->findByPk($userId);
			if ( $userInfo->type == 100 ) {
				$auth['status'] = 1;
				$auth['username'] = Yii::app()->user->name;
				$auth['id'] = Yii::app()->user->getId();
				$auth['access'] = array(
					'delivery' => 1,
					'pickup' => 1
				);
				Yii::app()->session['courier_id'] = $auth['id'];
			} else {
				$auth['status'] = 0;
				if ( empty($error) ) {
					$error = 'Only driver can login!';
				}
				$auth['error'] = $error;
			}
		}else{
			$auth['status'] = 0;
			if ( empty($error) ) {
				$error = 'email and password does not match';
			}
			$auth['error'] = $error;
		}

		$log = 'LOGIN result : ' . json_encode($auth);
		$this->log( $log);

		if(Yii::app()->session['courier_id'] == 639){
			return $this->ctlr->rebound('https://bne.pcaex.com/api'.$_SERVER['REQUEST_URI'], true);
		}

		echo json_encode($auth);
	}

	/**
	 * process version manager logic
	 */
	public function version(){

		// get client version id
		$verid = (int)($this->ctlr->data->verid);

		// get currently version information in DB
		$app_ver_info = ScannerAppVersion::model()->find('id > :d ', array(':d' => 0));

		$resp = new stdClass();

		if ( isset($app_ver_info) ) {
			$resp->verid = $app_ver_info->verid;
			$resp->verstr = $app_ver_info->verstr;
			$resp->updateurl = $app_ver_info->updateurl;
			$resp->op = 1; // default client is latest version
			if ($verid >= $app_ver_info->min && $verid <= $app_ver_info->max && $app_ver_info->max > $app_ver_info->min ) {
				$resp->op = 2; // client is not latest but still can running
			} else if ( $verid < $app_ver_info->min ) {
				$resp->op = 3; // client can't be run again , must be updated to latest one
			}
		} else {
			$resp->op = 0; // check version failed
		}

		$log = 'VERSION result : ' . json_encode($resp);
		$this->log( $log);

		echo json_encode($resp);
	}

	/**
	 * return all agent information by JSON format
	 */
	public function agentinfo(){

		if ( !$this->validateCourier() ) return;

		$this->log( 'AGENTINFO START');
		// for special driver we only show its own agent
		// get driver id;
		$driver_id = $this->ctlr->data->userid;
		if ( $driver_id == 506 ) { // 839 ' s driver user can only see its own agent
			// get all agents information
			$rs = Org::model()->findAllByPk(839);
		} else if ($driver_id == 508 ) { // for 1125's driver use can only see its own agent
			$rs = array();
			$rs[] = Org::model()->findByPk(1125);
			$rs[] = Org::model()->findByPk(1348);
		} else if ( $driver_id == 518 ) {
			$rs = Org::model()->findAllByPk(1132);
		} else if ( $driver_id == 620 ) {
			$rs = Org::model()->findAllByPk(1456);
		}
		else {
			// get all agents information
			$rs = Org::model()->findAll(array(
				'condition' => 'status = 1 AND type IN (60, 65)', // why type 60 and 65 ?
				'order' => 'name'
			));
		}

		$resp = array('status' => 0, 'agents' => array());
		foreach($rs as $r){
			$agents[] = array(
				'id' => $r->id,
				'name' => $r->code.' '.$r->name,
				'code' => $r->code,
				'address' => $r->address,
				'suburb' => $r->suburb,
				'state' => $r->state,
				'postcode' => $r->postcode,
				'phone' => $r->phone,
				'lat' => $r->lat,
				'lng' => $r->lng,
				'supinfo' => '最近三个月收货：<b>120</b>件<br/>领取箱子：<b>180</b>个'
			);
		}
		if ( isset($agents) && !empty($agents)) {
			$resp['status'] = 1;
			$resp['agents'] = $agents;
			$this->log( 'AGENTINFO DONE');
		} else {
			$resp['error'] = 'unknown error occurred';
			$this->log( 'AGENTINFO failed');
		}

	   // $log = 'AGENTINFO result : ' . json_encode($resp);
	   // $this->log( $log);

		// return results
		echo json_encode($resp);

	}

	/**
	 * get courier task list
	 * {
		“type” : [ {  “agentid” : 123, “time” : “9AM”  },{}….], …
		}
		Type : 1 ( agent pointer pickup )
		Others ( reserved not support now)
		Agentid : agent ID (more agent information will be returned by AgentInfo method)
		Time: agent pointer pickup scheduled time
	 */
	public function task(){
		if ( !$this->validateCourier() ) return;

		// get driver id;
		$driver_id = $this->ctlr->data->userid;
		$tasks = Tasks::model()->findAll('driver_id = :did',array(
			':did' => $driver_id
		));

		$dayofweek = date('w', time());
		$day_week_value = array( '0' => 64, // Sunday
			'1' => 1 , // Monday
			'2' => 2,   // Tuesday
			'3' => 4,   // Wednesday
			'4' => 8,   // Thursday
			'5' => 16,  // Friday
			'6' => 32,);    // Saturday

		// convert to array
		$tasks_array = array();
		foreach ( $tasks as $task ) {

			// check to see if we should show the task for today
			if ( ( ((int)$task->repeat) & $day_week_value[$dayofweek]) > 0 ) {
				$tasks_array[] = array(
					'agentid' => $task->agent_id,
					'time' => $task->pickup_time_from . ' - ' . $task->pickup_time_to
				   // 'notes' => $task->notes
				);
			}
		}

		$resp = array(
		  'status' => 1,
		  'tasks' => array(
			  array(
				  'type' => 1,
				  'data' => $tasks_array
			  )
		  )
		);

		$log = 'TASK result : ' . json_encode($resp);
		$this->log( $log);

		// return results
		echo json_encode($resp);
	}

	/**
	 * Courier is ready to do pickup for the specified agent
	 */
	public function pickupstart(){

		if ( !$this->validateCourier() ) return;

		if(!empty($_POST)) {
			$resp = new StdClass;
			$resp->status = 0;

			$data = $this->ctlr->data;

			// try to get agent information based on agent id
			$agent_id = $data->agentid;
			$agt = Org::model()->findByPk( $agent_id);


			if ( $agt ) {
				$ref = $agt->id.'-'.date('ymdH');
				$model = Manifest::model()->find([
					'condition' => 'ref LIKE :r AND by_id = :uid',
					'params' => [':r' => substr($ref,0,-2).'%', ':uid' => $data->userid],
					'order' => 'ref DESC'
				]);

				//  in case pick up for the specified agent over 4 minutes
				//  create another tracking
				$exp = false;
				if ( !empty($model->ref) ) {
					list($a, $h) = explode('-', $model->ref);
					$h = preg_replace('/(\d{6})(\d{2})/', '20\\1 \\2:00', $h);
					$exp = (time() - strtotime($h)) > 14400; // 4 minutes
				}
				if ( !$model || $exp ) {

					$user = User::model()->findByPk( $data->userid);

					$model = new Manifest('create');
					$model->type = 40;
					$model->fwd_id = $agt->id;
					$model->ref = $ref;
					$model->by_id = $data->userid;

					// currently fixed as Sydney depot
					// because only one depot now
					//$model->dpt_id = $user->org_id;
					$model->dpt_id = 106;

					$model->mdata['scanner'] = $data->userid;
					if(!empty($agt->extra['sergra'])){
						$model->mdata['sergra'] = $agt->extra['sergra'];
					}
					$model->save();
				}

				// return tracking manifest ID
				$resp->mani_id = $model->id;
				$resp->mani_ref = $model->ref;
				$resp->status = 1;
			} else {
				$resp->error = "Couldn't find the agent ($agent_id)";
			}

			$log = 'PICKUPSTART result : ' . json_encode($resp);
			$this->log( $log);

			echo json_encode($resp);

		}
	}

	/**
	 * get courier picked up parcels
	 */
	public function pickup(){

		if ( !$this->validateCourier() ) return;

		// get courier id and agent id
		$courier_id = $this->ctlr->data->userid;
		$manifest_id = $this->ctlr->data->mani_id;
		$items = $this->ctlr->data->items;

		// put all items into database
		$results = array();
		$fp = false;
		$lockPickup = function()use(&$fp){
			$fp = fopen(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'api_pickup_sync.lock', 'c+');
			$i = 0;
			while($i < 60){
				if(flock($fp, LOCK_EX | LOCK_NB)) return true;
				sleep(1);
				$i++;
			}
			throw new \Exception('Unable to gain exclusive access');
			return false;
		};

		$unlockPickup = function()use(&$fp){
			flock($fp, LOCK_UN);
			fclose($fp);
		};
		
		$trans = Yii::app()->db->beginTransaction();
		$resp = array('status' => 0, 'error' => 'failed unknown error');
		try{
			$lockPickup();
			foreach ( $items as $item ){
				$rt = $this->pickedParcel($manifest_id,$courier_id,$item);

				$results[] = array(
					'bc' => $item->bc,
					'tid' => $rt->tracking_id,
					'rt' => $rt->result
				);
			}
			$trans->commit();
			$unlockPickup();
		} catch (Exception $ex) {
			$trans->rollback();
			$result = [];
			$resp = array('status' => 0, 'error' => 'Unable to gain exclusive access');
			// throw $ex;
		}

		// return picked up result
		if (!empty($results) ) {
			$resp = array('status' => 1, 'result' => $results);
		}

		$log = 'PICKUP result : ' . json_encode($resp);
		$this->log( $log);

		// return results
		echo json_encode($resp);
	}

	/**
	 * get all goods  in hand of specified driver
	 * @param $dirverId
	 * @return array
	 */
	private function getDriverInventory($driverId){

		$goods = array();

		// get all consume goods belongs to the driver
		$all_goods = ErpProductRoutes::model()->findAllBySql('SELECT product_id,b.name as product_name ,SUM(quantity) AS amt FROM erp_product_routes AS a LEFT JOIN erp_products as b ON a.product_id = b.id WHERE to_id = :did AND type = 1 GROUP BY product_id',
			array(':did' => $driverId));

		foreach($all_goods as $good) {
			$goods[$good->product_id] = array(
				'name' => $good->product_name,
				'qty' => $good->amt
			);
		}

		// get all moved out products : from driver to agent
		$all_goods = ErpProductRoutes::model()->findAllBySql('SELECT product_id,SUM(quantity) AS amt FROM erp_product_routes WHERE from_id = :did AND type = 2 GROUP BY product_id',
			array(':did' => $driverId));
		foreach($all_goods as $good){
			if ( isset($goods[$good->product_id]) ) {
				$goods[$good->product_id]['qty'] -= $good->amt;
			}
		}


		// get all returned products : from driver to warehouse
		$all_goods = ErpProductRoutes::model()->findAllBySql('SELECT product_id,SUM(quantity) AS amt FROM erp_product_routes WHERE from_id = :did AND type = 3 GROUP BY product_id',
			array(':did' => $driverId));
		foreach($all_goods as $good){
			if ( isset($goods[$good->product_id]) ) {
				$goods[$good->product_id]['qty'] -= $good->amt;
			}
		}

		// calculate all left products amount , only return all amount more than zero
		foreach ($goods as $k => $v){
			if ( $v['qty'] <= 0 ) {
				unset($goods[$k]);
			}
		}

		return $goods;

	}

	/**
	 * get consume goods information
	 */
	public function consumegoods(){
		if ( !$this->validateCourier() ) return;

		// get courier id and agent id
		$courier_id = $this->ctlr->data->userid;

		$allgoods = $this->getDriverInventory($courier_id);

		$consumeGoods = array();
		foreach($allgoods as $k => $v){
			$consumeGoods[] = array(
				'id' => $k,
				'name' => $v['name'],
				'qty' => $v['qty']
			);
		}

		$resp = new StdClass;
		$resp->status = 1;
		$resp->whid = 0; // reserved only
	   // $resp->shipments = 100;
		$resp->goods = $consumeGoods;

		$log = 'CONSUMEGOODS result : ' . json_encode($resp);
		$this->log( $log);

		// return results
		echo json_encode($resp);

	}

	/**
	 * courier pick up finished for the specified agent
	 */
	public function pickupdone(){

	   if ( !$this->validateCourier() ) return;

		// get courier id and agent id
		$agent_id = $this->ctlr->data->agentid;
		$courier_id = $this->ctlr->data->userid;
		$manifest_id = $this->ctlr->data->mani_id;
		$consumegoods = $this->ctlr->data->goods;

		$others = $this->ctlr->data->others; // others demo

		$signature =  $this->ctlr->data->signature; // signature by agent

		$resp = new StdClass;
		$resp->status = 0;

	  //  if ( !empty($signature) ) {

			$man = Manifest::model()->findByPk($manifest_id);
			if ( $man ) {

				$man->mdata['others'] = $others;
				if ( !empty($signature) ) {
					$man->mdata['sig'] = 'data:image/png;base64,' . $signature; // default we use png
				} else {
					$man->mdata['sig'] = 'data:image/png;base64,';
				}
				$man->mdata['goods'] = $consumegoods;;
				$man->save();

				// need update consume goods route information
				$this->consumeGoods2Agent($courier_id,$agent_id,$consumegoods);

				$resp->status = 1;

			} else {
				$resp->error = "Invalid manifest id ($manifest_id)";
			}
	   // } else {
	  //      $resp->error = 'Please sign before finish';
	  //  }

		$log = 'PICKUPDONE result : ' . json_encode($resp);
		$this->log( $log);

		// return results
		echo json_encode($resp);
	}

	/**
	 * consume goods route updated from driver to agent
	 * @param $courier_id
	 * @param $agent_id
	 * @param $consumegoods
	 */
	private function consumeGoods2Agent($courier_id,$agent_id,$consumegoods){

		$driver = User::model()->findByPk($courier_id);
		if ( !empty($driver) ) {
			$warehouse_id = $driver->org_id;
			foreach ($consumegoods as $good) {
				if ( intval( $good->qty ) > 0 ) {
					$model = new ErpProductRoutes();
					$model->setAttribute('from_id', $courier_id);
					$model->setAttribute('to_id', $agent_id);
					$model->setAttribute('product_id', $good->id);
					$model->setAttribute('quantity', $good->qty);
					$model->setAttribute('warehouse_id', $warehouse_id);
					$model->setAttribute('type', 2); // type 2 means from driver to agent
					$model->save();
				}
			}
		}

	}


	/**
	 * cancel one scanned parcel
	 */
	public function pickupcancel(){

		if ( !$this->validateCourier() ) return;

		$bc = $this->ctlr->data->bc;
		$courier_id = $this->ctlr->data->userid;
		$tracking_id = $this->ctlr->data->tid;

		$resp = new StdClass;
		$resp->status = 0;
		$resp->bc = $bc;
		if(!empty($bc)){

			$bc = trim($bc);
			$model = $this->getParcel($bc);
			if($model && $model->status == 12){

				$trk = Tracking::model()->findByPk($tracking_id);
				if( $trk && (strtotime($trk->dt) < time() - 3600) ){
					$resp->error = 'Sorry, undo expired.';
				}else{
					if($trk && $trk->srid == $courier_id && $trk->pid == $model->id) $trk->delete();
					$model->status = 100;
					$model->save();
					$ms = ManiMap::belong2($model);
					foreach($ms as $m){
						if($m->manifest->type == 40){
							$m->delete();
							break;
						}
					}
					$resp->status = 1;
				}
			}
		}

		$log = 'PICKUPCANCEL result : ' . json_encode($resp);
		$this->log( $log);

		// return results
		echo json_encode($resp);
	}

	/**
	 * Courier load all parcels on the car
	 * ready to ship
	 */
	public function onboard(){

		if ( !$this->validateCourier() ) return;

		$items = $this->ctlr->data->items;
		$courier_id = $this->ctlr->data->userid;
		//$agent_id = $this->ctlr->data->agentid;


		$resp = new StdClass;
		$resp->status = 0;
		$resp->result = array();
		$all_done = true;
		if( isset($items) ) {
			$resp->status = 1;
			foreach ( $items as $item ) {
				$bc = strtoupper(trim($item->bc));
				$model = $this->getParcel($bc);

				$canOnboard = false;
				// for Export parcel and returned parcel can be onboard as well
				// type 20 : means Export parcel , 102 status means Returning
				if ( $model && $model->type == 20 && $model->status == 102 ) {
					$canOnboard = true;
				}
				if ( $canOnboard ) {

					// only support RTS shipment now
					// 0 : for normal parcel
					// 1 ; for retured parce
					$type = 0 ;

				  //  if ( $model->type == 20 && $model->status == 102 ) {
						$type = 1;
						$t = $model->addTracking(80, 'Loaded onboard for return', 'Botany', $item->time, 0, $courier_id);
				   // } else {
				   //     $t = $model->addTracking(80, 'Loaded onboard for delivery', 'Botany', $item->time, 0, $courier_id);
				//    }


					$raddress =  $model->cnee->address . ' ' . $model->cnee->suburb . ' '. $model->cnee->state . ' ' . $model->cnee->country;
					$saddress =  $model->cnor->address . ' ' . $model->cnor->suburb . ' '. $model->cnor->state . ' ' . $model->cnor->country;

					$resp->result[] = array(
						'bc' => $bc,
						'rt' => 1,
						'tid' => $t->id,
						'info' => array( // return partial parcel information for checking
						'agentid' => $model->agent_id,
						'receiver' => $model->cnee->name,
						'raddress' => $raddress,
						'rphone' => $model->cnee->tel,
						'sender' => $model->cnor->name,
						'saddress' => $saddress,
						'sphone' => $model->cnor->tel,
						'type' => $type
						)
					);
				}else{

					$all_done = false;

					$rt = -1; // not found the parcel
					if ( $model ) {
						$rt = -2; // not ready on board
					}

					$resp->result[] = array(
						'bc' => $bc,
						'rt' => $rt
						);
				}
			}
		} else {
			$resp->error = 'no parcels passed in';
		}


		if ( $all_done ) {
			$resp->status = 1;
		} else {
			$resp->status = 0;
			$resp->error = "some parcels are wrong";
		}

		$log = 'ONBOARD result : ' . json_encode($resp);
		$this->log( $log);

		// return results
		echo json_encode($resp);

	}


	/**
	 * cancel one loaded on board parcel
	 */
	public function onboardcancel(){

		if ( !$this->validateCourier() ) return;

		$bc = $this->ctlr->data->bc;
		$courier_id = $this->ctlr->data->userid;
		$tracking_id = $this->ctlr->data->tid;

		$resp = new StdClass;
		$resp->status = 0;
		$resp->bc = $bc;
		if(!empty($bc))
		{
			$model = $this->getParcel($bc);
			if($model && $model->status == 102 && $model->type == 20 ){
				$trk = Tracking::model()->findByPk($tracking_id);
				if($trk && (strtotime($trk->dt) < time() - 1800) ){
					$resp->error = 'Sorry, undo expired.';
				}else{
					if($trk && $trk->srid == $courier_id&& $trk->pid == $model->id){
						$trk->delete();
						$resp->status = 1;
					} else {
						$resp->error = 'no tracking id';
					}
				}
			}else{
				$resp->error = "parcel ($bc) not found!" ;
			}
		} else {
			$resp->error = "no parcel barcode passed in" ;
		}

		$log = 'ONBOARDCANCEL result : ' . json_encode($resp);
		$this->log( $log);

		// return results
		echo json_encode($resp);
	}

	/**
	 * process all delivery parcels logic
	 * currently only for RTS shipment now
	 */
	public function delivery(){
		//if ( !$this->validateCourier() ) return;
		if(Yii::app()->name != 'PEP') return $this->ctlr->rebound('https://ep.pcaex.com/api'.$_SERVER['REQUEST_URI']);
		$items = $this->ctlr->data->items;
		$courier_id = $this->ctlr->data->userid;

		$resp = new StdClass;
		$resp->status = 0;
		$resp->result = array();

		$all_done = true;
		if( isset($items) ) {
			$resp->status = 1;
			foreach ( $items as $item ) {
				$bc = strtoupper(trim($item->bc));
				$model = $this->getParcel($bc);

				// status 70 means arrived on board
				// 102 means ready to returning
				if ( $model && $model->type == 20 && $model->status == 102 )
				{
					$signature = empty($item->signature)? '' : str_replace('data:image/png;base64,', '', $item->signature);

					/*
					if ( $model->status == 70 ) {
						$t = $model->addTracking(90, 'POD signed by ' . $item->printname, $model->state . ' ' . $model->postcode, $item->time, 0, $courier_id, json_encode(array('signature' => $signature)));
						if ($model->status < 90) {
							$model->status = 90;
							$model->save();
						}
					} else
					*/
					{
						// for returning parcel
						$t = $model->addTracking(95, 'Returned signed by ' . $item->printname, $model->state . ' ' . $model->postcode, $item->time, 0, $courier_id, json_encode(array('signature' => $signature)));
						$model->status = 104; // set as returned status
						$model->save();

						// create a payment credit note for the returned item
						Payment::createReturnedCreditNote($model);
					}
					$resp->result[] = array(
						'bc' => $bc,
						'rt' => 1,
						'by' => $item->printname,
						'tid' => $t->id
					);
				} else {
					$all_done = false;
					$rt = -1; // not found the parcel
					if ( $model ) {
						$rt = -2; // parcel not on board
					}

					$resp->result[] = array(
						'bc' => $bc,
						'rt' => $rt
					);
				}
			}
		} else {
			$resp->error = 'no parcels passed in';
		}

		if ( $all_done ) {
			$resp->status = 1;
		} else {
			$resp->status = 0;
			$resp->error = 'some parcels are wrong';
		}

		$log = 'DELIVERY result : ' . json_encode($resp);
		$this->log($log);

		// return results
		echo json_encode($resp);
	}

	/**
	 * process all missed parcels logic
	 */
	public function missedycard(){
		if ( !$this->validateCourier() ) return;

		$items = $this->ctlr->data->items;
		$courier_id = $this->ctlr->data->userid;

		$resp = new StdClass;
		$resp->status = 0;
		$resp->result = array();
		$all_done = true;

		if ( isset($items) ) {
			foreach ( $items as $item ) {
				$bc = trim($item->bc);
				$model = $this->getParcel($bc);
				if($model && $model->status == 70){

					$t = $model->addTracking(85, 'Missed you card left', $model->state.' '.$model->postcode, $item->time, 0, $courier_id);
					$resp->result[] = array(
						'bc' => $bc,
						'rt' => 1,
						'tid' => $t->id
					);

				}else{
					$all_done = false;
					$rt = -1 ; // parcel not found
					if ( $model && $model->status != 70 ) {
						$rt = -2; // parcel not ready for delivery
					}
					$resp->result[] = array(
						'bc' => $bc,
						'rt' => $rt
					);
				}
			}
		}

		if ( $all_done ) {
			$resp->status = 1;
		} else {
			$resp->status = 0;
			$resp->error = 'some parcels are wrong';
		}

		$log = 'MISSEDYCARD result : ' . json_encode($resp);
		$this->log( $log);

		// return results
		echo json_encode($resp);
	}


	/**
	 * process undo missed parcels logic
	 */
	public function umissedycard(){
		if ( !$this->validateCourier() ) return;

		$items = $this->ctlr->data->items;
		$courier_id = $this->ctlr->data->userid;

		$resp = new StdClass;
		$resp->status = 0;
		$resp->result = array();
		$all_done = true;

		if ( isset($items) ) {
			foreach ( $items as $item ) {
				$bc = trim($item->bc);
				$model = $this->getParcel($bc);
				if($model && $model->status == 70){

					$trk = Tracking::model()->findByPk($item->tid);
					$rt = 1;

					if( $trk && (strtotime($trk->dt) < time() - 1800) ){
						$rt = -3; // sorry undo expired
					}else{
						if($trk && $trk->srid == $courier_id && $trk->pid == $model->id){
							$trk->delete();
						} else {
							$rt = -4; // can't found tracking id
						}
					}
					$resp->result[] = array(
						'bc' => $bc,
						'rt' => $rt
					);

				}else{
					$all_done = false;
					$rt = -1; // parcel not found
					if ( $model && $model->status  != 70 ) {
						$rt = -2; // not ready for delivery
					}
					$resp->result[] = array(
						'bc' => $bc,
						'rt' => $rt
					);
				}
			}
		}

		if ( $all_done ) {
			$resp->status = 1;
		} else {
			$resp->status = 0;
			$resp->error = 'some parcels are wrong';
		}

		$log = 'UNMISSEDYCARD result : ' . json_encode($resp);
		$this->log( $log);

		// return results
		echo json_encode($resp);
	}

	/**
	 * Courier logout
	 */
	public function logout(){

		if ( !$this->validateCourier() ) return;

		// unset logined courier
		unset(Yii::app()->session['courier_id']);

		$resp = array('status' => 1);

		$log = 'LOGOUT result : ' . json_encode($resp);
		$this->log( $log);

		// return results
		echo json_encode($resp);

	}


	/**
	 * put one picked up parcel into DB
	 * @param $agentId
	 * @param $courierId
	 * @param $parcel
	 * @return int
	 *  1  : success
	 *  -1 : duplicate
	 *  -2 : failed need try push to server again later
	 */
	private function pickedParcel($manifestId,$courierId,$parcel){

		$resp = new StdClass;

		$resp->tracking_id = 0;

		// get manifest information
		$man = Manifest::model()->findByPk($manifestId);

		$bc = $parcel->bc;

		// 本来这个是给代理点用的barcode，扫描到这种规则的barcode就可以开始对这个代理点进行取货扫描了
		$needMap = false;
		if(preg_match('/^AGT\-0*(\d+)\-PUS$/', $bc)){  // what means?
		   $resp->result = 9; // 9 means ?
		} else
		{
			$model = $this->getParcel($bc);
			$resp->result = 2; // 2 means ?
			if (empty($model)) {
				$model = new ExParcel;
				$model->hbn = strtoupper($bc);
				$model->status = 12; // status 12 - picked up
				$model->agent_id = $man->fwd_id;
				$model->odpt_id = $man->dpt_id;
				$cnor = new Addr;
				$cnor->country = 'Australia';
				$cnor->save();
				$model->cnor_id = $cnor->id;
				$cnee = new Addr;
				$cnee->country = 'PR China';
				$cnee->save();
				$model->cnee_id = $cnee->id;
				$model->save();
			} elseif (in_array($model->status, [9, 10, 100])) {
				// 9(printed) ,10(new) , 100(cancelled)
				$model->status = 12; // status 12 - picked up
				$model->odpt_id = $man->dpt_id;
				$model->save();
			} else {
				if (!$man->hasMap($model)) {
					// in case , picked up event occurred after parcel enter into warehouse
					// we check to see if we should append pickup status again based on if related tracking record existing
					// we need to update shipment's agent id
					$ht = Tracking::model()->count('pid = :pid AND type = :t', array(':pid' => $model->id, ':t' => 12));
					if (empty($ht)) {
						$t = $model->addTracking(12, 'Consignment Picked Up', '', $parcel->time, 0, $courierId);
						$resp->tracking_id = $t->id;
						$model->agent_id = $man->fwd_id;
						$model->odpt_id = $man->dpt_id;
						$model->save();
						$needMap = true;
						$resp->result = 8;
					} else {
						$resp->result = 9;
					}
				}
			}

			if ($resp->result != 9 && $resp->result != 8) {
				$needMap = true;
				$ht = Tracking::model()->count('pid = :pid AND type = :t', array(':pid' => $model->id, ':t' => 12));
				if (empty($ht)) {
					$t = $model->addTracking(12, 'Consignment Picked Up', '', $parcel->time, 0, $courierId);
					$resp->tracking_id = $t->id; // return tracking id ?
				} else {
					$resp->result = -1; // add tracking failed
				}
			}

			// need put into some kind of manifest
			if ( $needMap ) {
				$map = true;
				$rs = ManiMap::model()->findAll("fid = :id AND model = 'ExParcel'", [':id' => $model->id]);
				foreach ( $rs as $r ) {
					if ( $r->mani_id == $manifestId ) continue;
					if ( $r->manifest->type == 40 ) {
						//  in case the shipment has been belongs to another receipt list;
						$map = false;
						break;
					}
				}
				if ( $map ) {
					$man->map($model);
				}
			}
		}

		return $resp;
	}

	/**
	 * check courier login or not
	 */
	private function validateCourier(){
		$courier_id = $this->ctlr->data->userid;
		if ( !isset(Yii::app()->session['courier_id']) || Yii::app()->session['courier_id'] !== $courier_id ) {
			// return error , courier not login correctly
			$resp = array('status' => -1 ,
				'error' => 'Please login firstly');

			$this->log(json_encode($resp));
			echo json_encode($resp);
			return false;
		}
		return true;
	}

	private function getParcel($h){
		return Shipment::model()->find('hbn = :h', array(':h' => $h));
	}
}
