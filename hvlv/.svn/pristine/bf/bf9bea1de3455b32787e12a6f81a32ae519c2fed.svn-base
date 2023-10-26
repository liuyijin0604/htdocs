<?php

/**
 * This is the model class for table "word_replace_usage_log".
 *
 * The followings are the available columns in table 'word_replace_usage_log':
 * @property integer $id
 * @property integer $org_id
 * @property integer $shipment_id
 * @property integer $consol_id
 * @property string $original_word
 * @property string $replace_word
 * @property string $meta
 * @property string $process_time
 */
class WordReplaceUsageLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'word_replace_usage_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('org_id, original_word, process_time, shipment_id, consol_id', 'required'),
			array('org_id, shipment_id, consol_id', 'numerical', 'integerOnly'=>true),
			array('original_word, replace_word', 'length', 'max'=>50),
			array('meta', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, org_id, original_word, replace_word, meta, process_time, shipment_id, consol_id', 'safe', 'on'=>'search'),
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
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'org_id' => 'Org',
			'original_word' => 'Original Word',
			'replace_word' => 'Replace Word',
			'meta' => 'Meta',
			'process_time' => 'Process Time',
			'shipment_id' => 'Shipment Id',
			'consol_id' => 'Consol Id',
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
		$criteria->compare('org_id',$this->org_id);
		$criteria->compare('shipment_id',$this->shipment_id);
		$criteria->compare('consol_id',$this->consol_id);
		$criteria->compare('original_word',$this->original_word,true);
		$criteria->compare('replace_word',$this->replace_word,true);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('process_time',$this->process_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WordReplaceUsageLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getRef()
	{
		$imParcel = ImParcel::model()->findByPk($this->shipment_id);
		return !empty($imParcel) ? $imParcel->ref : '';
	}

	public function getConsolNo()
	{
		$consol = Consol::model()->findByPk($this->consol_id);
		return !empty($consol) ? $consol->no : '';
	}

	public function getOrgName()
	{
		$org = Org::model()->findByPk($this->org_id);
		return !empty($org) ? $org->name : '';
	}

	public function getGoods()
	{
		$imParcel = ImParcel::model()->findByPk($this->shipment_id);
		return !empty($imParcel) ? $imParcel->getGoods() : '';
	}
}
