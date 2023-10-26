<?php

/**
 * This is the model class for table "cnl_msg".
 *
 * The followings are the available columns in table 'cnl_msg':
 * @property string $id
 * @property integer $status
 * @property integer $type
 * @property string $no
 * @property string $sndr
 * @property string $rcvr
 * @property string $msg
 * @property integer $meta
 */
class CnlMsg extends CActiveRecord
{

	public static $states = [
		'10' => 'Received',
		'20' => 'Processed',
		'30' => 'Queued',
        '40' => 'Scheduled',
		'50' => 'Sent',
		'90' => 'Error',
		'100' => 'Cancelled',
	];

	public static $types = [
		11 => 'GFP_INTL_LINEHAUL_ORDER_NOTIFY',
		12 => 'GFP_INTL_LINEHAUL_ORDER_UPDATE_NOTIFY',
		15 => 'GFP_INTL_LINEHAUL_BOOKING_CONFIRMED_NOTIFY',
		19 => 'GFP_INTL_LINEHAUL_ORDER_CANCEL_NOTIFY',
		
		21 => 'GFP_WH_INBOUND_TALLY_CONFIRM_NOTIFY',

		101 => 'GFP_EX_LINEHAUL_PICKUP_CALLBACK',
		102 => 'GFP_EX_LINEHAUL_WH_ARRIVE_CALLBACK',
		103 => 'GFP_EX_LINEHAUL_ARRIVE_INTO_PORT_CALLBACK',

		111 => 'GFP_WH_INBOUND_RECEIPT_CALLBACK',
		112 => 'GFP_WH_PACK_COMPLETE_CALLBACK',
		119 => 'GFP_WH_INBOUND_ORDER_REJECT_CALLBACK',

		121 => 'GFP_ECUS_CLEARANCE_DOC_READY_CALLBACK',
		122 => 'GFP_ECUS_CLEARANCE_DOC_EXCEPTION_CALLBACK',
		123 => 'GFP_ECUS_EXCEPTION_CALLBACK',
		124 => 'GFP_ECUS_CLEARANCE_DONE_CALLBACK',
		
		131 => 'GFP_INTL_LINEHAUL_BOOKING_CONFIRMING_CALLBACK',
		132 => 'GFP_INTL_LINEHAUL_BOOKING_CALLBACK',
		133 => 'GFP_INTL_LINEHAUL_SHIPMENT_UPDATE_CALLBACK',
		134 => 'GFP_INTL_LINEHAUL_CARGO_RECEIPT_CALLBACK',
		135 => 'GFP_INTL_LINEHAUL_CARGO_UPDATE_CALLBACK',
		136 => 'GFP_INTL_LINEHAUL_DEPARTURE_CALLBACK',
		137 => 'GFP_INTL_LINEHAUL_ARRIVE_CALLBACK',
		138 => 'GFP_INTL_LINEHAUL_VAS_FEE_UPLOAD',
		139 => 'GFP_INTL_LINEHAUL_ORDER_REJECT_CALLBACK',
		140 => 'GFP_INTL_LINEHAUL_PRE_ALERT_CALLBACK',
	];

	public static $type_names = [
		11 => 'New Order',
		12 => 'Update Order',
		15 => 'Confirm Booking',
		19 => 'Cancel Order',
		
		21 => 'Tally Confirm',

		101 => 'Pickup',
		102 => 'Arrive Warehouse',
		103 => 'Arrive Port',

		111 => 'Inbound Receipt',
		112 => 'Pack Complete',
		119 => 'Inbound Reject',

		121 => 'Clearance Doc Ready',
		122 => 'Clearance Doc Problem',
		123 => 'Clearance Exception',
		124 => 'Clearance Done',
		
		131 => 'Confirm Booking',
		132 => 'Booking Sent',
		133 => 'Update Shipment',
		134 => 'Cargo Receipt',
		135 => 'Cargo Update',
		136 => 'Cargo Departure',
		137 => 'Cargo Arrival',
		138 => 'VAS Fees',
		139 => 'Order Reject',
	];

	public $mdata = [];
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'cnl_msg';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fid, status, dt, type, no, sndr, rcvr, msg, meta', 'safe'),
			array('fid, status, type, meta', 'numerical', 'integerOnly'=>true),
			array('no, sndr, rcvr', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, fid, status, dt, type, no, sndr, rcvr, msg, meta', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'odr' => [self::BELONGS_TO, 'CnlOrder', 'fid'],
		);
	}

	public function getStatus(){
		return isset(static::$states[$this->status])? Yii::t(strtolower(__CLASS__), static::$states[$this->status]) : $this->status;
	}

	public function getType(){
		return isset(static::$types[$this->type])? Yii::t(strtolower(__CLASS__), static::$types[$this->type]) : $this->type;	
	}

	public function beforeValidate(){
		foreach(['type'] as $k){
			if(is_string($this->{$k})) $this->_mapAttrVal($k);
		}
		return parent::beforeValidate();
	}

	private function _mapAttrVal($attr){
		$map = ['status' => 'states'];
		$v = in_array($attr, $map)? $map[$attr] : $attr.'s';
		if(!isset(self::$$v)) return false;
		foreach(self::$$v as $k => $v){
			if($v === $this->{$attr}){
				$this->{$attr} = $k;
				return true;
				break;
			}
		}
		return false;
	}

	public function beforeSave(){
		$this->meta = empty($this->mdata)? '' : json_encode($this->mdata);
		return parent::beforeSave();
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return parent::afterFind();
	}

	public static function saveMsg($d, $t=null){
  		$m = new CnlMsg;
  		$m->dt = empty($t)? date('Y-m-d H:i:s') : $t;
  		$m->msg = json_encode($d);
  		$m->status = 10;
  		$m->no = $d['msg_id'];
  		$m->sndr = $d['from_code'];
  		$m->rcvr = $d['partner_code'];
  		$m->type = $d['msg_type'];
  		$m->save();

  		return $m;
	}

	public static function createMsg(&$model, $t){
		switch($t){
			case 'GFP_INTL_LINEHAUL_BOOKING_CALLBACK':
			case 'GFP_INTL_LINEHAUL_SHIPMENT_UPDATE_CALLBACK':
				$vsl = empty($model->mdata['bk']['vsl'])? '' : $model->mdata['bk']['vsl'];
				$model->mdata['bk']['voy'] = strtoupper($model->mdata['bk']['voy']);
				$cc = $model->transportMode == 20? $cc = substr($model->mdata['bk']['voy'], 0, 2) : '';

				$j = '{"orderCode":"'.$model->orderCode.'","eta":"'.$model->mdata['bk']['eta'].' 00:00:00","etaTimezone":"UTC+8:00","etd":"'.$model->mdata['bk']['etd'].' 00:00:00","etdTimezone":"UTC'.date('P').'","pol":"'.$model->mdata['bk']['pol'].'","pod":"'.$model->mdata['bk']['pod'].'","blno":"'.$model->mdata['bk']['bln'].'","hbl":"'.$model->mdata['bk']['hbn'].'","routeList":[{"transportMode":"'.$model->getTransportMode().'","carrierCode":"'.$cc.'","vessel":"'.$vsl.'","voyage":"'.$model->mdata['bk']['voy'].'","pol":"'.$model->mdata['bk']['pol'].'","pod":"'.$model->mdata['bk']['pod'].'","eta":"'.$model->mdata['bk']['eta'].' 00:00:00","etaTimezone":"UTC+8:00","etd":"'.$model->mdata['bk']['etd'].' 00:00:00","etdTimezone":"UTC'.date('P').'","ladingBillNo":"'.$model->mdata['bk']['bln'].'","houseBillNo":"'.$model->mdata['bk']['hbn'].'"}]}';
			break;
			case 'GFP_EX_LINEHAUL_ARRIVE_INTO_PORT_CALLBACK':
				$j='{"orderCode":"'.$model->orderCode.'","operateTime":"'.date('Y-m-d H:i:s').'","operateTimezone":"UTC'.date('P').'","country":"AU","state":"NSW","city":"Kingsgrove","address":"6C The Crescent","zipcode":"2208","remark":"","feature":"","port":"'.$model->mdata['bk']['pol'].'"}';
			break;
			case 'GFP_INTL_LINEHAUL_DEPARTURE_CALLBACK':
				$vsl = empty($model->mdata['bk']['vsl'])? '' : $model->mdata['bk']['vsl'];
				$model->mdata['bk']['voy'] = strtoupper($model->mdata['bk']['voy']);
				$cc = $model->transportMode == 20? $cc = substr($model->mdata['bk']['voy'], 0, 2) : '';
				$j='{"orderCode":"'.$model->orderCode.'","operateTime":"'.date('Y-m-d H:i:s').'","operateTimezone":"UTC'.date('P').'","operator":"'.Yii::app()->user->name.'","port":"'.$model->mdata['bk']['pol'].'","atd":"'.$model->mdata['bk']['atd'].' 00:00:00","atdTimezone":"UTC'.date('P').'","remark":"","routeList":[{"transportMode":"'.$model->getTransportMode().'","carrierCode":"'.$cc.'","vessel":"'.$vsl.'","voyage":"'.$model->mdata['bk']['voy'].'","pol":"'.$model->mdata['bk']['pol'].'","pod":"'.$model->mdata['bk']['pod'].'","eta":"'.$model->mdata['bk']['eta'].' 00:00:00","etaTimezone":"UTC+8:00"}]}';
			break;
			case 'GFP_INTL_LINEHAUL_ARRIVE_CALLBACK':
				$j='{"orderCode":"'.$model->orderCode.'","operateTime":"'.date('Y-m-d H:i:s').'","operateTimezone":"UTC'.date('P').'","operator":"'.Yii::app()->user->name.'","port":"'.$model->mdata['bk']['pod'].'","ata":"'.$model->mdata['bk']['ata'].($model->mdata['bk']['ata'] == $model->mdata['bk']['atd']? ' 02' : ' 00').':00:00","ataTimezone":"UTC+8:00","remark":""}';
			break;
			case 'GFP_INTL_LINEHAUL_CARGO_RECEIPT_CALLBACK':
			case 'GFP_INTL_LINEHAUL_CARGO_UPDATE_CALLBACK':
				$c = $model->bCargos[0];
				$cl = [];
				if(!empty($c->containers)){
					foreach($c->containers as $p){
						if(empty($p['no'])) continue;
						$cl[] = ['containerNo' => $p['no'], 'type' => $p['type'], 'size' => $p['size']];
					}
				}
				$pl = [];
				if(!empty($c->packages)){
					foreach($c->packages as $p){
						if(empty($p['num']) || $p['packageUom'] != 'PLT') continue;
						$pl[] = ['palletNo' => $p['num'], 'height' => $p['height'], 'width' => $p['width'], 'length' => $p['length']];
					}
				}

				$j='{"orderCode":"'.$model->orderCode.'","operateTime":"'.date('Y-m-d H:i:s').'","operateTimezone":"UTC'.date('P').'","remark":"","cargo":{"totalPackages":"'.$c->totalPackages.'","totalUnits":'.$c->totalUnits.',"totalVolume":"'.$c->totalVolume.'","totalGrossWeight":"'.$c->totalGrossWeight.'","chargeWeight":'.$c->chargeWeight.',"chargeVolume":'.$c->chargeVolume.'},"documentList":'.$model->attDocs().',"containerList":'.json_encode($cl).',"palletList":'.json_encode($pl).'}';
			break;
			case 'GFP_INTL_LINEHAUL_PRE_ALERT_CALLBACK':
				$parties = $model->getParties();
				$emls = [];
				foreach($parties as $p){
					$ve = filter_var(trim($p->email), FILTER_VALIDATE_EMAIL);
					if($ve === false) continue;
					$emls[] = $ve;
				}
				if(empty($emls)) $emls = ['cainiao.notice@prioritycargo.com.au'];
				$subject = 'PRE-ALERT FOR '.$model->orderCode.' '.$model->focOrderCode.' /SHPR: '.(empty($parties['SHIPPER'])? '' : $parties['SHIPPER']['companyName']).' /CNEE: '.(empty($parties['CONSIGNEE'])? '' : $parties['CONSIGNEE']['companyName']).'';
				$body = 'FYI\n请点击下载文件:\n<a href=\"https://os.pcaex.com/filerepo/cnl/'.$model->id.'?type=BL\">Bill of Lading</a>\n<a href=\"https://os.pcaex.com/filerepo/cnl/'.$model->id.'?type=CI\">Commercial Invoice</a>\n<a href=\"https://os.pcaex.com/filerepo/cnl/'.$model->id.'?type=PL\">Packing List</a>';
				$j='{"orderCode":"'.$model->orderCode.'","operateTime":"'.date('Y-m-d H:i:s').'","operateTimezone":"UTC'.date('P').'","operator":"'.Yii::app()->user->name.'","blno":"'.$model->mdata['bk']['bln'].'","hbl":"'.$model->mdata['bk']['hbn'].'","remark":"","emailReceivers":"'.implode(',', $emls).'","emailTopic":"'.$subject.'","emailBody":"'.$body.'"}';
			break;
		}
		if(empty($j)) return false;
		$m = new CnlMsg;
		$m->fid = $model->id;
  		$m->dt = date('Y-m-d H:i:s');
  		$m->msg = $j;
  		$m->status = 30;
  		$m->no = '';
  		$m->sndr = CainiaoLinkAPI::getResourceCode($t);
  		$m->rcvr = 'fcp';
  		$m->type = $t;
  		return $m->save();
	}

	public function process(){
	  	if(method_exists($this, 'process_'.$this->type)){
			$d = json_decode($this->msg, true);
			$d['logistics_interface'] = preg_replace(['/\x{00a0}/u', '/\n/'], [' ', '\n'], $d['logistics_interface']);
			$d = json_decode($d['logistics_interface'], true);
	  		//print_r($d);
	  		$trans = Yii::app()->db->beginTransaction();
			try{
				if($this->{'process_'.$this->type}($d)){
					$this->status = 20;
					$this->save();
					$trans->commit();
				}else{
					$trans->rollback();
				}
			}catch (Exception $ex) {
				$trans->rollback();
				throw $ex;
			}
		}
	}

	public function send($debug = false){
		$api = new CainiaoLinkAPI($debug);
		if($api->request($this->prepMsg(), $this->getType())){
			if((!empty($api->result->success) && $api->result->success == 'true')){
				$this->status = 50;
				if($this->type == 137 && $this->odr->status < 90){
					$this->odr->status = 90;
					$this->odr->save();
				}
				$d = json_decode($this->msg);
				if(!empty($d->documentList)){
					foreach($d->documentList as $f){
						if(empty($f->fileData)) continue;
						if(preg_match('/^#CNLDOC:(\d+)#$/', $f->fileData, $m)){
							$doc = CnlDoc::model()->findByPk($m[1]);
							if(!empty($doc)){
								$doc->status = 50;
								$doc->save();
							}
						}
					}
				}
			}else{
				$this->status = 90;
				if(!empty($api->result->errorMsg)) $this->mdata['error'] = $api->result->errorMsg;
				if(!empty($this->odr->mdata['msg'][$this->type])){
					$this->odr->mdata['msg'][$this->type] = 0;
					$this->odr->custom_log_note = CnlMsg::$type_names[$this->type].' error: '.(empty($this->mdata['error'])? '' : $this->mdata['error']);
					$this->odr->save();
				}
			}
			$this->save();
		}
	}

	public function prepMsg(){
		$d = json_decode($this->msg);
		if(!empty($d->documentList)){
			foreach($d->documentList as $f){
				if(empty($f->fileData)) continue;
				if(preg_match('/^#CNLDOC:(\d+)#$/', $f->fileData, $m)){
					$doc = CnlDoc::model()->findByPk($m[1]);
					if(empty($doc)){
						$f->fileData = '';
					}else{
						$f->fileData = base64_encode(file_get_contents($doc->file->getFile()));
					}
				}
			}
		}
		return json_encode($d);
	}

	protected function process_11($d){
  		$o = CnlOrder::model()->find('orderCode = :c', [':c' => $d['order']['orderCode']]);
  		$neworder = false;
  		if(empty($o)){
  			$o = new CnlOrder;
  			$o->status = 10;
  			$neworder = true;
  		}

  		//$dp = ['cargoType', 'gmtModified', 'bizType', 'targetEta', 'pod', 'pol', 'incoterm', 'transportMode', 'orderCode', 'cargoReadyDate', 'containerLoad'];
  		//foreach($dp as $k) if(isset($d['order'][$k])) $o->{$k} = $d['order'][$k];
  		foreach($d['order'] as $k => $v){
  			if($k == 'status') continue;
  			if($o->hasAttribute($k)){
				$o->{$k} = $v;
			}else{
				$o->mdata[$k] = $v;
			}
  		}
  		if(!empty($d['serviceList'])){
  			$o->mdata['services'] = $d['serviceList'];
  		}
  		$o->save();
  		$this->fid = $o->id;
  		//var_dump($o->getErrors());

  		//items
  		if(!empty($d['itemOrderList'])){
	  		foreach($d['itemOrderList'] as $itm){
	  			$oi = CnlItem::model()->find('order_id = :oid AND itemOrderCode = :oc', [':oid' => $o->id, ':oc' => $itm['itemOrderCode']]);
	  			if(empty($oi)){
	  				$oi = new CnlItem;
	  				$oi->order_id = $o->id;
	  			}
	  			foreach($itm as $k=>$v){
	  				if($oi->hasAttribute($k)){
						$oi->{$k} = $v;
					}else{
						$oi->mdata[$k] = $v;
					}
	  			}
	  			$oi->save();
	  		}
	  	}

  		//parties
  		foreach($d['partyList'] as $pty){
  			$p = CnlParty::addParty($pty);
  			CnlParty2order::link($p, $o, $pty['type']);
  		}

  		//cargo
  		$c = CnlCargo::model()->find('order_id = :oid', [':oid' => $o->id]);
  		if(empty($c)){
  			$c = new CnlCargo;
  			$c->type = 10;
  			$c->order_id = $o->id;
  		}
  		$c->totalVolume = $d['cargo']['totalVolume'];
  		$c->totalGrossWeight = $d['cargo']['totalGrossWeight'];
  		$c->containers = $d['cargo']['containerList'];
  		$c->packages = $d['cargo']['packageList'];
  		$c->save();

  		if(!empty($d['documentList'])){
  			foreach($d['documentList'] as $d){
  				if(empty($d['url'])) continue;
  				$pi = pathinfo($d['url']);
				$ext = preg_replace('/\?.+/', '', $pi['extension']);
  				$fid = FileRepo::storeUrlFile($d['url'], $o->orderCode.'_'.$d['type'].'.'.$ext, 110 , 0);
  				if(!empty($fid)){
	  				$od = new CnlDoc;
  					$od->status = 10;
	  				$od->order_id = $o->id;
	  				$od->file_id = $fid;
	  				$od->type = $d['type'];
	  				$od->mdata['url'] = $d['url'];
	  				$od->save();
	  			}
  			}
  		}
  		$this->notify('Cainiao Order #'.$o->orderCode.' '.($neworder? 'Received' : 'Updated'), 'Please check system to follow up.');
  		return true;
	}

	protected function process_12($d){
		return $this->process_11($d);
	}

	protected function process_19($d){
		$o = CnlOrder::model()->find('orderCode = :c', [':c' => $d['orderCode']]);
		if(empty($o)) return false;
		$o->status = 100;
		$o->save();
  		$this->fid = $o->id;
  		$this->notify('Cainiao Order #'.$o->orderCode.' Cancelled', 'Please check system to follow up.');
		return true;
	}

	public function notify($subject, $body){
  		Emailog::sendEmailTo('cainiao.notice@prioritycargo.com.au', $body, $subject);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'fid' => 'FID',
			'status' => 'Status',
			'dt' => 'Time',
			'type' => 'Type',
			'no' => 'No',
			'sndr' => 'Sndr',
			'rcvr' => 'Rcvr',
			'msg' => 'Msg',
			'meta' => 'Meta',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search($pgn=true, $ps = 30, $ec = false){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('fid',$this->fid);
		$criteria->compare('status',$this->status);
		$criteria->compare('type',$this->type);
		$criteria->compare('dt',$this->dt, true);
		$criteria->compare('no',$this->no,true);
		$criteria->compare('sndr',$this->sndr,true);
		$criteria->compare('rcvr',$this->rcvr,true);
		$criteria->compare('msg',$this->msg,true);
		$criteria->compare('meta',$this->meta);
		$with = [];

		if(!empty($with)){
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if($ec) $criteria->mergeWith($ec);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'t.id DESC',
 			),
			'pagination'=> $pgn? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CnlMsg the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
