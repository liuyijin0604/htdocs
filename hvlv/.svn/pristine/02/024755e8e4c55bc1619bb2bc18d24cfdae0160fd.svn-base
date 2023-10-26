<?php

/**
 * This is the model class for table "storage".
 *
 * The followings are the available columns in table 'storage':
 * @property string $id
 * @property string $wid
 * @property string $pid
 * @property integer $type
 * @property integer $status
 * @property string $name
 * @property string $code
 * @property integer $cx
 * @property integer $cy
 * @property integer $cz
 * @property string $cap
 * @property string $cap_kg
 * @property string $cap_cbm
 * @property string $cap_item
 * @property string $width
 * @property string $depth
 * @property string $height
 * @property string $bwf
 * @property string $notes
 * @property string $meta
 */
class Storage extends CActiveRecord
{
	
	public static $types = array(
		10 => 'Area',
		20 => 'Rack',
		25 => 'Rack Shelf',
		30 => 'Parking Bay',
		40 => 'Customs Held',
		50 => 'Import Sorting',
		60 => 'Export Sorting',
		70 => 'Free Storage',
		80 => 'FAK Storage',
		90 => 'Distribution',
	);
	
	public static $states = array(
		1 => 'Active',
		0 => 'Inactive',
	);

	public static $bwfs = array(
	);
	
	public $extra = array();

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'storage';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('wid, type, name', 'required'),
			array('pid, status, code, cx, cy, cz, cap, cap_kg, cap_cbm, cap_item, width, height, depth, bwf, notes, meta', 'safe'),
			array('type, status, cx, cy, cz', 'numerical', 'integerOnly'=>true),
			array('wid, pid, cap_item, bwf', 'length', 'max'=>11),
			array('name, code', 'length', 'max'=>50),
			array('cap_kg, cap_cbm, height, width', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, wid, pid, type, status, name, code, cx, cy, cz, cap, cap_kg, cap_cbm, cap_item, width, height, depth, bwf, notes, meta', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'warehouse' => array(self::BELONGS_TO, 'Org', 'wid'),
			'parent' => array(self::BELONGS_TO, 'Storage', 'pid'),
			'items' => array(self::HAS_MANY, 'StorageLog', 'sid'),
		);
	}
	
	public function getType(){
		return Yii::t(strtolower(__CLASS__), empty(self::$types[$this->type])? '' : self::$types[$this->type]);
	}
	
	public function getStatus(){
		return Yii::t(strtolower(__CLASS__), empty(self::$states[$this->status])? '' : self::$states[$this->status]);
	}
	
	public function beforeSave(){
		$this->meta = json_encode($this->extra);
		return true;
	}
	
	public function afterFind(){
		$this->extra = json_decode($this->meta, true);
		return true;
	}

    /**
     * store the paracel at the specified area
     * @param $wid
     * @param $type
     * @param $code
     * @param $parcel
     */
    public static function allocateInDestArea($wid,$type,$code,$parcel){
        $rs = self::model()->findAll(array(
            'condition' => 'status = 1 AND cap < 100 AND wid = :wid AND type = :type AND code = :code',
            'params' => array(':wid' => $wid, ':type' => $type, ':code' => $code),
            'order' => 'wt, id',
        ));
        foreach($rs as $r){
            if($r->canFit($parcel)){
                $r->addItem($parcel);
                return $r;
            }
        }
        return false;
    }

	public static function allocate($wid, $type, $p, $n=false){
		if(!$n){
			$rs = self::model()->findAll(array(
				'condition' => 'status = 1 AND cap < 100 AND wid = :wid AND type = :type',
				'params' => array(':wid' => $wid, ':type' => $type),
				'order' => 'wt, id',
			));
		}else{
			$s = self::model()->find('name = :n OR code = :n', [':n' => $n]);
			if(empty($s)) return false;
			$rs = self::model()->findAll(array(
				'condition' => 'status = 1 AND cap < 100 AND wid = :wid AND type = :type AND wt >= :w',
				'params' => array(':wid' => $wid, ':type' => $type, ':w' => $s->wt),
				'order' => 'wt, id',
			));
		}
		
		foreach($rs as $r){
			if($r->canFit($p)){
				$r->addItem($p);
				return $r;
			}
		}

		return false;
	}

	public function canFit($p){
		$f = true;
		if($this->cap_item > 0){
			$f = ($this->totItems() + $p->pkg) <=  $this->cap_item;
		}
		if($f && $this->cap_cbm > 0){
			$f = ($this->totCBM() + $p->cbm) <= $this->cap_cbm;
		}
		if($f && $this->cap_kg > 0){
			$f = ($this->totWeight() + $p->weight) <= $this->cap_kg;
		}

		return $f;
	}

	public function addItem($p,$ckd=0){
		$sl = new StorageLog;
		$sl->sid = $this->id;
		$sl->model = get_class($p);
		$sl->fid = $p->id;
		$sl->in_dt = date('Y-m-d H:i:s');
		$sl->ckd = $ckd;
		$sl->save();
		$this->updateCap();
	}

	public function totItems(){
		return StorageLog::model()->count('out_dt IS NULL AND sid = :sid', [':sid' => $this->id]);
	}

	public function totCBM(){
		$t = 0;
		foreach($this->items as $r){
            if ( isset($r->cbm) ) {
                $t += $r->cbm;
            }
		}
		return $t;
	}

	public function totWeight(){
		$t = 0;
		foreach($this->items as $r){
            if ( isset($r->weight) ) {
                $t += $r->weight;
            }
		}
		return $t;
	}

	public function updateCap(){
		$ocap = $this->cap;
		if($this->cap_item + $this->cap_cbm + $this->cap_kg == 0){
			$this->cap = 0;
		}else{
			$items = $this->totItems();
			if(empty($items)){
				$this->cap = 0;
			}else{
				$pct = [0];
				if($this->cap_item > 0){
					$pct[] = round($this->totItems() / $this->cap_item * 100);
				}
				if($this->cap_cbm > 0){
					$pct[] = round($this->totCBM() / $this->cap_cbm * 100);
				}
				if($this->cap_kg > 0){
					$pct[] = round($this->totWeight() / $this->cap_kg * 100);
				}
				$this->cap = max($pct);
			}
		}

		if($ocap != $this->cap) $this->save();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'wid' => 'Warehouse',
			'pid' => 'Parent',
			'type' => 'Type',
			'status' => 'Status',
			'name' => 'Name',
			'code' => 'Code',
			'cx' => 'X',
			'cy' => 'Y',
			'cz' => 'Z',
			'cap' => 'Cap. %',
			'cap_kg' => 'Cap KG',
			'cap_cbm' => 'Cap CBM',
			'cap_item' => 'Cap Item',
			'width' => 'Width',
			'height' => 'Height',
			'depth' => 'Depth',
			'bwf' => 'Bwf',
			'notes' => 'Notes',
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
	public function search(){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('wid',$this->wid,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('status',$this->status);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('cx',$this->cx);
		$criteria->compare('cy',$this->cy);
		$criteria->compare('cz',$this->cz);
		$criteria->compare('cap',$this->cap,true);
		$criteria->compare('cap_kg',$this->cap_kg,true);
		$criteria->compare('cap_cbm',$this->cap_cbm,true);
		$criteria->compare('cap_item',$this->cap_item,true);
		$criteria->compare('width',$this->width,true);
		$criteria->compare('height',$this->height,true);
		$criteria->compare('depth',$this->depth,true);
		$criteria->compare('bwf',$this->bwf,true);
		$criteria->compare('notes',$this->notes,true);
		$criteria->compare('meta',$this->meta,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
    			'defaultOrder'=>'t.type ASC, t.name ASC',
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
	 * @return Storage the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
