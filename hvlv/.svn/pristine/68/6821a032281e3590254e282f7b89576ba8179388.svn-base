<?php

/**
 * This is the model class for table "translation".
 *
 * The followings are the available columns in table 'translation':
 * @property integer $id
 * @property integer $type
 * @property string $o
 * @property string $t
 */
class Translation extends CActiveRecord
{
	public static $types = array(
		10 => 'C2E',
		15 => 'E2C',
	);
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'translation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, o, t', 'safe'),
			array('type', 'numerical', 'integerOnly'=>true),
			array('o, t', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, o, t', 'safe', 'on'=>'search'),
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
	
	public function getType(){
		return Yii::t(strtolower(__CLASS__), self::$types[$this->type]);
	}
	
	public static function t($o, $t=false){
		if($t){
			$r = self::model()->find('o = :o && type = :t', array(':o' => $o, ':t' => $t));
		}else{
			$r = self::model()->find('o = :o', array(':o' => $o));
		}
		
		return empty($r)? false : $r->t;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'type' => 'Type',
			'o' => 'From',
			't' => 'To',
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
	public function search(){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('type',$this->type);
		$criteria->compare('o',$this->o,true);
		$criteria->compare('t',$this->t,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
    			'defaultOrder'=>'t.id DESC',
  			),
			'pagination'=>array(
				'pageSize'=>'30',
            ),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Translation the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
