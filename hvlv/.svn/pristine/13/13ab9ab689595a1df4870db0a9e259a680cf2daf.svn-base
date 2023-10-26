<?php

/**
 * This is the model class for table "shipment_package".
 *
 * The followings are the available columns in table 'shipment_package':
 * @property integer $id
 * @property string $ref
 * @property string $cref
 * @property string $packs
 * @property integer $user_id
 * @property string $create
 * @property double $weight
 * @property double $cbm
 * @property integer $pkg
 */
class ShipmentPackage extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'shipment_package';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cref, packages, user_id, create, weight, cbm, pkg', 'required'),
			array('user_id, pkg', 'numerical', 'integerOnly'=>true),
			array('weight, cbm', 'numerical'),
			array('ref, cref', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ref, cref, packages, user_id, create, weight, cbm, pkg', 'safe', 'on'=>'search'),
		);
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
			'ref' => 'Ref',
			'cref' => 'Cref',
			'packs' => 'Packs',
			'user_id' => 'User',
			'create' => 'Create',
			'weight' => 'Weight',
			'cbm' => 'Cbm',
			'pkg' => 'Pkg',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('ref',$this->ref,true);
		$criteria->compare('cref',$this->cref,true);
		$criteria->compare('packs',$this->packs,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('create',$this->create,true);
		$criteria->compare('weight',$this->weight);
		$criteria->compare('cbm',$this->cbm);
		$criteria->compare('pkg',$this->pkg);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ShipmentPackage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
