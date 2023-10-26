<?php

/**
 * This is the model class for table "custom_rate".
 *
 * The followings are the available columns in table 'custom_rate':
 * @property integer $id
 * @property string $hs
 * @property string $from_date
 * @property string $to_date
 * @property integer $charge_type //1=>%, 0=>prohibited currently.
 * @property string $rate
 * @property string $meta
 */
class CustomRate extends CActiveRecord
{
    
    public $mdata;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'custom_rate';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('hs,charge_type', 'required'),
			array('charge_type', 'numerical', 'integerOnly'=>true),
			array('hs', 'length', 'max'=>20),
			array('rate', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, hs, from_date, to_date, charge_type, rate, meta', 'safe', 'on'=>'search'),
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
			'hs' => 'Hs',
			'from_date' => 'From Date',
			'to_date' => 'To Date',
			'charge_type' => 'Charge Type',
			'rate' => 'Rate',
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
        
        public function beforeSave(){
            if(!empty($this->mdata)) $this->meta= json_encode ($this->mdata);
            return true;
        }
        
        public function  afterFind(){
            if(!empty($this->meta)) $this->mdata= json_decode ($this->meta,TRUE);
            return true;
        }
        
        /*get the custom rate based on current date,or specify date;
         * not found return 0
         * found 1.chargetype=0, prohibited
         *  chargetype=1, return the rate;
         */
        public static function getRate($hs,$date=null){
            if(empty($date)) $date=date('Y-m-d');
            $criteria=new CDbCriteria();
            $criteria->addCondition('hs=:hs AND (from_date<=:dt OR from_date="0000-00-00") AND (to_date>:dt OR to_date="0000-00-00")');
            $criteria->params=array(':hs'=>$hs,':dt'=>$date);
            $r= self::model()->find($criteria);
            if(!empty($r)){
                if($r->charge_type==0){   //0 charge_type=0 1 charge_type=1   2 not found, which means charge rate free
                    return [0,$r->rate/100];
                }else if($r->charge_type==1){
                     return [1,$r->rate/100];
                }
            }else{
                return [2,0];
            }
       }
       
       /*
        * suggest the hscode
        */
       public static function suggestHs($term){
           $rs = self::model()->findAll(array(
			'condition' => "`hs` LIKE :t", 
                        'group'=>'hs',
			'params' => array(':t' => '%'.$term.'%'), 
			'limit'=>10,
		));
		$a = array();
		foreach($rs as $r){
                $a[] =$r->hs;
		}
		echo json_encode($a);
         }
  public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('hs',$this->hs,true);
		$criteria->compare('from_date',$this->from_date,true);
		$criteria->compare('to_date',$this->to_date,true);
		$criteria->compare('charge_type',$this->charge_type);
		$criteria->compare('rate',$this->rate,true);
		$criteria->compare('meta',$this->meta,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CustomRate the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
