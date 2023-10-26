<?php

/**
 * This is the model class for table "edi_job_airline".
 *
 * The followings are the available columns in table 'edi_job_airline':
 * @property string $id
 * @property string $job_id
 * @property string $flight_no
 * @property int $transit
 * @property integer $atd
 * @property ingeger $ata
 * @property integer $plt
 */
class EdiJobAirline extends CActiveRecord
{

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'edi_job_airline';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('job_id, flight_no, transit, atd, ata, plt', 'required'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, job_id, flight_no, transit, atd, ata, plt', 'safe', 'on' => 'search'),
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
			'job' => array(self::BELONGS_TO, 'EdiJob', 'job_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'job_id' => 'Job ID',
			'flight_no' => 'Flight NO.',
			'transit' => 'Transit',
			'atd' => 'ATD',
			'ata' => 'ATA',
			'plt' => 'Pallet',
		);
	}

	public function search($page = true)
	{
		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.job_id', $this->job_id);
		$criteria->compare('t.flight_no', $this->flight_no);
		$criteria->compare('t.transit', $this->transit);
		$criteria->compare('t.atd', $this->atd, true);
		$criteria->compare('t.ata', $this->ata, true);
		$criteria->compare('t.plt', $this->plt);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 't.id DESC',
			),
			'pagination' => $page ? array(
				'pageSize' => 30,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Job the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

}
