<?php

/**
 * This is the model class for table "bwtrunk".
 *
 * The followings are the available columns in table 'bwtrunk':
 * @property integer $id
 * @property string $MAWB_number
 * @property integer $pieces_pick_up
 * @property string $CTO_start
 * @property string $CTO_finish
 * @property string $Client_start
 * @property string $Client_finish
 */
class Bwtrunk extends CActiveRecord
{
	public $dpt_id;
	public $consol;
	public $arrivingToday;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'bwtrunk';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pieces_pick_up', 'numerical', 'integerOnly'=>true),
			array('MAWB_number', 'length', 'max'=>255),
			array('CTO_start, CTO_finish, Client_start, Client_finish', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, MAWB_number, pieces_pick_up, CTO_start, CTO_finish, Client_start, Client_finish', 'safe', 'on'=>'search'),
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
			'MAWB_number' => 'Mawb Number',
			'pieces_pick_up' => 'Pieces Pick Up',
			'CTO_start' => 'Cto Start',
			'CTO_finish' => 'Cto Finish',
			'Client_start' => 'Client Start',
			'Client_finish' => 'Client Finish',
		);
	}

	public function getConsolAirport()
	{
		if(empty($this->consol))
		{
			$this->consol = ImcoConsol::model()->find("awb=:awb and status!=100",[":awb"=>$this->MAWB_number]);
		}
		if(empty($this->consol))
		{
			return "";
		}
		return explode(" ",explode(":",$this->consol->getDeportOriginAgent())[0])[0];
	}

	public function getConsolAirType()
	{
		if(!empty($this->consol))
		{
			if(!empty($this->consol->mdata['air_type']))
			{
				return ImcoConsol::$air_types[$this->consol->mdata['air_type']];
			}
		}
		return "";
	}

	public function getConsolPcs()
	{
		if(!empty($this->consol)&&empty($this->pieces_pick_up))
		{
			return @$this->consol->mdata['b&l_pcs'];
		}

		return $this->pieces_pick_up;
	}

	public function getCTOStartStr($type=false)
	{
		if(empty($this->CTO_start)&&empty($this->CTO_finish)&&empty($this->Client_start)&&empty($this->Client_finish))
		{
			return "ON THE WAY";
		}
		if(empty($this->CTO_start))
		{
			return "";
		}
		if($type)
		{
			return date("YmdHi",strtotime($this->CTO_start));
		}else{
			return $this->CTO_start;
		}
		return $this->CTO_start;
	}

	public function getCTOFinishStr($type=false)
	{
		if(empty($this->CTO_start)&&empty($this->CTO_finish)&&empty($this->Client_start)&&empty($this->Client_finish))
		{
			return "ON THE WAY";
		}
		if(empty($this->CTO_finish))
		{
			return "";
		}
		if($type)
		{
			return date("YmdHi",strtotime($this->CTO_finish));
		}else{
			return $this->CTO_finish;
		}
		return $this->CTO_finish;
	}

	public function getConsolWeight()
	{
		if(!empty($this->consol))
		{
			return round($this->consol->totWeight());
		}
		return "";
	}

	public function getConsol()
	{
		if(empty($this->consol))
		{
			$this->consol = ImcoConsol::model()->find("awb=:awb and status!=100",[":awb"=>$this->MAWB_number]);
		}
		return $this->consol;
	}

	public function getConsolWHNote()
	{
		$this->getConsol();
		if(!empty($this->consol))
		{
			return @$this->consol->mdata['wh_note'];
		}
		return "";
	}

	public function getConsolCustomer()
	{
		if(empty($this->consol))
		{
			$this->consol = ImcoConsol::model()->find("awb=:awb and status!=100",[":awb"=>$this->MAWB_number]);
		}
		if(empty($this->consol))
		{
			return "";
		}
		$p = ImParcel::model()->find("consol_id =:consol_id and status !=100",[":consol_id"=>$this->consol->id]);
		if(!empty($p))
		{
			return AppHelper::mb_str_split($p->agent->name,5);
		}else
		{
			return "";
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
	public function search($pgn=30,$ps=false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('MAWB_number',$this->MAWB_number,true);
		$criteria->compare('pieces_pick_up',$this->pieces_pick_up);
		$criteria->compare('CTO_start',$this->CTO_start,true);
		$criteria->compare('CTO_finish',$this->CTO_finish,true);
		$criteria->compare('Client_start',$this->Client_start,true);
		$criteria->compare('Client_finish',$this->Client_finish,true);
		$criteria->compare('assigned',$this->assigned);

		if(!empty($this->dpt_id))
		{
			$criteria->join = ' join consol c on c.awb = t.MAWB_number and c.status!=100';
			$criteria->addCondition('c.dpt_id='.$this->dpt_id);
		}
		if(!empty($this->arrivingToday))
		{
			$criteria->addCondition(' (CTO_start is null and CTO_finish is null and Client_start is null and Client_finish is null)  or (to_days(CTO_start) = to_days(NOW()) and Client_finish is null) or (to_days(Client_finish) = to_days(NOW()))');
		}
		$sort = new CSort();
		$sort->attributes = [
				'*'
			];
		$sort->defaultOrder = ' CTO_start DESC';

		if(!empty($this->arrivingToday))
		{
			$sort->defaultOrder = 'CTO_start ASC';
		}

		$pagerparams = $_GET;

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort' => $sort,
			'pagination' => $pgn ? [
				'pageSize' => $ps,
				'params' => $pagerparams,
			] : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Bwtrunk the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
