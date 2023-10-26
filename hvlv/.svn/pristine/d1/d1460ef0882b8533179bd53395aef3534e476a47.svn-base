<?php

/**
 * This is the model class for table "wms_prod_hscode".
 *
 * The followings are the available columns in table 'wms_prod_hscode':
 * @property int $id
 * @property string $prod_id
 * @property string $port
 * @property string $hscode
 */
class WmsProdHscode extends CActiveRecord
{

	public $mdata;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_prod_hscode';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('prod_id, port, hscode', 'required'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, prod_id, port, hscode', 'safe', 'on' => 'search'),
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
			'prod' => array(self::BELONGS_TO, 'WmsProd', 'prod_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'prod_id' => 'Prod',
			'port' => 'Port',
			'hscode' => 'HS Code',
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
	public function search($pgn = true, $ps = 30, $ec = false, $order = 't.port DESC')
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('prod_id', $this->prod_id);
		$criteria->compare('port', $this->port);
		$criteria->compare('hscode', $this->hscode, true);

		if ($ec) {
			$criteria->mergeWith($ec);
		}

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => $order,
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
	 * @return WmsProdOrg the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
