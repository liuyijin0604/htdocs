<?php

/**
 * This is the model class for table "cn_city".
 *
 * The followings are the available columns in table 'cn_city':
 * @property string $id
 * @property string $name
 * @property string $pid
 * @property string $init
 * @property integer $weight
 */
class CnCity extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'cn_city';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, name, pid, weight', 'safe'),
			array('weight', 'numerical', 'integerOnly'=>true),
			array('id, pid', 'length', 'max'=>11),
			array('name', 'length', 'max'=>50),
			array('init', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, pid, init, weight', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'province' => array(self::BELONGS_TO, 'CnProvince', 'pid'),
			'areas' => array(self::HAS_MANY, 'CnArea', 'city_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'pid' => 'Pid',
			'init' => 'Initial',
			'weight' => 'Weight',
		);
	}

	public function getZip(){
		$sql = "SELECT zip FROM cn_area WHERE city_id = ".$this->id." ORDER BY id LIMIT 1";
		$c = Yii::app()->db->createCommand($sql);
		return $c->queryScalar();
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('init',$this->init,true);
		$criteria->compare('weight',$this->weight);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CnCity the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
