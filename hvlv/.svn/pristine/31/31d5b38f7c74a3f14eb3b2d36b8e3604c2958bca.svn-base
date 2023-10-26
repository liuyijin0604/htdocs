<?php

/**
 * This is the model class for table "port_invoice_reconciliation".
 *
 * The followings are the available columns in table 'port_invoice_reconciliation':
 * @property string $id
 * @property string $created
 * @property string $invoice_ref
 * @property string $weight
 * @property string $amount
 * @property string $duplicate_count
 * @property string $notexisting_count
 * @property string $meta
 */
class PortInvoiceReconciliation extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'port_invoice_reconciliation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created, invoice_ref', 'required'),
			array('invoice_ref', 'length', 'max'=>45),
			array('weight, amount, duplicate_count, notexisting_count', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, created, invoice_ref, weight, amount, duplicate_count, notexisting_count, meta', 'safe', 'on'=>'search'),
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
            'lines' => array(self::HAS_MANY, 'PortInvoiceReconciliationLine', 'parent_id'),
		);
	}

    public function getTotalWeight(){
        $sql = 'SELECT SUM(weight) FROM PortInvoiceReconciliationLine WHERE parent_id = '.$this->id;
        $c = Yii::app()->db->createCommand($sql);
        return  round(floatval($c->queryScalar()),2);
    }

    public function getTotalAmount(){
        $sql = 'SELECT SUM(amont) FROM PortInvoiceReconciliationLine WHERE parent_id = '.$this->id;
        $c = Yii::app()->db->createCommand($sql);
        return  round(floatval($c->queryScalar()),2);
    }


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'created' => 'Created',
			'invoice_ref' => 'Invoice Ref',
			'weight' => 'Weight',
			'amount' => 'Amount',
			'duplicate_count' => 'Duplicate Count',
			'notexisting_count' => 'Notexisting Count',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('invoice_ref',$this->invoice_ref,true);
		$criteria->compare('weight',$this->weight,true);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('duplicate_count',$this->duplicate_count,true);
		$criteria->compare('notexisting_count',$this->notexisting_count,true);
		$criteria->compare('meta',$this->meta,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=> array(
                'pageSize'=> 100,
            ),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PortInvoiceReconciliation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
