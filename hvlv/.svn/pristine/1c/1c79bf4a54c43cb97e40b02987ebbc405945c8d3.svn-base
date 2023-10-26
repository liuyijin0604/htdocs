<?php

/**
 * This is the model class for table "word_replace".
 *
 * The followings are the available columns in table 'word_replace':
 * @property integer $id
 * @property string $pre_word
 * @property string $replace_word
 * @property integer $type
 * @property integer $status
 * @property string $meta
 * @property string $created
 */
class WordReplace extends CActiveRecord
{
	public $mdata;
	public $custom_log_note;
	public $nolog=false;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'word_replace';
	}
		
	public static $types=[
		1=>'Danger',
		2=>'Prohibited',
	];

	public static $states = [
		0 => 'Inactive',
		1 => 'Active',
	];

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['pre_word', 'required'],
			['status,type', 'numerical', 'integerOnly'=>true],
			['pre_word, replace_word', 'length', 'max'=>40],
			['pre_word','unique'],
			['id, pre_word, replace_word,type,status, meta, created', 'safe', 'on'=>'search'],
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
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'pre_word' => 'Old Word',
			'replace_word' => 'Replace Word',
			'type'=>'Type',
			'status' => 'Status',
			'meta' => 'Meta',
			'created' => 'Create Time'
		];
	}
	public function beforeSave()
	{
		if (!empty($this->mdata)) {
			$this->meta= json_encode($this->mdata);
		}
		return true;
	}
	public function afterFind()
	{
		if (!empty($this->meta)) {
			$this->mdata=json_decode($this->meta, true);
		}
		return true;
	}
		
	public function afterSave()
	{
		$opname='';
		if (!$this->nolog && !empty($this)) {
			if (isset(Yii::app()->user)) {
				$opname = Yii::app()->user->name;
			}
			$extra = empty($this->custom_log_note)? [] : ['note' => $this->custom_log_note];
			if ($this->isNewRecord) {
				Log::add($this, Log::LOG_TYPE_CREATE, array_merge(['notes' => $opname . ' create'], $extra));
			} else {
				Log::add($this, Log::LOG_TYPE_UPDATE, array_merge(['notes' => $opname . ' updated to:' .!empty($this->replace_word) ? $this->replace_word : ' '], $extra));
			}
		}
	}
		
	public function getType()
	{
		return self::$types[$this->type];
	}
		
	public static function replace(&$context, $orgId, $shipment)
	{
		$sql="SELECT pre_word, replace_word,type FROM `word_replace` WHERE status=1";
		$errs=[];
		$rs=Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rs as $r) {
			// if (preg_match("/".$r['pre_word']."/i", $context)) {
			// 	$context=preg_replace("/".$r['pre_word']."/i", $r['replace_word'], $context);
			// 	/* if ($r['type']==2) {//2 means prohibited
			// 		$errs[]="item contains Prohibited words:".$r['pre_word'];
			// 	} */
			// 	$wordReplaceUsage = new WordReplaceUsageLog();
			// 	$wordReplaceUsage->org_id = $orgId;
			// 	$wordReplaceUsage->original_word = $r['pre_word'];
			// 	$wordReplaceUsage->replace_word = $r['replace_word'];
			// 	$wordReplaceUsage->process_time = date('Y-m-d H:i:s');
			// 	$wordReplaceUsage->shipment_id = $shipment->id;
			// 	$wordReplaceUsage->consol_id = $shipment->consol_id;
			// 	$wordReplaceUsage->save();
			// }
			if (self::wordExistsInString($r['pre_word'], $context)) {
				$context = str_ireplace($r['pre_word'], !empty($r['replace_word']) ? $r['replace_word'] : ' ', $context);
				// Ignore word replace records that already exist
				$usage = WordReplaceUsageLog::model()->findByAttributes(array('shipment_id' => $shipment->id, 'original_word' => $r['pre_word']));
				if (empty($usage)) {
					$wordReplaceUsage = new WordReplaceUsageLog();
					$wordReplaceUsage->org_id = $orgId;
					$wordReplaceUsage->original_word = $r['pre_word'];
					$wordReplaceUsage->replace_word = !empty($r['replace_word']) ? $r['replace_word'] : ' ';
					$wordReplaceUsage->process_time = date('Y-m-d H:i:s');
					$wordReplaceUsage->shipment_id = $shipment->id;
					$wordReplaceUsage->consol_id = $shipment->consol_id;
					$wordReplaceUsage->save();
				}
			}
		}
		return [$context,$errs];
	}

	/**
	 * Check if a word exists in a string
	 */
	private function wordExistsInString($word, $string)
	{
		$pattern = '/\b' . preg_quote($word, '/') . '\b/i';
    	return preg_match($pattern, $string) === 1;
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

		$criteria->compare('id', $this->id);
		$criteria->compare('type', $this->type);
		$criteria->compare('pre_word', $this->pre_word, true);
		$criteria->compare('replace_word', $this->replace_word, true);
		$criteria->compare('status', $this->status);
		$criteria->compare('meta', $this->meta, true);
		$criteria->compare('created', $this->created, true);

		$sort=new CSort();
		$sort->attributes=[
			'created' =>[
				'asc'=>'created ASC',
				'desc'=>'created DESC'
			]
		];
		$sort->defaultOrder = 'created DESC';
		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
			'sort'=>$sort,
			'pagination'=> [
				'pageSize' =>30,
			],]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WordReplace the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
