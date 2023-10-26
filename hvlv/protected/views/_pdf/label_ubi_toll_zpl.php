<?php
        $settings = ['payor_account'=>'80518651','serviceDescription'=>'B2C','serviceCode'=>'004'];
        $connote = $model->ref;
        
        $payor = $settings['payor_account'];
        $ps = [];
        $items = [];
        
        $total_count = count($model->mdata['eiz']['label']);
        
        $sequence = 0;

        $eizAPI = EizAPI::getAPIInstance($model->mdata['org_rate_id']);

        $depotCode = $eizAPI->getZone($model);

        $phone = $model->cnee->tel;

        $phone = preg_replace("/[^0-9]/", "", $phone);

        $phone = sprintf("%010d", $phone);
        //phone

        $desp = date('d/m/Y');
        $receiver_name = $model->cnee->name;
        $receiver_street1 = $model->cnee->address;
        $receiver_street2 = '';
        $receiver_suburb = $model->cnee->suburb;
        $receiver_postcode = $model->cnee->postcode;
        $receiver_state = $model->cnee->state;
        $receiver_phone = $phone;
        $receiver_contact = $model->cnee->name;

        $prepareData = $eizAPI->prepareData($model);
        $sender_name = !empty($model->agent->extra['delivery_label_name'])?$model->agent->extra['delivery_label_name']:$prepareData->from_name;

        $sender_suburb_state = $prepareData->from_suburb ." ".$prepareData->from_state;
        $sender_country = $prepareData->from_country;
        $sender_postcode = $prepareData->from_postcode;
        $sender_phone = $prepareData->from_phone;
        $sender_contact = !empty($model->agent->extra['delivery_label_name'])?$model->agent->extra['delivery_label_name']:$prepareData->from_name;
        $consignmentObj = $prepareData->consignments[0]->data;
        $shippingMethodCode = 'Road express';
        $description_detail = ' ';
        $declaration_person = $prepareData->from_name;
        $dangerous_good_declarartion = 'I hereby declare that this consingment does not contain dangerous goods';
        $ref = $model->hbn;
        $instruction = '';

        $item_number = 0;

        $volumn = 0;

        $result = [];

        $items = [];

            $mitems = $model->mdata['eiz']['package'];
            $mitems = json_encode($mitems);
            $mitems = json_decode($mitems);
            $kk = 0;
            foreach ($mitems as $key => $item) {
                $refCSLocation = '';
                $refCSSku = '';
                $refCSSkuQty = '';

                $volumn = $item->width * $item->length * $item->height / 1000;

                $item_weight = sprintf("%06d", round($item->weight, 1) * 10);

                $srg = 'N';
                $nsr = 'N';
 
                if ($settings['serviceDescription'] == "B2C"){
                    $receiver = "R";
                    $adp = 'Y';
                } else {
                    $receiver = "B";
                    $adp = 'N';
                }
                $item_number += $item->qty;
                if ($item->qty > 1) {
                    for ($i = 0; $i < $item->qty; $i++) {
                        $trackingNumber = $model->mdata['eiz']['label'][$kk]['labelNumber'];
                        $items[] = [
                            'labelId' => $model->mdata['eiz']['label'][$kk]['id'],
                            'trackingNumber' => $trackingNumber,
                            'qty' => $item->qty,
                            'width' => $item->width,
                            'weight' => $item->weight,
                            'height' => $item->height,
                            'length' => $item->length,
                            'qrcode' => str_pad($trackingNumber, 22) . str_pad("B", 1) . str_pad($connote, 20) . str_pad(date('ymd'), 6) . str_pad($item_weight, 6) . str_pad(' ', 2) . str_pad(sprintf("%04d", $item->length), 4) . str_pad(sprintf("%04d", $item->width), 4) . str_pad(sprintf("%04d", $item->height), 4) . str_pad(sprintf("%05d", $volumn), 5) . str_pad($settings['serviceCode'], 3) . str_pad(' ', 18) . str_pad('N', 1) . str_pad('N', 1) . str_pad(' ', 10) . str_pad($adp, 1) . str_pad($nsr, 1) . str_pad('N', 1) . str_pad($srg, 1) . str_pad('N', 1) . str_pad(' ', 4) . str_pad('N', 11) . str_pad('S', 1) . str_pad($payor, 10) . str_pad(' ', 9) . str_pad($receiver_name, 40) . str_pad($receiver_street1, 30) . str_pad($receiver_street2, 30) . str_pad($receiver_suburb, 30) . str_pad($receiver_postcode, 10) . str_pad('AU', 2) . str_pad(' ', 14) . str_pad($receiver, 1) . str_pad($receiver_phone, 10) . str_pad(' ', 18) . str_pad('TF2', 3),
                            'refCSLocation' => $refCSLocation,
                            'refCSSku' => $refCSSku,
                            'refCSSkuQty' => $refCSSkuQty
                        ];
                        $kk++;
                    }
                } else {
                    $trackingNumber = $model->mdata['eiz']['label'][$kk]['labelNumber'];
                    $items[] = [
                        'labelId' => $model->mdata['eiz']['label'][$kk]['id'],
                        'trackingNumber' => $trackingNumber,
                        'qty' => $item->qty,
                        'width' => $item->width,
                        'weight' => $item->weight,
                        'height' => $item->height,
                        'length' => $item->length,
                        'qrcode' => str_pad($trackingNumber, 22) . str_pad("B", 1) . str_pad($connote, 20) . str_pad(date('ymd'), 6) . str_pad($item_weight, 6) . str_pad(' ', 2) . str_pad(sprintf("%04d", $item->length), 4) . str_pad(sprintf("%04d", $item->width), 4) . str_pad(sprintf("%04d", $item->height), 4) . str_pad(sprintf("%05d", $volumn), 5) . str_pad($settings['serviceCode'], 3) . str_pad(' ', 18) . str_pad('N', 1) . str_pad('N', 1) . str_pad(' ', 10) . str_pad($adp, 1) . str_pad($nsr, 1) . str_pad('N', 1) . str_pad($srg, 1) . str_pad('N', 1) . str_pad(' ', 4) . str_pad('N', 11) . str_pad('S', 1) . str_pad($payor, 10) . str_pad(' ', 9) . str_pad($receiver_name, 40) . str_pad($receiver_street1, 30) . str_pad($receiver_street2, 30) . str_pad($receiver_suburb, 30) . str_pad($receiver_postcode, 10) . str_pad('AU', 2) . str_pad(' ', 14) . str_pad($receiver, 1) . str_pad($receiver_phone, 10) . str_pad(' ', 18) . str_pad('TF2', 3),
                        'refCSLocation' => $refCSLocation,
                        'refCSSku' => $refCSSku,
                        'refCSSkuQty' => $refCSSkuQty
                    ];
                    $kk++;
                }
            }
           
        $ii = json_encode($items);
        $items = json_decode($ii);


            $item = $items[$sn-1];

            $package_dimensions = 'L:' . $item->length . 'cm &nbsp;&nbsp;W:' . $item->width . 'cm &nbsp;&nbsp;H:' . $item->height;
            $item_weight = $item->weight . "KG";


            $receiver_street1 = isset($receiver_street1) ? $receiver_street1 : '';
            $receiver_street2 = isset($receiver_street2) ? $receiver_street2 : '';

            $instruction = isset($instruction) ? $instruction : '';

            $strAdp = ''; 
            if($settings['serviceDescription'] == "B2C"){
                $strAdp = 'ADP';
            }

            $strNsr = 'NSR';//
            if($srg == 'Y'){
                $strNsr = 'Signature Required';
            }




?>
^XA

^PW812
^LL1218
~SD07
^PR4

^CFA,40
^FO50,40^FDTOLL^FS
^CFA,20
^FO250,40^FDIPEC^FS
^FO450,40^FDDESP:<?=$desp?>^FS

^LRY
^FO10,80
^GB550,50,25^FS
^CFA,20
^FO30,100^FD<?=$shippingMethodCode?>^FS

^LRY
^FO580,70
^GB100,60,3^FS
^CFA,25
^FO600,90^FD<?=$depotCode?>^FS

^LRY
^FO700,70
^GB80,60,30^FS
^CFA,35
^FO730,90^FDR^FS

^FO410,10
^BQN,2,6
^FDMM,A<?=$item->qrcode?>^FS


^CFA,20
^FO20,150^FDCONNOTE#:^FS
^CFA,30
^FO130,150^FD<?= $connote?>^FS
^CFA,20
^FO20,200^FDTO : <?=$receiver_name?>^FS
^FO20,250^FD<?= $receiver_street1?>^FS
^FO20,270^FD<?= $receiver_street2?>^FS

^FO20,330^FD<?=$receiver_suburb?>^FS
^CFA,25
^FO20,350^FD<?=$receiver_state.' '.$receiver_postcode?>^FS
^CFA,15
^FO20,380^FDPhone: <?= $receiver_phone?>^FS
^FO20,400^FDContact: <?=$receiver_contact?>^FS

^FO20,440^FDSPECIAL INSTRUCTIONS:<?=$instruction?>^FS
^FO20,480^FDCref:<?=$model->cref?>^FS

^LRY
^FO10,500
^GB370,50,25^FS

^FO20,510^FDRELEASE DATE:<?=$desp?>^FS


^LRY
^FO10,560
^GB110,70,2^FS
^CFA,15
^FO20,580^FDDG'S NO^FS


^LRY
^FO130,560
^GB110,70,2^FS
^CFA,25
^FO140,580^FD<?=$strAdp?>^FS

^LRY
^FO250,560
^GB110,70,2^FS
^CFA,25
^FO260,580^FD<?=$strNsr?>^FS

^LRY
^FO400,560
^GB130,70,2^FS
^CFA,25
^FO410,580^FD<?=$sn?>of<?=$total_count?>^FS

^LRY
^FO550,560
^GB110,70,2^FS
^CFA,15
^FO560,570^FDL:<?=$item->length?>cm^FS
^FO560,590^FDW:<?=$item->width?>cm^FS
^FO560,610^FDH:<?=$item->height?>cm^FS

^LRY
^FO670,560
^GB110,90,45^FS
^CFA,25
^FO680,600^FD<?=$item_weight?>^FS

^FB300,,,^FO0,750^AQR,1,1^FDCarrier Terms Clause^FS

^BY3,2,170
^FO80,650^BC,170,N^FD<?="421036" . $receiver_postcode. "403" . $settings['serviceCode']?>^FS
^CFA,15
^FO250,830^FD<?='(421) 036' . $receiver_postcode . ' (403) ' . $settings['serviceCode']?>^FS

^BY2,2,170
^FO80,850^BC,170,N^FD<?= $item->trackingNumber?>^FS
^CFA,15
^FO250,1030^FD<?='(00) ' . substr($item->trackingNumber, 2)?>^FS

^FO10,1050^GB790,2,2^FS



^CFA,15
^FO20,1060^FDFrom: <?=$sender_name?>^FS
^FO350,1060^FD1/233 Milperra Rd^FS


^FO20,1080^FD<?=$sender_suburb_state?>^FS


^FO20,1100^FD<?=$sender_postcode ?> <?=$sender_country?>^FS


^CFA,25
^FO20,1120^FDREF:<?=$ref?>^FS

^CFA,15
^FO20,1150^FDDescription of Goods:<?=$description_detail?>^FS

^FO20,1170^FDDECLARATION BY:     <?=$declaration_person?>^FS

^CFA,15,7
^FO20,1190^FD<?=$dangerous_good_declarartion?>^FS


^XZ