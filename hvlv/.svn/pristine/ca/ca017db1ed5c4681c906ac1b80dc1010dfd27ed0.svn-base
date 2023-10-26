<?php

/**
 * This is the model class for table "olist".
 *
 * The followings are the available columns in table 'olist':
 * @property string $id
 * @property string $pid
 * @property string $item
 * @property string $weight
 * @property integer $active
 */
class oList extends CActiveRecord
{
	public $mlvl, $slug, $lvl, $name;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return oList the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'olist';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pid, item, weight', 'safe'),
			array('active, weight', 'numerical', 'integerOnly'=>true),
			array('pid', 'length', 'max'=>50),
			array('item', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, pid, item, weight, active', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'parent' => array(self::BELONGS_TO, 'oList', 'pid'),
			'children' => array(self::HAS_MANY, 'oList', 'pid', 'on' => 'children.active = 1', 'order' => 'children.weight'),
		);
	}
	
	public static function inList($n, $l){
		$p = self::model()->find('item LIKE :s', array(':s' => $l.':%'));
		if(empty($p)) return array();
		$rs = self::subList($p->id, 1);
		foreach($rs as $r){
			if(strtolower($r->item) == strtolower($n)){
				return $r;
			}
		}
		return false;
	}
	
	public static function family($id){
		if(empty($id)) return array();
		$p = self::model()->findByPk($id);
		$r = array($id);
		foreach($p->children as $c){
			$r = array_combine($r, self::family($c->id));
		}
		return $r;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'pid' => 'Parent',
			'name' => 'Name',
			'lvl' => 'Levels',
			'item' => 'Item',
			'weight' => 'Weight',
			'active' => 'Active',
		);
	}
	
	public function afterFind(){
		if($this->pid == 0){
			list($s, $l, $n) = explode(':', $this->item);
			$this->slug = $s;
			$this->mlvl = $l;
			$this->name = $n;
		}
	}
	
	public function listChildren(){
		$rs = array_slice(self::kvp($this->slug), 0, 2);
		$l = implode(', ', $rs);
		return (sizeof($this->children) > 2)? $l.' ...' : $l;
	}
	
	public function treeList($options=array()){
		$ho = '';
		foreach($options as $k=>$v){
			$ho .= ' '.$k.'="'.$v.'"';
		}
		$r = '<ol'.$ho.'>';
		foreach($this->children() as $c){
			$r .= '<li id="'.$c->id.'"><span>'.$c->item.'</span>';
			if(sizeof($c->children()) > 0){
				$r .= $c->treeList();
			}
			$r .= '</li>';
		}
		return $r.'</ol>';
	}
	
	public static function getItem($id){
		$r = self::model()->findByPk($id);
		return empty($r)? '' : $r->item;
	}
	
	public static function subList($p, $act = 1, $lv=0){
		$rs = self::model()->findAll('pid = :p AND active = :a ORDER BY weight', array(':p' => $p, ':a' => $act));
		$rt = array();
		foreach($rs as $r){
			$r->lvl = $lv;
			$rt[] = $r;
			if(sizeof($r->children) > 0){
				$rt = array_merge($rt, self::subList($r->id, $act, $lv+1));
			}
		}
		return $rt;
	}
	
	public static function kvp($s, $lvl=0){
		$p = self::model()->find('item LIKE :s', array(':s' => $s.':%'));
		if(empty($p)) return array();
		$rs = self::subList($p->id, 1);
		$kvp = array();
		foreach($rs as $r){
			if($lvl > 0 && $r->lvl >= $lvl) continue;
			$kvp[$r->id] = str_repeat('-', $r->lvl).($r->lvl > 0? ' ':'').$r->item;
		}
		return $kvp;
	}
	

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	
	public function search(){
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		
		$criteria->compare('pid', 0);
		$criteria->compare('item',$this->item);
		$criteria->compare('active',$this->active);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
    			'defaultOrder'=>'weight',
  			),
			'pagination'=>array(
				'pageSize'=>20,
            ),
		));
	}
}