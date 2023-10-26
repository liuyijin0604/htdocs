<?php

/**
 * This is the model class for table "crm_comp".
 *
 * The followings are the available columns in table 'crm_comp':
 * @property integer $id
 * @property integer $crm_id
 * @property integer $flag
 * @property double $freight_fee
 * @property double $loss_fee
 * @property double $claim_amount
 * @property double $approve_ammount
 * @property string $port
 * @property string $bank_name
 * @property string $bsb
 * @property string $account_name
 * @property integer $account_number
 * @property string $meta
 */
class CrmComp extends CActiveRecord
{
    public $mdata=[];
    public $nolog,$custom_log_note;
       /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'crm_comp';
	}
        public  static $flags=array(
            0=>'需要填写申请表',
            1=>'已经填写申请表',
        );

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('crm_id', 'required'),
			array('crm_id,flag', 'numerical', 'integerOnly'=>true),
			array('freight_fee, claim_amount,loss_fee,approve_ammount', 'numerical'),
			array('bank_name', 'length', 'max'=>100),
                        array('port', 'length', 'max'=>100),
                        array('account_number', 'length', 'max'=>80),
			array('bsb', 'length', 'max'=>20),
			array('account_name', 'length', 'max'=>40),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, crm_id, freight_fee,loss_fee,approve_ammount,flag,claim_amount, bank_name, bsb,port, account_name, account_number, meta', 'safe', 'on'=>'search'),
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
			'crm_id' => 'Crm',
			'freight_fee' => 'Freight Fee',
			'claim_amount' => 'Claim Amount',
			'bank_name' => 'Bank Name',
			'bsb' => 'Bsb',
                        'port'=>'Port',
                        'flag'=> 'Flag',
                        'approve_ammount' =>'Approve Ammount',
                        'loss_fee' => 'Loss Fee',
			'account_name' => 'Account Name',
			'account_number' => 'Account Number',
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
		$criteria->compare('crm_id',$this->crm_id);
		$criteria->compare('freight_fee',$this->freight_fee);
                $criteria->compare('loss_fee',$this->loss_fee);
                $criteria->compare('approve_ammount',$this->approve_ammount);
                $criteria->compare('port',$this->port);
		$criteria->compare('claim_amount',$this->claim_amount);
		$criteria->compare('bank_name',$this->bank_name,true);
		$criteria->compare('bsb',$this->bsb,true);
		$criteria->compare('account_name',$this->account_name,true);
		$criteria->compare('account_number',$this->account_number);
		$criteria->compare('meta',$this->meta,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        public function afterFind(){
            if(!empty($this->meta)) $this->mdata= json_decode ($this->meta,true);
            return true;
        }
        public function beforeSave(){
            if(!empty($this->mdata)) $this->meta= json_encode ($this->mdata);
            return true;
        }
        public function afterSave(){
            	if(!$this->nolog && !empty($this)){
                        $this->custom_log_note.='Freight Fee: '. $this->freight_fee.' Loss Fee:'.$this->loss_fee. ' Claim Ammount: '.$this->claim_amount.' Approve Ammount: '.$this->approve_ammount.' BankName: '.$this->bank_name. ' Bsb: '. $this->bsb
                                .' AccountName: '. $this->account_name. ' Account Number: '.$this->account_number. 'Port: '.$this->port.' Currency: '.@$this->mdata['approve_currency']. ' Port comp: '.@$this->mdata['port_compensation'];
			$extra = empty($this->custom_log_note)? array() : array('note' => $this->custom_log_note);
			Log::add($this, $this->isNewRecord? 3 : 4, array_merge(array('status' => ''), $extra));
		}
        }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CrmComp the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
