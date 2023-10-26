<?php

/**
 * This is the model class for table "ex_prodb_map".
 *
 * The followings are the available columns in table 'ex_prodb_map':
 * @property string $id
 * @property string $pid
 * @property string $agt_id
 * @property string $name
 * @property string $meta
 */
class ExProdbMap extends CActiveRecord
{

	public $mdata = array();

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ex_prodb_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pid, name', 'required'),
			array('agt_id, meta', 'safe'),
			array('pid, agt_id', 'length', 'max'=>11),
			array('name', 'length', 'max'=>200),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, pid, agt_id, name, meta', 'safe', 'on'=>'search'),
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
			'prod' => array(self::BELONGS_TO, 'ExProdb', 'pid'),
			'agent' => array(self::BELONGS_TO, 'Org', 'agt_id'),
		);
	}
	
	protected function beforeSave(){
		if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		
		return true;
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'pid' => 'PID',
			'agt_id' => 'Agt',
			'name' => 'Name',
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
	public function search($pgn=true, $ps = 30, $ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('agt_id',$this->agt_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('meta',$this->meta,true);

		if($ec){
			$criteria->mergeWith($ec);
		}

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=> $pgn? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ExProdbPrice the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
