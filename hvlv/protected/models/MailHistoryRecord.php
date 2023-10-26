<?php

/**
 * This is the model class for table "mail_history_record".
 *
 * The followings are the available columns in table 'mail_history_record':
 * @property integer $id
 * @property integer $uid
 * @property integer $unfinished
 * @property integer $open
 * @property integer $read
 * @property integer $reply
 * @property integer $close
 * @property double $percent
 * @property string $record_date
 */
class MailHistoryRecord extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'mail_history_record';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('uid', 'required'),
			array('uid, unfinished, open, read, reply, close', 'numerical', 'integerOnly'=>true),
			array('percent', 'numerical'),
			array('record_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, uid, unfinished, open, read, reply, close, percent, record_date', 'safe', 'on'=>'search'),
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
			'uid' => 'Uid',
			'unfinished' => 'Unfinished',
			'open' => 'Open',
			'read' => 'Read',
			'reply' => 'Reply',
			'close' => 'Close',
			'percent' => 'Percent',
			'record_date' => 'Record Date',
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
		$criteria->compare('uid',$this->uid);
		$criteria->compare('unfinished',$this->unfinished);
		$criteria->compare('open',$this->open);
		$criteria->compare('read',$this->read);
		$criteria->compare('reply',$this->reply);
		$criteria->compare('close',$this->close);
		$criteria->compare('percent',$this->percent);
		$criteria->compare('record_date',$this->record_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        public static function genHistoryRecord($date){
             $result=[];
            foreach (ImportsMail::$importscs_list_id as $user_id=>$user_name){
                $result[$user_id]=[]; 
            }
            $sql="SELECT COUNT(*) as number,user_id FROM `mail_user` WHERE SUBDATE('$date',INTERVAL 360 MINUTE)<=`create_time` AND `create_time`<=SUBDATE('$date',INTERVAL -1080 MINUTE) GROUP BY user_id";
            $todyOpen=Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($todyOpen as $r){
                if(isset($result[$r['user_id']])){
                $result[$r['user_id']]['today_open']=$r['number'];
                }
            }
            $sql="SELECT COUNT(*) as number, user_id FROM `mail_user` WHERE SUBDATE('$date',INTERVAL 360 MINUTE)<=`close_time` AND `close_time`<=SUBDATE('$date',INTERVAL -1080 MINUTE) AND status=50 GROUP BY user_id";
            $todayReply=Yii::app()->db->createCommand ($sql)->queryAll();
            foreach ($todayReply as $r){
                if(isset($result[$r['user_id']])){
                $result[$r['user_id']]['today_reply']=$r['number'];
                }
            }
            $sql="SELECT COUNT(*) as number,user_id FROM `mail_user` WHERE SUBDATE('$date',INTERVAL 360 MINUTE)<=`close_time` AND `close_time`<=SUBDATE('$date',INTERVAL -1080 MINUTE) AND status=60  GROUP BY user_id";
            $todayRead=Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($todayRead as $r){
                if(isset($result[$r['user_id']])){
                $result[$r['user_id']]['today_read']=$r['number'];
                }
            }
            $sql="SELECT COUNT(*) as number,user_id FROM `mail_user` WHERE  `create_time`<=SUBDATE('$date',INTERVAL -1080 MINUTE) AND (`close_time`>=SUBDATE('$date',INTERVAL -1080 MINUTE) OR `close_time`<'1972-01-01') GROUP BY user_id";
            $todayLeft=Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($todayLeft as $r){
                if(isset($result[$r['user_id']])){
                $result[$r['user_id']]['today_left']=$r['number'];
                }
            }
            foreach ($result as $key=>$value){
                if(empty($value)) unset($result[$key]);
            }
            unset($result[530]);
            foreach($result as $user=>$values){
                $model= MailHistoryRecord::model()->find('uid=:uid AND record_date=:record_date',array(':uid'=>$user,':record_date'=>$date));
                if(empty($model)){
                   $model=new MailHistoryRecord();
                   $model->uid=$user;
                   $model->unfinished= intval(@$values['today_left']);
                   $model->open=intval(@$values['today_open']);
                   $model->read=intval(@$values['today_read']);
                   $model->reply=intval(@$values['today_reply']);
                   $model->close=$model->read+$model->reply;
                   $model->record_date=$date;
                   $percent= sprintf("%0.5f", ($model->unfinished+$model->close)>0?$model->close/($model->unfinished+$model->close):0 );
                   $model->percent=$percent*100;
                   $model->save();
                }else{
                   $model->unfinished= intval(@$values['today_left']);
                   $model->open=intval(@$values['today_open']);
                   $model->read=intval(@$values['today_read']);
                   $model->reply=intval(@$values['today_reply']);
                   $model->close=$model->read+$model->reply;
                   $percent= sprintf("%0.5f", ($model->unfinished+$model->close)>0?$model->close/($model->unfinished+$model->close):0 );
                   $model->percent=$percent*100;
                   $model->save();
                }
            }
        }
            
            
            
        

        
        /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MailHistoryRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
