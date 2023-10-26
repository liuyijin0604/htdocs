<?php

/**
 * This is the model class for table "faq".
 *
 * The followings are the available columns in table 'faq':
 * @property string $id
 * @property string $cid
 * @property string $weight
 * @property string $title
 * @property string $cont
 * @property string $meta
 */
class Faq extends CActiveRecord
{
	public $mdata = [];
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'faq';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['cid', 'required'],
			['weight, title, cont, meta', 'safe'],
			['cid, weight', 'length', 'max'=>11],
			['title', 'length', 'max'=>200],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, cid, weight, title, cont, meta', 'safe', 'on'=>'search'],
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [];
	}

	public function getCate()
	{
		$o = oList::getItem($this->cid);
		return empty($o)? 'UNKNOWN' : $o;
	}

	public function cateList()
	{
		$kvp = oList::kvp('faq_types');
		$html = '<select id="Faq_cid" name="Faq[cid]"><option value="">All</option>';
		foreach ($kvp as $k => $v) {
			$html .= '<option value="'.$k.'"'.($k == $this->cid? ' selected':'').(preg_match('/^\-\-/', $v)? '' : ' disabled').'>'.$v.'</option>';
		}
		return $html.'</select>';
	}
	
	public function beforeSave()
	{
		$this->meta = empty($this->mdata)? '' : json_encode($this->mdata);
		return parent::beforeSave();
	}
	
	public function afterFind()
	{
		if (!empty($this->meta)) {
			$this->mdata = json_decode($this->meta, true);
		}
		return parent::afterFind();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'cid' => 'Category',
			'weight' => 'Weight',
			'title' => 'Title',
			'cont' => 'Content',
			'meta' => 'Meta',
		];
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

		$criteria->compare('id', $this->id);
		$criteria->compare('cid', $this->cid);
		$criteria->compare('weight', $this->weight);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('cont', $this->cont, true);
		$criteria->compare('meta', $this->meta, true);
		$with = [];

		if (!empty($with)) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if ($ec) {
			$criteria->mergeWith($ec);
		}

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
			'sort'=>[
				'defaultOrder'=>'t.id DESC',
			],
			'pagination'=> $pgn? [
				'pageSize' => $ps,
			] : false,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Faq the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
