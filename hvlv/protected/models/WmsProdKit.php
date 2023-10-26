<?php
class WmsProdKit extends CActiveRecord
{

	public function tableName()
	{
		return 'wms_prod_kit';
	}

	public function rules()
	{
		return array(
			array('kit_id, item_id, qty', 'required'),
			array('id, kit_id, item_id, qty', 'safe', 'on' => 'search'),
		);
	}

	public function relations()
	{
		return array(
			'kit' => array(self::BELONGS_TO, 'WmsProd', 'kit_id'),
			'item' => array(self::BELONGS_TO, 'WmsProd', 'item_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'kit_id' => 'Kit',
			'item_id' => 'Item',
			'qty' => 'Qty',
		);
	}

	public function search($pgn = true, $ps = 30)
	{
		$criteria = new CDbCriteria;
		$criteria->compare('kit_id', $this->kit_id);
		$criteria->compare('item_id', $this->item_id);
		$criteria->compare('qty', $this->qty);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
					'defaultOrder' => 't.id DESC',
				),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

}