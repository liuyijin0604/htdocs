<?php

/**
 * This is the model class for table "cnl_item".
 *
 * The followings are the available columns in table 'cnl_item':
 * @property string $id
 * @property string $order_id
 * @property string $itemOrderCode
 * @property string $cargoDesc
 * @property string $totalPackages
 * @property string $packageUom
 * @property string $totalUnits
 * @property double $totalVolume
 * @property double $totalGrossWeight
 * @property string $meta
 */
class CnlItem extends CActiveRecord
{
	public $mdata = [];
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'cnl_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('order_id, itemOrderCode, cargoDesc, totalPackages, packageUom, totalUnits, totalVolume, totalGrossWeight, meta', 'safe'),
            array('totalVolume, totalGrossWeight', 'numerical'),
            array('order_id, totalPackages, totalUnits', 'length', 'max'=>11),
            array('itemOrderCode', 'length', 'max'=>100),
            array('packageUom', 'length', 'max'=>5),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, order_id, itemOrderCode, cargoDesc, totalPackages, packageUom, totalUnits, totalVolume, totalGrossWeight, meta', 'safe', 'on'=>'search'),
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

	
	
	public function beforeSave(){
		$this->meta = empty($this->mdata)? '' : json_encode($this->mdata);
		return parent::beforeSave();
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return parent::afterFind();
	}
	
	/**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'id' => 'ID',
            'order_id' => 'Order',
            'itemOrderCode' => 'Item Order Code',
            'cargoDesc' => 'Cargo Desc',
            'totalPackages' => 'Total Packages',
            'packageUom' => 'Package Uom',
            'totalUnits' => 'Total Units',
            'totalVolume' => 'Total Volume',
            'totalGrossWeight' => 'Total Gross Weight',
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
        $criteria->compare('itemOrderCode',$this->itemOrderCode,true);
        $criteria->compare('cargoDesc',$this->cargoDesc,true);
        $criteria->compare('totalPackages',$this->totalPackages,true);
        $criteria->compare('packageUom',$this->packageUom,true);
        $criteria->compare('totalUnits',$this->totalUnits,true);
        $criteria->compare('totalVolume',$this->totalVolume);
        $criteria->compare('totalGrossWeight',$this->totalGrossWeight);
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
	 * @return CnlItem the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
