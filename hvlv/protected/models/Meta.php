<?php

/**
 * This is the model class for table "meta".
 *
 * The followings are the available columns in table 'meta':
 * @property string $id
 * @property string $model
 * @property integer $fid
 * @property string $key
 * @property string $value
 */
class Meta extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'meta';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('model, fid, key, value', 'safe'),
			array('fid', 'numerical', 'integerOnly'=>true),
			array('id', 'length', 'max'=>11),
			array('model, key', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, model, fid, key, value', 'safe', 'on'=>'search'),
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
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'model' => 'Model',
			'fid' => 'Fid',
			'key' => 'Key',
			'value' => 'Value',
		);
	}

	public static function saveAll($m, $mkv='_mkv'){
		if(empty($m->{$mkv}) || empty($m->id)) return false;
		$attr = ['model' => get_class($m), 'fid' => $m->id,];
		foreach($m->{$mkv} as $k=>$v){
			$o = self::model()->findByAttributes($attr+['key' => $k]);
			if(!$o){
				$o = new Meta;
				$o->attributes = $attr+['key' => $k];
			}
			$o->value = $v;
			$o->save();
		}
	}

	public static function getAll(&$m, $mkv='_mkv'){
		if(!isset($m->{$mkv}) || empty($m->id)) return false;
		$rs = self::model()->findAllByAttributes(['model' => get_class($m), 'fid' => $m->id,]);
		if(empty($rs)) return false;
		foreach($rs as $r){
			$m->{$mkv}[$r->key] = $r->value;
		}
	}

	public static function getVal(&$m, $k){
		$r = self::model()->findByAttributes(['model' => get_class($m), 'fid' => $m->id, 'key' => $k]);
		if(empty($r)) return null;
		return $r->value;
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
		$criteria->compare('model',$this->model);
		$criteria->compare('fid',$this->fid);
		$criteria->compare('key',$this->key);
		$criteria->compare('value',$this->value,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Meta the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
