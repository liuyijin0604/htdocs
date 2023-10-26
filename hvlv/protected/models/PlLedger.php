<?php

/**
 * This is the model class for table "pl_ledger".
 *
 * The followings are the available columns in table 'pl_ledger':
 * @property string $id
 * @property string $fid
 * @property string $model
 * @property string $org_id
 * @property integer $dpt_id
 * @property integer $dpmt
 * @property integer $lid
 * @property string $grp1
 * @property string $grp2
 * @property string $gl
 * @property string $date
 * @property string $amt
 * @property string $actual_amt
 * @property string $gst
 * @property integer $acc
 * @property integer $weight
 * @property integer $zone
 * @property string $meta
 */
class PlLedger extends CActiveRecord
{
	public $mdata = [];

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'pl_ledger_management';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('gl, date, amt', 'required'),
			array('grp1, grp2,grp3, lid, gst, weight, zone, meta', 'safe'),
			array('dpmt, acc', 'numerical', 'integerOnly'=>true),
			array('fid, org_id, gl', 'length', 'max'=>11),
			array('model, grp1', 'length', 'max'=>50),
			array('amt, gst,actual_amt', 'length', 'max'=>12),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, fid, model, org_id, dpt_id, dpmt, lid, grp1, grp2,grp3, gl, date, amt, actual_amt,gst, acc,weight,zone,meta', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
			'consol' => [self::BELONGS_TO, 'Consol', 'grp1'],
		];
	}

	public static function add($d, $update = false, $ignore = []){
		if($update){
			$comp = ['fid', 'model', 'org_id', 'dpmt', 'gl', 'lid', 'date', 'grp1', 'grp2', 'grp3','weight','zone'];
			$cond = [];
			$para = [];
			foreach($comp as $f){
				if(in_array($f, $ignore) || !isset($d[$f])) continue;
				$cond[] = $f.' = :'.$f;
				$para[':'.$f] = $d[$f];
			}
			$r = self::model()->find(implode(' AND ', $cond), $para);
		}

		if(empty($r)) $r = new self;

		if(!empty($d['mdata'])){
			$r->mdata = $d->mdata;
			unset($d['mdata']);
		}
		$r->setAttributes($d);
		if(!empty($d['dpt_id']))
		{
			$r->dpt_id = $d['dpt_id'];
		}
        $r->save();
		return $r;
	}

	public static function addWithSum($d, $update = false, $ignore = []){
		if($update){
			$comp = ['fid', 'model', 'org_id', 'dpmt', 'gl', 'lid', 'date', 'grp1', 'grp2', 'grp3','weight','zone'];
			$cond = [];
			$para = [];
			foreach($comp as $f){
				if(in_array($f, $ignore) || !isset($d[$f])) continue;
				$cond[] = $f.' = :'.$f;
				$para[':'.$f] = $d[$f];
			}
			$r = self::model()->find(implode(' AND ', $cond), $para);
		}

		if(empty($r))
		{
			$r = new self;
			if(!empty($d['mdata'])){
				$r->mdata = $d->mdata;
				unset($d['mdata']);
			}
			$r->setAttributes($d);
			if(!empty($d['dpt_id']))
			{
				$r->dpt_id = $d['dpt_id'];
			}

	        $r->save();
    	}else
    	{
			$r->actual_amt +=$d['actual_amt'];
			$r->acc = $d['acc'];
			if(!empty($d['dpt_id']))
			{
				$r->dpt_id = $d['dpt_id'];
			}

			$r->save();
		}
        
		return $r;
	}

	public static function remove($d){
		$cond = [];
		$para = [];
		foreach($d as $f => $v){
			if(preg_match('/^!/', $v)){
				$cond[] = $f.' != :'.$f;
				$v = substr($v, 1);
			}else{
				$cond[] = $f.' = :'.$f;
			}
			$para[':'.$f] = $v;
		}
		return self::model()->deleteAll(implode(' AND ', $cond), $para);
	}


	public function beforeSave(){
		$this->meta = empty($this->mdata)? '' : json_encode($this->mdata);
		return parent::beforeSave();
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return parent::afterFind();
	}

	public static function getTotal($model, $g1, $g2='', $gl=0){
		$sql = "SELECT SUM(amt) as tot FROM pl_ledger p where";
		$sql .= " p.model = '" . $model ."'";
		$sql .= " AND p.grp1 = '" . $g1."'";
		if(!empty($g2)) $sql .= " AND p.grp2 = '" . $g2."'";
		if(!empty($gl)) $sql .= " AND p.gl = " . $gl;
		return Yii::app()->db->createCommand($sql)->queryScalar();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'fid' => 'FID',
			'model' => 'Model',
			'org_id' => 'Org',
			'dpt_id' => 'Branch',
			'dpmt' => 'Department',
			'lid' => 'Link ID',
			'grp1' => 'Grp1',
			'grp2' => 'Grp2',
			'grp3' => 'Grp3',
			'gl' => 'GL',
			'date' => 'Date',
			'amt' => 'Amount',
            'actual_amt' => 'Actual Amount',
			'gst' => 'GST',
			'acc' => 'Accrual',
			'weight' => 'Weight',
			'zone' => 'Zone',
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
	public function search($pgn=true, $ps = 30, $ec = false){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('fid',$this->fid);
		$criteria->compare('model',$this->model);
		$criteria->compare('org_id',$this->org_id);
		$criteria->compare('dpt_id',$this->dpt_id);
		$criteria->compare('dpmt',$this->dpmt);
		$criteria->compare('lid',$this->lid);
		$criteria->compare('grp1',$this->grp1);
		$criteria->compare('grp2',$this->grp2);
		$criteria->compare('grp3',$this->grp3);
		$criteria->compare('gl',$this->gl);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('amt',$this->amt,true);
        $criteria->compare('actual_amt',$this->actual_amt,true);
		$criteria->compare('gst',$this->gst,true);
		$criteria->compare('acc',$this->acc);
		$criteria->compare('weight',$this->weight,true);
		$criteria->compare('zone',$this->zone);
		$criteria->compare('meta',$this->meta,true);
		$with = [];

		if(!empty($with)){
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if($ec) $criteria->mergeWith($ec);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'t.id DESC',
 			),
			'pagination'=> $pgn? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PlLedger the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
