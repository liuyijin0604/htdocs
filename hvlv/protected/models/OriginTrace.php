<?php

/**
 * This is the model class for table "origin_trace".
 *
 * The followings are the available columns in table 'origin_trace':
 * @property string $id
 * @property integer $pvdr
 * @property string $no
 * @property string $pid
 * @property string $ref
 * @property string $bdate
 */
class OriginTrace extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'origin_trace';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pvdr, no', 'required'),
			array('pid, ref, bdate', 'safe'),
			array('pvdr', 'numerical', 'integerOnly'=>true),
			array('no, ref', 'length', 'max'=>50),
			array('pid', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, pvdr, no, pid, ref, bdate', 'safe', 'on'=>'search'),
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
		return parent::beforeSave();
	}
	
	public function afterFind(){
		return parent::afterFind();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'pvdr' => 'Pvdr',
			'no' => 'No',
			'pid' => 'Pid',
			'ref' => 'Ref',
			'bdate' => 'Bdate',
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
		$criteria->compare('pvdr',$this->pvdr);
		$criteria->compare('no',$this->no,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('ref',$this->ref,true);
		$criteria->compare('bdate',$this->bdate,true);
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
	 * @return OriginTrace the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
