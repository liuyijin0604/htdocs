<?php

/**
 * This is the model class for table "wms_dashboard".
 *
 * The followings are the available columns in table 'wms_dashboard':
 * @property string $id
 * @property string $date
 * @property int $in_schd_task
 * @property int $in_schd_unit
 * @property int $out_schd_task
 * @property int $out_schd_unit
 * @property int $in_compl_task
 * @property int $in_compl_unit
 * @property int $out_compl_task
 * @property int $out_compl_unit
 * @property int $out_compl_weight
 */
class WmsDashboard extends CActiveRecord
{

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_dashboard';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, in_schd_task, in_schd_unit, out_schd_task, out_schd_unit, in_compl_task, in_compl_unit, out_compl_task, out_compl_unit, out_compl_weight', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, date, in_schd_task, in_schd_unit, out_schd_task, out_schd_unit, in_compl_task, in_compl_unit, out_compl_task, out_compl_unit, out_compl_weight', 'safe', 'on' => 'search'),
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
			'date' => 'Date',
			'in_schd_task' => 'Scheduled Task',
			'in_schd_unit' => 'Scheduled Unit',
			'out_schd_task' => 'Scheduled Task',
			'out_schd_unit' => 'Scheduled Unit',
			'in_compl_task' => 'Completed Task',
			'in_compl_unit' => 'Completed Unit',
			'out_compl_task' => 'Completed Task',
			'out_compl_unit' => 'Completed Unit',
			'out_compl_weight' => 'Completed Weight', 
		);
	}

	public function beforeSave()
	{
		return true;
	}

	public function afterFind()
	{
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
	public function search($pgn = true, $ps = 30, $ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;
		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.date', $this->date);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 't.date DESC',
			),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WmsTaskMap the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

}