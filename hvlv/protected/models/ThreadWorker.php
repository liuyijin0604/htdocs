<?php

/**
 * This is the model class for table "thread_worker".
 *
 * The followings are the available columns in table 'thread_worker':
 * @property string $id
 * @property string $tid
 * @property integer $no
 * @property integer $status
 * @property string $stime
 * @property string $ftime
 * @property string $log
 */
class ThreadWorker extends CActiveRecord
{

	public static $states = array(
		10 => 'Created',
		20 => 'Running',
		99 => 'Completed',
		101 => 'Error',
		102 => 'Terminated',
	);

	public $logs, $totWorkers;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'thread_worker';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tid, status', 'required'),
			array('no, stime, ftime, log', 'safe'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('tid', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, tid, no, status, stime, ftime, log', 'safe', 'on'=>'search'),
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
			'thread' => array(self::BELONGS_TO, 'Thread', 'tid'),
		);
	}
	
	public function beforeSave(){
		if(!empty($this->logs)) $this->log = json_encode($this->logs);
		if(empty($this->status)) $this->status = 10;
		return true;
	}
	
	public function afterFind(){
		if(!empty($this->log)) $this->logs = json_decode($this->log, true);
		return true;
	}

	public function run($debug = false){
		$this->status = 20;
		$this->stime = date('Y-m-d H:i:s');
		$this->save();
		$this->totWorkers = $this->thread->totalWorkers();
		$err = $this->{'method'.ucfirst($this->thread->method)}();

		$this->ftime = date('Y-m-d H:i:s');
		
		if($err){
			$this->status = 101;
		}else{
			$this->status = 99;
		}
		$this->save();
		$this->thread->onFinish();
	}

	protected function methodConsolYto(){
		$model = ExcoConsol::model()->findByPk($this->thread->mdata['consol_id']);
		
		switch($model->poc){
			case 'CNCA2':
				$yto = new YtoAPI('K76968438', '0DxpUn9Z'); //GZ
			break;
			case 'CNTSN':
				//$yto = new YtoAPI('K33300029', 'hB8Eez5t'); //TJ
				$yto = new YtoAPI('K220122569', 'mSKTWw49'); //TJ
			break;
			case 'CNJNA':
				$yto = new YtoAPI('K53189864', 'Qd87294C'); //JN
			break;
			case 'CNCTU':
				$yto = new YtoAPI('K280257267', 'H7arz630'); //CD
			break;
			case 'CNJMN':
			case 'CNJM2':
				$yto = new YtoAPI('K75005171', '1wISkv31'); //JM
			break;
		}
		$err = false;
		foreach($model->shipments as $i => $p){
			if(!empty($p->ref) || $i % $this->totWorkers != $this->no) continue;
			$no = $yto->getNumber($p);
			if(empty($no)){
				if(!isset($this->logs['err'])) $this->logs['err'] = [];
				$this->logs['err'][] = $p->hbn.': '.$yto->err;
				$err = true;
				break;
			}else{
				$p->ref = (string) $no;
				if(!empty($yto->result->distributeInfo->shortAddress)) $p->mdata['dtb'] = (string) $yto->result->distributeInfo->shortAddress;
				if(!empty($yto->result->distributeInfo->consigneeBranchCode)) $p->mdata['cbc'] = (string) $yto->result->distributeInfo->consigneeBranchCode;
				if($p->status < 25) $p->status = 25;
				$p->save();
			}
		}

		return $err;
	}

	protected function methodConsolYtoGlobal(){
		$model = ExcoConsol::model()->findByPk($this->thread->mdata['consol_id']);
		
		switch($model->poc){
			case 'CNJMN':
			case 'CNJM2':
				$yto = new YtoAPI('tnbrwdhz7e4kvx1g', '75gfn3uw6osjr2z4ly9xap1k0icvdqht'); // Jack
				$pdc = 'AU0007';
			break;
			case 'CNXIA':
				$yto = new YtoAPI('tnbrwdhz7e4kvx1g', '75gfn3uw6osjr2z4ly9xap1k0icvdqht'); // Jack
				$pdc = 'AU0006';
			break;
		}
		$err = false;
		foreach($model->shipments as $i => $p){
			if(!empty($p->ref) || $i % $this->totWorkers != $this->no) continue;

			$r = $yto->createOrder($p, $pdc);
			$r = json_decode($r->ServiceEntranceResult);
			if(empty($r->success)){
				$this->logs['err'][] = $p->hbn.': '.$r->cnmessage;
				$err = true;
				continue;
			}
			$p->ref = $r->data->shipping_method_no;
			$p->save();
		}

		return $err;

	}

	protected function methodConsolYtoDTB(){
		$model = ExcoConsol::model()->findByPk($this->thread->mdata['consol_id']);
		
		switch($model->poc){
			case 'CNJMN':
			case 'CNJM2':
			case 'CNXIA':
				$yto = new YtoAPI('tnbrwdhz7e4kvx1g', '75gfn3uw6osjr2z4ly9xap1k0icvdqht'); // Jack
			break;
		}
		$err = false;
		foreach($model->shipments as $i => $p){
			if(empty($p->ref) || $i % $this->totWorkers != $this->no) continue;
			$r = $yto->getDistribute($p->ref);
			$r = json_decode($r->ServiceEntranceResult);
			if(isset($r->data->distribute_code)){
				$p->mdata['dtb'] = $r->data->distribute_code;
				$p->nolog = true;
				$p->save();
			}else{
				$this->logs['err'][] = $p->hbn.': '.$r->cnmessage;
				$err = true;
				continue;
			}
		}

		return $err;

	}

	protected function methodPrintYtoGlobal(){
		$rs = YtoOms::model()->findAll('api_id = :aid AND md = :md', [':aid' => $this->thread->mdata['api_id'], ':md' => $this->thread->mdata['md']]);
		
		switch($rs[0]->tpl){
			case 'CNJMN':
			case 'CNJM2':
				$yto = new YtoAPI('tnbrwdhz7e4kvx1g', '75gfn3uw6osjr2z4ly9xap1k0icvdqht'); // Jack
				$pdc = 'AU0007';
			break;
			case 'CNXIA':
				$yto = new YtoAPI('tnbrwdhz7e4kvx1g', '75gfn3uw6osjr2z4ly9xap1k0icvdqht'); // Jack
				$pdc = 'AU0006';
			break;
			case 'CNHGH':
				$yto = new YtoAPI('tnbrwdhz7e4kvx1g', '75gfn3uw6osjr2z4ly9xap1k0icvdqht'); // Jack
				$pdc = 'AU0003';
			break;
		}
		$err = false;
		foreach($rs as $i => $p){
			if(!empty($p->ref) || $i % $this->totWorkers != $this->no) continue;

			$r = $yto->createOmsOrder($p, $pdc);
			$r = json_decode($r->ServiceEntranceResult);
			if(empty($r->success)){
				$this->logs['err'][] = $p->hbn.': '.$r->cnmessage;
				$err = true;
				continue;
			}
			$p->ref = $r->data->shipping_method_no;
			$p->status = 1;
			$p->save();
		}

		return $err;

	}
	
	protected function methodPrintYtoDTB(){
		$rs = YtoOms::model()->findAll('api_id = :aid AND md = :md', [':aid' => $this->thread->mdata['api_id'], ':md' => $this->thread->mdata['md']]);
		
		switch($rs[0]->tpl){
			case 'CNJMN':
			case 'CNJM2':
			case 'CNXIA':
			case 'CNHGH':
				$yto = new YtoAPI('tnbrwdhz7e4kvx1g', '75gfn3uw6osjr2z4ly9xap1k0icvdqht'); // Jack
			break;
		}

		$err = false;
		foreach($rs as $i => $p){
			if(empty($p->ref) || $i % $this->totWorkers != $this->no) continue;
			$r = $yto->getDistribute($p->ref);
			$r = json_decode($r->ServiceEntranceResult);
			if(isset($r->data->distribute_code)){
				$p->mdata['dtb'] = $r->data->distribute_code;
				$p->status = 2;
				$p->save();
			}else{
				$this->logs['err'][] = $p->hbn.': '.$r->cnmessage;
				$err = true;
				continue;
			}
		}

		return $err;

	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'tid' => 'Tid',
			'no' => 'Worker #',
			'status' => 'Status',
			'stime' => 'Stime',
			'ftime' => 'Ftime',
			'log' => 'Log',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('tid',$this->tid);
		$criteria->compare('no',$this->no);
		$criteria->compare('status',$this->status);
		$criteria->compare('stime',$this->stime,true);
		$criteria->compare('ftime',$this->ftime,true);
		$criteria->compare('log',$this->log,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ThreadWorker the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
