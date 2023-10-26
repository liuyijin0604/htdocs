<?php
class ModelReportShipment {
    private $strTimeFrom = null;
	private $strTimeTo = null;
	private $strHbn = null;
	private $strRef = null;
	private $strConsolId = null;
	private $strOwnerId = null;
	private $strDeliveryType = null;
	private $strStatus = null;
	
	private $strState = null;
	private $listAirSea =[];
	
	
	private $strByUrgent = null;
	
	function __construct($strTimeFrom,$strTimeTo, $strHbn,$strRef, $strConsolId,$strOwnerId,$strDeliveryType,$strStatus ,$strState,$listAirSea,$strByUrgent)
	{
		$this->strTimeFrom = $strTimeFrom;
		$this->strTimeTo = $strTimeTo;
		$this->strHbn =  $strHbn;
		$this->strRef = $strRef;
		$this->strConsolId = $strConsolId;
		$this->strOwnerId = $strOwnerId;
		$this->strDeliveryType = $strDeliveryType;
		$this->strStatus = $strStatus;
		
		$this->strState = $strState;
		$this->listAirSea = $listAirSea;
		$this->strByUrgent = $strByUrgent;
	}
	
    
    public function funcRecords(){
		$listRecords = [];
		
		$strSqlJoinTable ='select * from(select t_consol_id , t_shipment_id , hbn,ref ,shipment_status,no , agent_id,org_name,org_code,state,service,etd,eta, scan, clearance ,courier , dt as delivery  from(select t_consol_id , t_shipment_id , hbn,ref ,shipment_status,no , agent_id,org_name,org_code,state,service,etd,eta, scan, clearance , dt as courier  from(select t_consol_id , t_shipment_id , hbn,ref ,shipment_status,no , agent_id,org_name,org_code,state,service,etd,eta, scan, dt as clearance from (select t_consol_id ,t_shipment_id , hbn,ref ,shipment_status,no , agent_id,t_table_consol_shipment.state,service,etd,eta, scan,org.name as org_name,org.code as org_code  from (select t_consol.id as t_consol_id , shipment.id as t_shipment_id , hbn,ref ,shipment.status as shipment_status,no , shipment.agent_id,state,service,etd,eta, JSON_UNQUOTE(JSON_EXTRACT(shipment.meta, "$.scan_time")) scan   from (select * from consol where eta >="'.$this->strTimeFrom.'" and eta <= "'.$this->strTimeTo.'" ) t_consol join shipment on t_consol.id = shipment.consol_id ) t_table_consol_shipment left join org on t_table_consol_shipment.agent_id = org.id ) t_table_consol_shipment_org left join tracking on t_table_consol_shipment_org.t_shipment_id = tracking.pid and tracking.type = 60)t_consol_shipment_org_clearance left join tracking on t_consol_shipment_org_clearance.t_shipment_id = tracking.pid and tracking.type = 68) t_consol_shipment_org_clearance_courier left join tracking on t_consol_shipment_org_clearance_courier.t_shipment_id = tracking.pid and tracking.type = 90)t_consol_shipment_org_clearance_courier_delivery ';
		$listWhere =[];
		if(!empty($this->strHbn)){
			$listWhere[] = 'hbn = "'. $this->strHbn.'"';
		}
		if(!empty($this->strRef)){
			$listWhere[] = 'ref = "'. $this->strRef.'"';
		}
		if(!empty($this->strConsolId)){
			$listWhere[] = 'no = "'. $this->strConsolId.'"';
		}
		if(!empty($this->strOwnerId)){
			$listWhere[] = 'org_code = "'. $this->strOwnerId.'"';
		}
		if(!empty($this->strDeliveryType)){
			$strPreg = (new ModelDeliveryType)->funcGetPregSql($this->strDeliveryType);
			$listWhere[] = 'ref REGEXP "'. $strPreg.'"';
		}
		if(!empty($this->strStatus)){
			$listWhere[] = 'shipment_status = "'. $this->strStatus.'"';
		}
		if(!empty($this->strState)){
			$listWhere[] = 'state = "'. $this->strState.'"';
		}
		if(!empty($this->listAirSea) && sizeof($this->listAirSea) !=2){
			$listWhere[] = 'service = "'. $this->listAirSea[0].'"';
		}
		
		if(sizeof($listWhere) !=0){
			$strSqlWhere = ' where ';
			foreach($listWhere as $i=>$strWhere){
				$strSqlWhere.=$strWhere;
				if($i != sizeof($listWhere)-1){
					$strSqlWhere.=' and ';
				}
			}
			$strSqlJoinTable.=$strSqlWhere;
		}
		
		
		
		$listRows =Yii::app()->db->createCommand($strSqlJoinTable)->queryAll();
		
        foreach($listRows as $objRow){
			$obj=new stdClass;
			$obj->shipment_id = $objRow['t_shipment_id'];
			$obj->shipment_no = $objRow['hbn'];
			$obj->ref = $objRow['ref'];
			$obj->status = $objRow['shipment_status'];
			$obj->consol_no = $objRow['no'];
			// $obj->customer = $objRow['agent_id'];
			$obj->org_name = $objRow['org_name'];
			$obj->state = $objRow['state'];
			$obj->service =  $objRow['service'];
			$obj->etd = $objRow['etd'];
			$obj->eta = $objRow['eta'];
			if($obj->service == Consol::AIRCONSOL){
				$obj->discharge = date("Y-m-d", strtotime('+1 day', strtotime($obj->eta)));
			}
			else{
				$obj->discharge = date("Y-m-d", strtotime('+2 day', strtotime($obj->eta)));
			}
			$obj->scan = $objRow['scan'];
			$obj->clearance = $objRow['clearance'];
			$obj->courier = $objRow['courier'];
			$obj->delivery = $objRow['delivery'];
			
			if(!empty($obj->scan)){
				$obj->days_scan = floor((strtotime($obj->scan )-strtotime($obj->discharge))/86400);
			}
			else{
				$obj->days_scan = floor((strtotime(date("Y-m-d") )-strtotime($obj->discharge))/86400);
			}
			if(!empty($obj->clearance)){
				$obj->days_clearance = floor((strtotime($obj->clearance )-strtotime($obj->discharge))/86400);
			}
			else{
				$obj->days_clearance = floor((strtotime(date("Y-m-d") )-strtotime($obj->discharge))/86400);
			}
			
			// if($obj->days_clearance > $obj->days_scan){
			// 	$numScanOrClearance = $obj->days_clearance;
			// }
			// else{
			// 	$numScanOrClearance = $obj->days_scan;
			// }
			
			// if(!empty($obj->courier)){
			// 	$obj->days_courier = floor((strtotime($obj->courier )-strtotime($obj->discharge))/86400)-$numScanOrClearance;
			// }
			// else{
			// 	$obj->days_courier = floor((strtotime(date("Y-m-d") )-strtotime($obj->discharge))/86400)-$numScanOrClearance;
			// }
			// if(!empty($obj->delivery)){
			// 	$obj->days_delivery = floor((strtotime($obj->delivery )-strtotime($obj->discharge))/86400)-$numScanOrClearance;
			// }
			// else{
			// 	$obj->days_delivery = floor((strtotime(date("Y-m-d") )-strtotime($obj->discharge))/86400)-$numScanOrClearance;
			// }
			if(!empty($obj->courier)){
				$obj->days_courier = floor((strtotime($obj->courier )-strtotime($obj->discharge))/86400);
			}
			else{
				$obj->days_courier = floor((strtotime(date("Y-m-d") )-strtotime($obj->discharge))/86400);
			}
			if(!empty($obj->delivery)){
				$obj->days_delivery = floor((strtotime($obj->delivery )-strtotime($obj->discharge))/86400);
			}
			else{
				$obj->days_delivery = floor((strtotime(date("Y-m-d") )-strtotime($obj->discharge))/86400);
			}
			
			$listRecords[] = $obj;
		}
		
		$listRecords = $this->funcFilterDays($listRecords);
		
        $listRecords = $this->funcSort($listRecords);
        
        
		
        return $listRecords;
    }
	
	private function funcSort($listRecords){
				// sort
				$dicDays2ListRecords=[];
				foreach($listRecords as $i=>$objRecord){
					$numDays = $objRecord->days_scan + $objRecord->days_courier+$objRecord->days_delivery ;
					if(!isset($dicDays2ListRecords[$numDays])){
						$dicDays2ListRecords[$numDays] = [];
					}
					$dicDays2ListRecords[$numDays][] = $objRecord;
				}
				
				if($this->strByUrgent=='asc'){
					ksort($dicDays2ListRecords);
				}
				else{
					krsort($dicDays2ListRecords);
				}
				
				$listResult=[];
				foreach($dicDays2ListRecords as $i=>$ListRecords){
					foreach($ListRecords as $j=>$objRecord){
						$listResult[] = $objRecord;
					}
				}
				return $listResult;
	}
	
	private function funcFilterDays($listRecords){
		$listResult=[];
		foreach($listRecords as $objRecord){
			if($objRecord->days_scan<0||$objRecord->days_clearance<0||$objRecord->days_courier<0||$objRecord->days_delivery<0){
				
			}
			else{
				$listResult[] = $objRecord;
			}
		}
		return $listResult;
	}
	
	
	public function funcListRecords2HTML($listRecords){
		
		$strHtml ='<table class="items"><thead><tr><th>Shipment No.</th><th>Ref</th><th>Type</th><th>Consol No.</th><th>Customer</th><th>State</th><th>Air/Sea</th><th>ETD</th><th>ETA</th><th>Discharge</th><th>Scan</th><th>Clearance</th><th>Courier</th><th>Delivery</th></tr></thead><tbody id="table_records">';
		$strHtml .=$this->funcListRecords2Tbody($listRecords);
		$strHtml .='</tbody></table>';
		return $strHtml;
	}
	
	public function funcListRecords2Tbody($listRecords){
		$strHtml = '';
		foreach ($listRecords as $i => $objRecord) {
			$strHtml .= '<tr class="' . ($i % 2 == 0 ? 'odd' : 'even') . '">';
			// $strHtml .= '<td>' . $objRecord->shipment_no . '</td>';
			$strHbn = '<a href="'.Yii::app()->createURL("imParcel/update", array("id" => $objRecord->shipment_id)).'" class="tab_link" title="'.$objRecord->shipment_no. '" >'.$objRecord->shipment_no.'</a>';
			$strHtml .= '<td>' . $strHbn . '</td>';
			$strHtml .= '<td>' . $objRecord->ref . '</td>';
			// $strHtml .= '<td>' . $this->funcLabelNumber2CourierName($objRecord->ref) . '</td>';
			$strDeliveryType = (new ModelDeliveryType)->funcLabelNumber2CourierName($objRecord->ref);
			$strHtml .= '<td>' . $strDeliveryType . '</td>';
			$strHtml .= '<td>' . ImParcel::$states[$objRecord->status]. '</td>';
			
			$strHtml .= '<td>' . $objRecord->consol_no . '</td>';
			$strHtml .= '<td>' . $objRecord->org_name . '</td>';
			$strHtml .= '<td>' . $objRecord->state . '</td>';
			$strHtml .= '<td>' . Consol::$services[ $objRecord->service ]. '</td>';
			$strHtml .= '<td>' . $objRecord->etd . '</td>';
			$strHtml .= '<td>' . $objRecord->eta . '</td>';
			// $strHtml .= '<td>' . $objRecord->discharge . '</td>';
			// $strHtml .= '<td>' . $objRecord->scan . '</td>';
			// $strHtml .= '<td>' . $objRecord->clearance . '</td>';
			// $strHtml .= '<td>' . $objRecord->courier . '</td>';
			// $strHtml .= '<td>' . $objRecord->delivery . '</td>';
			$strHtml .= '<td>';
			if (!empty($objRecord->discharge)) {
				$strHtml .=  date("m-d", strtotime($objRecord->discharge));
			}
			$strHtml.= '</td>';
			
			
			if($objRecord->service == ImcoConsol::AIRCONSOL){
				$strColorScan = $this->funcColor($objRecord->days_scan,1,2,3); 
			}
			if ($objRecord->service == ImcoConsol::SEACONSOL) {
				$strColorScan = $this->funcColor($objRecord->days_scan,4,5,6); 
			}
			
			$strColorClearance = 'black';
			if($strDeliveryType == ModelDeliveryType::type_cargo){
				$strColorCourier = $this->funcColor($objRecord->days_courier,6,7,8); 
			}
			else{
				$strColorCourier = $this->funcColor($objRecord->days_courier,4,5,6); 
			}
			$strColorDelivery = $this->funcColor($objRecord->days_courier,1,2,3); 
			
			
			$strHtml .= '<td>';
			if (!empty($objRecord->scan)) {
				$strHtml .=  date("m-d", strtotime($objRecord->scan));
			}
			$strHtml .= ' <span style="color:' . $strColorScan . '">(' . $objRecord->days_scan . 'D)</span></td>';
			$strHtml .= '</td>';
			
			$strHtml .= '<td>';
			if (!empty($objRecord->clearance)) {
				$strHtml .=  date("m-d", strtotime($objRecord->clearance));
			}
			$strHtml .= ' <span style="color:' . $strColorClearance . '">(' . $objRecord->days_clearance . 'D)</span></td>';
			$strHtml .='</td>';
			
			$strHtml .= '<td>';
			if (!empty($objRecord->courier)) {
				$strHtml .=  date("m-d", strtotime($objRecord->courier));
			}
			$strHtml .= ' <span style="color:' . $strColorCourier . '">(' . $objRecord->days_courier . 'D)</span></td>';
			$strHtml .=  '</td>';
			
			$strHtml .= '<td>';
			if (!empty($objRecord->delivery)) {
				$strHtml .=  date("m-d", strtotime($objRecord->delivery));
			}
			$strHtml .= ' <span style="color:' . $strColorDelivery . '">(' . $objRecord->days_delivery . 'D)</span></td>';
			$strHtml .= '</td>';
			
			$strHtml .= '</tr>';
		}
		
		$strHtml .='</tbody></table>';
		return $strHtml;
	}
	
	private function funcColor($numDays, $numYellow,$numOrange,$numRed){
		$strColor = 'black';
		if ($numDays > $numRed) {
			$strColor = 'red';
		} else if ($numDays > $numOrange) {
			$strColor = 'orange';
		} else if ($numDays > $numYellow) {
			$strColor = '#BBBB00';
		}
		return $strColor;
	}
    
}

class ModelDeliveryType{
	const type_aupost = 'AuPost';
	const type_cargo = 'Cargo';
	const type_tnt = 'TNT';
	const type_fastway = 'Fast Way';
	const type_toll = 'Toll';
	const type_pickup = 'Pick Up';
	
	private $listType2Preg = [
		self::type_aupost =>'/^(AMQ|UBY|333UF|33EVJ|33FJV|33FKB|33EVH|JDQ|SJU|ZK6|UC7|33A8Y|33MBQ|34AWE|TEST)\d{7}$/',
		self::type_cargo =>'/^(SKP|MKP|BKP)\d{10}$/',
		self::type_tnt =>'/^(LMA|TLS|TBC|LPC)\d{9}$/',
		self::type_fastway =>'/^(2X|QX)\d{10}$/',
		self::type_toll =>'/^(697328)\d{7}$/',
		self::type_pickup =>'/^(PICKUP)\d{7}$/',
	];
	
	private $listType2PregSql = [
		self::type_aupost=>'^(AMQ|UBY|333UF|33EVJ|33FJV|33FKB|33EVH|JDQ|SJU|ZK6|UC7|33A8Y|33MBQ|34AWE|TEST)',
		self::type_cargo=>'^(SKP|MKP|BKP)',
		self::type_tnt=>'^(LMA|TLS|TBC|LPC)',
		self::type_fastway=>'^(2X|QX)',
		self::type_toll =>'^(697328)',
		self::type_pickup =>'^(PICKUP)',
	];
	
	function __construct(){
		
	}
	
	
	public function funcLabelNumber2CourierName($strLabelNumber){
		foreach($this->listType2Preg as $strType=> $strPreg){
			if (preg_match( $strPreg, $strLabelNumber)) {
				return $strType;
			}
		}
	}
	
	public function funcGetPregSql($strType){
		return $this->listType2PregSql[$strType];
	}
	
		
}