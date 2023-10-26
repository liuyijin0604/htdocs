<?php

/**
 * This is the model class for table "thread".
 *
 * The followings are the available columns in table 'thread':
 * @property string $id
 * @property integer $status
 * @property string $method
 * @property string $stime
 * @property string $ftime
 * @property string $meta
 */
class Thread extends CActiveRecord
{
	public static $states = array(
		10 => 'New',
		20 => 'Running',
		30 => 'Queued',
		99 => 'Completed',
		101 => 'Error',
		102 => 'Terminated',
	);

	public $mdata = array();

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'thread';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('method', 'required'),
			array('status, stime, ftime, meta', 'safe'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('method', 'length', 'max'=>100),
			array('method', 'validMethod'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, status, method, stime, ftime, meta', 'safe', 'on'=>'search'),
		);
	}

	public function validMethod(){
		$valid = method_exists('ThreadWorker', 'method'.ucfirst($this->method));
		if(!$valid) $this->addError('method', Yii::t(strtolower(__CLASS__), ' worker method not defined'));
		return $valid;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'workers' => array(self::HAS_MANY, 'ThreadWorker', 'tid'),
		);
	}

	public function totalWorkers(){
		return empty($this->mdata['total_workers'])? ThreadWorker::model()->count('tid = :tid', [':tid' => $this->id]) : $this->mdata['total_workers'];
	}
	
	public function beforeSave(){
		if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		if(empty($this->status)) $this->status = 10;
		return true;
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return true;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'status' => 'Status',
			'method' => 'Method',
			'stime' => 'Stime',
			'ftime' => 'Ftime',
			'meta' => 'Meta',
		);
	}

	public function dispatch($workers = 1, $debug = false){
		$this->mdata['total_workers'] = $workers;
		$this->status = 20;
		$this->stime = date('Y-m-d H:i:s');
		$this->save();

		$win = substr(php_uname(),0,7) == 'Windows';
		$i = 0;
		while($i<$workers){
			$w = new ThreadWorker('create');
			$w->tid = $this->id;
			$w->no = $i;
			$w->status = 10;
			$w->save();
			if($debug) echo 'Creating worker ', $i, "\n";
			$dir = Yii::app()->basePath.DIRECTORY_SEPARATOR;
			$cmd = 'thread '.$w->id.($debug? ' -d':'');
			if($win){
				pclose(popen('start "" /D '.$dir.' /B php.exe yiic.php '.$cmd.' >nul 2>nul', 'r'));
			}else{
				AppHelper::exec($dir.'yiic '.$cmd.' >/dev/null 2>/dev/null &');
			}
			$i++;
		}
		if($debug) echo "All workers dispatched\n";
	}

	public function onFinish(){
		$err = false;
		foreach($this->workers as $w){
			if($w->status < 99) return false;
			if($w->status > 100) $err = true;
		}
		$fmn = 'finish'.ucfirst($this->method);
		
		$this->ftime = date('Y-m-d H:i:s');
		if($err){
			$this->status = 101;
		}else{
			$this->status = 99;
		}
		if(method_exists('Thread', $fmn)) $this->{$fmn}();
		$this->save();
	}

	protected function finishConsolYto(){
		if($this->status == 99){
			$model = ExcoConsol::model()->findByPk($this->mdata['consol_id']);
			$model->status = 20;
			$model->save();
		}
	}

	protected function finishConsolYtoGlobal(){
		if($this->status == 99){
			$thread = new Thread('create');
			$thread->method = 'consolYtoDTB';
			$thread->mdata['consol_id'] = $this->mdata['consol_id'];
			$thread->save();
			$thread->dispatch(4);
			$this->mdata['sub'] = $thread->id;
		}
	}

	protected function finishPrintYtoGlobal(){
		if($this->status == 99){
			$thread = new Thread('create');
			$thread->method = 'printYtoDTB';
			$thread->mdata['api_id'] = $this->mdata['api_id'];
			$thread->mdata['md'] = $this->mdata['md'];
			$thread->save();
			$thread->dispatch(2);
			$this->mdata['sub'] = $thread->id;
		}
	}

	protected function finishConsolYtoDTB(){
		$this->finishConsolYto();
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('method',$this->method,true);
		$criteria->compare('stime',$this->stime,true);
		$criteria->compare('ftime',$this->ftime,true);
		$criteria->compare('meta',$this->meta,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Thread the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
