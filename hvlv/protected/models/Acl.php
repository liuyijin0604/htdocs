<?php

/**
 * This is the model class for table "Acl".
 *
 * The followings are the available columns in table 'Acl':
 * @property string $id
 * @property string $oid
 * @property string $gid
 * @property string $uid
 * @property string $p
 */
class Acl extends CActiveRecord
{
	
	public static $perms = array(
		1 => 'Deny',
		2 => 'Allow',
	);

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'acl';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('oid, p', 'required'),
			array('gid, uid', 'safe'),
			array('oid, gid, uid, p', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, oid, gid, uid, p', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'obj' => array(self::BELONGS_TO, 'AclObj', 'oid'),
			'user' => array(self::BELONGS_TO, 'User', 'uid'),
		);
	}

	public static function register($o){
		$path = explode('/', $o);
		$pid = 0;
		foreach($path as $i=>$p){
			$n = implode('/', array_slice($path, 0, $i + 1));
			$ao = AclObj::model()->find('name = :n', array(':n' => $n));
			if(empty($ao)){
				$ao = new AclObj;
				$ao->pid = $pid;
				$ao->name = $n;
				$ao->save();
			}
			$pid = $ao->id;
		}
		return $ao;
	}

	public static function hasAccess($n, $te=false){
		self::register($n);
		if(!isset(Yii::app()->user->grp)) return false;
		if(Yii::app()->user->grp == 0) return true;
		$ao = AclObj::model()->find('name = :n', array(':n' => $n));
		if(empty($ao)){
			if($te) Acl::denied403();
			return false;
		}
		return $ao->hasAccess($te);
	}

	public static function denied403(){
		throw new CHttpException(403, 'Access Denied!');
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'oid' => 'Oid',
			'gid' => 'Gid',
			'uid' => 'Uid',
			'p' => 'P',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('oid',$this->oid,true);
		$criteria->compare('gid',$this->gid,true);
		$criteria->compare('uid',$this->uid,true);
		$criteria->compare('p',$this->p,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Acl the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
