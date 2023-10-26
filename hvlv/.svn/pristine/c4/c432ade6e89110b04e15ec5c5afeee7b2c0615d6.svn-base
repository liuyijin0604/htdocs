<?php

/**
 * This is the model class for table "zone_map".
 *
 * The followings are the available columns in table 'zone_map':
 * @property string $id
 * @property string $org_id
 * @property string $zone_id
 * @property string $z1
 * @property string $z2
 * @property integer $pc_lo
 * @property integer $pc_hi
 */
class ZoneMap extends CActiveRecord
{
	/**
	 * @param $orgId
	 * @param int $zoneId default 0 means it is zone map for our price to Our Customer
	 *          normally zoneId > 0 which is real cost price which a courier provides price to us
	 * @return array
	 */
	public static function getOrgZoneMap($orgId, $zoneId = 0, $simple = false)
	{
		$criteria = new CDbCriteria();
		$criteria->condition = 'org_id = :oid AND zone_id = :zoneid';
		$criteria->group = 'z1';
		$criteria->order = 'z1 ASC';
		$criteria->params = [':oid' => $orgId,':zoneid' => $zoneId];
		$zoneMap = ZoneMap::model()->findAll($criteria);

		$orgPriceZoneMap = [];
		foreach ($zoneMap as $map) {
			if($simple){
				$orgZoneMap[$map->z1] = $map->z1;
			}else{
				$orgPriceZoneMap[] = [
					'code' => $map->z1,
					'name' => $map->zone_name,
					'ppc' => 0,
					'pkg' => 0,
					'base' => 0,
					'minimum' => 0,
					'min_incl' => 0,
					'nkg' => 0
				];
			}
		}
		return $orgPriceZoneMap;
	}

	/**
	 * get zone map by charge code ID
	 * @param $chargecodeId
	 * @return array
	 */
	public static function getChargecodeZoneMap($chargecodeId)
	{
		$criteria = new CDbCriteria();
		$criteria->condition = 'chargecode_id = :cid';
		$criteria->group = 'z1';
		$criteria->order = 'z1 ASC';
		$criteria->params = [':cid' => $chargecodeId];
		$zoneMap = ZoneMap::model()->findAll($criteria);

		$orgPriceZoneMap = [];
		foreach ($zoneMap as $map) {
			$orgPriceZoneMap[] = [
				'code' => $map->z1,
				'name' => $map->zone_name,
				'ppc' => 0,
				'pkg' => 0,
				'base' => 0,
				'minimum' => 0,
				'min_incl' => 0,
				'nkg' => 0
			];
		}
		return $orgPriceZoneMap;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'zone_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['org_id, z1, z2, pc_lo, pc_hi,zone_name', 'safe'],
			['pc_lo, pc_hi,zone_id,chargecode_id', 'numerical', 'integerOnly'=>true],
			['org_id,chargecode_id', 'length', 'max'=>11],
			['z1, z2', 'length', 'max'=>20],
			['zone_name', 'length', 'max'=>100],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, org_id, chargecode_id,zone_id,z1, z2, pc_lo, pc_hi,zone_name', 'safe', 'on'=>'search'],
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
			'org' => [self::BELONGS_TO, 'Org', 'org_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'org_id' => 'Org',
			'zone_id' => 'Zone',
			'z1' => 'Zone 1',
			'z2' => 'Zone 2',
			'pc_lo' => 'Postcode From',
			'pc_hi' => 'Postcode To',
			'zone_name' => 'Zone Name'
		];
	}
	
	public static function getZone($oid, $pc,$zone = 0)
	{
		$zoneStr = "";
		$pc = ltrim($pc, 0);
		if(!empty($zone))
		{
			$zoneStr = " and zone_id = ".$zone;
		}

		$r = self::model()->find('org_id = :oid AND pc_lo <= :pc AND pc_hi >= :pc'.$zoneStr, [':oid' => $oid, ':pc' => $pc]);
		return empty($r)? false : $r->z1;
	}

	public static function getAusZone($postcode)
	{
		return self::getZone(101,$postcode,3);
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
	public function search($pgn = true, $ps = 30, $ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('org_id', $this->org_id);
		$criteria->compare('zone_id', $this->zone_id);
		$criteria->compare('z1', $this->z1);
		$criteria->compare('z2', $this->z2);
		$criteria->compare('zone_name', $this->zone_name, true);
		$criteria->compare('pc_lo', $this->pc_lo, true);
		$criteria->compare('pc_hi', $this->pc_hi, true);
		
		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
			'sort'=>[
				'defaultOrder'=>'t.pc_lo ASC',
			],
			'pagination'=>$pgn? [
				'pageSize'=>$ps,
			] : false,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ZoneMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
