<?php

/**
 * This is the model class for table "wms_serial_no".
 *
 * The followings are the available columns in table 'wms_serial_no':
 * @property string $id
 * @property string $task_id
 * @property string $location_id
 * @property string $stock_id
 * @property integer $type
 * @property string $dt
 * @property string $sn
 * @property string $meta
 */
class WmsSerialNo extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_serial_no';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['task_id, stock_id, type, sn', 'required'],
			['location_id, dt, meta', 'safe'],
			['type', 'numerical', 'integerOnly'=>true],
			['task_id, location_id, stock_id', 'length', 'max'=>11],
			['sn', 'validateSerial'],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, task_id, location_id, stock_id, type, dt, sn, meta', 'safe', 'on'=>'search'],
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
			'stock' => [self::BELONGS_TO, 'WmsStock', 'stock_id'],
			'loc' => [self::BELONGS_TO, 'WmsLocation', 'location_id'],
			'task' => [self::BELONGS_TO, 'WmsTask', 'task_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'task_id' => 'Task',
			'location_id' => 'Location',
			'stock_id' => 'Stock',
			'type' => 'Type',
			'dt' => 'Dt',
			'sn' => 'Sn',
			'meta' => 'Meta',
		];
	}

	public function validateSerial(){
		if(self::model()->count('sn = :sn AND stock_id = :sid AND task_id = :tid', [':sid' => $this->stock_id, ':tid' => $this->task_id, ':sn' => $this->sn]) > 0){//check dup
			$this->addError('sn', $this->sn.' already recorded');
		}else{
			$wpo = WmsProdOrg::model()->find('org_id = :oid AND prod_id = :pid', [':oid' => $this->stock->org_id, ':pid' => $this->stock->prod_id]);
			if(!empty($wpo->mdata['sn_regex']) && !preg_match('/^'.trim($wpo->mdata['sn_regex']).'$/', $this->sn)){
				$this->addError('sn', $this->sn.' not correct format');
			}
		}
	}

	public function beforeSave(){
		if(empty($this->dt)){
			$this->dt = date('Y-m-d H:i:s');
		}
		return true;
	}

	public function errMsg(){
		$es = $this->getErrors();
		$msg = '';

		if(empty($es)){
			return '';
		}else{
			foreach($es as $e){
				foreach($e as $el){
					$msg .= $el.'<br />';
				}
			}
			return $msg;
		}
	}

	public static function itemCount($task_id, $stock_id){
		return self::model()->count('task_id = :tid AND stock_id = :sid', [':tid' => $task_id, ':sid' => $stock_id]);
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

		$criteria->compare('id', $this->id, true);
		$criteria->compare('task_id', $this->task_id, true);
		$criteria->compare('location_id', $this->location_id, true);
		$criteria->compare('stock_id', $this->stock_id, true);
		$criteria->compare('type', $this->type);
		$criteria->compare('dt', $this->dt, true);
		$criteria->compare('sn', $this->sn, true);
		$criteria->compare('meta', $this->meta, true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WmsSerialNo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
