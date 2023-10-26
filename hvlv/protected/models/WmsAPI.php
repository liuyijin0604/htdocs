<?php

/**
 * This is the model class for table "wms_api".
 *
 * The followings are the available columns in table 'wms_api':
 * @property string $id
 * @property string $org_id
 * @property string $domain
 * @property string $api_key
 * @property string $api_secret
 * @property string $type
 * @property string $meta
 * @property string $status
 */
class WmsAPI extends CActiveRecord
{
	public static $types = array(
		1 => 'shopify',
		2 => 'cin7',
	);
	const WMS_API_TYPE_SHOPIFY = 1;
	const WMS_API_TYPE_CIN7 = 2;

	public static $states = array(
		1 => 'get order & send tracking',
		2 => 'only send tracking',
		3 => 'send stock',
	);
	const WMS_API_STATUS_ALL = 1;
	const WMS_API_STATUS_TRACKING = 2;
	const WMS_API_STATUS_STOCK = 3;

	const WMS_PLATFORM_SHOPIFY = 'shopify';
	const WMS_PLATFORM_CIN7 = 'cin7';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_api';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('org_id, domain, api_key, api_secret, type, meta', 'safe'),
			array('id, org_id, domain, api_key, api_secret, type, meta', 'safe', 'on' => 'search'),
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
			'cust' => array(self::BELONGS_TO, 'Org', 'org_id'),
		);
	}

	public function getType()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$types[$this->type]) ? '' : self::$types[$this->type]);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'org_id' => 'Cust',
			'type' => 'Platform',
			'meta' => 'Meta',
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
	public function search($pgn = true, $ps = 30, $ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
		$criteria = new CDbCriteria;
		$criteria->compare('t.id', $this->id, true);
		$criteria->compare('t.org_id', $this->org_id, true);
		$criteria->compare('t.type', $this->type, true);

		if ($ec) {
			$criteria->mergeWith($ec);
		}

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 't.id DESC',
			),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WmsAPI the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

}