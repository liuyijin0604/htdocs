<?php

/**
 * This is the model class for table "ex_prodb_price".
 *
 * The followings are the available columns in table 'ex_prodb_price':
 * @property string $id
 * @property string $pid
 * @property integer $type
 * @property string $agt_id
 * @property integer $status
 * @property string $poc
 * @property string $sn
 * @property string $name
 * @property string $price
 * @property string $hs
 * @property string $hs2
 * @property string $meta
 */
class ExProdbPrice extends CActiveRecord
{
	
	public static $types = array(
		10 => 'POC Rate',
		20 => 'Agent Rate',
	);
	
	public static $states = array(
		'0' => 'Inactive',
		'1' => 'Active',
	);

	public $mdata = array();

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ex_prodb_price';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pid, type, status', 'required'),
			array('agt_id, poc, sn, name, price, hs, hs2, meta', 'safe'),
			array('type, status', 'numerical', 'integerOnly'=>true),
			array('pid, agt_id', 'length', 'max'=>11),
			array('poc', 'length', 'max'=>5),
			array('sn', 'length', 'max'=>30),
			array('name', 'length', 'max'=>50),
			array('price, hs, hs2', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, pid, type, agt_id, status, poc, sn, name, price, hs, hs2, meta', 'safe', 'on'=>'search'),
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
		);
	}
	
	protected function beforeSave(){
		if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		if(empty($this->status)) $this->status = 1;
		
		return true;
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
	}

	public function getName(){
		return empty($this->name)? $this->prod->name_zh : $this->name;
	}

	public function getPrice(){
		return (empty($this->price) || $this->price == 0)? ($this->prod->price * 1.1) : $this->price;
	}

	public function getSku(){
		return empty($this->prod)? '' : $this->prod->sku;
	}

	public static function PrepProduct($pid){
		$ps = self::model()->findAll('type = 10 AND pid = :pid', [':pid' => $pid]);
		$pocs = [];
		foreach($ps as $p) $pocs[$p->poc] = $p;

		$cs = ExChannel::model()->findAll('status = 50');
		foreach($cs as $c){
			if(isset($pocs[$c->code])){
				if($pocs[$c->code]->status == 0){
					$pocs[$c->code]->status = 1;
					$pocs[$c->code]->save();
				}
			}else{ //new
				$pp = new ExProdbPrice('create');
				$pp->pid = $pid;
				$pp->type = 10;
				$pp->status = 1;
				$pp->poc = $c->code;
				$pp->save();
			}
		}
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'pid' => 'PID',
			'type' => 'Type',
			'agt_id' => 'Agt',
			'status' => 'Status',
			'poc' => 'Poc',
			'sn' => 'SKU',
			'name' => 'Name',
			'price' => 'Price',
			'hs' => 'HS',
			'hs2' => 'EC HS',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('pid',$this->pid);
		$criteria->compare('type',$this->type);
		$criteria->compare('agt_id',$this->agt_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('poc',$this->poc);
		$criteria->compare('sn',$this->sn);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('hs',$this->hs,true);
		$criteria->compare('hs2',$this->hs2,true);
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
