<?php

/**
 * This is the model class for table "live_chat_record".
 *
 * The followings are the available columns in table 'live_chat_record':
 * @property integer $id
 * @property integer $detail_id
 * @property integer $user_type
 * @property string $text
 * @property string $time
 */
class LiveChatRecord extends CActiveRecord
{

	const USERTYPEAGENT = 1;
	const USERTYPEVISITOR = 2; 
	/**
	 * @return string the associated database table name
	 */

	public static $userType = [
		1  => 'Agent',
		2  => 'Visitor'
	];

	public function tableName()
	{
		return 'live_chat_record';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('detail_id, user_type, text, time', 'required'),
			array('detail_id, user_type', 'numerical', 'integerOnly'=>true),
			array('text', 'length', 'max'=>200),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, detail_id, user_type, text, time', 'safe', 'on'=>'search'),
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
			'detail'=>[self::BELONGS_TO,"LiveChatDetail","detail_id"]
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'detail_id' => 'Detail',
			'user_type' => 'User Type',
			'text' => 'Text',
			'time' => 'Time',
		);
	}

	public function showUserType()
	{
		$userTypeStr = '';
		switch ($this->user_type) {
			case self::USERTYPEAGENT:
				$userTypeStr.="Agent";
				break;
			case self::USERTYPEVISITOR:
				$userTypeStr.="Visitor";
				break;
			
			default:
				// code...
				break;
		}
		return $userTypeStr;
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('detail_id',$this->detail_id);
		$criteria->compare('user_type',$this->user_type);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('time',$this->time,true);
		$with = [];

		if($this->status==null)
		{
			$criteria->compare('status',self::STATUSSUBMITTED);
		}else
		{
			$criteria->compare('status',$this->status);
		}

		$criteria->with = $with;
		$criteria->together = true;
		$sort = new CSort(get_called_class());
		$sort->defaultOrder = 't.id DESC';
		return new CActiveDataProvider($this, [
			'criteria' => $criteria,
			'sort' => $sort,
			'pagination' => [
			'pageSize' => 30,
			],
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return LiveChatRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
