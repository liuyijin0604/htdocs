<?php 
class ShipmentChangedService extends Service
{
	public static function getBlueLabel($p,$index,$type = false)
	{
		if(!preg_match('/PICKUP/i', $p->ref))
		{
			return "";
		}
		foreach ($p->blue_label as $key => $b) {
			if($b->label_lo<=$index&&$b->label_hi>=$index)
			{
				if($type)
				{
					$data = $b->sub_shipment->getCourierInfo();
					$name =$data[4];
					return $b->blue_label.' '.$name;
				}else
				{
					return $b->blue_label;
				}
			}
		}
		return "";
	}

	public static function getBlueLabelObj($p,$index)
	{
		$re = ImportsShipmentRelations::model()->find('pid = :pid and label_lo<=:index and label_hi>=:index',[":pid"=>$p->id,":index"=>$index]);
		return $re->sub_shipment;
	}

	public static function getBlueLabelWithIndex($p,$index)
	{
		$re = ImportsShipmentRelations::model()->find('pid = :pid and label_lo<=:index and label_hi>=:index',[":pid"=>$p->id,":index"=>$index]);
		return $re;
	}

	public static function getBlueLabelObjIndex($p,$index)
	{
		$re = ImportsShipmentRelations::model()->find('pid = :pid and label_lo<=:index and label_hi>=:index',[":pid"=>$p->id,":index"=>$index]);
		return ($index-$re->label_lo)+1;
	}

	public static function getParentIndex($p,$index)
	{
		$re = ImportsShipmentRelations::model()->find('cid = :cid ',[":cid"=>$p->id]);
		$parentIndex = $re->label_lo+($index-1);
		return [$re->original,$parentIndex];
	}
}
?>