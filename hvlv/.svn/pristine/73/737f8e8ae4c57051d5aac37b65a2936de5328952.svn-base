<?php 
class ShipmentUpdateService extends Service
{
	public static function generateBwfUpdateRecord($shipment, $owf, $nwf)
	{
		$model = new ShipmentUpdate();
		$model->shipment_id = $shipment->id;
		$model->type = ShipmentUpdate::BWF_UPDATE;
		$model->created = date("Y-m-d H:i:s");
		$model->num_value = $nwf;
		$model->o_num_value = $owf;
		$model->save();
	}

	public static function getAllUpdatedBwfRecord()
	{
		return ShipmentUpdate::model()->with(['imparcel'])->findAll("t.type = :type and t.status = :status",[":status"=>ShipmentUpdate::SCHEDULED,":type"=>ShipmentUpdate::BWF_UPDATE]);
	}

}
?>