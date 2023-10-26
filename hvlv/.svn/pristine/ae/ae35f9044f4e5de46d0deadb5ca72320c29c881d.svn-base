<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 6/07/2017
 * Time: 10:34 AM
 */

/**
 * This is the model class for table "consol_weight_check".
 *
 * The followings are the available columns in table 'consol_weight_check':
 * @property string $id
 * @property string $no
 * @property integer $status
 * @property string $dpt_id
 * @property string $channel
 * @property string $channel_cost
 * @property string $channel_weight
 * @property string $unit_cost_kg
 * @property integer $channel_qty
 * @property string $invoice_revenue
 * @property string $invoice_weight
 * @property string $charge_rate_kg
 * @property integer $awb_qty
 * @property string $created
 * @property integer $user_id
 * @property string $meta
 */
class ConsolWeightCheck extends CActiveRecord
{
    public $owner_id;
    public $qty_var,$weight_var,$unit_rate_var,$exchange_rate,$date,$due,$invoice_ref,$cost_var;
    public $mdata = array();

    public static $channels = array(
        'KM' => 'KM',
        'CD' => 'CD',
        'JM' => 'JM',
        'CS' => 'CS',
        'TJ' => 'TJ'
    );


    public function beforeSave(){
        if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);
        return true;
    }

    public function afterFind(){
        if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
        $this->qty_var = $this->channel_qty - $this->awb_qty;
        $this->weight_var = $this->channel_weight - $this->invoice_weight;
        $this->unit_rate_var = $this->unit_cost_kg - $this->charge_rate_kg;
        $this->exchange_rate = isset($this->mdata['exchange_rate']) ?  $this->mdata['exchange_rate'] : 0;
        $this->cost_var = $this->channel_cost - $this->invoice_revenue;
        if ( isset($this->mdata['date']) ) $this->date = $this->mdata['date'];
        if ( isset($this->mdata['due']) ) $this->date = $this->mdata['due'];
        if ( isset($this->mdata['invoice_ref']) ) $this->date = $this->mdata['invoice_ref'];

        return true;
    }

    /**
     * return original channel cost before calculate by exchange rate
     * @return int
     */
    public function getOriginChannelCostTotal(){
        if ( isset($this->mdata['o_channel_cost']) ) {
            return $this->mdata['o_channel_cost'];
        } else {
            return 0;
        }
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
        return 'consol_weight_check';
    }

    public function getInvoiceRef(){
        $invoiceRef = '';
        foreach ( $this->lines as $line ) {
            $invoiceRef = $line->invoice_ref;
            break;
        }
        return $invoiceRef;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('no, channel', 'required'),
            array('channel_qty, awb_qty, user_id', 'numerical', 'integerOnly'=>true),
            array('no', 'length', 'max'=>15),
            array('dpt_id, channel, channel_cost, channel_weight, unit_cost_kg, invoice_revenue, invoice_weight, charge_rate_kg', 'length', 'max'=>10),
            array('meta', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, no, dpt_id, channel, channel_cost,status, channel_weight, unit_cost_kg, channel_qty, invoice_revenue, invoice_weight, charge_rate_kg, awb_qty, created, user_id, meta', 'safe', 'on'=>'search'),
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
            'lines' => array(self::HAS_MANY, 'ConsolWeightCheckLine', 'parent_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'no' => 'No',
            'status' => 'Status',
            'dpt_id' => 'Dpt',
            'channel' => 'Channel',
            'channel_cost' => 'Channel Cost',
            'channel_weight' => 'Channel Weight',
            'unit_cost_kg' => 'Unit Cost / Kg',
            'channel_qty' => 'Channel Pkg Qty',
            'invoice_revenue' => 'Invoice Revenue',
            'invoice_weight' => 'Invoice Weight',
            'charge_rate_kg' => 'Charge Rate / Kg',
            'awb_qty' => 'Awb Qty',
            'created' => 'Created',
            'user_id' => 'User',
            'meta' => 'Meta',
            'qty_var' => 'Qty Variance',
            'weight_var' => 'Weight Variance',
            'unit_rate_var' => 'Unit Rate Variance'
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
        $criteria->compare('no',$this->no,true);
        $criteria->compare('status',$this->status);
        $criteria->compare('dpt_id',$this->dpt_id,true);
        $criteria->compare('channel',$this->channel,true);
        $criteria->compare('channel_cost',$this->channel_cost,true);
        $criteria->compare('channel_weight',$this->channel_weight,true);
        $criteria->compare('unit_cost_kg',$this->unit_cost_kg,true);
        $criteria->compare('channel_qty',$this->channel_qty);
        $criteria->compare('invoice_revenue',$this->invoice_revenue,true);
        $criteria->compare('invoice_weight',$this->invoice_weight,true);
        $criteria->compare('charge_rate_kg',$this->charge_rate_kg,true);
        $criteria->compare('awb_qty',$this->awb_qty);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('meta',$this->meta,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder'=>'t.created DESC',
            ),
            'pagination'=> array(
                'pageSize' => 30,
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ConsolWeightCheck the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}