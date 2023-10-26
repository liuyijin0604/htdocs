<?php

/**
 * This is the model class for table "zone_map".
 *
 * The followings are the available columns in table 'zone_map':
 * @property string $id
 * @property string $rate_id
 * @property string $zone_id
 * @property string $z1
 * @property string $z2
 * @property integer $pc_lo
 * @property integer $pc_hi
 */
class ZoneMapTest extends CActiveRecord
{
	public $mdata = [];
    /**
     * @param $orgId
     * @param int $zoneId default 0 means it is zone map for our price to Our Customer
     *          normally zoneId > 0 which is real cost price which a courier provides price to us
     * @return array
     */
    public static function getOrgRateZoneMap($rateId,$zoneId = 0, $simple = false){
        $criteria = new CDbCriteria();
        $criteria->condition = 'rate_id = :rid AND zone_id = :zoneid';
        $criteria->group = 'z1';
        $criteria->order = 'z1 ASC';
        $criteria->params = [':rid' => $rateId,':zoneid' => $zoneId];
        $ZoneMapTest = ZoneMapTest::model()->findAll($criteria);

        $orgRatePriceZoneMap = array();
        foreach ( $ZoneMapTest as $map ) {
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
					'nkg' => 0
				];
			}
        }
        return $orgRatePriceZoneMap;
    }

    /**
     * get zone map by charge code ID
     * @param $chargecodeId
     * @return array
     */
    public static function getChargecodeZoneMap($chargecodeId){
        $criteria = new CDbCriteria();
        $criteria->condition = 'chargecode_id = :cid';
        $criteria->group = 'z1';
        $criteria->order = 'z1 ASC';
        $criteria->params = [':cid' => $chargecodeId];
        $zoneMap = ZoneMap::model()->findAll($criteria);

        $orgPriceZoneMap = array();
        foreach ( $zoneMap as $map ) {
            $orgPriceZoneMap[] = array(
                'code' => $map->z1,
                'name' => $map->zone_name,
                'ppc' => 0,
                'pkg' => 0,
                'base' => 0,
                'minimum' => 0,
                'nkg' => 0
            );
        }
        return $orgPriceZoneMap;
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'zone_map_test';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('rate_id, z1, z2, pc_lo, pc_hi,zone_name,meta,import_id', 'safe'),
			array('pc_lo, pc_hi,zone_id,chargecode_id', 'numerical', 'integerOnly'=>true),
			array('rate_id,chargecode_id', 'length', 'max'=>11),
			array('z1, z2', 'length', 'max'=>20),
            array('zone_name', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, rate_id, chargecode_id,zone_id,z1, z2, pc_lo, pc_hi,zone_name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'rate_id' => 'Org Rate',
            'zone_id' => 'Zone',
			'z1' => 'Z1',
			'z2' => 'Z2',
			'pc_lo' => 'Pc Lo',
			'pc_hi' => 'Pc Hi',
            'zone_name' => 'Zone Name',
            'meta' => 'meta',
            'import_id' => 'Import Id'
		);
	}
	
	public static function getZone($oid, $pc){
		$pc = ltrim($pc,0);
		$r = self::model()->find('rate_id = :oid AND pc_lo <= :pc AND pc_hi >= :pc', array(':oid' => $oid, ':pc' => $pc));
		return empty($r)? false : $r->z1;
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
	public function search(){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('rate_id',$this->rate_id,true);
        $criteria->compare('zone_id',$this->zone_id,true);
		$criteria->compare('z1',$this->z1,true);
		$criteria->compare('z2',$this->z2,true);
		$criteria->compare('pc_lo',$this->pc_lo);
		$criteria->compare('pc_hi',$this->pc_hi);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function beforeSave(){
		if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		return parent::beforeSave();
	}

	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return parent::afterFind();
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ZoneMap the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
