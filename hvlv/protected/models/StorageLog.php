<?php

/**
 * This is the model class for table "storage_log".
 *
 * The followings are the available columns in table 'storage_log':
 * @property string $id
 * @property string $sid
 * @property string $model
 * @property string $fid
 * @property string $in_dt
 * @property string $out_dt
 * @property integer $ckd
 */
class StorageLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'storage_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sid, model, fid, in_dt', 'required'),
			array('out_dt, ckd', 'safe'),
			array('sid, fid', 'length', 'max'=>11),
			array('model', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, sid, model, fid, in_dt, out_dt, ckd', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'parcel' => array(self::BELONGS_TO, 'ImParcel', 'fid'),
			'storage' => array(self::BELONGS_TO, 'Storage', 'sid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'sid' => 'Sid',
			'model' => 'Model',
			'fid' => 'Fid',
			'in_dt' => 'In Dt',
			'out_dt' => 'Out Dt',
			'ckd' => 'Checked',
		);
	}

	public static function findItem($p){
		$r = self::model()->find('out_dt IS NULL AND fid = :id AND model = :m', array(':id' => $p->id, ':m' => get_class($p)));
		return empty($r)? false : $r;
	}

	public function mm(){
		$r = new $this->model;
		return $r::model()->findByPk($this->fid);
	}

	public function out($upcap = true){
		$this->out_dt = date('Y-m-d H:i:s');
		$this->save();
		if($upcap) $this->storage->updateCap();
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
	public function search(){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('sid',$this->sid);
		$criteria->compare('model',$this->model,true);
		$criteria->compare('fid',$this->fid);
		$criteria->compare('in_dt',$this->in_dt,true);
		$criteria->compare('out_dt',$this->out_dt,true);
		$criteria->compare('ckd',$this->ckd);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return StorageLog the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
