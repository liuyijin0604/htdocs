<?php

/**
 * This is the model class for table "acl_obj".
 *
 * The followings are the available columns in table 'acl_obj':
 * @property string $id
 * @property string $pid
 * @property string $name
 * @property string $desc
 */
class AclObj extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'acl_obj';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('pid, desc', 'safe'),
			array('pid', 'length', 'max'=>11),
			array('name', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, pid, name, desc', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'parent' => array(self::BELONGS_TO, 'AclObj', 'pid'),
			'children' => array(self::HAS_MANY, 'AclObj', 'pid'),
		);
	}

	public function hasAccess($te = false){
		$path = $this->aclPath();
		foreach($path as $c){
			$up = Acl::model()->find('oid = :oid AND uid = :uid', array(':oid' => $c->id, ':uid' => Yii::app()->user->id));
			if($up && $up->p > 0) return $up->p == 2;
			$gp = Acl::model()->find('oid = :oid AND gid = :gid', array(':oid' => $c->id, ':gid' => Yii::app()->user->grp));
			if($gp && $gp->p > 0) return $gp->p == 2;
		}
		if($te) Acl::denied403();
		return false;
	}

	public function aclPath(){
		$p = [$this];
		if($this->pid > 0){
			return array_merge($p, $this->parent->aclPath());
		}
		return $p;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'pid' => 'Pid',
			'name' => 'Name',
			'desc' => 'Desc',
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
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('desc',$this->desc,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AclObj the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
