<?php

/**
 * This is the model class for table "ex_image".
 *
 * The followings are the available columns in table 'ex_image':
 * @property integer $id
 * @property string $file_adr
 * @property string $hbn
 * @property integer $islinked   0:not have name,  1:linked   2: have name, but do not find exparcel record in our database yet.
 * @property string $agent_id
 * @property string $pdf_number
 * @property string $date
 */
class ExImage extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ex_image';
	}

	public static  $link_states=array(
		0=>'Unindentified',
		1=>'Linked',
		2=>'Shipment Uncreated'
	);

		/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('file_adr,islinked, pdf_number, date', 'required'),
			array('islinked', 'numerical', 'integerOnly'=>true),
			array('file_adr', 'length', 'max'=>100),
			array('hbn','length', 'max'=>300),
			array('agent_id', 'length', 'max'=>30),
			array('pdf_number', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, file_adr, hbn, islinked, agent_id, pdf_number, date', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'file_adr' => 'File Adr',
			'hbn' => 'Hbn',
			'islinked' => 'Islinked',
			'agent_id' => 'Agent',
			'pdf_number' => 'Pdf Number',
			'date' => 'Date',
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
	public function search($ec = false){
		$criteria=new CDbCriteria;
		$criteria->compare('hbn',$this->hbn,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('pdf_number',$this->pdf_number,true);

		if ($this->islinked === '') {
			$criteria->addCondition('islinked != 1');
		} else {
			$criteria->compare('islinked', $this->islinked);
		}

		if($ec) {
			$criteria->mergeWith($ec);
		}

		return new CActiveDataProvider($this, array(
			'sort'=>array(
				'defaultOrder'=>'date DESC',
			),
			'pagination'=>array(
				'pageSize' => 20,
			),
			'criteria'=>$criteria,
		));
	}

	/*
	 * return the linked specification for each image
	 * @param null
	 * @return String
	 * 
	 */  
	public function getLinkedStatus(){
		return isset(self::$link_states[$this->islinked])?self::$link_states[$this->islinked]:$this->islinked;
	}
		
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ExImage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function get_url_id($id){
		$r= ExImage::model()->find('id=:id',array(':id'=>$id));
		return $r->getfilename();   
	}

	public function getfilename(){
		//$file=substr($this->pdf_number,-19,8);
		preg_match('/[\d]+[_\-]+(\d{8})/', basename($this->pdf_number), $m);
		$file = $m[1];
		$bd = strtotime($m[1]) > strtotime('2018-08-10')? 'pep_img' : 'connote_img';
		
		return 'https://hk.pca168.com/'.$bd.'/'.$file.'/'.$this->file_adr;
	}
}