<?php

/**
 * This is the model class for table "ex_prodb".
 *
 * The followings are the available columns in table 'ex_prodb':
 * @property string $id
 * @property string $name
 * @property string $name_zh
 * @property string $sku
 * @property string $brand
 * @property string $model
 * @property string $hs
 * @property string $hs2
 * @property string $code
 * @property string $unit
 * @property string $price
 * @property string $tax
 * @property string $weight
 * @property string $tag
 * @property string $note
 * @property string $meta
 */
class ExProdb extends CActiveRecord
{
	
	public static $types = array(
		10 => 'Baby Formula',
		20 => 'Milk Powder',
		90 => 'Other',
	);
	
	public static $rtypes = array(
		'B' => 10,
		'M' => 20,
		'O' => 90,
	);

	public $mdata = array();

	public $noaup = false;

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'ex_prodb';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, type, name_zh, price', 'required'),
			array('sku, status, brand, model, hs, hs2, code, unit, weight, tag, tax, note, meta', 'safe'),
			array('name, brand, model', 'length', 'max'=>200),
			array('hs, code', 'length', 'max'=>30),
			array('code', 'unique'),
			array('name', 'validName'),
			array('unit', 'length', 'max'=>20),
			array('price, weight', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, status, name, type, sku, brand, name_zh, model, hs, hs2, code, unit, price, weight, tag, tax, note', 'safe', 'on'=>'search'),
		);
	}

	public function validName(){
		if(preg_match('/[\x{4e00}-\x{9fa5}]+/u', $this->name)){
			$this->addError('name', Yii::t(strtolower(__CLASS__), 'Product name should only contain english words'));
			return false;
		}
		
		return true;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'prices' => array(self::HAS_MANY, 'ExProdbPrice', 'pid'),
		);
	}

	public function brandEn($b = null){
		if(empty($b)) $b = $this->brand;
		$tm = [
		'贝拉米' => 'Bellamy',
	'拜耳' => 'Byer',
	'爱乐维' => 'Elevit',
	'爱他美' => 'Aptamil',
	'德运' => 'Davondale',
	'亨氏' => 'Heins',
	'吉百利' => 'Cadbury',
	'康维他' => 'Comvita',
	'可瑞康' => 'Caricare',
	'百丽康美' => 'Black Mores',
	'卡夫' => 'Karf',
	'雀巢' => 'Nestle',
	'佳思敏' => 'Jasmin',
	'惠氏' => 'Wyeth',
	'汤普森' => 'Tompson',
	'塔斯马尼亚' => 'Tasmania',
	'雅培' => 'Abbott',
	'康伟嘉' => 'Comvita',
	'绿芙' => 'Spring Leaf',
	'凡士林' => 'Vaseline',
	'SW' => 'SWISSE',
	'钙尔奇' => 'Caltrade',
	'星期四农庄' => 'Thursday Plantation',
	'富康' => 'Wealthy Health',
	];
		if(preg_match('/[\x{4e00}-\x{9fa5}]+/u', $b)){
			if(isset($tm[$b])){
				return $tm[$b];
			}else{
				//file_put_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'brand.log', $b."\n", FILE_APPEND);
			}
		}
		return $b;
	}

	public static function getIDByName($g){
		$p = ExProdb::model()->find([
				'condition' => 'name_zh = :n',
				'params' => [':n' => $g],
				'order' => 'price, weight',
			]);
		if(!empty($p)) return $p->id;
		$p = ExProdbPrice::model()->find([
				'condition' => 'type = 10 AND name = :n',
				'params' => [':n' => $g],
				'order' => 'price',
			]);
		if(!empty($p)) return $p->pid;
		return 0;
	}

	public function getPocPrice($poc){
		$pp = ExProdbPrice::model()->find('pid = :id AND poc = :poc', [':id' => $this->id, ':poc' => $poc]);
		if(empty($pp)) return false;
		return $pp;
	}
	
	public function getType(){
		return Yii::t(strtolower(__CLASS__), self::$types[$this->type]);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'status' => 'Status',
			'type' => 'Type',
			'name_zh' => '品名',
			'sku' => 'SKU',
 			'brand' => 'Brand',
			'model' => 'Model',
			'hs' => 'HS Code',
			'hs2' => 'BC HS',
			'code' => 'Quick Code',
			'unit' => 'Unit',
			'price' => 'Price',
			'weight' => 'Weight',
			'tag' => 'Tags',
			'tax' => 'Tariff',
			'note' => 'Note',
		);
	}
	
	public function beforeSave(){
		if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		return true;
	}

	public function afterSave(){
		if($this->noaup) return true;
		ExProdbPrice::PrepProduct($this->id);
		$rs = ExParcel::model()->findAll('status < 100 AND bwf & 8 > 0');
		foreach($rs as $r){
			if(empty($r->eitems['g'])) break;
			foreach($r->eitems['g'] as $i => $g){
				if(empty($g)) continue;
				if($this->code == $g || strpos($this->name_zh, $g) !== false || strpos($this->tag, $g) !== false){
					$r->save();
				}
			}
		}
	}

	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		$pps = ExProdbPrice::model()->findAll('pid = :id AND type = 10 AND status = 1', [':id' => $this->id]);
		foreach($pps as $p){
			if(!empty($p->price) && $p->price > 0) $this->mdata['price_'.$p->poc] = $p->price;
			if(!empty($p->name)) $this->mdata['name_'.$p->poc] = $p->name;
			if(!empty($p->hs)) $this->mdata['hs_'.$p->poc] = $p->hs;
			if(!empty($p->sn)) $this->mdata['id_'.$p->poc] = $p->sn;
		}
		return true;
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
	public function search(){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.type',$this->type);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('name_zh',$this->name_zh,true);
		$criteria->compare('sku',$this->sku,true);
		$criteria->compare('brand',$this->brand,true);
		$criteria->compare('model',$this->model,true);
		$criteria->compare('hs',$this->hs,true);
		$criteria->compare('hs2',$this->hs2,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('weight',$this->weight,true);
		$criteria->compare('tag',$this->tag,true);
		$criteria->compare('tax',$this->tax,true);
		$criteria->compare('note',$this->note,true);

		if($this->status !== 0) $this->status = 1;
		$criteria->compare('t.status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
    			'defaultOrder'=>'t.name ASC',
  			),
			'pagination'=>array(
				'pageSize'=>'30',
            ),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ExProdb the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
