<?php

/**
 * This is the model class for table "consumable_goods_inventory".
 *
 * The followings are the available columns in table 'consumable_goods_inventory':
 * @property integer $id
 * @property integer $cd_id
 * @property integer $op_id
 * @property integer $qty
 * @property string $added_time
 * @property string $note
 */
class CgInventory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'consumable_goods_inventory';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cd_id, qty', 'required'),
			array('cd_id, op_id, qty', 'numerical', 'integerOnly'=>true),
			array('added_time', 'safe'),
            array('note', 'length', 'max'=>250),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, cd_id, op_id, qty, added_time,note', 'safe', 'on'=>'search'),
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
            'user' => array(self::BELONGS_TO,'User' , 'op_id'),
            'cgoods' => array(self::BELONGS_TO,'ConsumableGoods' , 'cd_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'cd_id' => 'Cd',
			'op_id' => 'Op',
			'qty' => 'Qty',
			'added_time' => 'Added Time',
            'note' => 'Note',
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
		$criteria->compare('cd_id',$this->cd_id);
		$criteria->compare('op_id',$this->op_id);
		$criteria->compare('qty',$this->qty);
		$criteria->compare('added_time',$this->added_time,true);
        $criteria->compare('note',$this->note,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder'=> 't.id DESC',
            ),
            'pagination'=>array(
                'pageSize'=>'30',
            ),
		));
	}

    /**
     * insert a new consumable goods
     * if not existing insert a new one with zero
     * @param $cgoods
     */
    public static function addInventory($cgoods){
        $existing = CgInventory::model()->find('cd_id = :cid',[':cid' => $cgoods->id]);
        if ( empty($existing) ) {
            $cgi = new CgInventory();
            $cgi->cd_id = $cgoods->id;
            $cgi->op_id = !empty(Yii::app()->user) ?  Yii::app()->user->id : 0;
            $cgi->qty = 0;
            $cgi->added_time = date('Y-m-d h:i:s');
            $cgi->save();
        }
    }

    /**
     * insert a new consumable goods inventory row
     * @param $id
     * @param $qty
     */
    public static function addInventoryQty($id,$qty,$note = ''){
        $cgi = new CgInventory();
        $cgi->cd_id = $id;
        $cgi->op_id = !empty(Yii::app()->user) ?  Yii::app()->user->id : 0;
        $cgi->qty = $qty;
        if ( !empty($note) ) $cgi->note = $note;
        $cgi->added_time = date('Y-m-d h:i:s');
        $cgi->save();
    }

    /**
     * get all consumable goods inventory data
     * @return array
     */
    public static function getInventoryData(){
        $data = array();
        $sql = "SELECT cd_id,cg.name,sum(qty) as amt FROM consumable_goods_inventory cgi left join consumable_goods cg on cgi.cd_id = cg.id";
        $sql .= " group by cd_id";
        $sumRows = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ( $sumRows as $r ) {
            $line = new stdClass();
            $line->id = $r['cd_id'];
            $line->total = $r['amt'];
            $line->name = $r['name'];
            $data[] = $line;
        }
        return $data;
    }


    /**
     * statistic inventory data by month
     * @return array
     */
    public static function getInventoryDataByMonth($minusOnly = false){
        $data = array();
        $prev3Month = date('Y-m-d',strtotime('-3 month'));

        $sql = "SELECT MONTH(cgi.added_time) AS mon ,cd_id,cg.name,SUM(qty) AS amt FROM consumable_goods_inventory cgi LEFT JOIN consumable_goods cg ON cgi.cd_id = cg.id";
        $sql .= " WHERE cgi.added_time >= '" . $prev3Month . "'";
        if ( $minusOnly ) {
            $sql .= ' AND qty < 0'; // only care about consume goods
        }
        $sql .= " GROUP BY cd_id,mon ORDER BY mon DESC";

        $sumRows = Yii::app()->db->createCommand($sql)->queryAll();
        $index = 1;
        foreach ( $sumRows as $r ) {
            $line = new stdClass();
            $line->id = $index++;
            $line->month = $r['mon'];
            $line->cgid = $r['cd_id'];
            $line->total = $r['amt'];
            $line->name = $r['name'];
            $data[] = $line;
        }
        return $data;
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CgInventory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
