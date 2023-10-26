<?php

/**
 * This is the model class for table "dispute_line".
 *
 * The followings are the available columns in table 'dispute_line':
 * @property string $id
 * @property string $type
 * @property string $status
 * @property integer $flag
 * @property string $meta
 * @property string $dispute_id
 * @property string $si_reconcile_line_id
 * @property string $note
 * @property string $dispute_amount_ex_gst
 */
class DisputeLine extends OMetaModel
{
	public $rec_id = 0;
	const Failure = 99;
	const Success = 10;
	const Deleted = 100;

	public $line_ref, $line_item_code, $line_value, $line_my_value;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'dispute_line';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dispute_id, si_reconcile_line_id, dispute_amount_ex_gst', 'required'),
			array('flag', 'numerical', 'integerOnly'=>true),
			array('type, status', 'length', 'max'=>2),
			array('dispute_id, si_reconcile_line_id, dispute_amount_ex_gst', 'length', 'max'=>10),
			array('note, line_ref, line_item_code, line_value, line_my_value', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, status, flag, meta, dispute_id, si_reconcile_line_id, note, dispute_amount_ex_gst, line_ref, line_item_code, line_value, line_my_value', 'safe', 'on'=>'search'),
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
			'parent'=>[self::BELONGS_TO,'Dispute','dispute_id'],
			'line'=>[self::BELONGS_TO,'SiReconcileLine','si_reconcile_line_id']
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => 'Type',
			'status' => 'Status',
			'flag' => 'Flag',
			'meta' => 'Meta',
			'dispute_id' => 'Dispute',
			'si_reconcile_line_id' => 'Si Reconcil Line',
			'note' => 'Note',
			'dispute_amount_ex_gst' => 'Dispute Amount Ex Gst',
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
	public function search($pgn = true, $ps = 10)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('dispute_id',$this->dispute_id);
		$criteria->compare('si_reconcile_line_id',$this->si_reconcile_line_id,true);
		$criteria->compare('note',$this->note,true);
		$criteria->compare('dpmt',$this->dpmt);
		$criteria->compare('dispute_amount_ex_gst',$this->dispute_amount_ex_gst,true);

		$with = [];
		if(!empty($this->rec_id))
		{
			$with[] = 'parent';
			$criteria->compare('parent.rec_id',$this->rec_id);
		}
		if (!empty($this->line_ref)) {
			$with[] = 'line';
			$criteria->compare('line.ref', $this->line_ref, true);
		}
		if (!empty($this->line_item_code)) {
			$with[] = 'line';
			$criteria->compare('line.item_code', $this->line_item_code, true);
		}
		if (!empty($this->line_value)) {
			$with[] = 'line';
			$criteria->compare('line.value', $this->line_value, true);
		}
		if (!empty($this->line_my_value)) {
			$with[] = 'line';
			$criteria->compare('line.my_value', $this->line_my_value, true);
		}

		if (!empty($with)) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if(!empty($this->dispute_id))
		{
			$with[] = 'line';
			$criteria->with = array_unique($with);
			$criteria->group = "line.ref";
			$criteria->order = "t.id";
		}

		$pagerparams = $_GET;

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort' => array(
				'defaultOrder' => 't.id DESC',
			),
			'pagination' => $pgn ? [
				'pageSize' => $ps,
				'params' => $pagerparams,
			] : false,
		));
	}
	public function getAllValue()
	{
		$ref = $this->line->ref;
		$sql="SELECT sum(value) as value FROM `{$this->getDatabaseName()}`.`dispute_line` d join `{$this->getDatabaseName()}`.`si_reconcile_line` s on d.si_reconcile_line_id = s.id where ref = '{$ref}' and s.rec_id = {$this->parent->rec_id}";
		$rs=Yii::app()->db->createCommand($sql)->bindValues([':cid'=>$this->id])->queryAll();
		foreach ($rs as $r) {
			return $r['value'];
		}
	}

	public function getAllMyValue()
	{
		$ref = $this->line->ref;
		$sql="SELECT sum(my_value) as my_value FROM `{$this->getDatabaseName()}`.`dispute_line` d join `{$this->getDatabaseName()}`.`si_reconcile_line` s on d.si_reconcile_line_id = s.id where ref = '{$ref}' and s.rec_id = {$this->parent->rec_id}";
		$rs=Yii::app()->db->createCommand($sql)->bindValues([':cid'=>$this->id])->queryAll();
		foreach ($rs as $r) {
			return $r['my_value'];
		}
	}

	public function getAllDisputeValue()
	{
		$ref = $this->line->ref;
		$sql="SELECT sum(dispute_amount_ex_gst) as dispute_amount_ex_gst FROM `{$this->getDatabaseName()}`.`dispute_line` d join `{$this->getDatabaseName()}`.`si_reconcile_line` s on d.si_reconcile_line_id = s.id where ref = '{$ref}' and s.rec_id = {$this->parent->rec_id}";
		$rs=Yii::app()->db->createCommand($sql)->bindValues([':cid'=>$this->id])->queryAll();
		foreach ($rs as $r) {
			return $r['dispute_amount_ex_gst'];
		}
	}

	public function getAllCreditValue()
	{
		$ref = $this->line->ref;
		$sql="SELECT sum(credit_amount_ex_gst) as credit_amount_ex_gst FROM `{$this->getDatabaseName()}`.`dispute_line` d join `{$this->getDatabaseName()}`.`si_reconcile_line` s on d.si_reconcile_line_id = s.id where ref = '{$ref}' and s.rec_id = {$this->parent->rec_id}";
		$rs=Yii::app()->db->createCommand($sql)->bindValues([':cid'=>$this->id])->queryAll();
		foreach ($rs as $r) {
			return $r['credit_amount_ex_gst'];
		}
	}

	public function getAllSurchargeInvoice()
	{
		$ref = $this->line->ref;
		$surchargeStr = [];
		$sql="SELECT json_value(s.meta,'$.surcharge_inv_id') as surcharge_inv_id FROM `{$this->getDatabaseName()}`.`dispute_line` d join `{$this->getDatabaseName()}`.`si_reconcile_line` s on d.si_reconcile_line_id = s.id where ref = '{$ref}' and s.rec_id = {$this->parent->rec_id}";
		$rs=Yii::app()->db->createCommand($sql)->bindValues([':cid'=>$this->id])->queryAll();
		foreach ($rs as $r) {
			$surchargeStr[$r['surcharge_inv_id']] = !empty($r['surcharge_inv_id'])?"<a href=\"https://os.toplogistics.com.au/top/invoice/print.app?id=".$r['surcharge_inv_id']."\" target=\"_blank\">Invoice".$r['surcharge_inv_id']."</a>":"";
;
		}
		return join("</br>",$surchargeStr);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DisputeLine the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
