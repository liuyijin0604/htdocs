<?php

/**
 * This is the model class for table "insurance_claim".
 *
 * The followings are the available columns in table 'insurance_claim':
 * @property string $id
 * @property string $note
 * @property string $shipment_id
 * @property integer $status
 * @property string $date_added
 * @property string $meta
 */
class InsuranceClaim extends CActiveRecord
{
    public $mdata = array();

    // claim status
    public static $states = array(
        1 => 'New',
        2 => 'Pending',
        3 => 'Rejected',
        4 => 'Approved',
    );

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'insurance_claim';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date_added', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('note', 'length', 'max'=>240),
			array('shipment_id', 'length', 'max'=>10),
			array('meta', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, note, shipment_id, status, date_added, meta', 'safe', 'on'=>'search'),
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
            'shipment' => array(self::BELONGS_TO, 'Shipment', 'shipment_id'),
            'photos' => array(self::HAS_MANY, 'FileRepo', 'fid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'note' => 'Note',
			'shipment_id' => 'Shipment',
			'status' => 'Status',
			'date_added' => 'Date Added',
			'meta' => 'Meta',
		);
	}

    public function getStatus(){
        return isset(static::$states[$this->status])? Yii::t(strtolower(__CLASS__), static::$states[$this->status]) : $this->status;
    }

    public function getMemo(){
        if ( $this->status == 3 ) {
            return 'Reject Reason : ' . $this->mdata['reject_reason'];
        }
        return '';
    }

    public function beforeSave(){
        if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);
        return true;
    }

    public function afterFind(){
        if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
        return true;
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
	public function search($statusIds = array())
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('note',$this->note,true);
		$criteria->compare('shipment_id',$this->shipment_id,true);

        if(!empty($statusIds)){
            $sc1 = new CDbCriteria;
            $sc1->addInCondition("status", $statusIds);
            $criteria->mergeWith($sc1);
        } else {
            $criteria->compare('status', $this->status);
        }
		$criteria->compare('date_added',$this->date_added,true);
		$criteria->compare('meta',$this->meta,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return InsuranceClaim the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
