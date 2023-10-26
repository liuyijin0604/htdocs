<?php
class CShipmentReport{


	const dicOtid2Department=[
		0=>'Imports',
		10=>'3PL',
	];

	const dicPort2State = [
		Org::PCAE_DEPARTMENT_SYDNEY => 'Sydney',
		Org::PCAE_DEPARTMENT_MELBOURNE => 'Melbourne',
		Org::PCAE_DEPARTMENT_BRISBANE => 'Brisbane',
		Org::PCAE_DEPARTMENT_PERTH => 'Perth',
		Org::PCAE_DEPARTMENT_ADELAIDE => 'Adelaide',
		Org::PCAE_DEPARTMENT_FREMANTLE => 'FREMANTLE',
	];

    private $strFrom = null;
    private $strTo = null;
    private $numDepartment = null;
    private $numDeportId = null;
    private $numCustomerId =null;
    private $strDeliveryType = null;



    function __construct($strFrom,$strTo,$numDepartment,$numCustomerId,$strDeliveryType,$numDeportId)
    {
        $this->strFrom = $strFrom;
        $this->strTo = $strTo;
        $this->numDepartment = $numDepartment;
        $this->numDeportId = $numDeportId;
        $this->numCustomerId = $numCustomerId;
        $this->strDeliveryType = $strDeliveryType;
        
    }

    public function funcRecords(){
		$listRecords = [];
		
		// $strSqlJoinTable ='select * from (select ddpt_id,ot_id,hbn,ref,agent_id,consol_id,id_shipment,created,t_shipment_dispatch_handover_delivery_org_consol_clearance.scan_time,dispatch,handover,done,name,code,dpt_id,pod,etd,eta,clearance,MIN(gate_pass_time) as sorted from (select ddpt_id,ot_id,hbn,ref,agent_id,consol_id,id_shipment,created,scan_time,dispatch,handover,done,name,code,dpt_id,pod,etd,eta,MIN(dt) as clearance from (select ddpt_id,ot_id,hbn,ref,agent_id,consol_id,id_shipment,t_shipment_dispatch_handover_delivery_org.created,scan_time,dispatch,handover,done,name,code,dpt_id,pod,etd,eta from (select ddpt_id,ot_id,hbn,ref,agent_id,consol_id,id_shipment,created,scan_time,dispatch,handover,done,name,code from (select ddpt_id,ot_id,hbn,ref,agent_id,consol_id,id_shipment,created,scan_time,dispatch,handover,MIN(dt) as done from (select ddpt_id,ot_id,hbn,ref,agent_id,consol_id,id_shipment,created,scan_time,dispatch,MIN(dt) as handover from(select ddpt_id,ot_id,hbn,ref,agent_id,consol_id,id_shipment,created,scan_time,MIN(dt) as dispatch from (select ddpt_id,ot_id,hbn,ref,agent_id,consol_id,id as id_shipment,created,MIN(JSON_UNQUOTE(JSON_EXTRACT(shipment.meta, "$.scan_time"))) scan_time from shipment where created > "'.$this->strFrom.'" and created < "'.$this->strTo.'" group by id ) t_shipment left join tracking on t_shipment.id_shipment = tracking.pid and tracking.type = 38 group by id_shipment) t_shipment_dispatch left join tracking on t_shipment_dispatch.id_shipment = tracking.pid and tracking.type = 68 group by id_shipment) t_shipment_dispatch_handover left join tracking on t_shipment_dispatch_handover.id_shipment = tracking.pid and tracking.type = 90 group by id_shipment)t_shipment_dispatch_handover_delivery left join org on t_shipment_dispatch_handover_delivery.agent_id = org.id )t_shipment_dispatch_handover_delivery_org left join consol on t_shipment_dispatch_handover_delivery_org.consol_id = consol.id)t_shipment_dispatch_handover_delivery_org_consol left join tracking on t_shipment_dispatch_handover_delivery_org_consol.id_shipment = tracking.pid and tracking.type = 60 group by id_shipment)t_shipment_dispatch_handover_delivery_org_consol_clearance left join gatepass_shipment on t_shipment_dispatch_handover_delivery_org_consol_clearance.id_shipment = gatepass_shipment.fid and  gatepass_shipment.gate_pass_time != "0000-00-00 00:00:00" group by id_shipment)t_shipment_dispatch_handover_delivery_org_consol_clearance_sorted';
		$strSqlJoinTable = 'select * from (select ddpt_id,ot_id,hbn,ref,agent_id,consol_id,id_shipment,created,scan_time,org_rate_id,dispatch,handover,done,name,code,dpt_id,pod,etd,eta,clearance,org_id as trabship_org from (select ddpt_id,ot_id,hbn,ref,agent_id,consol_id,id_shipment,created,scan_time,org_rate_id,dispatch,handover,done,name,code,dpt_id,pod,etd,eta,MIN(dt) as clearance from (select ddpt_id,ot_id,hbn,ref,agent_id,consol_id,id_shipment,t_shipment_dispatch_handover_delivery_org.created,scan_time,org_rate_id,dispatch,handover,done,name,code,dpt_id,pod,etd,eta from (select ddpt_id,ot_id,hbn,ref,agent_id,consol_id,id_shipment,created,scan_time,org_rate_id,dispatch,handover,done,name,code from (select ddpt_id,ot_id,hbn,ref,agent_id,consol_id,id_shipment,created,scan_time,org_rate_id,dispatch,handover,MIN(dt) as done from (select ddpt_id,ot_id,hbn,ref,agent_id,consol_id,id_shipment,created,scan_time,org_rate_id,dispatch,MIN(dt) as handover from(select ddpt_id,ot_id,hbn,ref,agent_id,consol_id,id_shipment,created,scan_time,org_rate_id,MIN(dt) as dispatch from (select ddpt_id,ot_id,hbn,ref,agent_id,consol_id,id as id_shipment,created,MIN(JSON_UNQUOTE(JSON_EXTRACT(shipment.meta, "$.scan_time"))) scan_time,JSON_UNQUOTE(JSON_EXTRACT(shipment.meta, "$.org_rate_id")) as org_rate_id from shipment where created > "'.$this->strFrom.'" and created <= "'.$this->strTo.'" group by id ) t_shipment left join tracking on t_shipment.id_shipment = tracking.pid and tracking.type = 38 group by id_shipment) t_shipment_dispatch left join tracking on t_shipment_dispatch.id_shipment = tracking.pid and tracking.type = 68 group by id_shipment) t_shipment_dispatch_handover left join tracking on t_shipment_dispatch_handover.id_shipment = tracking.pid and tracking.type = 90 group by id_shipment)t_shipment_dispatch_handover_delivery left join org on t_shipment_dispatch_handover_delivery.agent_id = org.id )t_shipment_dispatch_handover_delivery_org left join consol on t_shipment_dispatch_handover_delivery_org.consol_id = consol.id)t_shipment_dispatch_handover_delivery_org_consol left join tracking on t_shipment_dispatch_handover_delivery_org_consol.id_shipment = tracking.pid and tracking.type = 60 group by id_shipment)t_shipment_dispatch_handover_delivery_org_consol_clearance left join tranship on tranship.pid = t_shipment_dispatch_handover_delivery_org_consol_clearance.id_shipment)t_shipment_dispatch_handover_delivery_org_consol_clearance_tranship';

		$listWhere =[];
		
        if($this->numDepartment!==''){
            $listWhere[] = 'ot_id = "'. $this->numDepartment.'"';
        }

        if(!empty($this->numDeportId)){
            $listWhere[] = '(ddpt_id = "'. $this->numDeportId.'" or dpt_id = "'. $this->numDeportId.'" )';
        }

        if(!empty($this->numCustomerId)){
            $listWhere[] = 'code = "'. $this->numCustomerId.'"';
        }

        if(!empty($this->strDeliveryType)){
			$strPreg = (new CDeliveryType)->funcGetPregSql($this->strDeliveryType);
			$listWhere[] = 'ref REGEXP "'. $strPreg.'"';
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

			$obj->depot_id = $objRow['ddpt_id'];
			$obj->consol_depot_id = $objRow['dpt_id'];
			$obj->ot_id = $objRow['ot_id'];
			$obj->hbn = $objRow['hbn'];
			$obj->ref = $objRow['ref'];
			$obj->customer_name = $objRow['name'];

			$obj->created = $objRow['created'];
			$obj->dispatch =  $objRow['dispatch'];
			if(empty($obj->dispatch)){
				$obj->dispatch = $objRow['etd'];
			}
			$obj->eta =  $objRow['eta'];
			$obj->clearance =  $objRow['clearance'];
			$obj->scan_time =  $objRow['scan_time'];
			$obj->handover = $objRow['handover'];
			// $obj->sorted =  $objRow['sorted'];
			$obj->sorted =  $obj->handover;
			$obj->done = $objRow['done'];

			$obj->days_handover_done = '';
			if(!empty($obj->handover)&&!empty($obj->done)){
				$obj->days_handover_done = round((strtotime(date('Y-m-d',strtotime($obj->done))) - strtotime(date('Y-m-d',strtotime($obj->handover))))/86400);
			}

			// $obj->days_dispatch_done = '';
			// if(!empty($obj->dispatch)&&!empty($obj->done)){
			// 	$obj->days_dispatch_done = round((strtotime(date('Y-m-d',strtotime($obj->done))) - strtotime(date('Y-m-d',strtotime($obj->dispatch))))/86400);
			// }

			$obj->days_eta_cleatance = '';
			if(!empty($obj->eta)&&!empty($obj->clearance)){
				$numDays = round((strtotime(date('Y-m-d',strtotime($obj->clearance))) - strtotime(date('Y-m-d',strtotime($obj->eta))))/86400);
				if($numDays < 0){
					$numDays = 0;
				}
				$obj->days_eta_cleatance = $numDays;
			}

			$obj->days_sorted_handover = '';
			if(!empty($obj->handover)&&!empty($obj->sorted)){
				$obj->days_sorted_handover = round((strtotime(date('Y-m-d',strtotime($obj->handover))) - strtotime(date('Y-m-d',strtotime($obj->sorted))))/86400);
			}

			$obj->days_scan_sorted = '';
			if(!empty($obj->scan_time)&&!empty($obj->sorted)){
				$obj->days_scan_sorted = round((strtotime(date('Y-m-d',strtotime($obj->sorted))) - strtotime(date('Y-m-d',strtotime($obj->scan_time))))/86400);
			}

			$obj->days_sorted_done = '';
			if(!empty($obj->sorted)&&!empty($obj->done)){
				$obj->days_sorted_done = round((strtotime(date('Y-m-d',strtotime($obj->done))) - strtotime(date('Y-m-d',strtotime($obj->sorted))))/86400);
			}

			$obj->depot = $obj->depot_id;
			if(isset(self::dicPort2State[$obj->depot_id])){
				$obj->depot = self::dicPort2State[$obj->depot_id];
			}
			else{
				if(isset(self::dicPort2State[$obj->consol_depot_id])){
					$obj->depot = self::dicPort2State[$obj->consol_depot_id];
				}
			}



			$obj->department = self::dicOtid2Department[$obj->ot_id];

			// $obj->delivery_type = (new CDeliveryType)->funcLabelNumber2CourierName($obj->ref);
			$numTranshipOrgId =  $objRow['trabship_org'];
			$numOrgRateId = $objRow['org_rate_id'];
			$obj->delivery_type = (new CDeliveryType)->funcChectCourierName($numTranshipOrgId,$numOrgRateId);

			
			$listRecords[] = $obj;
		}
		

        return $listRecords ;




    }

    public function funcListRecords2Tbody($listRecords){
		return $this->funcListRecords2TbodyBase($listRecords);
	}

	public function funcListRecords2Tbody100($listRecords){
		return $this->funcListRecords2TbodyBase($listRecords,100);
	}

	private function funcListRecords2TbodyBase($listRecords,$numLimit = null){


		$strHtml = '';
		foreach ($listRecords as $i => $objRecord) {
			$strHtml .= '<tr class="' . ($i % 2 == 0 ? 'odd' : 'even') . '">';

			if($numLimit != null && $i > $numLimit){
				$strHtml .= '<td colspan="16" align="center"><a style="cursor:pointer" onclick="funcExcel()">----please export excel to check more----</a></td>';
				$strHtml .= '</tr>';
				break;
			}
			
			$strHtml .= '<td>' .$objRecord->department . '</td>';
			$strHtml .= '<td>' .$objRecord->depot. '</td>';
			$strHtml .= '<td>' .$objRecord->delivery_type . '</td>';
			$strHtml .= '<td>' . $objRecord->hbn. '</td>';
			$strHtml .= '<td>' . $objRecord->ref . '</td>';
			$strHtml .= '<td>' . $objRecord->customer_name . '</td>';

            $strHtml .= '<td>' . $objRecord->created . '</td>';
			$strHtml .= '<td>' . $objRecord->eta . '</td>';
			$strHtml .= '<td>' . $objRecord->clearance . '</td>';
			$strHtml .= '<td>' . $objRecord->scan_time . '</td>';
			$strHtml .= '<td>' . $objRecord->sorted . '</td>';
			// $strHtml .= '<td>' . $objRecord->dispatch. '</td>';
			$strHtml .= '<td>' . $objRecord->handover . '</td>';
			$strHtml .= '<td>' . $objRecord->done . '</td>';
			$strHtml .= '<td>' . $objRecord->days_eta_cleatance . '</td>';
			$strHtml .= '<td>' . $objRecord->days_scan_sorted . '</td>';
			$strHtml .= '<td>' . $objRecord->days_sorted_handover . '</td>';
			// $strHtml .= '<td>' . $objRecord->days_handover_done . '</td>';
			$strHtml .= '<td>' . $objRecord->days_sorted_done . '</td>';
			
			
			$strHtml .= '</tr>';
		}
		
		$strHtml .='</tbody></table>';
		return $strHtml;
	}

	public function funcListRecords2Statistics($listRecords,$strToTime){
		$objResult = new stdClass;

		$listClearance = [];

		$dicCourier2ListDispatch =[];
		$dicCourier2ListDispatch[CDeliveryType::type_aupost] =[];
		$dicCourier2ListDispatch[CDeliveryType::type_fastway] =[];
		$dicCourier2ListDispatch[CDeliveryType::type_toll] =[];
		$dicCourier2ListDispatch[CDeliveryType::type_allied] =[];

		$dicCourier2ListToCourier =[];
		$dicCourier2ListToCourier[CDeliveryType::type_aupost] =[];
		$dicCourier2ListToCourier[CDeliveryType::type_fastway] =[];
		$dicCourier2ListToCourier[CDeliveryType::type_toll] =[];
		$dicCourier2ListToCourier[CDeliveryType::type_allied] =[];

		$dicCourier2ListDelivery =[];
		$dicCourier2ListDelivery[CDeliveryType::type_aupost] =[];
		$dicCourier2ListDelivery[CDeliveryType::type_fastway] =[];
		$dicCourier2ListDelivery[CDeliveryType::type_toll] =[];
		$dicCourier2ListDelivery[CDeliveryType::type_allied] =[];

		// for ($i=1; $i<=7; $i++) {
		for ($i=0; $i<7; $i++) {
			$strDateIndex = date('Y-m-d',strtotime('-'.$i.' day',strtotime($strToTime)));
			$listClearance[$strDateIndex]=[
				'null'=>0,
				'e0'=>0,
				'm0'=>0,
			];

			$dicCourier2ListDispatch[CDeliveryType::type_aupost][$strDateIndex] =['null'=>0,'e0'=>0,'m1'=>0];
			$dicCourier2ListDispatch[CDeliveryType::type_fastway][$strDateIndex] =['null'=>0,'e0'=>0,'m1'=>0];
			$dicCourier2ListDispatch[CDeliveryType::type_toll][$strDateIndex] =['null'=>0,'e0'=>0,'m1'=>0];
			$dicCourier2ListDispatch[CDeliveryType::type_allied][$strDateIndex] =['null'=>0,'e0'=>0,'m1'=>0];
	
			$dicCourier2ListToCourier[CDeliveryType::type_aupost][$strDateIndex] =['null'=>0,'e0'=>0,'m1'=>0];
			$dicCourier2ListToCourier[CDeliveryType::type_fastway][$strDateIndex] =['null'=>0,'e0'=>0,'m1'=>0];
			$dicCourier2ListToCourier[CDeliveryType::type_toll][$strDateIndex] =['null'=>0,'e0'=>0,'m1'=>0];
			$dicCourier2ListToCourier[CDeliveryType::type_allied][$strDateIndex] =['null'=>0,'e0'=>0,'m1'=>0];

			$dicCourier2ListDelivery[CDeliveryType::type_aupost][$strDateIndex] =['e1'=>0,'m1le3'=>0,'m3le5'=>0,'m5'=>0];
			$dicCourier2ListDelivery[CDeliveryType::type_fastway][$strDateIndex] =['e1'=>0,'m1le3'=>0,'m3le5'=>0,'m5'=>0];
			$dicCourier2ListDelivery[CDeliveryType::type_toll][$strDateIndex] =['e1'=>0,'m1le3'=>0,'m3le5'=>0,'m5'=>0];
			$dicCourier2ListDelivery[CDeliveryType::type_allied][$strDateIndex] =['e1'=>0,'m1le3'=>0,'m3le5'=>0,'m5'=>0];

		}

		foreach($listRecords as $objRecords){
			if(!in_array($objRecords->delivery_type,[CDeliveryType::type_aupost,CDeliveryType::type_fastway,CDeliveryType::type_toll,CDeliveryType::type_allied])){
				continue;
			}

			$strDate = $objRecords->created;
			$strCourier = $objRecords->delivery_type;

			if($objRecords->days_eta_cleatance === ''){
				$listClearance[$strDate]['null']++;
			}
			else if($objRecords->days_eta_cleatance == 0){
				$listClearance[$strDate]['e0']++;
			}
			else if ($objRecords->days_eta_cleatance > 0){
				$listClearance[$strDate]['m0']++;
			}

			// Dispatch
			if($objRecords->days_scan_sorted === ''){
				$dicCourier2ListDispatch[$strCourier][$strDate]['null']++;
			}
			else if($objRecords->days_scan_sorted == 0){
				$dicCourier2ListDispatch[$strCourier][$strDate]['e0']++;
			}
			else if ($objRecords->days_scan_sorted > 1){
				$dicCourier2ListDispatch[$strCourier][$strDate]['m1']++;
			}

			// To Courier
			if($objRecords->days_sorted_handover === ''){
				$dicCourier2ListToCourier[$strCourier][$strDate]['null']++;
			}
			else if($objRecords->days_sorted_handover == 0){
				$dicCourier2ListToCourier[$strCourier][$strDate]['e0']++;
			} 
			else if ($objRecords->days_sorted_handover > 1){
				$dicCourier2ListToCourier[$strCourier][$strDate]['m1']++;
			}

			// Delivery
			if($objRecords->days_sorted_done == 1){
				$dicCourier2ListDelivery[$strCourier][$strDate]['e1']++;
			}
			else if($objRecords->days_sorted_done >1&&$objRecords->days_sorted_done <= 3){
				$dicCourier2ListDelivery[$strCourier][$strDate]['m1le3']++;
			}
			else if ($objRecords->days_sorted_done >3&&$objRecords->days_sorted_done <= 5){
				$dicCourier2ListDelivery[$strCourier][$strDate]['m3le5']++;
			}
			else if ($objRecords->days_sorted_done > 5 || $objRecords->days_sorted_done===''){
				$dicCourier2ListDelivery[$strCourier][$strDate]['m5']++;
			}

		}

		// to html
		// clearance
		$strHtmlClearance ='';
		$num0 = 0;
		$numBig0 = 0;
		$numNull = 0;

		$i = 0;
		foreach($listClearance as $strDateIndex => $listCurrent){ // clearance
			$numAll = 0;
			foreach($listCurrent as $num){
				$numAll += $num;
			}
			
			$strHtmlClearance .= '<tr class="' . ($i % 2 == 0 ? 'odd' : 'even') . '">';
			$strHtmlClearance .= '<td>' .$strDateIndex.'</td>';
			// function funcShowDetailsClearance(strDate,isNull,isE0,isM0){
			$strHtmlClearance .= '<td onClick="funcShowDetailsClearance(\''.$strDateIndex.'\',false,true,false)">' .($numAll==0?0:(round($listCurrent['e0']/$numAll,4)*100)).'%</td>';
			$strHtmlClearance .= '<td onClick="funcShowDetailsClearance(\''.$strDateIndex.'\',false,false,true)">' .($numAll==0?0:(round($listCurrent['m0']/$numAll,4)*100)).'%</td>';
			$strHtmlClearance .= '<td onClick="funcShowDetailsClearance(\''.$strDateIndex.'\',true,false,false)">' .($numAll==0?0:(round($listCurrent['null']/$numAll,4)*100)).'%</td>';
			$strHtmlClearance .= '<td>100%</td>';
			$strHtmlClearance .= '</tr>';

			$num0 += $listCurrent['e0'];
			$numBig0 += $listCurrent['m0'];
			$numNull+= $listCurrent['null'];

			$i++;
		}

		$strHtmlClearance .= '<tr class="' . ($i % 2 == 0 ? 'odd' : 'even') . '">';
		$strHtmlClearance .= '<td>总计</td>';
		$strHtmlClearance .= '<td>' .$num0.'</td>';
		$strHtmlClearance .= '<td>' .$numBig0.'</td>';
		$strHtmlClearance .= '<td>' .$numNull.'</td>';
		$strHtmlClearance .= '<td></td>';
		$strHtmlClearance .= '</tr>';

		//dispatch
		$dicCourier2StrHtmlDispatch = [];
		foreach($dicCourier2ListDispatch as $strCourier => $listDispatch){
			$dicCourier2StrHtmlDispatch[$strCourier] = '';
			$numE0 = 0;
			$numM1 = 0;
			$numNull = 0;
			$i = 0;
			foreach($listDispatch as $strDateIndex => $listCurrent){ 
				$numAll = 0;
				foreach($listCurrent as $num){
					$numAll += $num;
				}
				
				$dicCourier2StrHtmlDispatch[$strCourier] .= '<tr class="' . ($i % 2 == 0 ? 'odd' : 'even') . '">';
				$dicCourier2StrHtmlDispatch[$strCourier] .= '<td>' .$strDateIndex.'</td>';
				$dicCourier2StrHtmlDispatch[$strCourier] .= '<td onClick="funcShowDetailsDispatch(\''.$strDateIndex.'\',\''.$strCourier.'\',false,true,false)">' .($numAll==0?0:(round($listCurrent['e0']/$numAll,4)*100)).'%</td>';
				$dicCourier2StrHtmlDispatch[$strCourier] .= '<td onClick="funcShowDetailsDispatch(\''.$strDateIndex.'\',\''.$strCourier.'\',false,false,true)">' .($numAll==0?0:(round($listCurrent['m1']/$numAll,4)*100)).'%</td>';
				$dicCourier2StrHtmlDispatch[$strCourier] .= '<td onClick="funcShowDetailsDispatch(\''.$strDateIndex.'\',\''.$strCourier.'\',true,false,false)">' .($numAll==0?0:(round($listCurrent['null']/$numAll,4)*100)).'%</td>';
				$dicCourier2StrHtmlDispatch[$strCourier] .= '<td>100%</td>';
				$dicCourier2StrHtmlDispatch[$strCourier] .= '</tr>';

				$numE0 += $listCurrent['e0'];
				$numM1 += $listCurrent['m1'];
				$numNull+= $listCurrent['null'];
	
				$i++;
			}
	
			$dicCourier2StrHtmlDispatch[$strCourier] .= '<tr class="' . ($i % 2 == 0 ? 'odd' : 'even') . '">';
			$dicCourier2StrHtmlDispatch[$strCourier] .= '<td>总计</td>';
			$dicCourier2StrHtmlDispatch[$strCourier] .= '<td>' .$numE0.'</td>';
			$dicCourier2StrHtmlDispatch[$strCourier] .= '<td>' .$numM1.'</td>';
			$dicCourier2StrHtmlDispatch[$strCourier] .= '<td>' .$numNull.'</td>';
			$dicCourier2StrHtmlDispatch[$strCourier] .= '<td></td>';
			$dicCourier2StrHtmlDispatch[$strCourier] .= '</tr>';
		}

		//To Courier
		$dicCourier2StrHtmlToCourier = [];
		foreach($dicCourier2ListToCourier as $strCourier => $listToCourier){
			$dicCourier2StrHtmlToCourier[$strCourier] = '';
			$numE0 = 0;
			$numM1 = 0;
			$numNull = 0;
			$i = 0;
			foreach($listToCourier as $strDateIndex => $listCurrent){ 
				$numAll = 0;
				foreach($listCurrent as $num){
					$numAll += $num;
				}
	
				$dicCourier2StrHtmlToCourier[$strCourier] .= '<tr class="' . ($i % 2 == 0 ? 'odd' : 'even') . '">';
				$dicCourier2StrHtmlToCourier[$strCourier] .= '<td>' .$strDateIndex.'</td>';
				$dicCourier2StrHtmlToCourier[$strCourier] .= '<td onClick="funcShowDetailsToCourier(\''.$strDateIndex.'\',\''.$strCourier.'\',false,true,false)">' .($numAll==0?0:(round($listCurrent['e0']/$numAll,4)*100)).'%</td>';
				$dicCourier2StrHtmlToCourier[$strCourier] .= '<td onClick="funcShowDetailsToCourier(\''.$strDateIndex.'\',\''.$strCourier.'\',false,false,true)">' .($numAll==0?0:(round($listCurrent['m1']/$numAll,4)*100)).'%</td>';
				$dicCourier2StrHtmlToCourier[$strCourier] .= '<td onClick="funcShowDetailsToCourier(\''.$strDateIndex.'\',\''.$strCourier.'\',true,false,false)">' .($numAll==0?0:(round($listCurrent['null']/$numAll,4)*100)).'%</td>';
				$dicCourier2StrHtmlToCourier[$strCourier] .= '<td>100%</td>';
				$dicCourier2StrHtmlToCourier[$strCourier] .= '</tr>';

				$numE0 += $listCurrent['e0'];
				$numM1 += $listCurrent['m1'];
				$numNull+= $listCurrent['null'];
	
				$i++;
			}
	
			$dicCourier2StrHtmlToCourier[$strCourier] .= '<tr class="' . ($i % 2 == 0 ? 'odd' : 'even') . '">';
			$dicCourier2StrHtmlToCourier[$strCourier] .= '<td>总计</td>';
			$dicCourier2StrHtmlToCourier[$strCourier] .= '<td>' .$numE0.'</td>';
			$dicCourier2StrHtmlToCourier[$strCourier] .= '<td>' .$numM1.'</td>';
			$dicCourier2StrHtmlToCourier[$strCourier] .= '<td>' .$numNull.'</td>';
			$dicCourier2StrHtmlToCourier[$strCourier] .= '<td></td>';
			$dicCourier2StrHtmlToCourier[$strCourier] .= '</tr>';
		}

		// Delivery
		$dicCourier2StrHtmlDelivery = [];
		foreach($dicCourier2ListDelivery as $strCourier => $listDelivery){
			$dicCourier2StrHtmlDelivery[$strCourier] = '';
			$numE0 = 0;
			$numM1le3 = 0;
			$numM3le5 = 0;
			$numM5 = 0;

			$i = 0;
			foreach($listDelivery as $strDateIndex => $listCurrent){ 
				$numAll = 0;
				foreach($listCurrent as $num){
					$numAll += $num;
				}
	
				$dicCourier2StrHtmlDelivery[$strCourier] .= '<tr class="' . ($i % 2 == 0 ? 'odd' : 'even') . '">';
				$dicCourier2StrHtmlDelivery[$strCourier] .= '<td>' .$strDateIndex.'</td>';
				// function funcShowDetailsDelivery(strDate,strDeliveryType,isE1,isM1le3,isM3le5,isM5){
				$dicCourier2StrHtmlDelivery[$strCourier] .= '<td onClick="funcShowDetailsDelivery(\''.$strDateIndex.'\',\''.$strCourier.'\',true,false,false,false)">' .($numAll==0?0:(round($listCurrent['e1']/$numAll,4)*100)).'%</td>';
				$dicCourier2StrHtmlDelivery[$strCourier] .= '<td onClick="funcShowDetailsDelivery(\''.$strDateIndex.'\',\''.$strCourier.'\',false,true,false,false)">' .($numAll==0?0:(round($listCurrent['m1le3']/$numAll,4)*100)).'%</td>';
				$dicCourier2StrHtmlDelivery[$strCourier] .= '<td onClick="funcShowDetailsDelivery(\''.$strDateIndex.'\',\''.$strCourier.'\',false,false,true,false)">' .($numAll==0?0:(round($listCurrent['m3le5']/$numAll,4)*100)).'%</td>';
				$dicCourier2StrHtmlDelivery[$strCourier] .= '<td onClick="funcShowDetailsDelivery(\''.$strDateIndex.'\',\''.$strCourier.'\',false,false,false,true)">' .($numAll==0?0:(round($listCurrent['m5']/$numAll,4)*100)).'%</td>';
				$dicCourier2StrHtmlDelivery[$strCourier] .= '<td>100%</td>';
				$dicCourier2StrHtmlDelivery[$strCourier] .= '</tr>';

				$numE0 +=$listCurrent['e1'];
				$numM1le3 +=$listCurrent['m1le3'];
				$numM3le5 +=$listCurrent['m3le5'];
				$numM5 +=$listCurrent['m5'];
	
				$i++;
			}
	
			$dicCourier2StrHtmlDelivery[$strCourier] .= '<tr class="' . ($i % 2 == 0 ? 'odd' : 'even') . '">';
			$dicCourier2StrHtmlDelivery[$strCourier] .= '<td>总计</td>';
			$dicCourier2StrHtmlDelivery[$strCourier] .= '<td>' .$numE0.'</td>';
			$dicCourier2StrHtmlDelivery[$strCourier] .= '<td>' .$numM1le3.'</td>';
			$dicCourier2StrHtmlDelivery[$strCourier] .= '<td>' .$numM3le5.'</td>';
			$dicCourier2StrHtmlDelivery[$strCourier] .= '<td>' .$numM5.'</td>';
			$dicCourier2StrHtmlDelivery[$strCourier] .= '<td></td>';
			$dicCourier2StrHtmlDelivery[$strCourier] .= '</tr>';
		}


		$objResult->strHtmlClearance= $strHtmlClearance;
		$objResult->dicCourier2StrHtmlDispatch= $dicCourier2StrHtmlDispatch;
		$objResult->dicCourier2StrHtmlToCourier= $dicCourier2StrHtmlToCourier;
		$objResult->dicCourier2StrHtmlDelivery= $dicCourier2StrHtmlDelivery;

		return $objResult;

	}


	

	



}




