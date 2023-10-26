<?php

/**
 * This is the model class for table "process_log".
 *
 * The followings are the available columns in table 'process_log':
 * @property integer $id
 * @property integer $fid
 * @property integer $status
 * @property string $time
 * @property integer $type
 * @property integer $user_id
 */
class ProcessLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
       public static $states=array(
           1=>'active',
           2=>'delete',
       );
       public function tableName()
	{
		return 'process_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fid, type,status', 'required'),
			array('fid, type, user_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, fid, time, type, status, user_id', 'safe', 'on'=>'search'),
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
			'time' => 'Time',
			'type' => 'Type',
			'user_id' => 'User',
                        'status'=> 'Status'
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
		$criteria->compare('time',$this->time,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('user_id',$this->user_id);
                $criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
       public function beforeSave(){
            if(empty($this->time))    $this->time= date('Y-m-d H:i:s');
            return true;
        }
        
    public static function addLog($fid, $type) {
        $log = ProcessLog::model()->find('fid=:fid AND type=:type', array(':fid' => $fid, ':type' => $type));
        $mapUsers=TypeMapUser::getUserFromType(TypeMapUser::TYPE_CUSTOM_PROCESS,$type); //an array
        if (empty($log)) {
            $log = new ProcessLog;
            $log->fid = $fid;
            $log->type = $type;
            $log->status = 1;
            $log->user_id=isset($mapUsers[0])?$mapUsers[0]:0;
            $log->save();
        } else if ($log->status != 1) {
            $log->status = 1;
            $log->user_id=isset($mapUsers[0])?$mapUsers[0]:0;
            $log->update(['status','user_id']);
        }else{
            $log->user_id=isset($mapUsers[0])?$mapUsers[0]:0;
            $log->update(['user_id']);
        }
    }

    public static function  deleteLog($fid,$type){
             $log= ProcessLog::model()->find('fid=:fid AND type=:type',array(':fid'=>$fid,':type'=>$type));
             if(!empty($log)){
                 $log->status=2;
                 $log->update('status');
             }
            
        }

        /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ProcessLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
