<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
ob_start();
class ApiInboundWebhookAction extends CAction
{
    public function run()
    {
       $this->inbound();
    }

    public function inbound()
    {
       if ($json = json_decode(file_get_contents("php://input"),true)) {
            $data = $json;
            print_r($data);
        } else {
            $data = $_POST;
            print_r($data);
        }
        

        if ($data['webhookEvent'] == 'App.BookingCreatedOrUpdated') {
            $bookingNumber = $data['data']['bookingNumber'];
            $facility = $data['data']['facility'];
            $preProcessed = 0;
            if (!empty($data['data']['preProcessed']) && $data['data']['preProcessed'] == true) {
                $preProcessed = 1;
            }
            $depot = 0;
            switch($facility) {
                case 'Top Logistics Bankstown':
                    $depot = 106;
                    break;
                case 'Top Logistics Sunshine':
                    $depot = 218;
                    break;
                case 'Top Logistics Coopers Plains':
                    $depot = 530;
                    break;
            }
            $bookingDate = $data['data']['bookingDate'];
            $bookingTime = $data['data']['startTimeWindow'];
            $status = PickupBooking::NEW;
            switch($data['data']['status']) {
                case 'Open':
                    $status = PickupBooking::SCHEDULED;
                    break;
                case 'Completed':
                    $status = PickupBooking::DONE;
                    break;
                case 'CancelledByFacility':
                case 'CancelledByTransporter':
                case 'CancelledBySystem':
                    $status = PickupBooking::CANCELLED;
                    break;
            }
            $fields = $data['data']['fields'];
            $rego = '';
            $containerNo = '';
            $hbn = '';
            $specialInstructions = '';

            foreach($fields as $field) {
                switch($field['field']) {
                    case 'Vehicle Rego':
                        $rego = $field['value'];
                        break;
                    case 'Container No.':
                        $containerNo = $field['value'];
                        break;
                    case 'House BL':
                        $hbn = $field['value'];
                        break;
                    case 'Special Instructions':
                        $specialInstructions = $field['value'];
                        break;
                }
            }
            $driverName = !empty($data['data']['guestName'])?$data['data']['guestName']:'inbound generate';
            $companyName = !empty($data['data']['transporter'])?$data['data']['transporter']:'inbound generate';
            $companyEmail = !empty($data['data']['guestEmail'])?$data['data']['guestEmail']:'inbound generate';
            echo 'hbn: ' . $hbn;
            echo "\ncontainer no: " . $containerNo;
            $sql = 'SELECT * FROM shipment WHERE `hbn` = "' . $hbn . '" AND consol_id IN (SELECT id FROM consol WHERE `container_no` = "' . $containerNo . '" AND `dpt_id` = ' . $depot . ') AND `status` != 100';
            $shipment = ImParcel::model()->findBySql($sql);
            $booking = PickupBooking::model()->findByAttributes(['booking_number' => $bookingNumber]);
            if (!empty($shipment)) {
                if (empty($booking)) {
                    $booking = new PickupBooking();
                    $booking->create_date = date('Y-m-d H:i:s');
                    $booking->booking_time = date('Y-m-d H:i:s', strtotime($bookingDate . ' ' . $bookingTime));
                    if ($shipment->status == ImParcel::STATE_HELD || $shipment->status == ImParcel::STATE_DUTY_HELD || $shipment->status == ImParcel::STATE_CLEAR_WAIT) {
                        $booking->status = PickupBooking::HELD;
                    } else {
                        $booking->status = $status;
                    }
                    $booking->driver_name = $driverName;
                    $booking->rego = $rego;
                    $booking->company_name = $companyName;
                    $booking->company_email = $companyEmail;
                    $booking->note = $specialInstructions;
                    $booking->fee = round(floatval($data['data']['baseAmount']), 2);
                    $booking->booking_number = $bookingNumber;
                    $booking->shipment_id = $shipment->id;
					$booking->ref = $shipment->ref;
                    $booking->mdata['depot'] = $depot;
                    $booking->mdata['container_no'] = $containerNo;
                    $booking->mdata['house_bl'] = $hbn;
                    $booking->mdata['pre_processed'] = $preProcessed;
                    //echo "\nHey" . $bookingNumber;
                    $booking->depot_id = $depot;
                    $shipment->mdata['pickup_booking_time'] = $booking->booking_time;
			        $shipment->mdata['pickup_booking_number'] = $booking->booking_number;
                    $shipment->updateMeta();
                    $booking->save();
                    //print_r($booking);

                    // Save DO files
                    $attachments = $data['data']['attachments'];
                    foreach($attachments as $attachment) {
                        $fileName = $attachment['fileName'];
                        $fileURL = $attachment['url'];
                        FileRepo::storeUrlFile($fileURL, $fileName, FileRepo::BOOKING_DO_FILE, $booking->shipment_id);
                    }
                } else {
                    if (!empty($booking->mdata['pre_processed']) && $booking->mdata['pre_processed'] == 1 && $preProcessed == 0) {
                        $booking->mdata['update_after_pre_process'] = 1;
                    }
                    // reset update_after_pre_process when pre process status change to true
                    if (!empty($booking->mdata['update_after_pre_process']) && $booking->mdata['update_after_pre_process'] == 1 && $preProcessed == 1) {
                        $booking->mdata['update_after_pre_process'] = 0;
                    }
                    $booking->booking_time = date('Y-m-d H:i:s', strtotime($bookingDate . ' ' . $bookingTime));
                    if ($shipment->status == ImParcel::STATE_HELD || $shipment->status == ImParcel::STATE_DUTY_HELD || $shipment->status == ImParcel::STATE_CLEAR_WAIT) {
                        $booking->status = PickupBooking::HELD;
                    } else {
                        $booking->status = $status;
                    }
                    $booking->driver_name = $driverName;
                    $booking->rego = $rego;
                    $booking->company_name = $companyName;
                    $booking->company_email = $companyEmail;
                    $booking->note = $specialInstructions;
                    $booking->fee = round(floatval($data['data']['baseAmount']), 2);
                    $booking->booking_number = $bookingNumber;
                    $booking->shipment_id = $shipment->id;
                    $booking->mdata['pre_processed'] = $preProcessed;
                    $booking->mdata['depot'] = $depot;
                    $booking->mdata['container_no'] = $containerNo;
                    $booking->mdata['house_bl'] = $hbn;
                    $shipment->mdata['pickup_booking_time'] = $booking->booking_time;
			        $shipment->mdata['pickup_booking_number'] = $booking->booking_number;
                    $shipment->updateMeta();
                    $booking->save();

                    // Save DO files
                    $attachments = $data['data']['attachments'];
                    foreach($attachments as $attachment) {
                        $fileName = $attachment['fileName'];
                        $fileURL = $attachment['url'];
                        FileRepo::storeUrlFile($fileURL, $fileName, FileRepo::BOOKING_DO_FILE, $booking->shipment_id);
                    }
                }
            } else {
                if (empty($booking)) {
                    $booking = new PickupBooking();
                    $booking->create_date = date('Y-m-d H:i:s');
                    $booking->booking_time = date('Y-m-d H:i:s', strtotime($bookingDate . ' ' . $bookingTime));
                    if ($status == PickupBooking::CANCELLED) {
                        $booking->status = PickupBooking::CANCELLED;
                    } else {
                        $booking->status = PickupBooking::INBOUND_ERROR;
                    }
                    $booking->driver_name = $driverName;
                    $booking->rego = $rego;
                    $booking->company_name = $companyName;
                    $booking->company_email = $companyEmail;
                    $booking->note = $specialInstructions;
                    $booking->fee = round(floatval($data['data']['baseAmount']), 2);
                    $booking->booking_number = $bookingNumber;
                    $booking->shipment_id = 0;
                    $booking->depot_id = $depot;
                    $booking->ref = $hbn;
                    $booking->mdata['depot'] = $depot;
                    $booking->mdata['container_no'] = $containerNo;
                    $booking->mdata['house_bl'] = $hbn;
                    $booking->mdata['pre_processed'] = $preProcessed;
                    $booking->save();

                    // Save DO files
                    $attachments = $data['data']['attachments'];
                    foreach($attachments as $attachment) {
                        $fileName = $attachment['fileName'];
                        $fileURL = $attachment['url'];
                        FileRepo::storeUrlFile($fileURL, $fileName, FileRepo::INBOUND_ERROR_BOOKING_DO_FILE, $booking->id);
                    }
                    // TODO: send email to someone
                    $emailService = new EmailService();
                    $emailService->sendInboundAlertEmail($booking, $containerNo);
                } else {
                    if (!empty($booking->mdata['pre_processed']) && $booking->mdata['pre_processed'] == 1 && $preProcessed == 0) {
                        $booking->mdata['update_after_pre_process'] = 1;
                    }
                    // reset update_after_pre_process when pre process status change to true
                    if (!empty($booking->mdata['update_after_pre_process']) && $booking->mdata['update_after_pre_process'] == 1 && $preProcessed == 1) {
                        $booking->mdata['update_after_pre_process'] = 0;
                    }
                    $booking->booking_time = date('Y-m-d H:i:s', strtotime($bookingDate . ' ' . $bookingTime));
                    if ($status == PickupBooking::CANCELLED) {
                        $booking->status = PickupBooking::CANCELLED;
                    } else {
                        $booking->status = PickupBooking::INBOUND_ERROR;
                    }
                    $booking->driver_name = $driverName;
                    $booking->rego = $rego;
                    $booking->company_name = $companyName;
                    $booking->company_email = $companyEmail;
                    $booking->note = $specialInstructions;
                    $booking->fee = round(floatval($data['data']['baseAmount']), 2);
                    $booking->booking_number = $bookingNumber;
                    $booking->shipment_id = 0;
                    $booking->depot_id = $depot;
                    $booking->ref = $hbn;
                    $booking->mdata['depot'] = $depot;
                    $booking->mdata['container_no'] = $containerNo;
                    $booking->mdata['house_bl'] = $hbn;
                    $booking->mdata['pre_processed'] = $preProcessed;
                    $booking->save();
                    // TODO: send email to someone
                    $emailService = new EmailService();
                    $emailService->sendInboundAlertEmail($booking, $containerNo);
                }
            }
        }

        if ($data['webhookEvent'] == 'App.AdditionalChargeCreatedOrUpdated') {
            switch (strtolower($data['data']['chargeCode'])) {
                case 'storage':
                    $bookingNumber = $data['data']['bookingNumber'];
                    $booking = PickupBooking::model()->findByAttributes(['booking_number' => $bookingNumber]);
                    if (!empty($booking)) {
                        if (!empty($booking->shipment_id) && $booking->shipment_id != 0) {
                            $shipment = ImParcel::model()->findByPk($booking->shipment_id);
                        } else {
                            $hbn = $data['data']['fieldValue'];
                            $shipment = ImParcel::model()->findByAttributes(['hbn' => $hbn]);
                        }
                        if (!empty($shipment)) {
                            $chargeId = $data['data']['additionalChargeId'];
                            $storageCharge = new stdClass;
                            $storageCharge->storage_paid = ($data['data']['paid'] == true) ? 1 : 0;
                            $storageCharge->storage_paid_time = !empty($data['data']['paidDateTime'])?date('Y-m-d H:i:s', strtotime($data['data']['paidDateTime'])):'';
                            $storageCharge->storage_cancelled = ($data['data']['cancelled'] == true) ? 1 : 0;
                            $storageCharge->storage_cancellationReason = $data['data']['cancellationReason'];
                            $storageCharge->storage_base_amount = $data['data']['baseAmount'];
                            $storageCharge->storage_tax_amount = $data['data']['taxAmount'];
                            $storageCharge->storage_total_including_tax = $data['data']['totalIncludingTax'];
                            $storageCharge->storage_commission = $data['data']['commission'];
                            if (isset($booking->mdata['additionalCharges'])) {
                                $booking->mdata['additionalCharges'][$chargeId] = $storageCharge;
                            } else {
                                $booking->mdata['additionalCharges'] = [];
                                $booking->mdata['additionalCharges'][$chargeId] = $storageCharge;
                            }
                            $booking->save();
                        }
                    }
                    break;
            }
        }
    }
}