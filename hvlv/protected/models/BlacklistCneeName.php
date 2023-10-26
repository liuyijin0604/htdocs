<?php

/**
 * This is the model class for table "blacklist_cnee_name".
 *
 * The followings are the available columns in table 'blacklist_cnee_name':
 * @property integer $id
 * @property string $keyword
 * @property string $updated
 * @property string $meta
 */
class BlacklistCneeName extends CActiveRecord
{

	public $mdata = [];

	public $note;
	public $name;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'blacklist_cnee_name';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('keyword', 'required'),
			array('keyword, note, name', 'length', 'max' => 255),
			// array('active', 'integerOnly' => true),
			array('keyword, meta, note, name, active, created', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, keyword, updated, meta, note, name, active, created', 'safe', 'on'=>'search'),
		);
	}

	public function beforeSave()
	{
		if (!empty($this->note)) {
			$this->mdata['note'] = $this->note;
		}
		if (!empty($this->name)) {
			$this->mdata['name'] = $this->name;
		}	

		if (!empty($this->mdata)) {
			$this->meta = json_encode($this->mdata);
		}
		return true;
	}

	public function afterFind()
	{
		if (!empty($this->meta)) {
			$this->mdata= json_decode($this->meta, true);
			if (!empty($this->mdata['note'])) {
				$this->note= $this->mdata['note'];
			}
			if (!empty($this->mdata['name'])) {
				$this->name= $this->mdata['name'];
			}			
		}
		return parent::afterFind();
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
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'keyword' => 'Keyword',
			'note' => 'Note',
			'updated' => 'Updated',
			'meta' => 'Meta',
			'active' => 'Active',
			'created' => 'Created',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('keyword',$this->keyword,true);
		$criteria->compare('updated',$this->updated,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('t.active', $this->active);

		if(!empty($this->note))
		{
			$criteria->addCondition("json_value(t.meta,'$.note') like '%".$this->note."%'");
		}
		if(!empty($this->name))
		{
			$criteria->addCondition("json_value(t.meta,'$.name') like '%".$this->name."%'");
		}

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function getNote()
	{
		if (!empty($this->mdata['note'])) {
			return $this->mdata['note'];
		}
		else{
			return null;
		}
	}

	public function getActive()
	{
		return Yii::t(strtolower(__CLASS__), $this->active == 1 ? 'Yes' : 'No');
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BlacklistCneeName the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
