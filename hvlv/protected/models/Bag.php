<?php

/**
 * This is the model class for table "bag".
 *
 * The followings are the available columns in table 'bag':
 * @property integer $id
 * @property integer $consol_id
 * @property string $no
 * @property string $note
 * @property integer $type
 * @property string $weight
 * @property string $total_cbm
 * @property integer $pkg
 * @property string $meta
 * @property string $create
 * @property integer $operator
 */
class Bag extends MetaModel
{
	const UBIBAG = 1;
	public static $type = [
		Self::UBIBAG=>'UBIBAG'
	];

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'bag';
	}
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('consol_id, no, type, weight, total_cbm, pkg, create, operator', 'required'),
			array('consol_id, type, pkg, operator', 'numerical', 'integerOnly'=>true),
			array('no', 'length', 'max'=>255),
			array('weight, total_cbm', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, consol_id, no, note, type, weight, total_cbm, pkg, meta, create, operator', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
			'consol' => [self::BELONGS_TO, 'Consol', 'consol_id'],
			'shipmentBags' => [self::HAS_MANY, 'BagShipment', 'bag_id']
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'consol_id' => 'Consol',
			'no' => 'No',
			'note' => 'Note',
			'type' => 'Type',
			'weight' => 'Weight',
			'total_cbm' => 'Total Cbm',
			'pkg' => 'Pkg',
			'meta' => 'Meta',
			'create' => 'Create',
			'operator' => 'Operator',
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
	public function search($pgn = true, $ps = 30)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('consol_id',$this->consol_id);
		$criteria->compare('no',$this->no,true);
		$criteria->compare('note',$this->note,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('weight',$this->weight,true);
		$criteria->compare('total_cbm',$this->total_cbm,true);
		$criteria->compare('pkg',$this->pkg);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('create',$this->create,true);
		$criteria->compare('operator',$this->operator);

		$sort = new CSort(get_called_class());
		$sort->defaultOrder = 't.id DESC';

		$pagerparams = $_GET;
		if ($this->consol_id > 0) {
			$pagerparams['consoleId'] = $this->consol_id;
		}
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort' => $sort,
			'pagination' => $pgn ? [
				'pageSize' => $ps,
				'params' => $pagerparams,
			] : false,
		));
	}

	public function updateShipmentData()
	{
		$shipmentBags = $this->shipmentBags;
		$pkg = 0;
		$cbm = 0;
		$weight = 0;
		foreach ($shipmentBags as $key => $sb) {
			$cbm+=$sb->shipment->getTotalCBM();
			$weight+=$sb->shipment->weight;
			$pkg+=$sb->shipment->pkg;
		}
		$this->pkg = $pkg;
		$this->cbm = $sbm;
		$this->weight = $weight;
		$this->save();
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Bag the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
