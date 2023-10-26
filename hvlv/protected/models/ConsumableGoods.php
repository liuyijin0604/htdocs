<?php

/**
 * This is the model class for table "consumable_goods".
 *
 * The followings are the available columns in table 'consumable_goods':
 * @property string $id
 * @property string $category_id
 * @property string $vendor_id
 * @property string $name
 * @property double $buy_price
 * @property double $sale_price
 * @property string $barcode
 * @property string $order_cycle
 * @property string $added_time
 * @property string $notes
 */
class ConsumableGoods extends CActiveRecord
{
    // define box type item ID array
    private static $CG_BOX_IDS = [18,19,20,21,22];
    const CG_STICKTAPE_ID = 23;
    const CG_RATIO_BOX2_STICKTAPE = 20; // which every 15 boxes will get 1 stick tape
    const CG_PERMISION_RATIO = 10; // every time we can give customer more 10% items

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'consumable_goods';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('buy_price, sale_price', 'numerical'),
			array('category_id', 'length', 'max'=>11),
			array('vendor_id,order_cycle', 'length', 'max'=>10),
			array('name', 'length', 'max'=>125),
			array('barcode', 'length', 'max'=>45),
			array('added_time, notes,order_cycle', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, category_id, vendor_id, name, buy_price, sale_price, barcode, order_cycle,added_time, notes', 'safe', 'on'=>'search'),
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
            'vendor' => array(self::BELONGS_TO, 'CgVendors', 'vendor_id')
		);
	}

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'category_id' => 'Category',
			'vendor_id' => 'Vendor',
			'name' => 'Name',
			'buy_price' => 'Buy Price',
			'sale_price' => 'Sale Price',
			'barcode' => 'Barcode',
			'added_time' => 'Added Time',
            'order_cycle' => 'Order Cycle(weeks)',
			'notes' => 'Notes',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('category_id',$this->category_id,true);
		$criteria->compare('vendor_id',$this->vendor_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('buy_price',$this->buy_price);
		$criteria->compare('sale_price',$this->sale_price);
		$criteria->compare('barcode',$this->barcode,true);
        $criteria->compare('order_cycle',$this->order_cycle,true);
		$criteria->compare('added_time',$this->added_time,true);
		$criteria->compare('notes',$this->notes,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>'30',
            ),
		));
	}


    /**
     * @return vendor name related to the product
     */
    public function getVendorName(){
        if ( empty($this->vendor) ) return '';
        return $this->vendor->name;
    }

    public function beforeSave() {
        if ($this->isNewRecord)
            $this->added_time = new CDbExpression('NOW()');
        return parent::beforeSave();
    }

    /**
     * @param $id
     * @return bool
     */
    public static function isBox($id){
        return in_array($id,self::$CG_BOX_IDS);
    }

    /**
     * @param $id
     * @return bool
     */
    public static function isTape($id){
        return $id == self::CG_STICKTAPE_ID ? true : false;
    }

    /**
     * get customer consumables limitations
     * in case not free , no any limiations
     * @param $oid
     * @return array
     */
    public static function getOrgCgLimitations($oid){
        $result = array('box' => 3000,'tape' => 50);

        $org = Org::model()->findByPk($oid);
        if ( !empty($org) ) {
            if ( $org->getCgSellModel() == 0  ) {
                // in case only free
                // get all received packages from last finished order
                // initially we give each one for maximum 1000 , if not found
                $corder = CgOrder::model()->find(
                    [
                        'condition' => 'client_id = ' .$oid .' AND status = ' . CgOrder::CGORDER_STATUS_FINISHED,
                        'order' => 'id DESC'
                    ]
                );
                if ( !empty($corder) ) {
                    // get last time delivery packages successfully date time
                    $ldate = $corder->added_datetime;
                    // find all picked up boxes after added  time
                    $plist = PickupList::model()->findAll('fwd_id = :oid AND created >= :ldate',[':oid' => $oid,':ldate' => $ldate]);
                    $totalItems = 0;
                    foreach ( $plist as $p) {
                        $totalItems += $p->countLines();
                    }

                    if ( $totalItems > 0 ) {
                        $result['box'] = floor($totalItems * ( 1 + ConsumableGoods::CG_PERMISION_RATIO / 100)) ;
                        $result['tape'] = floor( $result['box'] / ConsumableGoods::CG_RATIO_BOX2_STICKTAPE);
                        if ( $result['tape'] <= 0 ) $result['tape'] = 5; // set as default
                    } else {
                        // over limitations , can't order again
                        $result['box'] = 0 ;
                        $result['tape'] = 0;
                    }
                }
            }
        }
        return $result;
    }
    /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ConsumableGoods the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
