<?php 
class SzportalUbiService extends Service
{
	public function generateBag($clientId, $sortCode)
	{
		$bagNo = $clientId.$sortCode.date('Ymd');
		$bags = Bag::model()->count("no like '{$bagNo}%'");
		$bags = $bags+=1;
		$digitals = str_pad($bags,4,"0",STR_PAD_LEFT);
		$bagNo = $bagNo.$digitals;
		$bags = Bag::model()->count("no = '{$bagNo}'");
		if($bags>0)
		{
			$this->generateBagNumber($clientId, $sortCode);
		}else
		{
			$bag = new Bag();
			$bag->no = $bagNo;
			$bag->consol_id = 1;
			$bag->note = '';
			$bag->type = Bag::UBIBAG;
			$bag->weight = 0;
			$bag->total_cbm = 0;
			$bag->note = '';
			$bag->pkg = 0;
			$bag->create = date('Y-m-d H:i:s');
			$bag->operator = User::currentUserID();
			$bag->mdata['sort_code'] = $sortCode;
			$bag->save();
			if(!empty($bag->getErrors()))
			{
				print_r($bag);
				return false;
			}else
			{
				return $bag;
			}
		}
	}

	public function createBagForConsol($consolId)
	{
		$consol = ImcoConsol::model()->findByPk($consolId);
		$sortCode =  $this->getUbiSortcode($consol);
		$bag = $this->generateBag(UbiAPI::CLIENTID, $sortCode);
		if(empty($bag))
		{
			return false;
		}else
		{
			$bag->consol_id = $consolId;
			$bag->update();
			return $bag;
		}
	}

	public function hangoverConsol($consolId)//hangoverAllShipments to bags by UbiAPI
	{
		$consol = ImcoConsol::model()->findByPk($consolId);
		$sortCode =  $this->getUbiSortcode($consol);
		$bag = $this->generateBag(UbiAPI::CLIENTID, $sortCode);
		if(empty($bag))
		{
			return false;
		}else
		{
			$bag->consol_id = $consolId;
			$bag->update();
			return $bag;
		}
	}

	public function scanShipmentToBag($bagNo,$barcode)
	{
		$bag = Bag::model()->find("no = :no",[":no"=>$bagNo]);
		$sn = 0;
		$courierId = 0;
		$p = ShipmentScan::getShipmentByBarcode($barcode,$sn,$courierId);
		if(!empty($p))
		{
			$shipment = BagShipment::model()->find('shipment_id = :shipment_id',[":shipment_id"=>$p->id]);
			$consol = $bag->consol;
			if(!empty($shipment))
			{
				return "shipment exist in bag ".$shipment->bag->no;
			}
			if(empty($p->mdata['sort_code']))
			{
				return "shipment has not sort code";
			}
			$sortCode = $p->mdata['sort_code'];
			$ubiSortCode = $this->getUbiSortcode($consol);
			if($sortCode!=$ubiSortCode)
			{
				return "the parcel sort:".$sortCode." and the consol sort:".$ubiSortCode;
			}
			$bagShipment = new BagShipment();
			$bagShipment->bag_id = $bag->id;
			$bagShipment->shipment_id = $p->id;
			$bagShipment->operator = User::currentUserID();
			$bagShipment->create = date('Y-m-d H:i:s');
			$bagShipment->save();
			$bag->updateShipmentData();
			return true;
		}
		return 'shipment not exist';
	}

	public function printBagLabel($bagNo)
	{
		$bag = Bag::model()->find("no = :no",[":no"=>$bagNo]);
		
	}

	public function getUbiSortcode($consol)
	{
		$sortcode = ImcoConsol::$podToFacility[$consol->pod];
		return $sortcode;
	}
}
?>