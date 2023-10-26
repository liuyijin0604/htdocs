<?php

/**
 * This is the model class for table "addr_correction_log".
 *
 * The followings are the available columns in table 'addr_correction_log':
 * @property integer $id
 * @property integer $org_id
 * @property string $wrong_address
 * @property string $correct_address
 * @property string $process_time
 * @property string $cref
 * @property string $meta
 */
class AddrCorrectionLog extends CActiveRecord
{
	public $mdata;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'addr_correction_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('org_id, wrong_address, correct_address, process_time, cref', 'required'),
			array('org_id', 'numerical', 'integerOnly'=>true),
			array('wrong_address, correct_address', 'length', 'max'=>200),
			array('cref', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, org_id, wrong_address, correct_address, process_time, cref, meta', 'safe', 'on'=>'search'),
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
			'org_id' => 'Org',
			'wrong_address' => 'Wrong Address',
			'correct_address' => 'Correct Address',
			'process_time' => 'Process Time',
			'cref' => 'Cref',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('org_id',$this->org_id);
		$criteria->compare('wrong_address',$this->wrong_address,true);
		$criteria->compare('correct_address',$this->correct_address,true);
		$criteria->compare('process_time',$this->process_time,true);
		$criteria->compare('cref',$this->cref,true);
		$criteria->compare('meta',$this->meta,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AddrCorrectionLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function beforeSave()
	{
		if (!empty($this->mdata)) {
			$this->meta= json_encode($this->mdata);
		}
		return true;
	}

	public function afterFind()
	{
		if (!empty($this->meta)) {
			$this->mdata= json_decode($this->meta, true);
		}
		return true;
	}

	public function savelog($org_id, $wrong_address, $correct_address, $cref, $serviceType)
	{
		$this->org_id=$org_id;
		$this->wrong_address=$wrong_address;
		$this->correct_address=$correct_address;
		$this->process_time=date('Y-m-d h:i:s a', time());
		$this->cref=$cref;
		$this->mdata['address_valid']=$serviceType;
		return $this->save();
	}
}
