<?php

/**
 * This is the model class for table "crm_map".
 *
 * The followings are the available columns in table 'crm_map':
 * @property integer $id
 * @property integer $crm_id
 * @property string $model
 * @property integer $fid
 * @property integer $bwf
 * @property integer $status
 * @property string $meta
 */
class CrmMap extends CActiveRecord
{
         public $mdata;
	/**
	 * @return string the associated database table name
	 */
         
        public static  $states=array(
            1=>'active',
            2=>'delete',
        );
        public function tableName()
	{
		return 'crm_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('crm_id, model, fid, status', 'required'),
			array('crm_id, fid, bwf, status', 'numerical', 'integerOnly'=>true),
			array('model', 'length', 'max'=>40),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, crm_id, model, fid, bwf, status, meta', 'safe', 'on'=>'search'),
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
			'crm_id' => 'Crm',
			'model' => 'Model',
			'fid' => 'Fid',
			'bwf' => 'Bwf',
			'status' => 'Status',
			'meta' => 'Meta',
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
		$criteria->compare('crm_id',$this->crm_id);
		$criteria->compare('model',$this->model,true);
		$criteria->compare('fid',$this->fid);
		$criteria->compare('bwf',$this->bwf);
		$criteria->compare('status',$this->status);
		$criteria->compare('meta',$this->meta,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
        public function beforeSave(){
            if(!empty($this->mdata)) $this->meta= json_encode ($this->mdata);
            return true;
        }
        
        public function afterFind(){
            if(!empty($this->meta))  $this->mdata= json_decode ($this->mdata,true);
            return true;
        }
        
        public static function  createCrmMaps($crm_id,$m,$pids=[]){ //m the class
            foreach ($pids as $fid){
                $model=self::model()->find('crm_id=:cid AND fid=:fid AND model=:model',array(':cid'=>$crm_id,':fid'=>$fid,':model'=> get_class($m)));
                if(empty($model)){
                    $model=new CrmMap();
                    $model->crm_id=$crm_id;
                    $model->fid=$fid;
                    $model->status=1;
                    $model->model= get_class($m);
                    $model->save();
                }
           }
        }
        /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CrmMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
