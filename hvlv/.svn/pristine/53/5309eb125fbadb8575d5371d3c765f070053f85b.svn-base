<?php

/**
 * This is the model class for table "ex_ledger".
 *
 * The followings are the available columns in table 'ex_ledger':
 * @property string $id
 * @property integer $from_id
 * @property string $invoice_number
 * @property string $invoice_date
 * @property string $gst
 * @property string $amount
 * @property string $total
 * @property string $chargecode
 * @property string $weight
 * @property string $volume
 * @property string $chargeable
 * @property string $created
 * @property integer $status
 * @property integer $fid
 * @property string $model
 * @property integer $type
 * @property integer $currency
 */
class ExLedger extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ex_ledger';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('from_id, invoice_number, invoice_date, invoice_due_date, gst, amount, total, chargecode, weight, volume, chargeable, created, fid, model, type', 'required'),
			array('from_id, status, fid, type, currency', 'numerical', 'integerOnly'=>true),
			array('invoice_number, model', 'length', 'max'=>45),
			array('gst', 'length', 'max'=>25),
			array('amount, total, weight, volume, chargeable', 'length', 'max'=>10),
			array('chargecode', 'length', 'max'=>15),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, from_id, invoice_number, invoice_date,invoice_due_date, gst, amount, total, chargecode, weight, volume, chargeable, created, status, fid, model, type, currency', 'safe', 'on'=>'search'),
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
            'creditor' => array(self::BELONGS_TO,'Org','from_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'from_id' => 'From',
			'invoice_number' => 'Invoice Number',
			'invoice_date' => 'Invoice Date',
            'invoice_due_date' => 'Invoice Due',
			'gst' => 'GST',
			'amount' => 'Amount(exl.GST)',
			'total' => 'Total',
			'chargecode' => 'Chargecode',
			'weight' => 'Weight(Kg)',
			'volume' => 'Volume(m3)',
			'chargeable' => 'Chargeable(Kg)',
			'created' => 'Created',
			'status' => 'Status',
			'fid' => 'Fid',
			'model' => 'Model',
			'type' => 'Type',
			'currency' => 'Currency',
		);
	}

    public function getTaxType(){
        return Yii::t(strtolower(__CLASS__), Invoice::$TaxType[$this->tax]);
    }

    public function getCCodeDesc() {
        if (!isset($this->chargecode) ) return '';
        $desc = $this->chargecode;

        $fixedCodes = array(
            ['value' => '91010', 'label' => 'COS commercial documentation'],
            ['value' => '91014', 'label' => 'COS export/Import commercial local'],
            ['value' => '91014', 'label' => 'EDF Surcharge (Missing FWB)'],
            ['value' => '91012', 'label' => 'COS export /import commercial parcel arifreight/ocean']
        );
        foreach ( $fixedCodes as $code ) {
            if ( $code['value'] == $desc ) {
                $desc = $code['label'];
                break;
            }
        }

        return $desc;
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('from_id',$this->from_id);
		$criteria->compare('invoice_number',$this->invoice_number,true);
		$criteria->compare('invoice_date',$this->invoice_date,true);
        $criteria->compare('invoice_due_date',$this->invoice_date,true);
		$criteria->compare('gst',$this->gst,true);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('total',$this->total,true);
		$criteria->compare('chargecode',$this->chargecode,true);
		$criteria->compare('weight',$this->weight,true);
		$criteria->compare('volume',$this->volume,true);
		$criteria->compare('chargeable',$this->chargeable,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('fid',$this->fid);
		$criteria->compare('model',$this->model,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('currency',$this->currency);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ExLedger the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
