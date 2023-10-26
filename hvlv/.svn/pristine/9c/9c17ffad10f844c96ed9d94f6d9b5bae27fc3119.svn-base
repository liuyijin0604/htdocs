<?php

/**
 * This is the model class for table "email_tpl".
 *
 * The followings are the available columns in table 'email_tpl':
 * @property string $id
 * @property string $slug
 * @property string $language
 * @property string $subject
 * @property string $body
 * @property string $updated
 */
class EmailTpl extends oActiveRecord
{
	const courierParcelSearch = "courierParcelSearch";
	public $isTLA = false;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return EmailTpl the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'email_tpl';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('slug, subject, body', 'required'),
			array('language, updated', 'safe'),
			array('slug', 'length', 'max'=>50),
			array('language', 'length', 'max'=>5),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, slug, language, subject, body, updated', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'slug' => 'Slug',
			'language' => 'Language',
			'subject' => 'Subject',
			'body' => 'Body',
			'updated' => 'Updated',
		);
	}

	// for TLA
	public function getDbConnection(){
		if(Yii::app()->name == 'TLA' && !empty(Yii::app()->db_tla) && in_array($this->tableName(), Yii::app()->db_tla->schema->getTableNames())) return self::getTlaConnection();
		if($this->isTLA==1){
			return self::getTlaConnection();
		}
		return parent::getDbConnection();
	}
	
	public static function getTplBySlug($s,$isTLA = false) {
		$emailTpl = new EmailTpl();
		if($isTLA==1)
		{
			$emailTpl->isTLA = 1;
		}
		return $emailTpl->find('slug = :s', array(':s' => $s));
	}
	
	public function assign($k, $v) {
		$this->body = str_replace('{'.$k.'}', $v, $this->body);
	}

	public function assignThese($vs) {
		foreach ($vs as $k=>$v)
			$this->assign($k, $v);
	}

	public function assignSubject($k, $v) {
		$this->subject = str_replace('{'.$k.'}', $v, $this->subject);
	}

	public function getContent() {
		return '<div style="font-family:Tahoma,Arial,Helvetica,sans-serif;font-size:12px;">'.preg_replace('/\{\S+\}/', '', $this->body).'</div>';
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search(){
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('slug',$this->slug,true);
		$criteria->compare('language',$this->language,true);
		$criteria->compare('subject',$this->subject,true);
		$criteria->compare('body',$this->body,true);
		$criteria->compare('updated',$this->updated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>[
				'defaultOrder'=>'t.slug',
			],
			'pagination'=>[
				'pageSize'=>'30',
			],
		));
	}
}