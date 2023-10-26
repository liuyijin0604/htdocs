<?php

/**
 * This is the model class for table "port_invoice_reconciliation_line".
 *
 * The followings are the available columns in table 'port_invoice_reconciliation_line':
 * @property string $id
 * @property integer $parent_id
 * @property string $date
 * @property string $connote
 * @property string $destination
 * @property string $weight
 * @property string $amount
 */
class PortInvoiceReconciliationLine extends CActiveRecord
{
    public $mdata = array();

    public $duplicate_count = 0,$invoice_refs = '',$consols = '' , $notexisting = false;

    public function beforeSave(){
        if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);
        return true;
    }

    public function afterFind(){
        if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
        if ( isset($this->mdata['dup_count']) )  $this->duplicate_count = $this->mdata['dup_count'];
        if ( isset($this->mdata['consol']) )  $this->consols = $this->mdata['consol'];
        if ( isset($this->mdata['invoice_ref']) )  $this->invoice_refs = $this->mdata['invoice_ref'];
        if ( isset($this->mdata['notfound']) )  $this->notexisting = true;
        return true;
    }

    public function updateMeta(){
        $this->meta = json_encode($this->mdata);
        $this->update(['meta']);
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'port_invoice_reconciliation_line';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parent_id, date, connote, destination, weight, amount', 'required'),
			array('parent_id', 'numerical', 'integerOnly'=>true),
			array('connote', 'length', 'max'=>45),
			array('destination', 'length', 'max'=>120),
			array('weight, amount', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, parent_id, date, connote, destination, weight, amount,meta', 'safe', 'on'=>'search'),
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
            'parent' => array(self::BELONGS_TO, 'PortInvoiceReconciliation', 'parent_id'),
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
			'date' => 'Date',
			'connote' => 'Connote',
			'destination' => 'Destination',
			'weight' => 'Weight',
			'amount' => 'Amount',
            'invoice_refs' => 'Invoice Refs',
            'duplicate_count' => 'Duplicate Count',
            'consols' => 'Consol No.',
            'notexisting' => 'Existing'
		);
	}

    public function getExistingStatus(){
        return $this->notexisting ? 'False' : 'True';
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
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('connote',$this->connote,true);
		$criteria->compare('destination',$this->destination,true);
		$criteria->compare('weight',$this->weight,true);
		$criteria->compare('amount',$this->amount,true);

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
	 * @return PortInvoiceReconciliationLine the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
