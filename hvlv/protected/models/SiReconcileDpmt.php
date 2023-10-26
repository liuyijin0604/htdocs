<?php

/**
 * This is the model class for table "si_reconcile_dpmt".
 *
 * The followings are the available columns in table 'si_reconcile_dpmt':
 * @property string $id
 * @property integer $si_reconcile_id
 * @property integer $dpmt
 * @property integer $flag
 * @property string $meta
 * @property string $total
 * @property string $total_confirmed
 * @property string $total_gst
 * @property string $total_gst_confirmed
 * @property string $total_ex_gst_confirmed
 * @property string $total_ex_gst
 * @property integer $confirm_status
 * @property string $total_ex_gst_api
 * @property string $total_ex_gst_my
 */
class SiReconcileDpmt extends OMetaModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'si_reconcile_dpmt';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('si_reconcile_id, dpmt, total, total_gst, total_ex_gst', 'required'),
			array('si_reconcile_id, dpmt, flag, confirm_status', 'numerical', 'integerOnly'=>true),
			array('total, total_ex_gst', 'length', 'max'=>20),
			array('total_confirmed, total_gst_confirmed, total_ex_gst_confirmed', 'length', 'max'=>255),
			array('total_gst, total_ex_gst_api, total_ex_gst_my', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, si_reconcile_id, dpmt, flag, meta, total, total_confirmed, total_gst, total_gst_confirmed, total_ex_gst_confirmed, total_ex_gst, confirm_status, total_ex_gst_api, total_ex_gst_my', 'safe', 'on'=>'search'),
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
			'si_reconcile_id' => 'Si Reconcile',
			'dpmt' => 'Dpmt',
			'flag' => 'Flag',
			'meta' => 'Meta',
			'total' => 'Total',
			'total_confirmed' => 'Total Confirmed',
			'total_gst' => 'Total Gst',
			'total_gst_confirmed' => 'Total Gst Confirmed',
			'total_ex_gst_confirmed' => 'Total Ex Gst Confirmed',
			'total_ex_gst' => 'Total Ex Gst',
			'confirm_status' => 'Confirm Status',
			'total_ex_gst_api' => 'Total Ex Gst Api',
			'total_ex_gst_my' => 'Total Ex Gst My',
		);
	}

	public function beforeSave()
	{
		$this->total_confirmed = empty($this->total_confirmed)?"[]":$this->total_confirmed;
		$this->total_gst_confirmed = empty($this->total_gst_confirmed)?"[]":$this->total_gst_confirmed;
		$this->total_ex_gst_confirmed = empty($this->total_ex_gst_confirmed)?"[]":$this->total_ex_gst_confirmed;

		return parent::beforeSave();
	}


	public function getCurrentConfirmedTotal($model)
	{
		$confirmedTotal = json_decode($this->total_ex_gst_confirmed,true);
		if($model->status<30)
		{
			if(!empty($confirmedTotal))
			{
				return @$confirmedTotal[$model->status];
			}
		}else
		{
			return @$confirmedTotal[30]+@$confirmedTotal[40];
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('si_reconcile_id',$this->si_reconcile_id);
		$criteria->compare('dpmt',$this->dpmt);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('total',$this->total,true);
		$criteria->compare('total_confirmed',$this->total_confirmed,true);
		$criteria->compare('total_gst',$this->total_gst,true);
		$criteria->compare('total_gst_confirmed',$this->total_gst_confirmed,true);
		$criteria->compare('total_ex_gst_confirmed',$this->total_ex_gst_confirmed,true);
		$criteria->compare('total_ex_gst',$this->total_ex_gst,true);
		$criteria->compare('confirm_status',$this->confirm_status);
		$criteria->compare('total_ex_gst_api',$this->total_ex_gst_api,true);
		$criteria->compare('total_ex_gst_my',$this->total_ex_gst_my,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SiReconcileDpmt the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
