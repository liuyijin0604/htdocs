<?php

/**
 * This is the model class for table "consol_weight_check_line".
 *
 * The followings are the available columns in table 'consol_weight_check_line':
 * @property integer $id
 * @property integer $parent_id
 * @property string $consol_id
 * @property string $consol_date
 * @property string $awb_no
 * @property string $awb_weight
 * @property integer $awb_qty
 * @property string $invoice_weight
 * @property string $invoice_revenue
 * @property string $channel_weight
 * @property integer $channel_qty
 * @property string $clearance_cost
 * * @property string $delivery_cost
 * @property string $duty
 * @property string $others
 * @property string $invoice_ref
 * @property integer $selected
 */
class ConsolWeightCheckLine extends CActiveRecord
{
    public $ttlCost,$ttlInvoice,$weight_var,$qty_var,$clearance_var,$delivery_var,$duty_var,$others_var,$cost_var,$consol,$accr_clearance,$accr_delivery,$accr_duty,$accr_others;


    public function getTtlCost(){
        return $this->clearance_cost  + $this->delivery_cost + $this->duty + $this->others;
    }

    public function getTtlInvoice(){
        $exConsol = ExcoConsol::model()->find('awb = :awb' , [':awb' => $this->awb_no]);
        if ( !empty($exConsol) ) {
            return  $exConsol->totRevenue();
        }
        return 0;
    }

    public function afterFind(){
        $this->consol = ExcoConsol::model()->find('no = :no', array(':no' => $this->consol_id));
        $sql = 'select * from consol_weight_check_line where consol_id = "' . $this->consol_id . '" AND parent_id = ' . $this->parent_id . ' AND id < ' . $this->id;
        $dup_line = count(Yii::app()->db->createCommand($sql)->queryAll()) > 0;
        $this->ttlCost = $this->clearance_cost + $this->delivery_cost + $this->duty + $this->others;
        $this->weight_var = number_format($this->invoice_weight - $this->channel_weight, 2, '.', '');
        $this->qty_var = $this->awb_qty - $this->channel_qty;
        $this->accr_clearance = $dup_line ? '-' : number_format(PlLedger::getTotal('ExParcel', $this->consol->id, 'dc', 3) * $this->consol->exrate, 2, '.', '');
        $this->accr_delivery = $dup_line ? '-' : number_format(PlLedger::getTotal('ExParcel', $this->consol->id, 'cr', 3) * $this->consol->exrate, 2, '.', '');
        $this->accr_duty = $dup_line ? '-' : 0;
        $this->accr_others = $dup_line ? '-' : 0;
        $this->clearance_var = $dup_line ? 0 - $this->clearance_cost : ($this->accr_clearance - $this->clearance_cost);
        $this->delivery_var = $dup_line ? 0 - $this->delivery_cost : ($this->accr_delivery - $this->delivery_cost);
        $this->duty_var = $dup_line ? 0 - $this->duty : ($this->accr_duty - $this->duty);
        $this->others_var = $dup_line ? 0 - $this->others : ($this->accr_others - $this->others);
        $this->cost_var = $dup_line ? 0 - $this->ttlCost : ($this->invoice_revenue - $this->ttlCost);
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'consol_weight_check_line';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// array('parent_id, consol_id, consol_date, awb_no, awb_weight, awb_qty, invoice_weight, channel_weight, channel_qty, clearance_cost, duty, others, invoice_ref', 'required'),
			array('parent_id, awb_qty, channel_qty, selected', 'numerical', 'integerOnly'=>true),
			array('consol_id', 'length', 'max'=>15),
			array('awb_no', 'length', 'max'=>20),
			array('awb_weight, invoice_weight, invoice_revenue, channel_weight, clearance_cost,delivery_cost, duty, others', 'length', 'max'=>10),
			array('invoice_ref', 'length', 'max'=>25),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, parent_id, consol_id, consol_date, awb_no, awb_weight, awb_qty, invoice_weight, invoice_revenue, channel_weight, channel_qty, clearance_cost, delivery_cost,duty, others, invoice_ref, selected', 'safe', 'on'=>'search'),
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
			'cwc' => array(self::BELONGS_TO, 'ConsolWeightCheck', 'parent_id'),
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
			'consol_id' => 'Consol',
			'consol_date' => 'Consol Date',
			'awb_no' => 'Awb No',
			'awb_weight' => 'Awb Weight',
			'awb_qty' => 'Awb Qty',
			'invoice_weight' => 'Invoice Weight',
			'invoice_revenue' => 'Invoice Revenue',
			'channel_weight' => 'Channel Weight',
			'channel_qty' => 'Channel Qty',
			'clearance_cost' => 'Clearance Cost',
            'delivery_cost' => 'Delivery Cost',
			'duty' => 'Duty',
			'others' => 'Others',
			'invoice_ref' => 'Invoice Ref',
			'selected' => 'Selected',
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
	public function search($odr = 't.id ASC')
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('consol_id',$this->consol_id,true);
		$criteria->compare('consol_date',$this->consol_date,true);
		$criteria->compare('awb_no',$this->awb_no,true);
		$criteria->compare('awb_weight',$this->awb_weight,true);
		$criteria->compare('awb_qty',$this->awb_qty);
		$criteria->compare('invoice_weight',$this->invoice_weight,true);
		$criteria->compare('invoice_revenue',$this->invoice_revenue,true);
		$criteria->compare('channel_weight',$this->channel_weight,true);
		$criteria->compare('channel_qty',$this->channel_qty);
		$criteria->compare('clearance_cost',$this->clearance_cost,true);
        $criteria->compare('delivery_cost',$this->delivery_cost,true);
		$criteria->compare('duty',$this->duty,true);
		$criteria->compare('others',$this->others,true);
		$criteria->compare('invoice_ref',$this->invoice_ref,true);
		$criteria->compare('selected',$this->selected);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort' => array(
				'defaultOrder' => $odr
			),
            'pagination'=> array(
                'pageSize' => 30,
            ),
		));
	}

	public function getTotal($records, $column, $check_dup = true) {
		$total = 0;
		$dup = array();
		foreach ($records as $record) {
			if ($check_dup && in_array($record->consol_id, $dup)) continue;
			$total += is_numeric($record->$column) ? $record->$column : 0;
			$dup[] = $record->consol_id;
		}
		return $total;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ConsolWeightCheckLine the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
