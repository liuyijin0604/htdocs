<?php

/**
 * This is the model class for table "cartage".
 *
 * The followings are the available columns in table 'cartage':
 * @property string $id
 * @property string $org_id
 * @property string $op_id
 * @property integer $status
 * @property integer $type
 * @property string $from_id
 * @property string $to_id
 * @property string $from_addr
 * @property string $from_contact
 * @property string $to_addr
 * @property string $to_contact
 * @property string $ref
 * @property string $plt
 * @property string $cbm
 * @property string $kg
 * @property string $rego
 * @property string $scd_time
 * @property string $take_time
 * @property string $comp_time
 * @property string $meta
 */
class Cartage extends CActiveRecord
{
	public $org_name, $job_owner, $wmstask_ref, $wmstask_plt, $job_awb, $job_flight, $job_etd, $note;
	/**
	 * @return string the associated database table name
	 */
	public $mdata = array();

	public function tableName()
	{
		return 'cartage';
	}
	public static $types = array(
		10 => 'C2W',
		20 => 'W2C',
		30 => 'W2T',
		40 => 'T2W',
		50 => 'C2C',
	);

	public static $states = array(
		10 => 'Scheduled',
		20 => 'Accepted',
		65 => 'Xray Completed',
		70 => 'Completed',
		80 => 'Confirmed',
		100 => 'Cancelled',
	);

	public static $proc_types = array(
		1 => 'Cartage',
		2 => 'Xray',
		3 => 'Both',
	);

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('op_id, status, type, from_addr, from_id, ref, to_id, scd_time', 'required'),
			array('status, type', 'numerical', 'integerOnly' => true),
			array('org_id, op_id, from_id, to_id, job_id', 'length', 'max' => 11),
			array('from_addr, to_addr,ref', 'length', 'max' => 200),
			array('from_contact, to_contact', 'length', 'max' => 100),
			array('plt, cbm, kg', 'length', 'max' => 50),
			array('rego', 'length', 'max' => 20),
			array('mdata', 'safe'),
//            // The following rule is used by search().
			//            // @todo Please remove those attributes that should not be searched.
			array('id, org_id, org_name, op_id, status, type, from_id, to_id, from_addr, from_contact, to_addr, take_time, to_contact, ref, plt, cbm, kg, rego, scd_time, comp_time, meta, job_id, job_owner, wmstask_ref, wmstask_plt, job_awb, job_flight, job_etd, note, mdata', 'safe', 'on' => 'search'),
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
			'from_org' => array(self::HAS_ONE, 'Org', ['id' => 'from_id']),
			'to_org' => array(self::HAS_ONE, 'Org', ['id' => 'to_id']),
			'org' => array(self::HAS_ONE, 'Org', ['id' => 'org_id']),
			'job' => array(self::HAS_ONE, 'EdiJob', ['id' => 'job_id']),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'org_id' => 'Org',
			'op_id' => 'Op',
			'status' => 'Status',
			'type' => 'Type',
			'from_id' => 'From',
			'to_id' => 'To',
			'from_addr' => 'From Addr',
			'from_contact' => 'From Contact',
			'to_addr' => 'To Addr',
			'to_contact' => 'To Contact',
			'plt' => 'Plt',
			'cbm' => 'Cbm',
			'ref' => 'Reference',
			'kg' => 'Kg',
			'rego' => 'Rego',
			'scd_time' => 'Scd Time',
			'take_time' => 'Take Time',
			'comp_time' => 'Comp Time',
			'meta' => 'Meta',
			'mdata[pmc]' => 'PMC',
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
	public static function gettypes($type)
	{
		foreach (self::$types as $key => $value) {
			if ($value == $type) {
				return $key;
			}

		}
		return null;
	}

	public function getWmsTaskRef()
	{
		$result = '';
		if (!empty($this->job->wmstasks)) {
			foreach ($this->job->wmstasks as $k => $wmstask) {
				$result .= $wmstask->no;
				if ($k != sizeof($this->job->wmstasks) - 1) {
					$result .= '<br />';
				}
			}
		}
		return $result;
	}

	public function getWmsTaskRefView()
	{
		$result = '';
		if (!empty($this->job->wmstasks)) {
			foreach ($this->job->wmstasks as $k => $wmstask) {
				$result .= '<a href="' . Yii::app()->createUrl('wmsTask/update', ['id' => $wmstask->id]) . '" title="' . $wmstask->getNo() . '" class="tab_link">' . '<span style="color:' . ($k % 2 == 0 ? 'blue' : 'green') . '">' . $wmstask->no . '</span>' . '</a>';
				if ($k != sizeof($this->job->wmstasks) - 1) {
					$result .= '<br />';
				}
			}
		}
		return $result;
	}

	public function getWmsTaskPlt()
	{
		$count = 0;
		if (!empty($this->job)) {
			foreach ($this->job->wmstasks as $wmstask) {
				$count += count($wmstask->actionTask->items);
			}
		}
		return $count;
	}

	public function getJobAwb()
	{
		if (!empty($this->job)) {
			return '<a href="' . Yii::app()->createUrl('ediJob/update', ['id' => $this->job->id]) . '" title="' . $this->job->no . '" class="tab_link">' . $this->job->awb . '</a>'.(empty($this->job->mdata['secDecOpt'])? '' : ' <a href="'.Yii::app()->createUrl('ediJob/securityDec', ['id' => $this->job->id]).'" title="Security Dec." class="jqm_link"><div style="background-position: -128px -576px" class="icon"></div></a>');
		} else {
			return @$this->mdata['awb'];
		}
	}

	public function getJobFlight()
	{
		if (!empty($this->job)) {
			$awbModel = EdiAwbConsol::model()->find('awb = :awb', [':awb' => $this->job->awb]);
			if (!empty($awbModel)) {
				return '<a href="' . Yii::app()->createUrl('edi/update', ['id' => $awbModel->id]) . '" title="' . $awbModel->flight . '" class="tab_link">' . $awbModel->flight . '</a>';
			} else {
				return '';
			}
		} else {
			return @$this->mdata['flight'];
		}
	}

	public function getJobETD()
	{
		if (!empty($this->job)) {
			$awbModel = EdiAwbConsol::model()->find('awb = :awb', [':awb' => $this->job->awb]);
			return $awbModel->etd;
		} else {
			return '';
		}
	}

	public function getOpNote()
	{
		return @$this->mdata['notes']['op'];
	}

	public function getPrint()
	{
		$v = '<a href="' . Yii::app()->createUrl('cartage/print', ['id' => $this->id]) . '" class="tab_link" title="Print ' . $this->id . '"><div style="background-position: -128px -576px" class="icon"></div></a>';
		if (!empty($this->mdata['printed'])) {
			$v .= '<span class="warn">Printed</span><br />';
		}

		$files = FileRepo::model()->findAll('type = 92 AND status = 20 AND fid = :fid', [':fid' => $this->id]);
		if (!empty($files)) {
			$v .= '<span style="background: #66ff66; font-weight: bold;">Uploaded</span>';
		}

		return $v;
	}

	public function getPMC()
	{
		if (!empty($this->mdata['pmc']) && $this->mdata['pmc'] > 0) {
			return "<span class=\"icon icon-check\"></span>";
		} else {
			return "<div style=\"background-position: -224px -256px\" class=\"icon\"></div>";
		}
	}

	public function ifCartage()
	{
		if (!empty($this->mdata['proc_type']) && in_array($this->mdata['proc_type'], [1,3])) {
			return true;
		} else {
			return false;
		}
	}

	public function showCartage()
	{
		if (!empty($this->mdata['proc_type']) && in_array($this->mdata['proc_type'], [1,3])) {
			if ($this->status >= 70 && $this->status != 100) {
				return "<div class=\"icon\" style=\"background-position: 0 -672px\">";
			} else {
				return "<span class=\"icon icon-check\"></span>";
			}
		} else {
			return "<div style=\"background-position: -224px -256px\" class=\"icon\"></div>";
		}
	}

	public function ifXray()
	{
		if (!empty($this->mdata['proc_type']) && in_array($this->mdata['proc_type'], [2,3])) {
			return true;
		} else {
			return false;
		}
	}

	public function showXray()
	{
		if (!empty($this->mdata['proc_type']) && in_array($this->mdata['proc_type'], [2,3])) {
			if ($this->status >= 65 && $this->status != 100) {
				return "<div class=\"icon\" style=\"background-position: 0 -672px\">";
			} else {
				return "<span class=\"icon icon-check\"></span>";
			}
		} else {
			return "<div style=\"background-position: -224px -256px\" class=\"icon\"></div>";
		}
	}

	public function beforeSave()
	{
		if (!empty($this->mdata)) {
			$this->meta = json_encode($this->mdata);
		}

		if (empty($this->comp_time) && $this->status == 70) {
			$this->comp_time = date('Y-m-d H:i:s');
		}

		return true;
	}

	public function afterSave()
	{
		Log::add($this, $this->isNewRecord ? 3 : 4, array('status' => $this->getStatus()));
	}

	public function afterFind()
	{
		if (!empty($this->meta)) {
			$this->mdata = json_decode($this->meta, true);
		}

		return true;
	}

	public function search($ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id, true);
		// if (Yii::app()->user->grp > 40) {
		// 	$criteria->addCondition('t.status=10');
		// }

		$criteria->with = array('org');
		$criteria->compare('org.name', $this->org_name, true);
		$criteria->compare('t.org_id', $this->org_id, true);
		$criteria->compare('t.op_id', $this->op_id, true);
		$criteria->compare('t.ref', $this->ref, true);
		$criteria->compare('t.status', $this->status);
		$criteria->compare('t.type', $this->type);
		$criteria->compare('t.from_id', $this->from_id, true);
		// $criteria->compare('t.to_id', $this->to_id, true);
		$criteria->compare('t.from_addr', $this->from_addr, true);
		$criteria->compare('t.from_contact', $this->from_contact, true);
		$criteria->compare('t.to_addr', $this->to_addr, true);
		$criteria->compare('t.to_contact', $this->to_contact, true);
		$criteria->compare('t.plt', $this->plt, true);
		$criteria->compare('t.cbm', $this->cbm, true);
		$criteria->compare('t.kg', $this->kg, true);
		$criteria->compare('t.rego', $this->rego, true);
		$criteria->compare('t.scd_time', $this->scd_time, true);
		$criteria->compare('t.take_time', $this->take_time, true);
		$criteria->compare('t.comp_time', $this->comp_time, true);
		$criteria->compare('meta', $this->meta, true);

		$with = [];
		if (!empty($this->to_id)) {
			$with[] = 'to_org';
			$criteria->addCondition('to_org.name like "%' . $this->to_id . '%"');
		}

		if (!empty($this->job_owner)) {
			$with[] = 'job.owner';
			$criteria->addCondition('owner.name like "%' . $this->job_owner . '%"');
		}

		if (!empty($this->job_awb)) {
			$with[] = 'job';
			$criteria->addCondition('job.awb like "%' . $this->job_awb . '%" OR t.meta like "%' . $this->job_awb . '%"');
		}

		if (!empty($this->job_flight)) {
			$with[] = 'job.awbconsol';
			$criteria->addCondition('awbconsol.flight like "%' . $this->job_flight . '%" OR t.meta like "' . $this->job_flight . '"');
		}

		if (!empty($this->job_etd)) {
			$with[] = 'job.awbconsol';
			$criteria->addCondition('awbconsol.etd like "%' . $this->job_etd . '%"');
		}

		if (!empty($this->note)) {
			$criteria->compare('JSON_VALUE(t.meta, "$.notes.op")', $this->note, true);
		}

		if (!empty($with)) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if ($ec) {
			$criteria->mergeWith($ec);
		}

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'attributes' => array(
					'org_name' => array(
						'asc' => 'org.name',
						'desc' => 'org.name DESC',
					),
					'*',
				),
			),
			'pagination' => array(
				'pageSize' => 30,
			),
		));
	}

	public function search1()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id, true);
		$criteria->compare('org_id', $this->org_id, true);
		$criteria->compare('t.op_id', $this->op_id, true);
		$criteria->addCondition('t.status=20');
		$criteria->with = array('org');
		$criteria->compare('org.name', $this->org_name, true);
		$criteria->compare('t.ref', $this->ref, true);
		$criteria->compare('t.status', $this->status, true);
		$criteria->compare('t.type', $this->type);
		$criteria->compare('t.from_id', $this->from_id, true);
		$criteria->compare('t.to_id', $this->to_id, true);
		$criteria->compare('t.from_addr', $this->from_addr, true);
		$criteria->compare('t.from_contact', $this->from_contact, true);
		$criteria->compare('t.to_addr', $this->to_addr, true);
		$criteria->compare('t.to_contact', $this->to_contact, true);
		$criteria->compare('t.plt', $this->plt, true);
		$criteria->compare('t.cbm', $this->cbm, true);
		$criteria->compare('t.kg', $this->kg, true);
		$criteria->compare('t.rego', $this->rego, true);
		$criteria->compare('t.scd_time', $this->scd_time, true);
		$criteria->compare('t.take_time', $this->take_time, true);
		$criteria->compare('t.comp_time', $this->comp_time, true);
		$criteria->compare('t.meta', $this->meta, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'attributes' => array(
					'org_name' => array(
						'asc' => 'org.name',
						'desc' => 'org.name DESC',
					),
					'*',
				),
			), 'pagination' => array(
				'pageSize' => 30,
			),
		));
	}

	public function search2()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id, true);
		$criteria->compare('org_id', $this->org_id, true);
		$criteria->compare('t.op_id', $this->op_id, true);
		$criteria->addCondition('t.status=70 OR  t.status=80');
		$criteria->with = array('org');
		$criteria->compare('org.name', $this->org_name, true);
		$criteria->compare('t.ref', $this->ref, true);
		$criteria->compare('t.status', $this->status, true);
		$criteria->compare('t.type', $this->type);
		$criteria->compare('t.from_id', $this->from_id, true);
		$criteria->compare('t.to_id', $this->to_id, true);
		$criteria->compare('t.from_addr', $this->from_addr, true);
		$criteria->compare('t.from_contact', $this->from_contact, true);
		$criteria->compare('t.to_addr', $this->to_addr, true);
		$criteria->compare('t.to_contact', $this->to_contact, true);
		$criteria->compare('t.plt', $this->plt, true);
		$criteria->compare('t.cbm', $this->cbm, true);
		$criteria->compare('t.kg', $this->kg, true);
		$criteria->compare('t.rego', $this->rego, true);
		$criteria->compare('t.scd_time', $this->scd_time, true);
		$criteria->compare('t.take_time', $this->take_time, true);
		$criteria->compare('t.comp_time', $this->comp_time, true);
		$criteria->compare('t.meta', $this->meta, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'attributes' => array(
					'org_name' => array(
						'asc' => 'org.name',
						'desc' => 'org.name DESC',
					),
					'*',
				),
			), 'pagination' => array(
				'pageSize' => 30,
			),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Cartage the static model class
	 */
	public function getStatus()
	{

		return self::$states[$this->status];
	}
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
