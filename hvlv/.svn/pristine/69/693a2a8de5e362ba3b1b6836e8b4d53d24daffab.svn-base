<?php

/**
 * This is the model class for table "user_warehouse_group_rule".
 *
 * The followings are the available columns in table 'user_warehouse_group_rule':
 * @property integer $id
 * @property string $from
 * @property string $to
 * @property string $agent_ids
 * @property string $other_agent
 * @property string $group
 * @property integer $maximum
 * @property integer $priority
 */
class UserWarehouseGroupRule extends CActiveRecord
{
	CONST INACTIVE =  1;
	CONST ACTIVE =  0;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_warehouse_group_rule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('from, to, agent_ids, other_agent, group, maximum, priority', 'required'),
			array('maximum, priority', 'numerical', 'integerOnly'=>true),
			array('from, to', 'length', 'max'=>10),
			array('group', 'length', 'max'=>4),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, from, to, agent_ids, other_agent, group, maximum, priority', 'safe', 'on'=>'search'),
		);
	}

	public function getAgentNames($isHtml = false)
	{
		$name = [];
		$ids = explode(",", $this->agent_ids);
		foreach ($ids as $key => $value) {
			$org = Org::model()->findByPk($value);
			$name[] = $org->name;
		}
		if($isHtml)
		{
			return  join("</br>",$name);
		}
		return join(",",$name);
	}


	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'branch' => array(self::BELONGS_TO, 'Org', 'dpt_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'from' => 'From',
			'to' => 'To',
			'agent_ids' => 'Agent Ids',
			'other_agent' => 'Other Agent',
			'group' => 'Group',
			'maximum' => 'maximum(t)',
			'dpt_id'=>'Depot'
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
		$criteria->compare('from',$this->from,true);
		$criteria->compare('to',$this->to,true);
		$criteria->compare('agent_ids',$this->agent_ids,true);
		$criteria->compare('other_agent',$this->other_agent,true);
		$criteria->compare('group',$this->group,true);
		$criteria->compare('maximum',$this->maximum);
		$criteria->compare('priority',$this->priority);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserWarehouseGroupRule the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
