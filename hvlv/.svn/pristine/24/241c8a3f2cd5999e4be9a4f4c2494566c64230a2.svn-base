<?php

/**
 * This is the model class for table "cg_order".
 *
 * The followings are the available columns in table 'cg_order':
 * @property integer $id
 * @property integer $client_id
 * @property integer $op_id
 * @property string $added_datetime
 * @property string $finished_datetime
 * @property integer $status
 * @property string $dispatch_date
 * @property string $amount
 * @property string $no
 * @property integer $delivery_by
 */
class CgOrder extends CActiveRecord
{
    const CGORDER_STATUS_NEW = 0;
    const CGORDER_STATUS_PROCESSING = 1;
    const CGORDER_STATUS_DELIVERING = 3;
    const CGORDER_STATUS_FINISHED = 4;
    const CGORDER_STATUS_CANCELLED = 5;

    public static $states = array(
        self::CGORDER_STATUS_NEW => 'New',
        self::CGORDER_STATUS_PROCESSING => 'Processing',
        self::CGORDER_STATUS_DELIVERING => 'Deliverying',
        self::CGORDER_STATUS_FINISHED => 'Finished',
        self::CGORDER_STATUS_CANCELLED => 'Cancelled'
    );

    public function getStatus(){
        if ( isset(self::$states[$this->status]) ) {
            return Yii::t(strtolower(__CLASS__), self::$states[$this->status]);
        }
        return '';
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cg_order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
           // array('no', 'required'),
			array('client_id, op_id, status,delivery_by', 'numerical', 'integerOnly'=>true),
			array('amount', 'length', 'max'=>10),
            array('no', 'length', 'max'=>15),
			array('added_datetime,finished_datetime, dispatch_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, client_id, op_id, added_datetime, status, dispatch_date, delivery_by,finished_datetime,amount,no', 'safe', 'on'=>'search'),
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
            'org' => array(self::BELONGS_TO,'Org' , 'client_id'),
            'lines' => array(self::HAS_MANY,'CgOrderLine','cd_order_id')
        );
    }

    public function beforeSave(){
        if(empty($this->added_datetime)) $this->added_datetime = date('Y-m-d H:i:s');
        if(empty($this->status)) $this->status = self::CGORDER_STATUS_NEW;
        if(empty($this->no)) $this->no = $this->genNo();
        return true;
    }

    /**
     * generate billing number
     * @return string
     */
    public function genNo(){
        $n = 'CG'.date('ymd', strtotime($this->added_datetime));
        $s = self::model()->count('no LIKE :n', [':n' => $n.'%']) + 1;
        return $n.sprintf('%02d',$s).'SYD';
    }

    /**
     * get order details
     * @return string
     */
    public function getDetails()
    {
        $odata = CgOrderLine::model()->findAll('cd_order_id = :oid', [':oid' => $this->id]);
        $oDetails = '';
        foreach ($odata as $line) {
            $cgf = ConsumableGoods::model()->findByPk($line->cg_id);
            if ( !empty($cgf) ) {
                $oDetails .= $cgf->name . ' : ' . $line->qty . '<br>';
            }
        }
        $oDetails .= 'Amount : ' . $this->amount . '(AUD)' ;
        return $oDetails;
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'client_id' => 'Client',
			'op_id' => 'Op',
			'added_datetime' => 'Added Datetime',
            'finished_datetime' => 'Finished Datetime',
            'delivery_by' => 'Delivery By',
			'status' => 'Status',
			'dispatch_date' => 'Dispatch Date',
			'amount' => 'Amount',
            'no' => 'No',
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
		$criteria->compare('client_id',$this->client_id);
		$criteria->compare('op_id',$this->op_id);
		$criteria->compare('added_datetime',$this->added_datetime,true);
        $criteria->compare('finished_datetime',$this->finished_datetime,true);
        $criteria->compare('delivery_by',$this->delivery_by);
		$criteria->compare('status',$this->status);
		$criteria->compare('dispatch_date',$this->dispatch_date,true);
		$criteria->compare('amount',$this->amount,true);
        $criteria->compare('no',$this->no,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder'=> 't.id DESC',
            ),
            'pagination'=>array(
                'pageSize'=>'30',
            ),
		));
	}

    /**
     * create an others type invoice for me
     */
    public function createInvoice(){
        if ( $this->amount > 0 ) {
            // if old one existing just update
            $invoice = Invoice::model()->find('cg_order_id = :coid',[':coid' => $this->id]);
            if ( empty($invoice) ) {
                $invoice = new Invoice('create');
                $invoice->type = Invoice::INVOICE_TYPE_OTHERS;
                $invoice->dpmt = Invoice::DPMT_IMPORT;
                $invoice->to_id = $this->client_id;
                $invoice->cg_order_id = $this->id;
                $invoice->currency = 1; // default as AUD
                $invoice->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
                $invoice->status = Invoice::INVOICE_STATUS_PENDING;
            } else {
                // clear all old invoice lines
                InvLine::model()->deleteAll('inv_id = :invid' , [':invid' => $invoice->id]);
            }
            $invoice->total = $this->amount;
            $invoice->date = date('Y-m-d');
            $invoice->due = date('Y-m-d');
            $invoice->mdata['name'] = $this->org->name;
            $invoice->mdata['address'] = $this->org->getAddress();
            $invoice->mdata['payterm'] = empty($this->org->extra['payterm'])? 'COD' : $this->org->extra['payterm'].' days';

            if ( !$invoice->save() ) {
                $errors = $invoice->getErrors();
            }


            foreach ($this->lines as $line ) {
                $il = new InvLine;
                $il->inv_id = $invoice->id;
                $il->ccode = '86020'; // default as others income
                $il->det = !empty($line->cg) ? $line->cg->name : 'Unknown items';
                $il->tax = 'OUTPUT';

                // for consumables invoice always including gst
                $amount = round($line->price * 100 / 110,2);
                $gst = round($amount * 10 / 100,2);
               // $il->amount = number_format( $amount + $gst  ,2,'.','');
                $il->amount = number_format( $line->price + $gst  ,2,'.','');
                $il->gst = $gst;
                $il->qty = $line->qty;
                $il->fid = $this->id;
                $il->model = 'CgOrder';
                if ( !$il->save() ) {
                    $errors = $this->getErrors();
                }
            }
        }
    }

    /**
     * @return array
     */
    public static function getNewsOrdersStatic($status = CgOrder::CGORDER_STATUS_NEW){
        $info = array();
        $newOrders = CgOrder::model()->findAll('status = :nstatus', [ ':nstatus' => $status]);
        $info['total'] = count($newOrders);

        // calculate all items count
        $cgInfo = array();
        foreach ( $newOrders as $order ) {
            foreach ( $order->lines as $line ) {
                if ( !isset($cgInfo[$line->cg_id]) ) {
                    $cgInfo[$line->cg_id] = array(
                        'name' => !empty($line->cg) ? $line->cg->name : '',
                        'total' => $line->qty
                    );
                } else {
                    $cgInfo[$line->cg_id]['total'] += $line->qty;
                }
            }
        }
        $info['details'] = $cgInfo;
        return $info;

    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CgOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
