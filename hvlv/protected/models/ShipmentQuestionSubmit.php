<?php

/**
 * This is the model class for table "shipment_question_submit".
 *
 * The followings are the available columns in table 'shipment_question_submit':
 * @property integer $id
 * @property string $ticket
 * @property string $date
 */
class ShipmentQuestionSubmit extends MetaModel
{
	public $mhbns = null;
	const USERGENERATE = 0;
	const SERVICEGENERATE = 1;
	const FRONTENDGENERATE = 2;
	public static $types= [
		0=>"customer submit",
		1=>"server submit",
		2=>"frontend submit"
	];
	private $read;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'shipment_question_submit';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date,faq', 'required'),
			array('ticket', 'length', 'max'=>45),
			array('email', 'length', 'max'=>255),
			array('phone', 'length', 'max'=>45),
			array('c_note,email,phone', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, ticket, org_id, date, c_note, faq, email, phone,type', 'safe', 'on'=>'search'),
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
			"org"=>[self::BELONGS_TO,'Org','org_id'],
			"shipmentQuestion"=>[self::HAS_MANY,'ShipmentQuestion','submit_id'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return AppHelper::tArray(strtolower(__CLASS__), [
			'id' => 'ID',
			'ticket' => 'Ticket',
			'date' => 'Date',
			'c_note'=>'Customer Note',
			'org_id' => 'Org Id',
			'read' =>'Read',
			'email'=>'Email',
			'phone'=>'Phone'
		]);
	}


	public function getSubmitUserType()
	{
		if($this->org!=null)
		{
			if($this->type==0)
			{
				return $this->org->name."[C]";
			}else if($this->type==1)
			{
				return $this->org->name."[S]";
			}
		}
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

		$criteria->compare('id',$this->id);
		if($this->ticket!=null)
		{
			$criteria->addCondition("ticket like '%{$this->ticket}%'");
		}

		$criteria->compare('date',$this->date,true);
		$criteria->compare('org_id',$this->org_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function afterFind()
	{
		if(empty($this->ticket))
		{
			$this->ticket =$this->generateSubmitCode();
			$this->save();
		}
		return parent::afterFind();
	}

	public function generateSubmitCode()
	{
		// $lastQuestion = ShipmentQuestionSubmit::model()->find("1=1 order by id desc");
		// return $lastQuestion==null?"CS".($this->org_id*11)."0":"CS".($this->org_id*11).($this->id+200);
		return "CS".($this->org_id*11).($this->id+200);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ShipmentQuestionSubmit the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	public static function saveFiles($files,$shipmentQuestion,$single = false)
	{
		if (!empty($files['ShipmentQuestionSubmit'])) 
			{
				$errors=[];
				$photos=empty($files['ShipmentQuestionSubmit']['tmp_name']['photos'])?[]:$files['ShipmentQuestionSubmit']['tmp_name']['photos'];
				foreach ($photos as $key => $photo) 
				{
					if (empty($photo)) 
					{
						$errors[]='images not exists';
					} else 
					{
						$name = $files['ShipmentQuestionSubmit']['name']['photos'][$key];
						if((preg_match("/{$shipmentQuestion->getRef()}/", $name)||preg_match("/{$shipmentQuestion->Shipment->hbn}/", $name))||$single)
						{
							if(!is_uploaded_file($photo)) $errors[]=$name.'images not exists';
							$hash = FileRepo::uploadHash($shipmentQuestion, FileRepo::CUSTOMERSERVICEFILE);
							$filesize = filesize($photo);
							$date = date('Y-m-d H:i:s');
							$fileHash = hash_file('crc32b', $photo).hash('crc32b', $filesize);
							$finfo = finfo_open(FILEINFO_MIME_TYPE);
							$mime = finfo_file($finfo, $photo);
							$fr = CargoProcess::updateUploadSingleFile($filesize,$date,$fileHash,$finfo,$mime,$photo,$name, $hash);
						}
						
					}
				}
			}
	}

	public static function getItems($items,$model,$shipment)
	{
		$isRef = preg_split("/{$shipment->ref}/", $model->mhbns);
		$isHbn = preg_split("/{$shipment->hbn}/", $model->mhbns);
		$items .= "\n";
		$pattern = "";
		$explo = "";
		$explodeItems = preg_split('/\n/',$items);
		$item = "";
		if($isRef)
		{
			foreach ($explodeItems as $key => $value) {
				if(preg_match("/{$shipment->ref}/", $value))
				{
					$item = preg_split("/{$shipment->ref}/",$value);
					$item = trim($item[1]);
					break;
				}
			}
			$explo = $shipment->ref;
		}

		if($isHbn)
		{
			foreach ($explodeItems as $key => $value) {
				if(preg_match("/{$shipment->hbn}/",$value))
				{
					$item = preg_split("/{$shipment->hbn}/",$value);
					$item = trim($item[1]);
					break;
				}
			}
		}

		return $item;
	}

	public static function sendNoticeSystemMessageToStaff($mapType,$number,$ticket)
	{
		$rs =TypeMapUser::model()->findAll('type=:type and status=1 and map_type = :mapType',array(':type'=> TypeMapUser::TYPE_CUSTOMER_SERVICE,':mapType'=>$mapType));
	    foreach($rs as $oneMap){
	        $model=new Message;
			$model->to_id=$oneMap['user_id'];
			$model->msg = "There are ".$number." new customer service questions submitted: Ticket:".$ticket;
			$model->from_id = !empty(Yii::app()->user->id)?Yii::app()->user->id:1;
			$model->status = 0;
			$model->type = 0;
			$model->save();
	    }

	}

}
