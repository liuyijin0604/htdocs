<?php

/**
 * This is the model class for table "reply_email".
 *
 * The followings are the available columns in table 'reply_email':
 * @property integer $id
 * @property integer $email_id
 * @property integer $reply_op
 * @property string $reply_body
 * @property string $reply_time
 * @property string $reply_email
 * @property string $reply_from
 * @property string $reply_subject
 * @property interger $status
 * @property string $meta
 */
class ReplyEmail extends CActiveRecord
{
	public $mdata;
	
	public $reply_body;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'reply_email';
	}
		
	public static $replay_states=[
		1=>'Send',
		2=>'Failed',
		3=>'Queued'
	];

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['email_id, reply_op,reply_email,reply_from,reply_subject', 'required'],
			['email_id, reply_op,status', 'numerical', 'integerOnly'=>true],
			['reply_from', 'length', 'max'=>100],
			['reply_subject,reply_email', 'length', 'max'=>200],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, email_id, reply_op, reply_body, reply_time,status,reply_from,reply_email, reply_subject, meta', 'safe', 'on'=>'search'],
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
			'main_email'=>[self::BELONGS_TO,'ImportsMail','email_id'],
			'attachments' => [self::HAS_MANY, 'FileRepo', 'fid', 'on'=>'attachments.type = 29'],
		];
	}
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'email_id' => 'Email',
			'reply_op' => 'Reply Op',
			'reply_time' => 'Reply Time',
			'reply_email' => 'Reply Email',
			'reply_subject' => 'Reply Subject',
			'status'=> 'Status',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('email_id', $this->email_id);
		$criteria->compare('reply_op', $this->reply_op);
		$criteria->compare('status', $this->status);
		$criteria->compare('reply_time', $this->reply_time, true);
		$criteria->compare('reply_email', $this->reply_email, true);
		$criteria->compare('reply_from', $this->reply_from, true);
		$criteria->compare('reply_subject', $this->reply_subject, true);
		$criteria->compare('meta', $this->meta, true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}
	/**
	 * Reply The email to client
	 * @return type
	 */
	public function sendMail()
	{
		include_once('PHPMailer/class.phpmailer.php');
		require_once('PHPMailer/PHPMailerAutoload.php');///SMTP
		$o=['status'=>false,'msg'=>'Email address not valid!'];
		$mail = new PHPmailer();
		$mail->CharSet = "UTF-8";
		$mail->IsHTML(true);
		/***************SMTP*********/
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'mail.toplogistics.com.au';  // Specify main and backup SMTP servers
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 587;                                    // TCP port to connect to
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'imports@toplogistics.com.au';                 // SMTP username
		$mail->Password = 'mxK+fU$n&LDK';
		/***************SMTP*********/
		// file_put_contents("a.txt", $this->reply_body);
		$mail->Subject = $this->reply_subject;
		$result=$this->changeBodyImage($mail, $this->reply_body);
		$mail->AltBody = preg_replace("/[\n\r\t]+/", "\n", strip_tags($this->reply_body));
		 
		if (!empty($this->reply_email)) {
			foreach (preg_split("/[;,]+/i", $this->reply_email) as $to) {
				if (filter_var(trim($to), FILTER_VALIDATE_EMAIL)) {
					$mail->AddAddress(trim($to));//add customer email address
				}
			}
			//          foreach(preg_split("/[;,]+/i",$this->mdata['from']) as $from){
			//               if(filter_var(trim($from),FILTER_VALIDATE_EMAIL)){
			//                   $mail->From=trim($from);
			//                   $mail->FromName=!empty($this->mdata['fromName'])?$this->mdata['fromName']:(explode("@", $from)[0]);
			//                }
			//              }
			$mail->From=$this->reply_from;
			$mail->FromName='TLA imports';
				
			//CC
			if (!empty($this->mdata['cc'])) {
				foreach (preg_split("/[;,]+/i", $this->mdata['cc']) as $cc) {
					if (filter_var(trim($cc), FILTER_VALIDATE_EMAIL)) {
						$mail->AddCC(trim($cc));//add customer email address
					}
				}
			}
			if (!empty($files)) {
				foreach ($files as $fn) {
					$mail->AddAttachment($fn[0], basename($fn[1]), 'base64', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				}
			}
			foreach ($this->attachments as $att) {
				$mail->AddAttachment($att->getFile(), $att->name, 'base64', $att->mime);
			}
			if ($mail->Send()) {
				$this->status = 1;
				$this->reply_time=date('Y-m-d H:i:s');
				$this->save();
				$message=$mail->getSentMIMEMessage();
				$this->saveMail($message);
				$o['status']=true;
				$o['msg']='send successfully!';
			} else {
				$this->status = 2;
				$o['status']=false;
				$o['msg']=$mail->ErrorInfo;
			}
			if (!empty($result)&& is_array($result)) {
				foreach ($result as $fileaddress) {
					unlink($fileaddress);
				}
			}
			return $o;
		}
	}
	
	public function saveMail($message)
	{
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'importsmail'.DIRECTORY_SEPARATOR.'replys'.DIRECTORY_SEPARATOR;
		file_put_contents($tmp.$this->id.".emz", gzencode($message));
	}

	public function loadReplyBody()
	{
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'importsmail'.DIRECTORY_SEPARATOR.'replys'.DIRECTORY_SEPARATOR;
		$replyFileName=$tmp.$this->id.".emz";
		if(!is_file($replyFileName)) return;
		$message= gzdecode(file_get_contents($replyFileName));
		$this->reply_body= $this->getTheMailBody($message);
	}
	
	public function getTheMailBody($message)
	{
		require_once(Yii::app()->basePath.'/vendor/autoload.php');
		$parser = new PhpMimeMailParser\Parser();
		$parser->setText($message);
		$body= $parser->getMessageBody('text');
		if (empty($body)) {
			require_once(Yii::app()->basePath.'/vendor/autoload.php');
			$html=$this->getBodyFromParser($parser);
			$body = Html2Text\Html2Text::convert($html, true);
		}
		return $body;
	}
	public function getBodyFromParser($parser)
	{
		$html = $parser->getMessageBody('htmlEmbedded');
		$html=preg_replace('/\bcharset=.*(?=")/', 'charset=utf8', $html);
		$html=preg_replace("/(?<=<style>)<!--.*(?=<\/style>)/Ums", "", $html);
		if (empty($html)) {
			$html=$parser->getMessageBody('text');
		}
		return $html;
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
			$this->mdata= json_decode($this->meta, true);
		}
	}
		
	/*
	 * if the mail body have base64 pics,we need change it to embedimage,as gmail,homtail not supported base64 image
	 *
	 * @param $mail,$body
	 * @return an array of deleted pictures;
	 */
	public function changeBodyImage(&$mail, $body)
	{
		$result= $this->prebody($body);
		if (!empty($result[1])) {
			foreach ($result[1] as $key=>$value) {
				$mail->AddEmbeddedImage($value, $key);
			}
		}
		$mail->Body = $result[0];
		return $result[1];
	}
	private function prebody($text1)
	{
		$pattern="/data:image\/[a-zA-Z]*;base64,[^\"]*/";
		$imageArray=[];
		while (preg_match($pattern, $text1, $match)) {
			$imageText=$match[0];
			$filename=$this->genImage($imageText);
			$imageArray[basename($filename)]=$filename;
			$name="cid:".basename($filename);
			$text1=preg_replace($pattern, $name, $text1, 1);
		};
		return [$text1,$imageArray];
	}

	public function genImage($text)
	{
		$td = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'importsmail'.DIRECTORY_SEPARATOR.'images';
		if (!file_exists($td)) {
			mkdir($td);
		}
		$fd = base64_decode(preg_replace('/data:image\/[^;]+;base64,/', '', $text));
		$file =$td.DIRECTORY_SEPARATOR.uniqid() . '.jpg';
		file_put_contents($file, $fd);
		return $file;
	}
	
	public function getReplyOp()
	{
		return User::getUserName($this->reply_op);
	}



	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ReplyEmail the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
