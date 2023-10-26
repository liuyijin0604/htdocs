<?php

/**
 * This is the model class for table "shipment_question_log".
 *
 * The followings are the available columns in table 'shipment_question_log':
 * @property integer $id
 * @property integer $fid
 * @property integer $status
 * @property string $time
 * @property integer $type
 * @property integer $user_id
 */
class ShipmentQuestionLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'shipment_question_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fid, status, time, type, user_id', 'required'),
			array('fid, status, user_id', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, fid, status, time, type, user_id', 'safe', 'on'=>'search'),
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
			'fid' => 'Fid',
			'status' => 'Status',
			'time' => 'Time',
			'type' => 'Type',
			'user_id' => 'User',
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
		$criteria->compare('fid',$this->fid);
		$criteria->compare('status',$this->status);
		$criteria->compare('time',$this->time,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('user_id',$this->user_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


	public static function addLog($fid, $type) {
        $mapUsersArr=[];
        $rs =TypeMapUser::model()->findAll('type=:type and status=1',array(':type'=> TypeMapUser::TYPE_CUSTOMER_SERVICE));
        $faqList = CsFaq::getFullFaqList();
       foreach($rs as $oneMap){
       		if($faqList[$oneMap['map_type']][0]==$type||$faqList[$oneMap['map_type']][1]==$type)
       		{
       			$mapUsersArr[$oneMap['map_type']][]=$oneMap['user_id'];
       		}
       }
        foreach ($mapUsersArr as $key => $mapUsers) {
        	foreach ($mapUsers as $key => $user) {
        		        $log = ShipmentQuestionLog::model()->find('fid=:fid AND type=:type AND user_id = :userId', array(':fid' => $fid, ':type' => $type,':userId'=>$user));
        		    if (empty($log)) {
			            $log = new ShipmentQuestionLog;
			            $log->fid = $fid;
			            $log->type = $type;
			            $log->status = 1;
			            $log->user_id=isset($user)?$user:0;
			            $log->time = date("Y-m-d H:i:s");
			            $log->save();
			        } else if ($log->status != 1) {
			            $log->status = 1;
			            $log->user_id=isset($user)?$user:0;
			            $log->update(['status','user_id']);
			        }else{
			            $log->user_id=isset($user)?$user:0;
			            $log->update(['user_id']);
			        }
        	}
        }
    }
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ShipmentQuestionLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
