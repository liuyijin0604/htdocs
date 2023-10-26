<?php
        $settings = ['payor_account'=>'80518651','serviceDescription'=>'B2C','serviceCode'=>'004'];
        $connote = $model->ref;
        
        $payor = $settings['payor_account'];
        $ps = [];
        $items = [];
        
        $total_count = count($model->mdata['eiz']['label']);
        

        $eizAPI = EizAPI::getAPIInstance($model->mdata['org_rate_id']);

        $depotCode = $eizAPI->getAlliedDepotCode($model);

        $phone = $model->cnee->tel;

        $phone = preg_replace("/[^0-9]/", "", $phone);

        $phone = sprintf("%010d", $phone);

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
        $sender_address = $prepareData->from_address1.",".$prepareData->from_suburb.','.$prepareData->from_state.' '.$prepareData->from_postcode;
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
                        $trackingNumber = $model->mdata['eiz']['label'][$kk]['labelNumber'].'-'.str_pad($kk+1,3,'0',STR_PAD_LEFT);
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
                    $trackingNumber = $model->mdata['eiz']['label'][$kk]['labelNumber'].'-'.str_pad($kk+1,3,'0',STR_PAD_LEFT);
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


?>


^XA

^PW812
^LL1218
~SD07
^PR4

^FO20,50^AAN,20,10,^TBN,400,150^FH^FDFrom:<?=$sender_name?>^FS
^FO200,50^AAN,20,10,^TBN,400,150^FH^FDP/U Phone:<?=$sender_phone?>^FS
^FO550,50^AAN,20,10,^TBN,400,150^FH^FDDate:<?=$desp?>^FS

^FO20,100^AAN,20,10,^TBN,800,150^FH^FD<?=$sender_address?>^FS

^FO20,150^AAN,20,10,^TBN,400,150^FH^FDContact:<?=$sender_contact?>^FS
^FO200,150^AAN,20,10,^TBN,400,150^FH^FDPhone:<?=$sender_phone?>^FS
^FO550,150^AAN,20,10,^TBN,400,150^FH^FDPackage:<?=$sn?> of <?=$total_count?>^FS

^FO20,200^AAN,20,10,^TBN,800,150^FH^FDParcel ID:<?=$item->trackingNumber?>^FS

^FO20,250^AAN,30,15,^TBN,400,150^FH^FDCref:<?=$model->cref?>^FS
^FO500,250^AAN,20,10,^TBN,400,150^FH^FDOrderRef:^FS

^BY3,2,100
^FO20,300^BC,100,N^FD<?=$item->trackingNumber?>^FS

^FO20,430^AAN,30,15,^TBN,400,150^FH^FD<?=$item->trackingNumber?>^FS
^FO300,440^AAN,20,10,^TBN,400,150^FH^FDservice:^FS
^FO430,430^AAN,20,10,^TBN,400,150^FH^FDRoad^FS
^FO540,420^AAN,20,10,^TBN,400,150^FH^FDPackage:<?=$sn?> of <?=$total_count?>^FS
^FO550,450^AAN,20,10,^TBN,400,150^FH^FDWeight:<?=$item->weight?>^FS

^FO20,490^AAN,30,15,^TBN,400,150^FH^FDTO: <?=$receiver_name?>^FS
^FO90,530^AAN,20,10,^TBN,400,150^FH^FD<?=$receiver_street1.' '.$receiver_street2?>^FS 

^FO20,660^AAN,20,10,^TBN,400,150^FH^FDItem <?=$sn.':' . $item->length.'x'.$item->width.'x'.$item->height . '=' . (($item->length*$item->width*$item->height)/1000000) .'('.$sn.')'?>^FS 

^FO400,660^AAN,30,15,^TBN,400,150^FH^FD<?=$receiver_suburb?>^FS 
^FO400,720^AAN,30,15,^TBN,400,150^FH^FD<?= $receiver_state.' '.$depotCode.' '.$receiver_postcode?>^FS 

^FO20,860^AAN,20,10,^TBN,400,150^FH^FDSpecial instruction:^FS 

^FO300,890^AAN,20,10,^TBN,400,150^FH^FDDate: <?=$desp?>^FS 
^FO600,890^AAN,20,10,^TBN,400,150^FH^FDDate^FS 

^FO20,940^AAN,20,10,^TBN,400,150^FH^FDName^FS 
^FO300,940^AAN,20,10,^TBN,400,150^FH^FDSignature^FS 
^FO600,940^AAN,20,10,^TBN,400,150^FH^FDTime^FS 

^FO150,1060^AAN,15,5,^TBN,800,150^FH^FDRECEIVED IN GOOD CONDITION. SUBJECT TO CARRIER TERMS AND CONDITIONS.^FS 

^FO20,1100^AAN,30,15,^TBN,400,150^FH^FD<?=$model->hbn?>^FS 
^FO20,1150^AAN,30,15,^TBN,400,150^FH^FD<?=@$model->mdata['oref']?>^FS 

^FO600,1120^AAN,50,20,^TBN,400,150^FH^FDALLIED^FS 

^LRY
^FO400,420
^GB110,50,25^FS 

^FO650,905^GB120,1,1^FS 

^FO70,955^GB120,1,1^FS 
^FO410,955^GB120,1,1^FS 
^FO650,955^GB120,1,1^FS 

^FO20,1080^GB750,1,1^FS 
^FO570,1100^GB200,70,3^FS 

^XZ