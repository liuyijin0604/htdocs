<?php

/**
 * This is the model class for table "mail_user".
 *
 * The followings are the available columns in table 'mail_user':
 * @property integer $id
 * @property integer $user_id
 * @property integer $mail_id
 * @property string $create_time
 * @property string $close_time
 * @property integer $status
 * @property integer $mail_type
 * @property string $meta
 */
class MailUser extends MetaModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'mail_user';
	}

	public static $states = [
		10 => 'New',
		50 => 'Replied',
		60 => 'Read',
	];

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['user_id, mail_id, create_time,status', 'required'],
			['user_id, mail_id, status, mail_type', 'numerical', 'integerOnly' => true],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, user_id, mail_id, create_time, close_time, status, mail_type, meta', 'safe', 'on' => 'search'],
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
			'mail' => [self::BELONGS_TO, 'ImportsMail', 'mail_id'],
			'user' => [self::BELONGS_TO, 'User', 'user_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'user_id' => 'User',
			'mail_id' => 'Mail',
			'create_time' => 'Create Time',
			'close_time' => 'Close Time',
			'status' => 'Status',
			'mail_type' => 'Mail Type',
			'meta' => 'Meta',
		];
	}

	public function beforeSave()
	{
		if ($this->isNewRecord && self::model()->count('mail_id = :mid AND user_id = :uid', [':mid' => $this->mail_id, ':uid' => $this->user_id]) > 0) {
			return false;
		}
		return parent::beforeSave();
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

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('user_id', $this->user_id);
		$criteria->compare('mail_id', $this->mail_id);
		$criteria->compare('create_time', $this->create_time, true);
		$criteria->compare('close_time', $this->close_time, true);
		$criteria->compare('status', $this->status);
		$criteria->compare('mail_type', $this->mail_type);
		$criteria->compare('meta', $this->meta, true);

		return new CActiveDataProvider($this, [
			'criteria' => $criteria,
		]);
	}

	public static function getUserNewIds($user_id = '')
	{
		$ids = [0];
		if (empty($user_id)) {
			$user_id = Yii::app()->user->id;
		}
		$sql = "SELECT mail_id from `mail_user` WHERE user_id = :uid AND status = 10";
		$rs = Yii::app()->db->createCommand($sql)->bindValues([':uid' => $user_id])->queryAll();
		foreach ($rs as $r) {
			$ids[] = $r['mail_id'];
		}
		return $ids;
	}

	public static function getUserAllIds($user_id = '')
	{
		$ids = [0];
		if (empty($user_id)) {
			$user_id = Yii::app()->user->id;
		}
		$sql = "SELECT mail_id from `mail_user` WHERE user_id = :uid";
		$rs = Yii::app()->db->createCommand($sql)->bindValues([':uid' => $user_id])->queryAll();
		foreach ($rs as $r) {
			$ids[] = $r['mail_id'];
		}
		return $ids;
	}

	public function getStatus()
	{
		return isset(self::$states[$this->status]) ? self::$states[$this->status] : "";
	}

	public function getDeadline()
	{
		$deadlineDate = explode(" ", $this->create_time)[0];
        $createTime = explode(" ", $this->create_time)[1];

        //park
        $isMailPark = ($this->mail->flag&ImportsMail::FLAG_PARK)>0?1:0;
        if(!empty($isMailPark))
        {
          for($i=0; $i < 2; $i++)
          {
            $deadlineDate = HolidayHelper::getNextWeekday($deadlineDate,null);
          }


        }else
        {
          //before 17:30
          $bonus = 0;
          if(strtotime($this->create_time)<strtotime($deadlineDate." "."17:30:00"))
          {
              $isHoliday = HolidayHelper::checkDateIsHoliday($deadlineDate);
              if($isHoliday)
              {
                 $deadlineDate = HolidayHelper::getNextWeekday($deadlineDate,null);
              }

          }else//after 17:30
          {
              $deadlineDate = HolidayHelper::getNextWeekday($deadlineDate,null);
          }
        }
        return $deadlineDate." "."18:00:00";
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MailUser the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
