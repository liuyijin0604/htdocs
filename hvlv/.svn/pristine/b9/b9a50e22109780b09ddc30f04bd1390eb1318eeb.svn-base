<?php 
class ContainerQuotationService extends Service
{
    public function createContainerQuotationEmail($containerQuotationModel,$cuEmail,$cuName,$contactNumber,$enquiryDate){
	    	$ddpt = '';
	    	$distance = $containerQuotationModel->distance;
	    	$standardTrailer = $containerQuotationModel->standard_trailer;
	    	if ($containerQuotationModel->state=="VIC") {
				$ddpt = 'MEL';
			}
			elseif ($containerQuotationModel->state=="NSW") {
				$ddpt = 'SYD';			
			}
			elseif ($containerQuotationModel->state=="QLD") {
				$ddpt = 'BNE';	
			}
			$Rates=SystemSetting::getContainerQuotationRates();
			$cqRates=$Rates[$ddpt];
			$fuelRate=(double)$cqRates['fuel_rate'];
			$timeslot=(int)$cqRates['timeslot'];
			$infrastructure=(int)$cqRates['infrastructure'];
			$emptyDeHire=(int)$cqRates['empty_de_hire'];
			$toll=(int)$cqRates['toll'];

			switch ($distance) {
				case ($distance>=0&&$distance<10):
					$cartageFee = (int)$cqRates['0_9'][$standardTrailer];
					break;
				case ($distance>=10&&$distance<20):
					$cartageFee = (int)$cqRates['10_19'][$standardTrailer];
					break;
				case ($distance>=20&&$distance<30):
					$cartageFee = (int)$cqRates['20_29'][$standardTrailer];
					break;
				case ($distance>=30&&$distance<40):
					$cartageFee = (int)$cqRates['30_39'][$standardTrailer];
					break;
				case ($distance>=40&&$distance<50):
					$cartageFee = (int)$cqRates['40_49'][$standardTrailer];
					break;
				case ($distance>=50&&$distance<60):
					$cartageFee = (int)$cqRates['50_59'][$standardTrailer];
					break;
				case ($distance>=60&&$distance<70):
					$cartageFee = (int)$cqRates['60_69'][$standardTrailer];
					break;
				
				default:
					// code...
					break;
			}
			$fuel = $cartageFee*$fuelRate;
			$totalFee = $cartageFee+$fuel+$timeslot+$infrastructure+$emptyDeHire+$toll;
			$model = ["distance"=>$distance, "quotation_number"=>$containerQuotationModel->quotation_num, "cartage_fee"=>$cartageFee, "fuel_charge"=>$fuel, "timeslot"=>$timeslot, "infrastructure"=>$infrastructure, "empty_de-hire_fee"=>$emptyDeHire, "toll_surcharge"=>$toll,"total_fee"=>$totalFee];

			$customerDetail = ["email"=>$cuEmail, "customer_name"=>$cuName, "contact_number"=>$contactNumber, "enquiry_date"=>$enquiryDate];


		    $emailContent = $this->printEmailContent($model,$containerQuotationModel,$customerDetail);
		    $toEmail = 'sales@topligistics.com.au';
		    $subject = 'Container Quotation';
		    $cc = 'imports@toplogistics.com.au';
		    // $cc = 'ray.tang@toplogistics.com.au';
		    $fromName = $cuName;
		    $fromEmail = $cuEmail;
		    Emailog::sendEmailTo($toEmail,$emailContent,$subject,$fromEmail,$fromName,[],$cc);
		    // $this->sendQuotationEmail($toEmail,$subject,$fromName,$fromEmail,$emailContent,$cc);
    }    	

    public function printEmailContent($model,$containerQuotationModel,$customerDetail){
    	$contentHtml = '';
    	$contentHtml.= '
<div style="font-family:Tahoma,Arial,Helvetica,sans-serif;font-size:12px;">
<table>
<tr>
	<td>
		<table cellpadding="0" cellspacing="0" width="100%">
			<tbody>
				<tr>
					<td height="18" colspan="2" style="font-size:18px;line-height:18px">&nbsp;</td>
				</tr>
				<tr>
					<td align="center">
						<p style="font-family:Arial,Helvetica,sans-serif;font-size:28px;color:#544d44;margin:0;padding:0;font-weight:bold">Container Quotation</p>
					</td>
				</tr>
				<tr>
					<td height="28" colspan="2" style="font-size:28px;line-height:28px">&nbsp;</td>
				</tr>
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tbody>
								<tr>
									<td width="22" style="font-size:23px;line-height:23px">&nbsp;</td>
									<td>
										<table cellpadding="0" cellspacing="0" width="100%">
											<tbody>
												<tr>
													<td>
														<p style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#6d6862;margin:0px;padding:0px">
															<b>Customer Name:</b> '.$customerDetail['customer_name'].'
														</p>
													</td>
												</tr>
												<tr>
													<td height="7" colspan="2"></td>
												</tr>
												<tr>
													<td>
														<p style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#6d6862;margin:0px;padding:0px">
															<b>E-mail:</b> <a href="mailto:'.$customerDetail['email'].'" target="_blank">'.$customerDetail['email'].'</a>
														</p>
													</td>
												</tr>
												<tr>
													<td>
														<p style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#6d6862;margin:0px;padding:0px">
															<b>Contact Number:</b> '.$customerDetail['contact_number'].'
														</p>
													</td>
												</tr>
												<tr>
													<td>
														<p style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#6d6862;margin:0px;padding:0px">
															<b>Enquiry Date:</b> '.$customerDetail['enquiry_date'].'
														</p>
													</td>
												</tr>
												<tr>
													<td height="7" colspan="2"></td>
												</tr>
												<tr>
													<td>
														<p style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#6d6862;margin:0px;padding:0px">
															<b>Delivery Address:</b>'.$containerQuotationModel->getAddress().' 
														</p>
													</td>
												</tr>
												<tr>
													<td>
														<p style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#6d6862;margin:0px;padding:0px">
															<b>Standard Trailer Request:</b>'.$containerQuotationModel->standard_trailer.' 
														</p>
													</td>
												</tr>
												<tr>
													<td height="7" colspan="2"></td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>



				<tr>
					<td height="20">
					</td>
				</tr>


				<tr>
					<td align="center">

						<table cellpadding="0" cellspacing="0" width="580">
							<tbody><tr>
								<td rowspan="3" bgcolor="#fff" width="1"></td><td bgcolor="#fff" height="1"></td><td rowspan="3" bgcolor="#fff" width="1"></td>
							</tr>
							<tr><td align="center">
								<table cellpadding="0" cellspacing="0" width="600" style="font-family:Arial,Helvetica,sans-serif">
		<tbody>

		<tr>
			<td height="1" bgcolor="#dddddd" style="font-size:1px;line-height:1px;background-color:#dddddd">&nbsp;</td>
		</tr>
		<tr>
			<td height="1" bgcolor="#ffffff" style="font-size:1px;line-height:1px;background-color:#ffffff">&nbsp;</td>
		</tr>';
					
		$contentHtml.= 
		'<tr style="background-color:#fafafa" bgcolor="#fafafa">
			<table style="border-collapse: collapse; width: 100%;">
				<tr>
                    <th style="border: 1px solid black; padding: 8px;">Delivery Distance</th>
                    <td style="border: 1px solid black; padding: 8px;">'.$model['distance'].' kilometers</td>
                </tr>
                <tr>
                    <th style="border: 1px solid black; padding: 8px;">Reference Number</th>
                    <td style="border: 1px solid black; padding: 8px;">'.$model['quotation_number'].'</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">Cartage Fee</td>
                    <td style="border: 1px solid black; padding: 8px;">$'.$model['cartage_fee'].'</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">Fuel Surcharge</td>
                    <td style="border: 1px solid black; padding: 8px;">$'.$model['fuel_charge'].'</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">Timeslot Fee</td>
                    <td style="border: 1px solid black; padding: 8px;">$'.$model['timeslot'].'</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">Infrastructure Fee</td>
                    <td style="border: 1px solid black; padding: 8px;">$'.$model['infrastructure'].'</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">Empty De-hire Fee</td>
                    <td style="border: 1px solid black; padding: 8px;">$'.$model['empty_de-hire_fee'].'</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">Toll Surcharge</td>
                    <td style="border: 1px solid black; padding: 8px;">$'.$model['toll_surcharge'].'</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">Total Fee</td>
                    <td style="border: 1px solid black; padding: 8px;">$'.$model['total_fee'].'</td>
                </tr>
		    </table>
		</tr>';

		$contentHtml.= '			
		<tr>
			<td height="1" bgcolor="#dddddd" style="font-size:1px;line-height:1px;background-color:#dddddd">&nbsp;</td>
		</tr>
		<tr>
			<td height="1" bgcolor="#ffffff" style="font-size:1px;line-height:1px;background-color:#ffffff">&nbsp;</td>
		</tr>

		<tr>
			<td>
				<table cellpadding="0" cellspacing="0" width="100%">
					<tbody>
						<tr>
							<td height="28" colspan="2">

							</td>
						</tr>
							<tr>
								<td width="20"></td>
								<td height="20" align="left" style="color:#cac9c7;font-size:14px">
									Our Website: <a href="https://toplogistics.com.au/" style="text-decoration:none;color:#4d7cb3" target="_blank" data-saferedirecturl="https://toplogistics.com.au/">https://toplogistics.com.au/</a>
								</td>
							</tr>
					</tbody>
				</table>
			</td>
		</tr>


	</tbody>
</table>


										</td></tr>
										<tr><td bgcolor="#fff" height="1" style="font-size:1px;line-height:1px"></td></tr>
									</tbody></table>
								</td>
							</tr>
							<tr>
								<td height="50">

								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
</table>
</div>';
	return $contentHtml;
    }


}
?>