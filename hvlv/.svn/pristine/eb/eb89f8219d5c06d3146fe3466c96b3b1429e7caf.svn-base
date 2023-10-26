<?php

/**
 * This is the model class for table "cargo_process_log".
 *
 * The followings are the available columns in table 'cargo_process_log':
 * @property integer $id
 * @property integer $cpid
 * @property integer $status
 * @property string $time
 * @property integer $type
 * @property integer $user_id
 */
class CargoProcessLog extends CActiveRecord
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
		return 'cargo_process_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cpid, type,status', 'required'),
			array('cpid, type, user_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, cpid, time, type, status, user_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
			'cargoProcces' => [self::BELONGS_TO, 'cargoProcces', 'cpid'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'cpid' => 'CPid',
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
		$criteria->compare('cpid',$this->cpid);
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
        
    public static function addLog($cpid, $type) {
        $log = CargoProcessLog::model()->find('cpid=:cpid AND type=:type', array(':cpid' => $cpid, ':type' => $type));
        $mapUsers=TypeMapUser::getUserFromType(TypeMapUser::TYPE_CUSTOM_PROCESS,$type); //an array
        if (empty($log)) {
            $log = new CargoProcessLog;
            $log->cpid = $cpid;
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

    public static function  deleteLog($cpid,$type){
             $log= CargoProcessLog::model()->find('cpid=:cpid AND type=:type',array(':cpid'=>$cpid,':type'=>$type));
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
