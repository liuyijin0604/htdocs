<?php

/**
 * This is the model class for table "payment_gateway_history".
 *
 * The followings are the available columns in table 'payment_gateway_history':
 * @property integer $id
 * @property string $invoice_id
 * @property string $pid
 * @property string $type
 * @property string $trn
 * @property string $token
 * @property string $payed_time
 * @property string $create_time
 * @property string $result
 */
class PaymentGatewayHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'payment_gateway_history';
	}

	// define payment gateway type
	const PAYMENT_GATEWAY_TYPE_POLI = 10;
	const PAYMENT_GATEWAY_TYPE_PAYPAL = 20;
	const PAYMENT_GATEWAY_TYPE_SUPAY = 30;
	public static $types = array(
		10 => 'POLi',
		20 => 'PayPal',
		30 => 'Supay'
	);

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, trn', 'required'),
			array('invoice_id,pid,type', 'length', 'max'=>10),
			array('trn, result', 'length', 'max'=>20),
			array('token', 'length', 'max'=>45),
			array('payed_time, create_time,meta', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, invoice_id, type, trn, token, payed_time,pid, create_time, result,meta', 'safe', 'on'=>'search'),
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
            'invoice' => array(self::HAS_MANY, 'Invoice', 'invoice_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'invoice_id' => 'Invoice',
			'type' => 'Type',
			'trn' => 'Trn',
			'token' => 'Token',
			'payed_time' => 'Payed Time',
			'create_time' => 'Create Time',
			'result' => 'Result',
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
		$criteria->compare('invoice_id',$this->invoice_id);
                $criteria->compare('pid',$this->pid);
		$criteria->compare('type',$this->type);
		$criteria->compare('trn',$this->trn);
		$criteria->compare('token',$this->token);
		$criteria->compare('payed_time',$this->payed_time,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('result',$this->result);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function beforeSave() {
        if ($this->isNewRecord)
            $this->create_time = new CDbExpression('NOW()');

        return parent::beforeSave();
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PaymentGatewayHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
