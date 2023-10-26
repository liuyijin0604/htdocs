<?php

/**
 * This is the model class for table "hash_verify".
 *
 * The followings are the available columns in table 'hash_verify':
 * @property integer $id
 * @property integer $pid
 * @property integer $type
 * @property string $hash
 * @property string $date
 * @property string $meta
 */
class HashVerify extends CActiveRecord
{
	public $mdata;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hash_verify';
	}
		
	public static $types=[
		'ImParcel'=>1,
		'ExCrm' => 2,
	];

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['pid, type, hash,', 'required'],
			['pid, type', 'numerical', 'integerOnly'=>true],
			['hash', 'length', 'max'=>40],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, pid, type, hash, date,meta', 'safe', 'on'=>'search'],
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'pid' => 'Pid',
			'type' => 'Type',
			'hash' => 'Hash',
			'meta' => 'Meta',
		];
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

		$criteria->compare('id', $this->id);
		$criteria->compare('pid', $this->pid);
		$criteria->compare('type', $this->type);
		$criteria->compare('hash', $this->hash, true);
		$criteria->compare('date', $this->date, true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}
		
	public static function genHash($m)
	{
		$theType= self::$types[get_class($m)];
		if (empty($theType)) {
			$theType=1;
		}
		$r= self::model()->find('pid=:pid and type=:type', [':pid'=>$m->id,':type'=>$theType]);
		if (empty($r)) {
			$r=new HashVerify();
			$r->pid=$m->id;
			$r->type=$theType;
			$hash = md5(serialize($m).microtime());
			$r->hash=$hash;
		}
		$r->date=date('Y-m-d');
		$r->save();
		return $r->hash;
	}

	public function afterFind()
	{
		if (!empty($this->meta)) {
			$this->mdata= json_decode($this->meta, true);
		}
	}
	
	public function beforeSave()
	{
		if (!empty($this->mdata)) {
			$this->meta= json_encode($this->mdata);
		}
		return true;
	}
		
	public static function verify($hash, $pid, $type='ImParcel')
	{
		$theType= self::$types[$type];
		if (empty($type)) {
			return false;
		}
		$r= self::model()->find('hash=:hash and pid=:pid and type=:type AND date>=SUBDATE(:date,INTERVAL 30 DAY)', [':hash'=>$hash,':pid'=>$pid,':type'=>$theType,':date'=>date('Y-m-d')]);
		if (!empty($r)) {
			return true;
		}
		return false;
	}
		
		

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HashVerify the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
