<?php

/**
 * This is the model class for table "imports_mail_signature".
 *
 * The followings are the available columns in table 'imports_mail_signature':
 * @property integer $id
 * @property integer $user_id
 * @property string $sig_name
 * @property integer $status
 * @property string $sig_body
 */
class ImportsMailSignature extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $new_sig_body;
	public function tableName()
	{
		return 'imports_mail_signature';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
        
        public static $states=array(
            0=>'active',
            1=>'default',
            10=>'delete',
            11=>'fastway mail'
        );
        public function getStatus(){
            return isset(self::$states[$this->status])?self::$states[$this->status]:'';
        }
        public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, sig_name', 'required'),
			array('user_id, status', 'numerical', 'integerOnly'=>true),
			array('sig_name', 'length', 'max'=>40),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, sig_name, status, sig_body,new_sig_body', 'safe', 'on'=>'search'),
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
			'user_id' => 'User',
			'sig_name' => 'Sig Name',
			'status' => 'Status',
			'sig_body' => 'Sig Body',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('sig_name',$this->sig_name,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('sig_body',$this->sig_body,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        public static function removeDefault(){
            $rs=ImportsMailSignature::model()->findAll('user_id=:user_id and status=1',array(':user_id'=>Yii::app()->user->id));
            foreach ($rs as $r){
                $r->status=0;
                $r->update('status');
            }
        }
        public static function haveDefaultSignature(){
             $rs=ImportsMailSignature::model()->findAll('user_id=:user_id and status=1',array(':user_id'=>Yii::app()->user->id));
            if(!empty($rs)){
                return true;
            }
            return false;
        }

        public static function getDefaultSignature()
        {
        	$rs=ImportsMailSignature::model()->find('user_id=:user_id and status=1',array(':user_id'=>Yii::app()->user->id));
            return $rs;
        }



        /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ImportsMailSignature the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
