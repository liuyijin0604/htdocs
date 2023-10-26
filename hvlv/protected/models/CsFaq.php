<?php

/**
 * This is the model class for table "cs_faq".
 *
 * The followings are the available columns in table 'cs_faq':
 * @property integer $id
 * @property string $content
 * @property integer $user_id
 * @property integer $status
 */
class CsFaq extends CActiveRecord
{

	const active =0;
	const inactive =1;
	const dispute_type =2;
	const dispute_type_type =3;
	const EMAIL = 2;
	const other = 10;
	const top_courier_service = 50;
	public static $state=[
		false=>"active",
		true=>"inactive"];

	public static $shipment_types = [
		10 => 'Others',
		50 => 'TopCourierService',
	];

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cs_faq';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('content, user_id', 'required'),
			array('user_id, status', 'numerical', 'integerOnly'=>true),
			array('content', 'length', 'max'=>255),
			array('frontend_content', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, content, user_id, status, dpt_id, agent_id, invoice_type, shipment_type', 'safe', 'on'=>'search'),
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
			'assignUser' => [self::HAS_ONE, 'CsFaqUserMap', 'cs_faq_id'],
			'warehouse' => [self::BELONGS_TO, 'Org', 'dpt_id'],
			'agent' => [self::BELONGS_TO, 'Org', 'agent_id'],
		);
	}

	public function getInvoiceType()
	{
		$info = '';
		$types = explode(",",$this->invoice_type);
		foreach (Invoice::$types as $k => $v) {
			if (in_array($k,$types)) {
				$info .= empty($info) ? $v : ';' . $v;
			}
		}
		return $info;
	}

	public function getShipmentType()
	{
		$info = '';
		$types = explode(",",$this->shipment_type);
		foreach (self::$shipment_types as $k => $v) {
			if (in_array($k,$types)) {
				$info .= empty($info) ? $v : ';' . $v;
			}
		}
		return $info;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'content' => 'Content',
			'frontend_content' => 'Frontend Content',
			'user_id' => 'User',
			'status' => 'Status',
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
	public function search($pgn = true, $ps = 30)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('frontend_content',$this->frontend_content,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('status',$this->status);

		$sort = new CSort(get_called_class());
		$sort->attributes = [

			'*'
		];


		// in case sub-gridview, we need consol_id set by parent grid view
		$pagerparams = $_GET;

		return new CActiveDataProvider($this, [
			'criteria' => $criteria,
			'sort' => $sort,
			'pagination' => $pgn ? [
				'pageSize' => $ps,
				'params' => $pagerparams,
			] : false,
		]);
	}

	public static function getFaqList($type = true,$status = 0)
	{
		if($status==2)
		{
			$list = CsFaq::model()->findAll(" status =  :status and type =:type",array(":status"=>$status,":type"=>$type));
			$results = [];
			foreach ($list as $key => $value) {
				$results[$value->id] = $value->content;
			}
			return $results;
		}
		$list = CsFaq::model()->findAll(" status =  :status and type =:type",array(":status"=>$status,":type"=>$type));
		$results = [];
		foreach ($list as $key => $value) {
			if($type)
			{
				$results[$value->content] = $value->content;
			}else
			{
				$results[$value->frontend_content] = $value->frontend_content;
			}
		}
		return $results;
	}

	public static function getFullFaqList()
	{
		$list = CsFaq::model()->findAll(" status =  :status",array(":status"=>CsFaq::active));
		$results = [];
		foreach ($list as $key => $value) 
		{
			$results[$value->id] = [$value->content,$value->frontend_content];
		}
		return $results;
	}
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CsFaq the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
