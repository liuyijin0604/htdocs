<?php
/**
 * This is the model class for table "tms_task".
 *
 * The followings are the available columns in table 'tms_task':
 * @property string $id
 * @property string $carrier_id
 * @property string $from_id
 * @property string $to_id
 * @property integer $type
 * @property integer $status
 * @property string $ref
 * @property string $cref
 * @property integer $plt
 * @property double $weight
 * @property double $cbm
 * @property string $schd_time
 * @property string $due_time
 * @property string $compl_time
 * @property integer $bwf
 * @property string $meta
 */
class TmsTask extends CActiveRecord
{
	public static $types = array(
		10 => 'C2W',
		20 => 'C2C',
		30 => 'W2C',
		40 => 'W2T',
		50 => 'W2W',
		60 => 'T2W',
	);

	public static $states = array(
		10 => 'Pending',
		20 => 'Scheduled',
		30 => 'Accepted',
		99 => 'Completed',
		100 => 'Cancelled',
	);

	public $mdata = [];
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'tms_task';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('carrier_id, from_id, to_id, type, status', 'required'),
			array('ref, cref, plt, weight, cbm, bwf, meta', 'safe'),
			array('type, status, plt, bwf', 'numerical', 'integerOnly'=>true),
			array('weight, cbm', 'numerical'),
			array('carrier_id, from_id, to_id', 'length', 'max'=>11),
			array('ref, cref', 'length', 'max'=>50),
			array('schd_time, due_time, compl_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, carrier_id, from_id, to_id, type, status, ref, cref, plt, weight, cbm, schd_time, due_time, compl_time, bwf, meta', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	
	
	public function beforeSave(){
		$this->meta = empty($this->mdata)? '' : json_encode($this->mdata);
		return parent::beforeSave();
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return parent::afterFind();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'carrier_id' => 'Carrier',
			'from_id' => 'From',
			'to_id' => 'To',
			'type' => 'Type',
			'status' => 'Status',
			'ref' => 'Ref',
			'cref' => 'Cref',
			'plt' => 'Plt',
			'weight' => 'Weight',
			'cbm' => 'Cbm',
			'schd_time' => 'Schd Time',
			'due_time' => 'Due Time',
			'compl_time' => 'Compl Time',
			'bwf' => 'Bwf',
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
	public function search($pgn=true, $ps = 30, $ec = false){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('carrier_id',$this->carrier_id,true);
		$criteria->compare('from_id',$this->from_id,true);
		$criteria->compare('to_id',$this->to_id,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('status',$this->status);
		$criteria->compare('ref',$this->ref,true);
		$criteria->compare('cref',$this->cref,true);
		$criteria->compare('plt',$this->plt);
		$criteria->compare('weight',$this->weight);
		$criteria->compare('cbm',$this->cbm);
		$criteria->compare('schd_time',$this->schd_time,true);
		$criteria->compare('due_time',$this->due_time,true);
		$criteria->compare('compl_time',$this->compl_time,true);
		$criteria->compare('bwf',$this->bwf);
		$criteria->compare('meta',$this->meta,true);
		$with = [];

		if(!empty($with)){
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if($ec) $criteria->mergeWith($ec);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'t.id DESC',
 			),
			'pagination'=> $pgn? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TmsTask the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
