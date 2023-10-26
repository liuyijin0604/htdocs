<?php

/**
 * This is the model class for table "dxt_push_queue".
 *
 * The followings are the available columns in table 'dxt_push_queue':
 * @property string $id
 * @property integer $type
 * @property string $message
 * @property string $added_time
 * @property string $send_time
 * @property integer $status
 */
class DxtPushQueue extends CActiveRecord
{
	// message type:
	// 1 -  stock in message
	// 2 - stock out message
	// 3 - route message
	const MSGTYPE_STOCKIN = 1;
	const MSGTYPE_STOCKOUT = 2;
	const MSGTYPE_ROUTE = 3;
	const MSGTYPE_CONSOLREADY = 4;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'dxt_push_queue';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, message', 'required'),
			array('type, status', 'numerical', 'integerOnly'=>true),
			array('message', 'length', 'max'=>450),
			array('added_time, send_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, message, added_time, send_time, status', 'safe', 'on'=>'search'),
		);
	}

	public function beforeSave() {
		if ($this->isNewRecord)
			$this->added_time = new CDbExpression('NOW()');

		return parent::beforeSave();
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => 'Type',
			'message' => 'Message',
			'added_time' => 'Added Time',
			'send_time' => 'Send Time',
			'status' => 'Status',
		);
	}

	/**
	 * add stock in item into push queue
	 * @param $shipmentId
	 */
	public static function addStockIn($shipmentId){

		// get shipment weight now
		$p = ExAfs::model()->findByPk($shipmentId);
		if ( isset($p) ) {
			$model = new DxtPushQueue();
			$model->setAttribute('type',self::MSGTYPE_STOCKIN);
			$model->setAttribute('status',0);
			$model->setAttribute('ref',$p->ref);

			// ... TODO confirm with frank ? unit : KG or G ?
			$msg = array('epsID' => $p->ref,'inDate' => date('Y-m-d h:i:s'), 'gwet' => $p->weight,'vlm' => '0');
			$model->setAttribute('message',json_encode($msg) );
			$model->save();
			return true;
		}
		// shipment not existing
		return false;
	}

	/**
	 * add stock out information by parcel
	 * @param $p
	 */
	public static function addStockOutByParcel(&$p){

		// get stock out time
		// normally should be saved in meta field with key : dxt_departure_time
		if ( isset($p->mdata['dxt_departure_time']) ) {
			$outDate = $p->mdata['dxt_departure_time'];
		} else {
			$outDate = date('Y-m-d h:i:s'); // 出库时间
		}
		$model = new DxtPushQueue();
		$model->setAttribute('type',self::MSGTYPE_STOCKOUT);
		$model->setAttribute('ref',$p->ref);
		$model->setAttribute('status',0);
		$msg = array(
			'epsID' => $p->ref, // 快递ID
			'vessel' => $p->consol->airline, // 运输工具名称
			'voy' => $p->consol->flight, // 运输工具航次(航班)
			'ETD' => $p->consol->etd, // 离港日期
			'blNo' => $p->consol->no, // 提运单
			'portLoad' => $p->consol->pol, // 起运港
			'portDis' => $p->consol->pod, // 卸货港
			'outDate' => $outDate // 出库时间
		);
		$model->setAttribute('message',json_encode($msg) );
		$model->save();
	}


	/**
	 * push Dxt order into DXT's new console
	 * in order to send this parcel firstly
	 * once real order information arrived from DXT we send related information to DXT again
	 * @param $data
	 *     $data['hbn'] scan barcode
	 *     $data['weight]
	 */
	public static function pushDxtOrder2Console($data){
		include(Yii::app()->basePath.DIRECTORY_SEPARATOR .'commands/DxtCommand.php');
		$dxtCmd = new DxtCommand('pcadxt',null);
		$dxtCmd->pushDxtOrder2Console($data);
	}

	/**
	 * add stock out item into push queue
	 * @param $consolId
	 */
	public static function addStockOut(&$p){

		// get shipment weight now
		if ( isset($p) ) {

			// we only care about DXT console
			include(Yii::app()->basePath.DIRECTORY_SEPARATOR .'commands/DxtCommand.php');
			if ( $p->owner_id != DxtCommand::DXT_ORG_ID ) return;

			// only for Acknowledge console
			if ( $p->status != 40 && $p->status != 20  )  return;  // ... TODO confirm with frank?

			// put all parcel without weight information to another new console
			DxtCommand::wrapNoweightShipments($p->id);

			// get all parcels in this console
			$parcels = ExAfs::model()->findAll(array(
				'condition' => 'consol_id = :cid',
				'params' => array(
					':cid' => $p->id
				)
			));

			// push all parcels in push queue table
			foreach ( $parcels as $parcel ){
				// 20 means parcel status : Consolidated
				// .... TODO confirm with frank ?
				if ( $parcel->status != 20 ) continue;

				// in case there is no order information ( arrived later from DXT )
				// there will be no ref value
				// we should not push it into push queue
				// but we should save departure time now
				if ( empty($parcel->ref) )  {

					// save departure time in meta field
					// soon when real order information arrived will use it
					$parcel->mdata['dxt_departure_time'] = date('Y-m-d h:i:s');
					$parcel->save();
					continue;
				}

				$model = new DxtPushQueue();
				$model->setAttribute('type',self::MSGTYPE_STOCKOUT);
				$model->setAttribute('ref',$parcel->ref);
				$model->setAttribute('status',0);
				$msg = array(
					'epsID' => $parcel->ref, // 快递ID
					'vessel' => $p->airline, // 运输工具名称
					'voy' => $p->flight, // 运输工具航次(航班)
					'ETD' => $p->etd, // 离港日期

					// ..TODO confirm with frank ??
					'blNo' => $p->no, // 提运单

					'portLoad' => $p->pol, // 起运港
					'portDis' => $p->pod, // 卸货港
					'outDate' => date('Y-m-d h:i:s') // 出库时间
				);
				$model->setAttribute('message',json_encode($msg) );
				$model->save();
			}
			return true;
		}
		// shipment not existing
		return false;
	}

	/**
	 * add route message into DXT push queue based on Parcel
	 * @param $p
	 * @param string $etd
	 * @param string $eta
	 */
	public static function addRouteByParcel(&$p,$etd='',$eta=''){
		if ( $p->status != 60 && $p->status != 70 ) return;

		// try to merge departue and arrive into one record
		$routes = DxtPushQueue::model()->findAll(array(
			'condition' => 'ref = :ref AND type = 3',
			'params' => array(
				':ref' => $p->ref
			)));

		if ( empty($routes) ) {
			$model = new DxtPushQueue();
			$model->setAttribute('type', self::MSGTYPE_ROUTE);
			$model->setAttribute('ref', $p->ref);
			$model->setAttribute('status', 0);
			$msg = array(
				'epsID' => $p->ref, // 快递ID
				'departure' => $etd, // 起运时间
				'arrive' => $eta      // 抵达时间
			);
			$model->setAttribute('message', json_encode($msg));
			$model->save();

		} else {
			foreach ( $routes as $route ) {
				$msg = json_decode($route->message);
				$route->status = 0; // need resend again
				if ( !empty($etd) ) $msg->departure = $etd;
				if ( !empty($eta) ) $msg->arrive = $eta;
				$route->message = json_encode($msg);
				$route->save();
			}
		}
	}

	/**
	 * add item route into push queue
	 * @param $shipmentId
	 */
	public static function addRoute($consolId,$etd = '',$eta=''){

		// get shipment weight now
		$p = ExacConsol::model()->findByPk($consolId);
		if ( isset($p) ) {

			// only check consol status for
			// 70 - departure
			// 80 - arrive
			if ( $p->status != 70 && $p->status != 80 )  return;

			// get all parcels in this consol
			$parcels = ExAfs::model()->findAll(array(
				'condition' => 'consol_id = :cid',
				'params' => array(
					':cid' => $consolId
				)
			));

			// push all parcels in push queue table
			foreach ( $parcels as $parcel ){

				// in case there is no any ref id from DXT
				// which means order information from DXT server too later , compare to parcel
				// we have put the parcel into our system
				// we can't push this kind of route information to DXT
				if ( empty($parcel->ref) ) continue;

				// parcel status
				// 60 - departure
				// 70 - arrive
				if ( $parcel->status != 60 && $parcel->status != 70 ) continue;

				// try to merge departue and arrive into one record
				$routes = DxtPushQueue::model()->findAll(array(
					'condition' => 'ref = :ref AND type = 3',
					'params' => array(
						':ref' => $parcel->ref
				)));

				if ( empty($routes) ) {
					$model = new DxtPushQueue();
					$model->setAttribute('type', self::MSGTYPE_ROUTE);
					$model->setAttribute('ref', $parcel->ref);
					$model->setAttribute('status', 0);
					$msg = array(
						'epsID' => $parcel->ref, // 快递ID

						// ... TODO confirm with frank , exactly time or just estimated
						'departure' => $etd, // 起运时间
						'arrive' => $eta      // 抵达时间
					);
					$model->setAttribute('message', json_encode($msg));
					$model->save();

				} else {
					foreach ( $routes as $route ) {
						$msg = json_decode($route->message);
						$route->status = 0; // need resend again
						if ( !empty($etd) ) $msg->departure = $etd;
						if ( !empty($eta) ) $msg->arrive = $eta;
						$route->message = json_encode($msg);
						$route->save();
					}
				}

			}
			return true;
		}
		// shipment not existing
		return false;
	}

	public static function addConsolReady($consolId){
		$c = ExacConsol::model()->findByPk($consolId);
		if (isset($c)){
			if ( $c->status != 40) return;
			$d = ['ConsolNo' => $c->no, 'AWB' => $c->awb, 'Flight' => $c->flight, 'ETD' => $c->etd, 'POL' => $c->pol, 'POD' => $c->pod, 'Packs' => $c->totPacks(), 'Weight' => $c->totWeight(), 'ClearanceMode' => 'BBC', 'ClearncePlace' => 'CNCAN', 'CorpCode' => 'VIP', 'Shipper' => [], 'Consignee' => [], 'Notify' => [], 'Shipments' => []];
			foreach ($c->shipments as $p ){
				if ( empty($p->ref) ) continue;
				if ( $p->status != 60 && $p->status != 70 ) continue;
				$d['Shipments'][] = ['OrderID' => $p->cref, 'Connote' => $p->hbn, 'Ref' => $p->ref, 'PltNo' => 1, 'Weight' => $p->weight];
			}
			$model = new DxtPushQueue();
			$model->type = self::MSGTYPE_CONSOLREADY;
			$model->ref = $consolId;
			$model->status = 0;
			$model->message = json_encode($d);
			$model->save();
			return true;
		}
		return false;
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('added_time',$this->added_time,true);
		$criteria->compare('send_time',$this->send_time,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DxtPushQueue the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
