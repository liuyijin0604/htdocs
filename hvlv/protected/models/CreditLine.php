<?php

/**
 * This is the model class for table "credit_line".
 *
 * The followings are the available columns in table 'credit_line':
 * @property integer $id
 * @property integer $pid
 * @property double $amount
 * @property double $rate
 * @property double $gst
 * @property integer $qty
 * @property string $description
 * @property string $tax
 * @property string $meta
 */
class CreditLine extends oActiveRecord
{
    protected $mdata;
    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'credit_line';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pid, amount, qty', 'required'),
			array('pid, qty', 'numerical'),
			array('amount, gst', 'numerical'),
			array('description', 'length', 'max'=>200),
                        array('tax', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, pid, amount, gst, qty,rate,description,tax, meta', 'safe', 'on'=>'search'),
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
                    'credit'=>array(self::BELONGS_TO,'Payment','pid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'pid' => 'Pid',
			'amount' => 'Amount',
                        'rate'=>'Rate',
			'gst' => 'Gst',
			'qty' => 'Qty',
                         'tax'=>'Tax',
			'description' => 'Description',
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
        
        public function beforeSave(){
            if(!empty($this->mdata)) $this->meta= json_encode ($this->mdata);
            return true;
         }
        public function afterFind(){
            if(!empty($this->meta)) $this->mdata= json_decode($this->meta,true);
            return true;
         }
        public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('pid',$this->pid);
		$criteria->compare('amount',$this->amount);
                $criteria->compare('rate',$this->rate);
		$criteria->compare('gst',$this->gst);
                $criteria->compare('tax',$this->tax);
		$criteria->compare('qty',$this->qty);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('meta',$this->meta,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CreditLine the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
