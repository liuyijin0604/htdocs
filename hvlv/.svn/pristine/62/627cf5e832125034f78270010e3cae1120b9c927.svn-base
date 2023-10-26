<?php

/**
 * This is the model class for table "gatepass_mawb".
 *
 * The followings are the available columns in table 'gatepass_mawb':
 * @property integer $id
 * @property integer $parent_id
 * @property string $ocourier_id
 * @property integer $consol_id
 * @property string $created
 * @property integer $status
 */
class GatepassMawb extends CActiveRecord
{
	const STATE_NEW=10;
	const STATE_NEED_RECLAIM=20;
	const STATE_PROCESSED = 90;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gatepass_mawb';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parent_id, ocourier_id, consol_id, status', 'required'),
			array('parent_id, consol_id, status', 'numerical', 'integerOnly'=>true),
			array('ocourier_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, parent_id, ocourier_id, consol_id, created, status', 'safe', 'on'=>'search'),
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
			'consol' => [self::BELONGS_TO, 'Consol', 'consol_id'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'parent_id' => 'Parent',
			'ocourier_id' => 'Ocourier',
			'consol_id' => 'Consol',
			'created' => 'Signed',
			'status' => 'Status',
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
	public function search($pgn=false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.parent_id',$this->parent_id);
		$criteria->compare('t.warehouse',$this->warehouse);
		$criteria->compare('t.ocourier_id',$this->ocourier_id);
		$criteria->compare('t.created',$this->created,true);
		$criteria->compare('t.status',$this->status);
		if(empty($this->status))
		{
			$criteria->compare('t.status',self::STATE_NEW);
		}
		if(!empty($this->consol_id))
		{
			$criteria->with = "consol";
			$criteria->addCondition('consol.awb like "%'.$this->consol_id.'%" or consol.container_no like "%'.$this->consol_id.'%"');
		}
		$criteria->addCondition("to_days(t.created)=to_days(NOW())");
		$criteria->group="consol_id";

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
                        'pagination'=> $pgn? array(
				'pageSize' => 300,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GatepassMawb the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
