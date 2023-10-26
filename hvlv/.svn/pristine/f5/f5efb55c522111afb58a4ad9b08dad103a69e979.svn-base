<?php

/**
 * This is the model class for table "erp_products".
 *
 * The followings are the available columns in table 'erp_products':
 * @property string $id
 * @property string $category_id
 * @property string $vendor_id
 * @property string $name
 * @property double $buy_price
 * @property double $sale_price
 * @property string $barcode
 * @property string $added_time
 * @property string $notes
 */
class ErpProducts extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'erp_products';
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
			array('vendor_id', 'length', 'max'=>10),
			array('name', 'length', 'max'=>125),
			array('barcode', 'length', 'max'=>45),
			array('added_time, notes', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, category_id, vendor_id, name, buy_price, sale_price, barcode, added_time, notes', 'safe', 'on'=>'search'),
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
            'vendor' => array(self::BELONGS_TO, 'ErpVendors', 'vendor_id')
		);
	}

    /**
     * @return vendor name related to the product
     */
    public function getVendorName(){
        if(empty($this->vendor_id)){
            return '';
        }else{
            return $this->vendor->name;
        }
    }

    public function beforeSave() {
        if ($this->isNewRecord)
            $this->added_time = new CDbExpression('NOW()');
        return parent::beforeSave();
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
		//$criteria->compare('category_id',$this->category_id,true);
		//$criteria->compare('vendor_id',$this->vendor_id,true);
		$criteria->compare('name',$this->name,true);
		//$criteria->compare('buy_price',$this->buy_price);
		//$criteria->compare('sale_price',$this->sale_price);
		//$criteria->compare('barcode',$this->barcode,true);
		//$criteria->compare('added_time',$this->added_time,true);
		//$criteria->compare('notes',$this->notes,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>'30',
            ),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ErpProducts the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
