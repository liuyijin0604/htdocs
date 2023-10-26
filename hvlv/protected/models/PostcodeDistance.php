<?php

/**
 * This is the model class for table "postcode_distance".
 *
 * The followings are the available columns in table 'postcode_distance':
 * @property integer $id
 * @property integer $origin
 * @property string $destination
 * @property integer $distance
 * @property string $suburb
 * @property string $state
 * @property string $country
 */
class PostcodeDistance extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'postcode_distance';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('origin, destination, distance, suburb, state, country', 'required'),
			array('origin, distance', 'numerical', 'integerOnly'=>true),
			array('destination, suburb, state, country', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, origin, destination, distance, suburb, state, country', 'safe', 'on'=>'search'),
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
			'origin' => 'Origin',
			'destination' => 'Destination',
			'distance' => 'Distance',
			'suburb' => 'Suburb',
			'state' => 'State',
			'country' => 'Country',
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
		$criteria->compare('origin',$this->origin);
		$criteria->compare('destination',$this->destination,true);
		$criteria->compare('distance',$this->distance);
		$criteria->compare('suburb',$this->suburb,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('country',$this->country,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function savePostcodeDistance($p,$distance,$ddpt_id=false)
	{
		if($ddpt_id==false)
		{
			$ddpt_id = $p->ddpt_id;
		}
		$this->suburb = $p->cnee->suburb;
		$this->state = $p->cnee->state;
		$this->country = $p->cnee->country;
		$this->destination = $p->cnee->postcode;
		$this->origin = $ddpt_id;
		$this->distance = $distance;
		$this->save();
	}

	public static function getDistance($ddpt_id,$imparcel)
	{
		$distanceObj = PostcodeDistance::model()->find("origin = :origin and destination=:destination and state = :state",[":origin"=>$ddpt_id,":destination"=>$imparcel->cnee->postcode,":state"=>$imparcel->cnee->state]);
		$distance =0;
		if($distanceObj==null)
		{
			$destination = $imparcel->cnee->suburb." ".$imparcel->cnee->state." ".$imparcel->cnee->country." ".$imparcel->cnee->postcode;
			$distance = GoogleMapAPI::getDistanceBetweenPostcode($imparcel->ddpt_id,$destination);
			$postcodeDistance = new PostcodeDistance();
			$postcodeDistance->savePostcodeDistance($imparcel,$distance);
		}else
		{
			$distance =$distanceObj['distance'];
		}
		return $distance;
	}
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PostcodeDistance the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
