<?php

/**
 * This is the model class for table "shipment_scan_overview".
 *
 * The followings are the available columns in table 'shipment_scan_overview':
 * @property integer $id
 * @property string $date
 * @property integer $dpt_id
 * @property integer $pre_left_packs
 * @property double $pre_left_weight
 * @property integer $today_packs
 * @property double $today_weight
 * @property integer $today_left_packs
 * @property double $today_left_weight
 * @property string $meta
 */
class ShipmentScanOverview extends MetaModel
{
	
	public static $dpt_ids=[
		106=>'Sydney',
		218=>'Melbourne',
	];

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'shipment_scan_overview';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['date,dpt_id', 'required'],
			['pre_left_packs, today_packs,today_left_packs,dpt_id', 'numerical', 'integerOnly'=>true],
			['pre_left_weight, today_weight, today_left_weight', 'numerical'],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, date, pre_left_packs, pre_left_weight,today_packs, dpt_id, today_weight, today_left_packs, today_left_weight, meta', 'safe', 'on'=>'search'],
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
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'date' => 'Date',
			'pre_left_packs' => 'Pre Left Packs',
			'pre_left_weight' => 'Pre Left Weight',
			'today_packs' => 'Today Packs',
			'today_weight' => 'Today Weight',
			'today_left_packs' => 'Today Left Packs',
			'today_left_weight' => 'Today Left Weight',
			'meta' => 'Meta',
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
		$criteria->compare('date', $this->date, true);
		$criteria->compare('pre_left_packs', $this->pre_left_packs);
		$criteria->compare('pre_left_weight', $this->pre_left_weight);
		$criteria->compare('today_packs', $this->today_packs);
		$criteria->compare('today_weight', $this->today_weight);
		$criteria->compare('today_left_packs', $this->today_left_packs);
		$criteria->compare('today_left_weight', $this->today_left_weight);
		$criteria->compare('meta', $this->meta, true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}
		
	public static function updateData($dpt_id=106, $date='')
	{
		if(empty($date)) $date=date('Y-m-d');
		$model= ShipmentScanOverview::model()->find('date=:date and dpt_id=:dpt_id and type = 0', [':date'=>$date,':dpt_id'=>$dpt_id]);
		if (empty($model)) {
			$model=new ShipmentScanOverview();
			$model->date=$date;
			$model->dpt_id=$dpt_id;
			$leftInfo= self::getPreLeft($date, $dpt_id);
			$model->pre_left_packs=$leftInfo[0];
			$model->pre_left_weight= number_format($leftInfo[1], 2, '.', '');
		}
		$totalInfo= self::getTodayInfo($date, $dpt_id);
		$model->today_packs=$totalInfo[0];
		$model->today_weight=$totalInfo[1];
		$today=date('Y-m-d');
		$todayLeftInfo= self::getPreLeft($today, $dpt_id);
		$model->today_left_packs=$todayLeftInfo[0];
		$model->today_left_weight=$todayLeftInfo[1];
		$todayScanInfo= ShipmentScan::getTodayScanInfo($dpt_id);
		$model->pre_left_packs=$todayLeftInfo[0] + intval($todayScanInfo['packs'])-$totalInfo[0];
		$model->pre_left_weight=$todayLeftInfo[1] + floatval($todayScanInfo['weight'])-$totalInfo[1];
		$model->save();
	}

	public static function updateSimpleData($dpt_id=106, $date='')
	{
		//for air
		if(empty($date)) $date=date('Y-m-d');
		$model= ShipmentScanOverview::model()->find('date=:date and dpt_id=:dpt_id and type = 1', [':date'=>$date,':dpt_id'=>$dpt_id]);
		$lastDayModel = ShipmentScanOverview::model()->find('date=:date and dpt_id=:dpt_id and type = 1', [':date'=>HolidayHelper::getLastDay($date),':dpt_id'=>$dpt_id]);
		if (empty($model)) {
			$model=new ShipmentScanOverview();
			$model->type = 1;
			$model->date=$date;
			$model->dpt_id=$dpt_id;
			if(empty($lastDayModel))
			{
				$model->pre_left_packs = 0;
				$model->pre_left_weight = 0;
			}else
			{
				$model->pre_left_packs=$lastDayModel->today_left_packs;
				$model->pre_left_weight= $lastDayModel->today_left_weight;
			}
		}
		$totalInfo= self::getTodayInfo($date, $dpt_id,1);
		$model->today_packs=$totalInfo[0];
		$model->today_weight=$totalInfo[1];
		$today=date('Y-m-d');
		$todayScanInfo= ShipmentScan::getDayScanInfo($dpt_id,0,$date);
		$todayResortingInfo= ShipmentScan::getDayScanInfo($dpt_id,1,$date);
		$model->today_left_packs=$model->today_packs+$model->pre_left_packs-intval($todayScanInfo['packs']);
		$model->today_left_weight=$model->today_weight+$model->pre_left_weight-floatval($todayScanInfo['weight']);
		$model->mdata['todayScanInfo']['packs'] = $todayScanInfo['packs'];
		$model->mdata['todayScanInfo']['weight'] = $todayScanInfo['weight'];
		$model->mdata['todayResortingInfo']['packs'] = $todayResortingInfo['packs'];
		$model->mdata['todayResortingInfo']['weight'] = $todayResortingInfo['weight'];
		$model->save();






		//for sea
		if(empty($date))
		{
				$seaDate=HolidayHelper::getLastDay(date('Y-m-d'));
				$date = date('Y-m-d');
		}else
		{
				$seaDate=HolidayHelper::getLastDay($date);
		}
		$seaModel= ShipmentScanOverview::model()->find('date=:date and dpt_id=:dpt_id and type = 2', [':date'=>$date,':dpt_id'=>$dpt_id]);
		$lastDaySeaModel = ShipmentScanOverview::model()->find('date=:date and dpt_id=:dpt_id and type = 2', [':date'=>HolidayHelper::getLastDay($date),':dpt_id'=>$dpt_id]);
		if (empty($seaModel)) {
			$seaModel=new ShipmentScanOverview();
			$seaModel->type = 2;
			$seaModel->date=$date;
			$seaModel->dpt_id=$dpt_id;
			if(empty($lastDaySeaModel))
			{
				$seaModel->pre_left_packs = 0;
				$seaModel->pre_left_weight = 0;
			}else
			{
				$seaModel->pre_left_packs=$lastDaySeaModel->today_left_packs;
				$seaModel->pre_left_weight= $lastDaySeaModel->today_left_weight;
			}
		}
		$totalInfo= self::getTodaySeaInfo($date, $dpt_id);
		$seaModel->today_packs=$totalInfo[0];
		$seaModel->today_weight=$totalInfo[1];
		$today=date('Y-m-d');
		$todayScanInfo= ShipmentScan::getDayScanInfo($dpt_id,0,$seaDate,2);
		$todayResortingInfo= ShipmentScan::getDayScanInfo($dpt_id,1,$seaDate,2);
		$seaModel->today_left_packs=$seaModel->today_packs+$seaModel->pre_left_packs-intval($todayScanInfo['packs']);
		$seaModel->today_left_weight=$seaModel->today_weight+$seaModel->pre_left_weight-floatval($todayScanInfo['weight']);
		$seaModel->mdata['todayScanInfo']['packs'] = $todayScanInfo['packs'];
		$seaModel->mdata['todayScanInfo']['weight'] = $todayScanInfo['weight'];
		$seaModel->mdata['todayResortingInfo']['packs'] = $todayResortingInfo['packs'];
		$seaModel->mdata['todayResortingInfo']['weight'] = $todayResortingInfo['weight'];
		$seaModel->save();




	}

	public static function getPreLeft($date, $dpt_id=106)
	{
		$leftInfo=[0,0];
		$proceses=ConsolProcess::model()->findAll('main_type = 1 AND status in (24,28) AND  to_warehouse>"2000-01-01" AND to_warehouse<:thedate', [':thedate'=>$date.' 16:00:00']);
		foreach ($proceses as $pro) {
			$consol=$pro->consol;
			if (empty($consol)) {
				continue;
			}
			if ($consol->dpt_id!=$dpt_id) {
				continue;
			}
			$leftInfo[0]+= intval($consol->totLeftPacks());
			$leftInfo[1]+= floatval($consol->totLeftWeight());
		}
		return $leftInfo;
	}
	
	public static function getTodayInfo($date, $dpt_id=106,$main_type = 1)
	{
		$todayInfo=[0,0];
		$date = date('Y-m-d 16:00', strtotime($date.' -1 day'));
		$proceses=ConsolProcess::model()->findAll('main_type = :main_type AND status in (24,28,32,80) AND to_warehouse>:date AND to_warehouse<DATE_ADD(:date, INTERVAL 1 DAY)', [':date'=>$date,':main_type'=>$main_type]);
		foreach ($proceses as $pro) {
			$consol=$pro->consol;
			if (empty($consol) || $consol->dpt_id != $dpt_id) {
				continue;
			}
			$todayInfo[0]+= intval($consol->totPacks());
			$todayInfo[1]+= floatval($consol->totWeight());
		}
		return $todayInfo;
	}

	public static function getTodaySeaInfo($date, $dpt_id=106)
	{
		$todayInfo=[0,0];
		$date = date('Y-m-d 08:00', strtotime($date.' -1 day'));
		$proceses=ConsolProcess::model()->findAll('main_type = 2 AND status in (24,28,32,80) AND to_warehouse>:date AND to_warehouse<DATE_ADD(:date, INTERVAL 1 DAY)', [':date'=>$date,':main_type'=>$main_type]);
		foreach ($proceses as $pro) {
			$consol=$pro->consol;
			if (empty($consol) || $consol->dpt_id != $dpt_id) {
				continue;
			}
			$todayInfo[0]+= intval($consol->totPacks());
			$todayInfo[1]+= floatval($consol->totWeight());
		}
		return $todayInfo;
	}

	public static function getTodayInfoSimple($date, $dpt_id=106)
	{
		$todayInfo=[0,0];
		$proceses=ConsolProcess::model()->findAll('main_type = 1 AND status in (24,28,32,80) AND to_days(to_warehouse) = to_days(:date)', [':date'=>$date]);
		foreach ($proceses as $pro) {
			$consol=$pro->consol;
			if (empty($consol) || $consol->dpt_id != $dpt_id) {
				continue;
			}
			$todayInfo[0]+= intval($consol->totPacks());
			$todayInfo[1]+= floatval($consol->totWeight());
		}
		return $todayInfo;
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ShipmentScanOverview the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
