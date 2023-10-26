<?php

/**
 * This is the model class for table "change_shipment_label".
 *
 * The followings are the available columns in table 'change_shipment_label':
 * @property integer $id
 * @property integer $pid
 * @property string $pref
 * @property string $phbn
 * @property string $newref
 */
class ChangeShipmentLabel extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'change_shipment_label';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['pid, pref, phbn, newref', 'required'],
			['pref, phbn, newref', 'length', 'max'=>20],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, pid, pref, phbn, newref', 'safe', 'on'=>'search'],
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
			'shipment' => array(self::BELONGS_TO, 'Shipment', 'pid'),
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'pid' => 'PID',
			'pref' => 'Pref',
			'phbn' => 'Phbn',
			'newref' => 'Newref',
		];
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

		$criteria->compare('id', $this->id);
		$criteria->compare('pid', $this->pid);
		$criteria->compare('pref', $this->pref, true);
		$criteria->compare('phbn', $this->phbn, true);
		$criteria->compare('newref', $this->newref, true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ChangeShipmentLabel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function afterSave()
	{
		parent::afterSave();
		if ($this->isNewRecord) {
			// Generate gatepass
			$imParcel = ImParcel::model()->findByPk($this->pid);
			if (!empty($imParcel)) {
				if (($imParcel->status >= ImParcel::STATE_CLEAR && $imParcel->status < ImParcel::DELIVERED) || $imParcel->status == ImParcel::STATE_LOCAL_ARRIVAL) {
					$gatepassShipments = GatepassShipment::model()->findAllByAttributes(array('fid' => $imParcel->id), 'status = 0');
					if (count($gatepassShipments) == 0) { // Create a new gatepass shipment when there is no active gatepass shipment
						$gatepassShipment = new GatepassShipment();
						$gatepassShipment->fid = $imParcel->id;
						if (!empty($imParcel->mdata['org_rate_id'])) {
							$orgRate = OrgRate::model()->findByPk($imParcel->mdata['org_rate_id']);
						}
						$gatepassShipment->courier_id = !empty($orgRate) ? $orgRate->org_id : 0;
						$gatepassShipment->connote_no = $this->newref;
						$gatepassShipment->courier_id = !empty($orgRate) ? $orgRate->org_id : 0;
						$gatepassShipment->status = 0;
						$gatepassShipment->warehouse = !empty($imParcel->ddpt_id) ? $imParcel->ddpt_id : $imParcel->consol->dpt_id;
						$gatepassShipment->shipment_status = $imParcel->status;
						$gatepassShipment->parent_id = 0;
						$gatepassShipment->scan_time = $imParcel->getScanTime();
						$gatepassShipment->gate_pass_time = '0000-00-00 00:00:00';
						$gatepassShipment->save();
					} else { // Update existing gatepass shipment
						foreach ($gatepassShipments as $gatepassShipment) {
							$gatepassShipment->connote_no = $this->newref;
							if (!empty($imParcel->mdata['org_rate_id'])) {
								$orgRate = OrgRate::model()->findByPk($imParcel->mdata['org_rate_id']);
							}
							$gatepassShipment->courier_id = !empty($orgRate) ? $orgRate->org_id : 0;
							$gatepassShipment->status = 0;
							$gatepassShipment->shipment_status = $imParcel->status;
							$gatepassShipment->parent_id = 0;
							$gatepassShipment->scan_time = $imParcel->getScanTime();
							$gatepassShipment->gate_pass_time = '0000-00-00 00:00:00';
							$gatepassShipment->save();
						}
					}
				} else if ($imParcel->status == ImParcel::DELIVERED) {
					$signedGatepassShipment = GatepassShipment::model()->findByAttributes(array('fid' => $imParcel->id), 'status = 10');
					if (!empty($signedGatepassShipment) && $signedGatepassShipment->gate_pass_time != '0000-00-00 00:00:00') {
						$lastGatepassSignedTime = $signedGatepassShipment->gate_pass_time;
						$sql = 'SELECT * FROM log WHERE `model` = "ImParcel" AND `lid` = ' . $imParcel->id . ' AND `time` < "' . $lastGatepassSignedTime . '" ORDER BY id DESC';
						$previousStatus = 0;
						$logs = Log::model()->findAllBySql($sql);
						foreach ($logs as $log) {
							if (!empty($log->mdata['status'])) {
								$previousStatus = array_search($log->mdata['status'], ImParcel::$states);
								if (!empty($previousStatus)) {
									break;
								}
							}
						}
						$imParcel->status = $previousStatus;
						$imParcel->update('status');
					}
				}
			}
		}
	}
}
