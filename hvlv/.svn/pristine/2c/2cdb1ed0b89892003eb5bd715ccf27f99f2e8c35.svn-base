<?php
class CDeliveryType{
	const type_aupost = 'AuPost';
	// const type_cargo = 'Cargo';
	const type_cargo = 'TLA Delivery';
	const type_tnt = 'TNT';
	const type_fastway = 'Fast Way';
	const type_toll = 'Toll';
	const type_pickup = 'Pick Up';
	const type_sf = 'SF';
	const type_allied = 'Allied';

	const listDeliveryType = [
		Self::type_aupost => 'AuPost',
		Self::type_cargo=> 'TLA Delivery',
		Self::type_tnt => 'TNT',
		Self::type_fastway=> 'Fast Way',
		Self::type_toll=> 'Toll',
		Self::type_pickup=> 'Pick Up',
		Self::type_sf=> 'SF',
	];
 
	private $listType2Preg = [
		self::type_aupost =>'/^(AMQ|UBY|333UF|33EVJ|33FJV|33FKB|33EVH|JDQ|SJU|ZK6|UC7|33A8Y|33MBQ|34AWE|TEST)\d{7}$/',
		self::type_cargo =>'/^(SKP|MKP|BKP)\d{10}$/',
		self::type_tnt =>'/^(LMA|TLS|TBC|LPC)\d{9}$/',
		self::type_fastway =>'/^(2X|QX)\d{10}$/',
		self::type_toll =>'/^(697328|698044)\d{7}$/',
		self::type_pickup =>'/^(PICKUP|SYDPICKUP|SKPPICKUP|PICKUPSKP)/',
		self::type_sf =>'/^(SF)\d{13}$/',
	];
	
	private $listType2PregSql = [
		self::type_aupost=>'^(AMQ|UBY|333UF|33EVJ|33FJV|33FKB|33EVH|JDQ|SJU|ZK6|UC7|33A8Y|33MBQ|34AWE|TEST)',
		self::type_cargo=>'^(SKP|MKP|BKP)',
		self::type_tnt=>'^(LMA|TLS|TBC|LPC)',
		self::type_fastway=>'^(2X|QX)',
		self::type_toll =>'^(697328|698044)',
		self::type_pickup =>'^(PICKUP|SYDPICKUP|SKPPICKUP|PICKUPSKP)',
		self::type_sf =>'^(SF)',
	];


	// --------------
	const dicOrgId2CourierName = [
		org::ORGID_COURIER_AUPOST=>Self::type_aupost,
		org::ORGID_COURIER_FASTWAY=>self::type_fastway,
		Org::ORGID_COURIER_TNT =>self::type_tnt,
		Org::ORGID_COURIER_SF=>self::type_sf,
	];

	const dicOrgRateId2CourierName=[
		Org::TPLORGRATE_COURIER_EIZTOLL_MEL=>self::type_toll,
		Org::TPLORGRATE_COURIER_EIZTOLL_SYD=>self::type_toll,
		Org::TPLORGRATE_COURIER_ALLIED_MEL=>self::type_allied,
		Org::TPLORGRATE_COURIER_ALLIED_SYD=>self::type_allied,
		Org::TPLORGRATE_COURIER_TOLL_BNE =>self::type_toll,
		Org::TPLORGRATE_COURIER_ALLIED_BNE =>self::type_allied,
		Org::TPLORGRATE_COURIER_ALLIED_PER =>self::type_allied,
		Org::TPLORGRATE_COURIER_ALLIED_ADL =>self::type_allied,
	];
	

	const listOrgRateIdToll = [
		Org::TPLORGRATE_COURIER_EIZTOLL_MEL,
		Org::TPLORGRATE_COURIER_EIZTOLL_SYD
	];

	const listOrgRateIdAllied = [
		Org::TPLORGRATE_COURIER_ALLIED_MEL,
		Org::TPLORGRATE_COURIER_ALLIED_SYD
	];

	
	function __construct(){
		
	}
	
	
	public function funcLabelNumber2CourierName($strLabelNumber){
		$strLabelNumber = trim($strLabelNumber);
		foreach($this->listType2Preg as $strType=> $strPreg){
			if (preg_match( $strPreg, $strLabelNumber)) {
				return $strType;
			}
		}
	}
	
	public function funcGetPregSql($strType){
		return $this->listType2PregSql[$strType];
	}

	public function funcChectCourierName($numTranshipOrgId,$numOrgRateId){
		$strCourierName = '';
		if(empty($numTranshipOrgId)){
			$strCourierName = 'TLA Delivery';
		}
		else if(isset(self::dicOrgId2CourierName[$numTranshipOrgId])){
			$strCourierName = self::dicOrgId2CourierName[$numTranshipOrgId]; 
		}
		else if($numTranshipOrgId == Org::ORGID_COURIER_EIZ){
			$strCourierName = 'Eiz';
			if(isset(self::dicOrgRateId2CourierName[$numOrgRateId])){
				$strCourierName = self::dicOrgRateId2CourierName[$numOrgRateId];
			}
		}
		return $strCourierName;
	}
	
		
}