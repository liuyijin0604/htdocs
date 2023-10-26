<?php
class ImParcelArchive extends ImParcel
{

	public function tableName()
	{
		return 'shipment_archive';
	}

	protected function instantiate($attr)
	{
		$cls = get_class($this);
		return new $cls(null);
	
	}
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
			'consol' => [self::BELONGS_TO, 'Consol', 'consol_id'],
			'manif' => [self::BELONGS_TO, 'Manifest', 'man_id'],
			'rec' => [self::BELONGS_TO, 'Manifest', 'rec_id'],
			'cnor' => [self::BELONGS_TO, 'AddrArchive', 'cnor_id'],
			'cnee' => [self::BELONGS_TO, 'AddrArchive', 'cnee_id'],
			'receiver'=>[self::BELONGS_TO, 'AddrArchive', 'receiver_id'],
			'notifier'=>[self::BELONGS_TO,'AddrArchive', 'notify_id'],
			'zrate' => [self::BELONGS_TO, 'ZoneRate', 'zr_id'],
			'trans' => [self::HAS_MANY, 'Tranship', 'pid'],
			'tracks' => [self::HAS_MANY, 'TrackingArchive', 'pid', 'order'=>'tracks.dt ASC'],
			'owner' => [self::BELONGS_TO, 'Org', 'owner_id'],
			'agent' => [self::BELONGS_TO, 'Org', 'agent_id'],
			'odepot' => [self::BELONGS_TO, 'Org', 'odpt_id'],
			'ddepot' => [self::BELONGS_TO, 'Org', 'ddpt_id'],
			'logs' => [self::HAS_MANY, 'Log', 'lid', 'on' => "logs.model = '".get_called_class()."'", 'order' => 'logs.time ASC'],
			'srec' => [self::BELONGS_TO, 'ShipmentReceipt', 'srec_id'],
			'process' => [self::HAS_ONE,'ShipmentProcess','pid'],
			'err_record' => [self::HAS_ONE,'ShipmentErrRecord','fid'],
			'cargo_delivery_info' => [self::HAS_ONE, 'CargoDeliveryInfo', 'shipment_id'],
			'amazon_info' => [self::HAS_ONE, 'AmazonInfo', 'fid'],
			'rack_record' => [self::HAS_MANY, 'WmsRackShipment', 'shipment_id'],
			'csls' => [self::HAS_MANY, 'ChangeShipmentLabel', 'pid'],
			'cogs' => [self::HAS_ONE, 'CogsLine', 'fid', 'on' => "cogs.model='" . get_called_class() . "'"],
			'mms' => [self::HAS_MANY, 'ManiMap', 'fid', 'on' => "mms.model='ImParcel'"],
			'cargo_process' => [self::HAS_ONE, 'CargoProcess', 'shipment_id', 'order' => 'cargo_process.id desc'],
			'metaData' => [self::HAS_MANY, 'Meta', 'fid', 'on' => "metaData.model = '".get_called_class()."'"],
			'amazon_info' => [self::HAS_ONE, 'AmazonInfo', 'fid', 'on' => "amazon_info.model='" . get_called_class() . "'"],
			'cargo_delivery_info' => [self::HAS_ONE, 'CargoDeliveryInfo', 'shipment_id','on' => "cargo_delivery_info.model='" . get_called_class() . "'"],
			'sea_process' => [self::HAS_ONE, 'SeaProcess', 'pid'],
			'gatepass_shipment' => [self::HAS_MANY, 'GatepassShipment', 'fid'],
			'deconciliation' => [self::HAS_ONE, 'Tracking', 'pid', 'on' => "deconciliation.type = 61"],
			'bag_tag' => [self::HAS_ONE, 'ImportBagTag', 'shipment_id'],
			'blue_label' => [self::HAS_MANY, 'ImportsShipmentRelations', 'pid'],
			'rts_record' => [self::HAS_MANY, 'ShipmentRtsRecord', 'shipment_id'],
			'shipmentCharge' => [self::HAS_ONE, 'ImportsShipmentCharge', 'shipment_id'],
			'fakParent' => [self::HAS_ONE, 'ImportsShipmentFakRelations', 'cid']
		];
	}

}
