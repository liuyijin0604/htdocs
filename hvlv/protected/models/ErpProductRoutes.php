<?php

/**
 * This is the model class for table "erp_product_routes".
 *
 * The followings are the available columns in table 'erp_product_routes':
 * @property string $id
 * @property string $product_id
 * @property string $warehouse_id
 * @property integer $from_id
 * @property integer $to_id
 * @property integer $type
 * @property integer $quantity
 * @property double $price
 * @property double $cost
 * @property string $added_time
 * @property string $notes
 * @property string $meta
 */
class ErpProductRoutes extends CActiveRecord
{
    // update or revert can only be valid in sepcified time
    const FUNCS_VALID_IN_TIME = 30; // unit is mimutes

    // in order to get product inventory and product name dynamically
    public $amt;
    public $product_name;


    // define product route types
    /*
        save product route staus
        0 - product input , default value
        1 - product output , product from warehouse to driver
        2 - product arrival agent
        3 - product returned by driver
        4 - product disappear , unknow status, for example : scrap , lost
        5 - product output , internal used
        6 - product output to agent directly
    */
    public static $ROUTE_TYPS = array(
        '0' => 'IN_V_2_W',
        '1' => 'OUT_W_2_D',
        '2' => 'OUT_D_2_A',
        '3' => 'RETURN',
        '4' => 'UNKNOWN',
        '5' => 'OUT_W_2_I',
        '6' => 'OUT_W_2_A'
    );

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'erp_product_routes';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, warehouse_id, from_id, to_id,quantity', 'required'),
			array('from_id, to_id, type, quantity,status', 'numerical', 'integerOnly'=>true),
			array('price, cost', 'numerical'),
			array('product_id, warehouse_id', 'length', 'max'=>10),
			array('added_time, notes, meta', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, product_id, warehouse_id, from_id, to_id, type, status,quantity, price, cost, added_time, notes, meta', 'safe', 'on'=>'search'),
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
            'product' => array(self::BELONGS_TO, 'ErpProducts', 'product_id'),
            'fromuser' => array(self::BELONGS_TO, 'Org', 'from_id'),
            'driver' => array(self::BELONGS_TO, 'User', 'to_id','condition' => 'driver.type = :dtype','params' => array(
                ':dtype' => 100
            )),
            'fdriver' => array(self::BELONGS_TO, 'User', 'from_id','condition' => 'fdriver.type = :dtype','params' => array(
                ':dtype' => 100
            )),
            'touser' => array(self::BELONGS_TO, 'Org', 'to_id'),
            'warehouse' => array(self::BELONGS_TO, 'Org', 'warehouse_id','condition' => 'warehouse.type = :whtype','params' => array(
                ':whtype' => 25
            )),
            'agent' => array(self::BELONGS_TO, 'Org', 'to_id','condition' => 'agent.type = :dtype OR agent.type = :dtype2','params' => array(
                ':dtype' => 60,':dtype2' => 65
            )),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'product_id' => 'Product',
			'warehouse_id' => 'Warehouse',
			'from_id' => 'From',
			'to_id' => 'To',
			'type' => 'Type',
			'quantity' => 'Quantity',
			'price' => 'Price',
			'cost' => 'Cost',
			'added_time' => 'Added Time',
			'notes' => 'Notes',
			'meta' => 'Meta',
            'status' => 'Status',
            'to_driver_id' => 'Driver',
            'to_agent_id' => 'Agent'
		);
	}

    /**
     * update and revert functions can only be available in special time
     * @return bool
     */
    private function showFunctionButtons(){
        if ( empty($this->added_time) ){
            return false;
        } else {
            if  ($this->status == 1 ) {
                return false;
            } else {
                $cur_time = new DateTime('now', new DateTimeZone('Australia/Sydney'));
                $route_time = new DateTime($this->added_time, new DateTimeZone('Australia/Sydney'));
                $time_diff = $cur_time->diff($route_time,true);
                if  (  ($time_diff->h * 60 +  $time_diff->i)  > self::FUNCS_VALID_IN_TIME) {
                    return false;
                }
            }
        }
        return true;
    }


    /**
     * check to see if we should show update button
     * @return bool
     */
    public function showUpdateButton(){
        return $this->showFunctionButtons();
    }

    /**
     * check to see if we should show revert button
     * @return bool
     */
    public function showRevertButton(){
        if ( $this->showFunctionButtons() ) {
            // in case virtual and input status we can revert route
            // in case input you can modify change quantity to zero
            // 6 - warehouse to agent directly
            if ( $this->type == 4 || $this->type == 0 || $this->type == 2 || $this->type == 6 ) {
                return false;
            }  else {
                return true;
            }
        }
        return false;
    }

    /**
     * @return product name related to the product
     */
    public function getProductName(){
        if(empty($this->product_id)){
            return '';
        }else{
            return $this->product->name;
        }
    }

    public function getFromName(){

        switch ($this->type) {
            case 0 : // in case product input : from vendor to warehouse
            {
                // from name should be vendor name
                $vendor = ErpVendors::model()->find('id = :vendorId',array(':vendorId' => $this->from_id));
                if ( !empty($vendor) ) {
                    return $vendor->name;
                } else {
                    return '';
                }
            }
                break;

            case 1: // in case product output : from warehouse to driver
            {
                // from name should be warehouse name
                $warehouse = Org::model()->find('id = :id',array(':id' => $this->warehouse_id));
                if ( !empty($warehouse) ) {
                    return $warehouse->name;
                } else {
                    return '';
                }
            }
                break;

            case 2: // from driver to agent
            case 3: // in case product returned by driver : from driver to warehouse
            {
                // from driver returned
                if (empty($this->from_id) || (!isset($this->fdriver))) {
                    return '';
                } else {
                    return $this->fdriver->name;
                }
            }
            break;

            default:{
                // other cases , get from name from Org
                if (empty($this->from_id) || (!isset($this->fromuser))) {
                    return '';
                } else {
                    return $this->fromuser->name;
                }
            }
                break;
        }

    }

    /**
     * get route type name
     */
    public function getType(){
        if ( isset(self::$ROUTE_TYPS[$this->type]) ) {
            return self::$ROUTE_TYPS[$this->type];
        }
        return 'UNKNOWN';
    }

    public function getToName(){
        // in case product input : from vendor to warehouse
        switch ($this->type) {
            case 0:{
                // in case product input from vendor to warehouse ,to name means warehouse name
                $warehouse = Org::model()->find('id = :whId',array(':whId' => $this->to_id));
                if ( !empty($warehouse) ) {
                    return $warehouse->name;
                } else {
                    return '';
                }
            }
            break;

            case 3:{
                // in case product returned from driver to warehouse
                $warehouse = Org::model()->find('id = :whId',array(':whId' => $this->to_id));
                if ( !empty($warehouse) ) {
                    return $warehouse->name;
                } else {
                    return '';
                }
            }
            break;


            case 1: // in case product out from warehouse to driver
            {
                if (empty($this->to_id) || (!isset($this->driver))) {
                    return '';
                } else {
                    return $this->driver->name;
                }
            }
            break;

            case 2: // in case product arrive agent
            default:{
                if (empty($this->to_id) || (!isset($this->touser))) {
                    return '';
                } else {
                    return $this->touser->name;
                }
            }
            break;
        }

    }

    public function getWarehouseName(){
        if(empty($this->warehouse_id) || !isset($this->warehouse) ){
            return '';
        }else{
            return $this->warehouse->name;
        }
    }

    public static function productList(){
        $rs = ErpProducts::model()->findAll('id > 0');
        $a = array();
        foreach($rs as $v){
            $a[$v['id']] = $v['name'];
        }
        return $a;
    }

    public static function warehouseList(){
        $rs = Org::model()->findAll('id > 0 AND type = 25'); // type 25 means warehouse type id
        $a = array();
        foreach($rs as $v){
            $a[$v['id']] = $v['name'];
        }
        return $a;
    }

    public static function driverList(){
        $rs = User::model()->findAll('id > 0 AND type = 100'); // type 100 means driver type id
        $a = array();
        foreach($rs as $v){
            $a[$v['id']] = $v['fname'] . ' ' . $v['lname'];
        }
        return $a;
    }

    public static function agentList(){
        $rs = Org::model()->findAll('id > 0 AND type IN (60,65)'); // type 60,65 means agent type id
        $a = array();
        foreach($rs as $v){
            $a[$v['id']] = $v['name'];
        }
        return $a;
    }

    public static function userList(){
        $rs = Org::model()->findAll('id > 0');
        $a = array();
        foreach($rs as $v){
            $a[$v['id']] = $v['name'];
        }
        return $a;
    }


    public function beforeSave() {
        if ($this->isNewRecord)
            $this->added_time = new CDbExpression('NOW()');
        return parent::beforeSave();
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

        $with = array();
        if ( !empty($this->product_id) ){
            $with[] = 'product';
            $criteria->compare('product.name',$this->product_id,true);
        }

        if ( !empty($this->warehouse_id) ) {
            $with[] = 'warehouse';
            $criteria->compare('warehouse.name', $this->warehouse_id, true);
        }

		$criteria->compare('from_id',$this->from_id);
		$criteria->compare('to_id',$this->to_id);

		$criteria->compare('t.type',$this->type);
		$criteria->compare('quantity',$this->quantity);
        $criteria->compare('price',$this->price);
		$criteria->compare('cost',$this->cost);
		//$criteria->compare('added_time',$this->added_time,true);
		$criteria->compare('t.notes',$this->notes,true);
		//$criteria->compare('meta',$this->meta,true);

        if ( !empty($with) ) {
            $criteria->with = array_unique($with);
            $criteria->together = true;
        }

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder'=>'t.id DESC',
            ),
            'pagination'=>array(
                'pageSize'=>'30',
            ),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ErpProductRoutes the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
