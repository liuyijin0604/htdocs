<?php

class CargoAddressConfirmationController extends Controller
{
    protected $nonAjax = array('cargoConfirmValidate', 'cargoConfirm', 'getAddressBook', 'changeAddress', 'summary', 'confirm', 'paymentSuccess', 'confirmedSummary');
    protected $skipAcl = array('cargoConfirmValidate', 'cargoConfirm', 'getAddressBook', 'changeAddress', 'summary', 'confirm', 'paymentSuccess', 'confirmedSummary');
    protected $skipLogin = array('cargoConfirmValidate', 'cargoConfirm', 'getAddressBook', 'changeAddress', 'summary', 'confirm', 'paymentSuccess', 'confirmedSummary');
    protected $noRecaptha = array('cargoConfirmValidate', 'cargoConfirm', 'getAddressBook', 'changeAddress', 'summary', 'confirm', 'paymentSuccess', 'confirmedSummary');

    protected $org, $type;

    public function beforeAction($action)
    {
        $this->layout = "ims";
        if (!Yii::app()->user->isGuest) {
            $this->org = Org::model()->findByPk(Yii::app()->user->org);
            $this->type = 'im';
        }
        return parent::beforeAction($action);
    }

    public function actionCargoConfirmValidate()
    {
        if (!empty($_GET['ref'])) {
            $ref = $_GET['ref'];
            $p = ImParcel::model()->find('ref = :ref', [":ref" => $ref]);
            echo "<script>function postcodeVeryfi() {
                var postcode;
                var systempostcode = '{$p->cnee->postcode}'
                postcode = prompt('Enter Your Postcode.')
                if (systempostcode == postcode) {
                    window.location.href='https://ims.toplogistics.com.au/cargoAddressConfirmation/cargoConfirm?ref={$ref}';
                } else {
                    return false;
                }
                return false;
            }
            postcodeVeryfi();</script>";
        }
    }

    public function actionCargoConfirm()
    {
        $this->layout = false;
        if (!empty($_GET['ref'])) {
            $shipment = ImParcel::model()->findByAttributes(array('ref' => $_GET['ref']));
            if (!empty($shipment)) {
                $cargoProcess = CargoProcess::model()->findByAttributes(array('shipment_id' => $shipment->id));
                if (!empty($cargoProcess)) {
                    if (!empty($cargoProcess->mdata['customer_confirmed']) && !empty($cargoProcess->mdata['responsed_notice'])) {
                        $this->render('summary', array('cargoProcess' => $cargoProcess));
                    } else {
                        $this->render('cargo_confirm', array('shipment' => $shipment, 'cnee' => $shipment->cnee));
                    }
                } else {
                    $this->render('error_page');
                }
            } else {
                $this->render('error_page');
            }
        } else {
            $this->render('error_page');
        }
    }

    public function actionGetAddressBook()
    {
        $res = new stdClass;
        if (!empty($_POST['tel'])) {
            $tel = Addr::auPhoneValid($_POST['tel']);
            if (!empty($tel)) {
                $addressBook = CargoAddressBook::model()->findByAttributes(array('tel' => $tel, 'status' => 1));
                if (!empty($addressBook)) {
                    $res->success = true;
                    $res->addressType = CargoAddressBook::ADDRESS_TYPES_MAP[$addressBook->type];
                    $res->leaveAtDoor = !empty($addressBook->mdata['authorize_to_leave']) ? 'Yes' : 'No';
                    $res->hasForklift = !empty($addressBook->mdata['has_folklift']) ? 'Yes' : 'No';
                    $res->needUnloading = !empty($addressBook->mdata['need_unloading_service']) ? 'Yes' : 'No';
                    $res->businessHours = $addressBook->mdata['open'] . ' ~ ' . $addressBook->mdata['close'];
                } else {
                    $res->success = false;
                }
            } else {
                $res->success = false;
            }
        } else {
            $res->success = false;
        }
        echo json_encode($res);
    }

    public function actionChangeAddress()
    {
        $res = new stdClass;
        $minCharge = 30.00; // minimum change address fee 30 AUD
        if (!empty($_POST)) {
            $newStreetAddress = $_POST['streetAddress'];
            $newSuburb = $_POST['suburb'];
            $newPostcode = $_POST['postcode'];
            $hbn = $_POST['hbn'];
            // Check if postcode matches suburb
            $objPostcode = Postcode::model()->find('postcode=:postcode and suburb=:suburb', [':postcode' => $newPostcode, ':suburb' => strtoupper($newSuburb)]);
            if (empty($objPostcode)) {
                $res->success = false;
                $res->msg = 'postcode and suburb not match';
                echo json_encode($res);
                return;
            }
            $shipment = ImParcel::model()->findByAttributes(array('hbn' => $hbn), 'status != ' . ImParcel::STATE_CANCELLED);
            if (!empty($shipment)) {
                $chargeWeight = $shipment->chargeWeight(); // charge weight
                // Customer cannot change delivery address to another state
                $originalPostcode = $shipment->cnee->postcode;
                if (substr($newPostcode, 0, 1) != substr($originalPostcode, 0, 1)) {
                    $res->success = false;
                    $res->msg = 'Cannot change delivery address to another state';
                    echo json_encode($res);
                    return;
                }
                // Check if new postcode is in delivery zone
                $chargecode = ImportChargeCode::model()->findByAttributes(array('chargecode' => $shipment->mdata['chargecode']));
                if (!empty($chargecode)) {
                    $newZoneMap = ZoneMap::model()->find('chargecode_id = :cid AND pc_lo <= :pc AND pc_hi >= :pc', array(':cid' => $chargecode->id, ':pc' => $newPostcode));
                    if (empty($newZoneMap) || $newZoneMap->z1 == 'N4') {
                        $res->success = false;
                        $res->msg = 'New address is not in delivery zone';
                        echo json_encode($res);
                        return;
                    }
                    $newDeliveryFee = $this->calculateNormalByWeight($chargeWeight, $chargecode, $newPostcode);
                    $originalDeliveryFee = $this->calculateNormalByWeight($chargeWeight, $chargecode, $originalPostcode);
                    if ($newDeliveryFee - $originalDeliveryFee <= 0) {
                        $changeAddressFee = $minCharge;
                    } else {
                        $changeAddressFee = $minCharge + round(($newDeliveryFee - $originalDeliveryFee), 2);
                    }
                    $res->success = true;
                    $res->streetAddress = $newStreetAddress;
                    $res->suburb = $newSuburb;
                    $res->postcode = $newPostcode;
                    $res->amount = $changeAddressFee;
                    echo json_encode($res);
                }
            }
        }
    }

    private function calculateNormalByWeight($myWeight, $chargecodeInfo, $postcode, &$selectedRate = null)
    {
        $zoneMaps = ZoneMap::model()->findAll('chargecode_id = :cid AND pc_lo <= :pc AND pc_hi >= :pc', [':cid' => $chargecodeInfo['id'], ':pc' => $postcode]);
        $amount = 0;
        foreach ($zoneMaps as $zoneMapKey => $zoneMap) {
            $amount = 0;
            $checkWeight = $myWeight;

            if ($checkWeight < 0.5) {
                $checkWeight = ceil($checkWeight * 1000) / 1000;
            } else {
                $checkWeight = ceil($checkWeight * 10) / 10;
            }

            if (!empty($zoneMap)) {
                $zoneRates = ZoneRate::model()->findAll('chargecode_id = :cid AND weight_lo <:w AND weight_hi >= :w AND zone = :z AND base+item+perkg > 0', [':cid' => $chargecodeInfo['id'], ':w' => $checkWeight, ':z' => $zoneMap['z1']]);
                if (empty($zoneRates)) {
                    $zoneRates = ZoneRate::model()->findAll('chargecode_id = :cid AND weight_lo <=:w AND weight_hi >= :w AND zone = :z AND base+item+perkg > 0', [':cid' => $chargecodeInfo['id'], ':w' => $checkWeight, ':z' => $zoneMap['z1']]);
                }
            }
            foreach ($zoneRates as $zoneRate) {
                $itemPrice = $zoneRate['item'];

                if ($zoneRate['nkg'] > 0) {
                    $temp = $zoneRate['base'] + $itemPrice + $zoneRate['perkg'] * ($zoneRate['nkg'] - fmod($myWeight, $zoneRate['nkg']) + $myWeight);
                } else {
                    $temp = $zoneRate['base'] + $itemPrice + $zoneRate['perkg'] * $myWeight;
                }
                if ($zoneRate['minimum'] > 0) {
                    $temp = max($temp, $zoneRate['minimum']);
                }
                if ($temp > $amount) {
                    //$tempAmount = $temp;
                    $selectedRate = $zoneRate;
                }
                $amount = max($amount, $temp);
            }
            if ($amount > 0) {
                break;
            }
        }
        return $amount;
    }

    public function actionSummary()
    {
        $res = new stdClass;
        if (!empty($_POST)) {
            $shipment = ImParcel::model()->findByPk($_POST['shipmentId']);
            $res->success = true;
            $changeAddress = (!empty($_POST['address_correct']) && $_POST['address_correct'] == 'incorrect') ? true : false;
            if ($changeAddress) {
                $newAddress = $_POST['change_address_st_address'];
                $newSuburb = $_POST['changed_suburb'];
                $newPostcode = $_POST['changed_postcode'];
                $changeAddressFee = $_POST['change_address_fee'];
            }

            $useAddressBookInfo = (!empty($_POST['address_book_correct']) && $_POST['address_book_correct'] == 'address_book_correct') ? true : false;
            if ($useAddressBookInfo) {
                $tel = Addr::auPhoneValid($_POST['tel']);
                $addressBook = CargoAddressBook::model()->findByAttributes(array('tel' => $tel, 'status' => 1));
                $addressType = CargoAddressBook::ADDRESS_TYPES_MAP[$addressBook->type];
                $leaveAtDoor = !empty($addressBook->mdata['authorize_to_leave']) ? true : false;
                $hasForklift = !empty($addressBook->mdata['has_folklift']) ? true : false;
                $needUnloading = !empty($addressBook->mdata['need_unloading_service']) ? true : false;
                $businessHours = $addressBook->mdata['open'] . ' ~ ' . $addressBook->mdata['close'];
            } else {
                switch ($_POST['address_type']) {
                    case 'apartment':
                        $addressType = CargoAddressBook::ADDRESS_TYPES_MAP[CargoAddressBook::ADDRESS_TYPE_APARTMENT];
                        break;
                    case 'house':
                        $addressType = CargoAddressBook::ADDRESS_TYPES_MAP[CargoAddressBook::ADDRESS_TYPE_HOUSE];
                        break;
                    case 'warehouse':
                        $addressType = CargoAddressBook::ADDRESS_TYPES_MAP[CargoAddressBook::ADDRESS_TYPE_WAREHOUSE];
                        break;
                    case 'shop_roadside':
                        $addressType = CargoAddressBook::ADDRESS_TYPES_MAP[CargoAddressBook::ADDRESS_TYPE_SHOP_ROADSIDE];
                        break;
                    case 'shop_shopping_centre':
                        $addressType = CargoAddressBook::ADDRESS_TYPES_MAP[CargoAddressBook::ADDRESS_TYPE_SHOP_SHOPPING_CENTRE];
                        break;
                    case 'shop_office':
                        $addressType = CargoAddressBook::ADDRESS_TYPES_MAP[CargoAddressBook::ADDRESS_TYPE_OFFICE_BUILDING];
                        break;
                }

                if (in_array($addressType, array('Apartment', 'House / Townhouse'))) {
                    $leaveAtDoor = (!empty($_POST['atl']) && $_POST['atl'] == 'atl_yes') ? true : false;
                    $hasForklift = false;
                } else {
                    $leaveAtDoor = false;
                    $hasForklift = (!empty($_POST['unloading']) && $_POST['unloading'] == 'has_forklift') ? true : false;
                }
                $hasSpecialDeliveryTime = (!empty($_POST['special_time']) && $_POST['special_time'] == 'has_special_time') ? true : false;
                if ($hasSpecialDeliveryTime) {
                    list($day, $month, $year) = explode('/', $_POST['delivery_date']);
                    $specialDeliveryDate = $year . '-' . $month . '-' . $day;
                    $specialDeliveryTime = $_POST['delivery_time'];

                    /**
                     * Calculate unloading fee
                     */

                    $chargeWeight = $shipment->chargeWeight(); // charge weight
                    $chargecode = ImportChargeCode::model()->findByAttributes(array('chargecode' => $shipment->mdata['chargecode']));
                    if ($changeAddress) {
                        $deliveryFee = $this->calculateNormalByWeight($chargeWeight, $chargecode, $newPostcode);
                    } else {
                        $deliveryFee = $this->calculateNormalByWeight($chargeWeight, $chargecode, $shipment->cnee->postcode);
                    }
                    $timedDeliveryFee = $deliveryFee * 0.3;
                    // timed delivery fee max 100 AUD
                    if ($timedDeliveryFee > 100) {
                        $timedDeliveryFee = 100;
                    }
                }
            }

            $res->shipmentId = $shipment->id;
            $res->changeAddress = $changeAddress;
            $res->streetAddress = !empty($newAddress) ? $newAddress : $shipment->cnee->address;
            $res->suburb = !empty($newSuburb) ? $newSuburb : $shipment->cnee->suburb;
            $res->postcode = !empty($newPostcode) ? $newPostcode : $shipment->cnee->postcode;
            $res->changeAddressFee = !empty($changeAddressFee) ? $changeAddressFee : 0;
            $res->addressType = $addressType;
            $res->leaveAtDoor = $leaveAtDoor;
            $res->hasForklift = $hasForklift;
            $res->needUnloading = !empty($needUnloading) ? $needUnloading : false;
            $res->businessHours = !empty($businessHours) ? $businessHours : '';
            $res->hasSpecialDeliveryTime = !empty($hasSpecialDeliveryTime) ? $hasSpecialDeliveryTime : false;
            $res->specialDeliveryDate = !empty($specialDeliveryDate) ? $specialDeliveryDate : '';
            $res->specialDeliveryTime = !empty($specialDeliveryTime) ? $specialDeliveryTime : '';
            $res->timedDeliveryFee = !empty($timedDeliveryFee) ? $timedDeliveryFee : 0;
            $res->specialNote = !empty($_POST['special_note']) ? $_POST['special_note'] : '';

            echo json_encode($res);
        }
    }

    public function actionConfirm()
    {
        $this->layout = false;
        if (!empty($_POST)) {
            $shipment = ImParcel::model()->findByPk($_POST['final_shipment_id']);
            $changeAddressFee = floatval($_POST['final_change_address_fee']);
            $timedDeliveryFee = floatval($_POST['final_timed_delivery_fee']);
            $totalAmount = $changeAddressFee + $timedDeliveryFee;
            if ($totalAmount <= 0) {
                $cargoProcess = CargoProcess::model()->findByAttributes(array('shipment_id' => $shipment->id));
                $confirmedInfo = new stdClass;
                $confirmedInfo->address_type = $_POST['final_address_type'];
                $confirmedInfo->leave_at_address = ($_POST['final_leave_at_door'] == 'Yes') ? 'Leave at address : yes' : 'Leave at address : no';
                $confirmedInfo->business_unloading = ($_POST['final_need_unloading'] == 'Yes') ? 'We need unloading service' : 'We can unload ourselves';
                //$confirmedInfo->OtherInquery = ()
                $confirmedInfo->hasForklift = $_POST['final_has_forklift'];
                $confirmedInfo->changeAddress = 0;
                $confirmedInfo->timedDelivery = 0;
                $confirmedInfo->OtherInquery = !empty($_POST['final_special_note']) ? $_POST['final_special_note'] : '';
                $cargoProcess->mdata['customer_confirmed'] = $confirmedInfo;
                $cargoProcess->mdata['other_inquery'] = !empty($_POST['final_special_note']) ? $_POST['final_special_note'] : '';
                $cargoProcess->mdata['custconfirmforklift'] = ($_POST['final_has_forklift'] == 'Yes') ? 1 : 0;
                $cargoProcess->mdata['responsed_notice'] = 1;
                $cargoProcess->update('meta');
                
                /**
                 * save to cargo address book
                 */
                $tel = Addr::auPhoneValid($shipment->cnee->tel);
                $addressBook = CargoAddressBook::model()->findByAttributes(array('tel' => $tel, 'status' => 1));
                if (empty($addressBook)) {
                    $addressBook = new CargoAddressBook();
                    $addressBook->tel = $tel;
                    $addressBook->mdata['address_type'] = array_search($confirmedInfo->address_type, CargoAddressBook::ADDRESS_TYPES_MAP);
                    $addressBook->mdata['authorize_to_leave'] = ($_POST['final_leave_at_door'] == 'Yes') ? 1 : 0;
                    $addressBook->mdata['has_folklift'] = ($_POST['final_has_forklift'] == 'Yes') ? 1 : 0;
                    $addressBook->mdata['need_unloading_service'] = 0;
                    $addressBook->status = 1;
                    $addressBook->type = array_search($confirmedInfo->address_type, CargoAddressBook::ADDRESS_TYPES_MAP);
                    $addressBook->save();
                } else {
                    $addressBook->mdata['address_type'] = array_search($confirmedInfo->address_type, CargoAddressBook::ADDRESS_TYPES_MAP);
                    $addressBook->mdata['authorize_to_leave'] = ($_POST['final_leave_at_door'] == 'Yes') ? 1 : 0;
                    $addressBook->mdata['has_folklift'] = ($_POST['final_has_forklift'] == 'Yes') ? 1 : 0;
                    $addressBook->mdata['need_unloading_service'] = 0;
                    $addressBook->status = 1;
                    $addressBook->type = array_search($confirmedInfo->address_type, CargoAddressBook::ADDRESS_TYPES_MAP);
                    $addressBook->save();
                }
                $this->render('payment_success');
            } else {
                $confirmedInfo = new stdClass;
                foreach ($_POST as $key => $value) {
                    $confirmedInfo->$key = $value;
                }
                $this->render('check_out', array('shipment' => $shipment, 'changeAddressFee' => $changeAddressFee, 'timedDeliveryFee' => $timedDeliveryFee, 'totalAmount' => $totalAmount, 'confirmedInfo' => $confirmedInfo));
            }
        }
    }

    public function actionPaymentSuccess()
    {
        $this->layout = false;
        $res = new stdClass;
        if (!empty($_POST)) {
            $shipment = ImParcel::model()->findByPk($_POST['shipmentId']);
            if (!empty($shipment)) {
                $isChangeAddress = ($_POST['changeAddress'] == 'Yes') ? 1 : 0;
                if (!empty($isChangeAddress)) {
                    // Change delivery address
                    // save original cnee info to receiver
                    $streetAddress = $_POST['streetAddress'];
                    $suburb = $_POST['suburb'];
                    $postcode = $_POST['postcode'];
                    $shipment->note .= "Delivery address changed to " . $streetAddress . " " . $suburb . " " . $postcode;   // save change address info to shipment's note
                    $receiver = new Addr();
                    $receiver->owner_id = $shipment->cnee->owner_id;
                    $receiver->cnid_id = $shipment->cnee->cnid_id;
                    $receiver->name = $shipment->cnee->name;
                    $receiver->company = $shipment->cnee->company;
                    $receiver->address = $shipment->cnee->address;
                    $receiver->suburb = $shipment->cnee->suburb;
                    $receiver->city = $shipment->cnee->city;
                    $receiver->state = $shipment->cnee->state;
                    $receiver->postcode = $shipment->cnee->postcode;
                    $receiver->country = $shipment->cnee->country;
                    $receiver->tel = $shipment->cnee->tel;
                    $receiver->email = $shipment->cnee->email;
                    $receiver->acc = $shipment->cnee->acc;
                    $receiver->cnid_no = $shipment->cnee->cnid_no;
                    $receiver->save();
                    $shipment->receiver_id = $receiver->id;
                    // update cnee address
                    $shipment->cnee->address = $streetAddress;
                    $shipment->cnee->suburb = $suburb;
                    $shipment->cnee->postcode = $postcode;
                    $shipment->cnee->save();
                    $shipment->save();
                }

                $cargoProcess = CargoProcess::model()->findByAttributes(array('shipment_id' => $shipment->id));
                $confirmedInfo = new stdClass;
                $confirmedInfo->address_type = $_POST['addressType'];
                $confirmedInfo->leave_at_address = ($_POST['leaveAtDoor'] == 'Yes') ? 'Leave at address : yes' : 'Leave at address : no';
                $confirmedInfo->business_unloading = ($_POST['needUnloading'] == 'Yes') ? 'We need unloading service' : 'We can unload ourselves';
                $confirmedInfo->hasForklift = $_POST['hasForklift'];
                $confirmedInfo->changeAddress = $isChangeAddress ? 1 : 0;
                $confirmedInfo->OtherInquery = !empty($_POST['note']) ? $_POST['note'] : '';
                if (!empty($_POST['timedDelivery']) && $_POST['timedDelivery'] == 'Yes') { // for timed delivery service
                    $confirmedInfo->timedDelivery = 1;
                    $confirmedInfo->timed_delivery_date = !empty($_POST['deliveryDate']) ? $_POST['deliveryDate'] : '';
                    $confirmedInfo->timed_delivery_time = !empty($_POST['deliveryTime']) ? $_POST['deliveryTime'] : '';
                } else {
                    $confirmedInfo->timedDelivery = 0;
                }
                $cargoProcess->mdata['customer_confirmed'] = $confirmedInfo;
                $cargoProcess->mdata['other_inquery'] = !empty($_POST['note']) ? $_POST['note'] : '';
                $cargoProcess->mdata['custconfirmforklift'] = ($_POST['hasForklift'] == 'Yes') ? 1 : 0;

                $cargoProcess->mdata['responsed_notice'] = 1;
                $cargoProcess->update('meta');

                /**
                 * save to cargo address book
                 */
                $tel = Addr::auPhoneValid($shipment->cnee->tel);
                $addressBook = CargoAddressBook::model()->findByAttributes(array('tel' => $tel, 'status' => 1));
                if (empty($addressBook)) { // create a new one
                    $addressBook = new CargoAddressBook();
                    $addressBook->tel = $tel;
                    $addressBook->mdata['address_type'] = array_search($confirmedInfo->address_type, CargoAddressBook::ADDRESS_TYPES_MAP);
                    $addressBook->mdata['authorize_to_leave'] = ($_POST['leaveAtDoor'] == 'Yes') ? 1 : 0;
                    $addressBook->mdata['has_folklift'] = ($_POST['hasForklift'] == 'Yes') ? 1 : 0;
                    $addressBook->mdata['need_unloading_service'] = 0;
                    $addressBook->status = 1;
                    $addressBook->type = array_search($confirmedInfo->address_type, CargoAddressBook::ADDRESS_TYPES_MAP);
                    $addressBook->save();
                } else {    // update existing one
                    $addressBook->mdata['address_type'] = array_search($confirmedInfo->address_type, CargoAddressBook::ADDRESS_TYPES_MAP);
                    $addressBook->mdata['authorize_to_leave'] = ($_POST['leaveAtDoor'] == 'Yes') ? 1 : 0;
                    $addressBook->mdata['has_folklift'] = ($_POST['hasForklift'] == 'Yes') ? 1 : 0;
                    $addressBook->mdata['need_unloading_service'] = 0;
                    $addressBook->status = 1;
                    $addressBook->type = array_search($confirmedInfo->address_type, CargoAddressBook::ADDRESS_TYPES_MAP);
                    $addressBook->save();
                }

                /**
                 * Create invoice for customer payment
                 */
                $totalAmount = floatval($_POST['totalAmount']);
                $changeAddressFee = floatval(($_POST['changeAddressFee']));
                $timedDeliveryFee = floatval($_POST['timedDeliveryFee']);
                $gst = ($changeAddressFee + $timedDeliveryFee) * 0.1;
                $invoiceService = new InvoiceService();
                $invoiceService->createChangeAddressInvoice($shipment, $totalAmount, $changeAddressFee, $timedDeliveryFee, $gst);
            }
            $res->success = true;
            echo json_encode($res);
        }
        $this->render('payment_success');
    }

    public function actionConfirmedSummary()
    {
        $this->layout = false;
        if (!empty($_GET['ref'])) {
            $shipment = ImParcel::model()->findByAttributes(array('ref' => $_GET['ref']));
            $cargoProcess = CargoProcess::model()->findByAttributes(array('shipment_id' => $shipment->id));
            if (!empty($cargoProcess)) {
                $this->render('summary', array('cargoProcess' => $cargoProcess));
            }
            else {
                $this->render('error_page');
            }
        }
        
    }
}
