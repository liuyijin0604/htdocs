<?php

/**
 * This is the model class for table "cnl_cargo".
 *
 * The followings are the available columns in table 'cnl_cargo':
 * @property string $id
 * @property string $order_id
 * @property integer $type
 * @property double $totalVolume
 * @property double $totalGrossWeight
 * @property string $totalPackages
 * @property string $totalUnits
 * @property double $chargeWeight
 * @property double $chargeVolume
 * @property string $containerList
 * @property string $packageList
 * @property string $meta
 */
class CnlCargo extends CActiveRecord
{
	public $mdata = [], $containers = [], $packages = [];

	public static $types = [
		10 => 'Request',
		20 => 'Booked',
	];

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'cnl_cargo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('order_id, type, totalVolume, totalGrossWeight, totalPackages, totalUnits, chargeWeight, chargeVolume, containerList, packageList, meta', 'safe'),
            array('type', 'numerical', 'integerOnly'=>true),
            array('totalVolume, totalGrossWeight, chargeWeight, chargeVolume', 'numerical'),
            array('chargeWeight, chargeVolume', 'nonZero', 'on' => 'update'),
            array('order_id, totalPackages, totalUnits', 'length', 'max'=>11),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, order_id, type, totalVolume, totalGrossWeight, totalPackages, totalUnits, chargeWeight, chargeVolume, containerList, packageList, meta', 'safe', 'on'=>'search'),
        );
	}

	public function nonZero($attr, $params){
		if($this->type == 10) return;
		if($this->{$attr} == 0){
			$this->addError($attr, $this->getAttributeLabel($attr).' can not be zero');
		}
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'order' => array(self::BELONGS_TO, 'CnlOrder', 'order_id'),
		);
	}
	
	public function beforeSave(){
		$this->meta = empty($this->mdata)? '' : json_encode($this->mdata);
		$this->containerList = empty($this->containers)? '' : json_encode($this->containers);
		$this->packageList = empty($this->packages)? '' : json_encode($this->packages);
		return parent::beforeSave();
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		if(!empty($this->containerList)) $this->containers = json_decode($this->containerList, true);
		if(!empty($this->packageList)) $this->packages = json_decode($this->packageList, true);
		return parent::afterFind();
	}

	public function afterSave(){
		if($this->isNewRecord && $this->type == 10){
			$a = clone $this;
			$a->id = null;
			$a->type = 20;
			$a->save();
		}
	}

	public function conatinerDP(){
		$rs = [];
		foreach($this->containers as $i => $itm){
			$d = new CnlCargoContainer;
			$d->id = $i;
			foreach(['size', 'no', 'type'] as $k){
				$d->{$k} = isset($itm[$k])? $itm[$k] : '';
			}
			$rs[] = $d;
		}
		return new CArrayDataProvider($rs, array('pagination' => false));
	}

	public function containerCount($txt = false){
		$rs = [];
		foreach($this->containers as $i => $itm){
			if(!isset($rs[$itm['size'].$itm['type']])){
				$rs[$itm['size'].$itm['type']] = 0;
			}
			$rs[$itm['size'].$itm['type']]++;
		}
		if($txt){
			$tr = [];
			foreach($rs as $k=>$v){
				$tr[] = $v.' x '.$k;
			}
			return implode(', ', $tr);
		}
		return $rs;
	}

	public function packageDP(){
		$rs = [];
		foreach($this->packages as $i => $itm){
			$d = new CnlCargoPackage;
			$d->id = $i;
			foreach(['packageUom', 'feature', 'num', 'length', 'width', 'height'] as $k){
				$d->{$k} = isset($itm[$k])? $itm[$k] : '';
			}
			$rs[] = $d;
		}
		return new CArrayDataProvider($rs, array('pagination' => false));
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
    public function attributeLabels(){
        return array(
            'id' => 'ID',
            'order_id' => 'Order',
            'type' => 'Type',
            'totalVolume' => 'Total Volume',
            'totalGrossWeight' => 'Total Gross Weight',
            'totalPackages' => 'Total Packages',
            'totalUnits' => 'Total Units',
            'chargeWeight' => 'Charge Weight',
            'chargeVolume' => 'Charge Volume',
            'containerList' => 'Container List',
            'packageList' => 'Package List',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('order_id',$this->order_id,true);
        $criteria->compare('type',$this->type);
        $criteria->compare('totalVolume',$this->totalVolume);
        $criteria->compare('totalGrossWeight',$this->totalGrossWeight);
        $criteria->compare('totalPackages',$this->totalPackages,true);
        $criteria->compare('totalUnits',$this->totalUnits,true);
        $criteria->compare('chargeWeight',$this->chargeWeight);
        $criteria->compare('chargeVolume',$this->chargeVolume);
        $criteria->compare('containerList',$this->containerList,true);
        $criteria->compare('packageList',$this->packageList,true);
        $criteria->compare('meta',$this->meta,true);
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
	 * @return CnlCargo the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
