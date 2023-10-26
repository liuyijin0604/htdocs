<?php
class RtsList extends Manifest{

	public static $my_type = 70;

	public function rules(){
		return parent::rules();
	}
	
	public function beforeSave(){
		return parent::beforeSave();
	}

	public function attributeLabels(){
		return parent::attributeLabels();
	}

	public function getNewRef()
	{
		$shipment = ImParcel::model()->find('ref = :ref', [':ref' => trim($this->ref)]);
		if (!empty($shipment)) {
			return $shipment->getRtsNewShipmentRef();
		} else {
			return '';
		}
	}

	public function getNewRefStatus()
	{
		$shipment = ImParcel::model()->find('ref = :ref', [':ref' => trim($this->ref)]);
		if (!empty($shipment)) {
			$new = ImParcel::model()->findByPk($shipment->getRTSTranshipNoId());
			if(!empty($new))
			{
				return $new->getStatus();
			}
		} else {
			return '';
		}
	}

}
