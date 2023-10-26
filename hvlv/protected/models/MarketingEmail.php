<?php
Class MarketingEmail  extends MetaModel{

	public const templete_tla = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title></title>
        </head>
        <body>
        <div style="background-color:white">
			--OnlineView--
            <img src="http://branch.toplogistics.com.au:8189/images/marketing_email_header.jpg"alt="tla" />
			<div style="border:2px solid rgb(209, 81 ,76);width: 540px;margin-left: 30px;margin-bottom: 20px;">
                <div style="background-color: rgb(209, 81 ,76);text-align:center;font-size: 20px;color: white;padding: 3px;">
                Heading1
                </div>
                <div style="font-size: 18px;padding: 10px;word-wrap:break-word">
                Content1
                </div>
            </div>

			<div style="border:2px solid rgb(209, 81 ,76);width: 540px;margin-left: 30px;margin-bottom: 20px;">
				<div style="background-color: rgb(209, 81 ,76);text-align:center;font-size: 20px;color: white;padding: 3px;">
				Heading2
				</div>
				<div style="font-size: 18px;padding: 10px;word-wrap:break-word">
				Content2
				</div>
			</div>

			<div style="border:2px solid rgb(209, 81 ,76);width: 540px;margin-left: 30px;margin-bottom: 20px;">
				<div style="background-color: rgb(209, 81 ,76);text-align:center;font-size: 20px;color: white;padding: 3px;">
				Heading3
				</div>
				<div style="font-size: 18px;padding: 10px;word-wrap:break-word">
				Content3
				</div>
			</div>

			<div style="border:2px solid rgb(209, 81 ,76);width: 540px;margin-left: 30px;margin-bottom: 20px;">
				<div style="background-color: rgb(209, 81 ,76);text-align:center;font-size: 20px;color: white;padding: 3px;">
				Heading4
				</div>
				<div style="font-size: 18px;padding: 10px;word-wrap:break-word">
				Content4
				</div>
			</div>
			<div style="border:2px solid rgb(209, 81 ,76);width: 540px;margin-left: 30px;margin-bottom: 20px;">
				<div style="background-color: rgb(209, 81 ,76);text-align:center;font-size: 20px;color: white;padding: 3px;">
				Heading5
				</div>
				<div style="font-size: 18px;padding: 10px;word-wrap:break-word">
				Content5
				</div>
			</div>

        <div style="height:50px;margin-top:30px">
            <div style="border-top: 1px solid rgb(41, 50 ,106);color: rgb(209, 81 ,76);font-size: 15px;font-weight: 900;font-family: Arial, Helvetica, sans-serif;float:left;vertical-align: top;width: 120px;height: 30px;padding-left: 30px;padding-top: 10px;">
                CONTACT US
            </div>
        
            <div style="background-color: rgb(41, 50 ,106);color: white;float:left;vertical-align: top;width: 440px;padding-left: 10px;padding-top: 13px;padding-bottom: 15px;">
                sales@toplogistics.com.au | toplogistics.com.au | 02 90668206
            </div>
        
        </div>

        </div>
		<br/>

		<a style="color:rgb(240, 240 ,240);text-decoration:none" href="http://branch.toplogistics.com.au:8189/ims/customerService/marketingEmailUnsubscribe">Unsubscribe</a>

        </body>
        </html>';

    public const status_new = 10;
    public const status_pending = 20;
    public const status_sent = 30;
    public const status_cancel = 40;

    public const listStatus=[
        self::status_new => 'new',
        // self::status_pending => 'pending',
		self::status_pending => 'Schedule',
        self::status_sent => 'sent',
        self::status_cancel => 'Cancel',
    ];

    
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'marketing_email';
	}

	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// The following rule is used by search().
			array('id,status,date,subject,html,meta', 'safe'),
			// @todo Please remove those attributes that should not be searched.
			array('id,status,date,subject,html,meta', 'safe', 'on' => 'search'),
		);
	}

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
            'status'=>'status',
            'date'=>'date',
            'subject'=>'subject',
            'html'=>'html',
			'meta' => 'meta',
		);
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
		$criteria->compare('status',$this->status,true);
        $criteria->compare('date',$this->date,true);
        $criteria->compare('subject',$this->subject,true);
        $criteria->compare('html',$this->html,true);
		$criteria->compare('meta',$this->meta,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>[
				'defaultOrder'=>'t.id desc',
			],
			'pagination'=>[
				'pageSize'=>'30',
			],
		));
	}
}