<?php
class FedExAPI {



    public function Authorization()
    {
        $fedExKey = SystemSetting::model()->findByAttributes(['key'=>'fedex_api_key']);
        $api_key = $fedExKey->mdata['api_key'];
        $api_secret = $fedExKey->mdata['api_secret'];
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://apis-sandbox.fedex.com/oauth/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials&client_id=' . $api_key . '&client_secret=' . $api_secret,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        print_r($response);
        $token = $response->access_token;
        return $token;
    }

    public function validateShipment($shipment)
    {
        // Get FedEx account number
        $fedExKey = SystemSetting::model()->findByAttributes(['key'=>'fedex_api_key']);
		$accNo = $fedExKey->mdata['accNo'];
        // Get access token
        $fedexToken = Service::getCacheData('Fedex_Token');
		if (!empty($fedexToken)) {
			$fedexToken = json_decode($fedexToken);
			$token = $fedexToken->token;
		} else {
			$token = $this->Authorization();
			Service::setCacheData('Fedex_Token', '{"token": "'. $token . '"}', 3590);   // save token to redis, token expires in 1 hour
		}

        $postFields = new stdClass;
        $postFields->mergeLabelDocOption = "LABELS_AND_DOCS";
        $postFields->requestedShipment = new stdClass;
        $postFields->requestedShipment->shipper = new stdClass;
        $postFields->requestedShipment->shipper->address = new stdClass;
        $postFields->requestedShipment->shipper->contact = new stdClass; 
        $postFields->requestedShipment->shippingChargesPayment = new stdClass;
        $postFields->requestedShipment->customsClearanceDetail = new stdClass;
        $postFields->requestedShipment->shippingDocumentSpecification = new stdClass;
        $postFields->requestedShipment->labelSpecification = new stdClass;
        $postFields->requestedShipment->shipmentSpecialServices = new stdClass;
        $postFields->requestedShipment->shipmentSpecialServices->etdDetail = new stdClass;
        $postFields->accountNumber = new stdClass;

        $postFields->requestedShipment->shipDatestamp = date('Y-m-d');
        $postFields->requestedShipment->pickupType = 'USE_SCHEDULED_PICKUP';
        $postFields->requestedShipment->serviceType = 'FEDEX_INTERNATIONAL_PRIORITY_EXPRESS';
        $postFields->requestedShipment->packagingType = 'YOUR_PACKAGING';
        $postFields->requestedShipment->totalWeight = $shipment->weight;
        $postFields->requestedShipment->shipper->address->streetLines = ['3/47-53 Moxon Road'];
        $postFields->requestedShipment->shipper->address->city = 'Punchbowl';
        //$postFields->requestedShipment->shipper->address->stateOrProvinceCode = 'NSW';
        $postFields->requestedShipment->shipper->address->postalCode = '2196';
        $postFields->requestedShipment->shipper->address->countryCode = 'AU';
        $postFields->requestedShipment->shipper->contact->personName = 'John Taylor';
        $postFields->requestedShipment->shipper->contact->phoneNumber = '0400000000';
        $postFields->requestedShipment->shipmentSpecialServices->specialServiceTypes = ["ELECTRONIC_TRADE_DOCUMENTS"];
        //$postFields->requestedShipment->shipmentSpecialServices->etdDetail->attributes = "POST_SHIPMENT_UPLOAD_REQUESTED";
        $postFields->requestedShipment->shipmentSpecialServices->etdDetail->requestedDocumentTypes = ["COMMERCIAL_INVOICE"];

        $recipient = new stdClass;
        $recipient->address = new stdClass;
        $recipient->contact = new stdClass;

        $recipient->address->streetLines = [$shipment->cnee->address];
        $recipient->address->city = $shipment->cnee->city;
        $recipient->address->stateOrProvinceCode = $shipment->cnee->state;
        $recipient->address->postalCode = $shipment->cnee->postcode;
        $recipient->address->countryCode = $shipment->cnee->country;
        //$recipient->address->residential = false;
        $recipient->contact->personName = $shipment->cnee->name;
        $recipient->contact->emailAddress = !empty($shipment->cnee->email)?$shipment->cnee->email:'';
        //$recipient->contact->phoneExtension = '000';
        $recipient->contact->phoneNumber = $shipment->cnee->tel;
        $recipient->contact->companyName = !empty($shipment->cnee->company)?$shipment->cnee->company:'';
        $postFields->requestedShipment->recipients = [];
        array_push($postFields->requestedShipment->recipients, $recipient);

        $postFields->requestedShipment->shippingChargesPayment->paymentType = "SENDER";

        // $postFields->requestedShipment->customsClearanceDetail->regulatoryControls = 'NOT_IN_FREE_CIRCULATION';

        $postFields->requestedShipment->shippingDocumentSpecification->shippingDocumentTypes = ['COMMERCIAL_INVOICE'];
        $postFields->requestedShipment->shippingDocumentSpecification->commercialInvoiceDetail = new stdClass;
        $postFields->requestedShipment->shippingDocumentSpecification->commercialInvoiceDetail->documentFormat = new stdClass;
        $letterHead = new stdClass;
        $letterHead->id = "IMAGE_1";
        $letterHead->type = "LETTER_HEAD";
        $letterHead->providedImageType = "LETTER_HEAD";

        $postFields->requestedShipment->shippingDocumentSpecification->commercialInvoiceDetail->customerImageUsages = [$letterHead];
        $postFields->requestedShipment->shippingDocumentSpecification->commercialInvoiceDetail->documentFormat->stockType = "PAPER_LETTER";
        $postFields->requestedShipment->shippingDocumentSpecification->commercialInvoiceDetail->documentFormat->locale = "en_US";
        $postFields->requestedShipment->shippingDocumentSpecification->commercialInvoiceDetail->documentFormat->docType = "PDF";
        // $postFields->requestedShipment->labelSpecification->labelFormatType = 'COMMON2D';
        // $postFields->requestedShipment->labelSpecification->labelOrder = 'SHIPPING_LABEL_FIRST';
        $postFields->requestedShipment->labelSpecification->labelStockType = 'PAPER_4X6';
        $postFields->requestedShipment->labelSpecification->imageType = 'PDF';

        $postFields->requestedShipment->customsClearanceDetail->totalCustomsValue = new stdClass;
        $postFields->requestedShipment->customsClearanceDetail->totalCustomsValue->amount = $shipment->dvalue;
        $currency = '';
        switch ($shipment->currency) {
            case 1:
                $currency = 'AUD';
                break;
            case 2:
                $currency = 'USD';
                break;
            case 3:
                $currency = 'CNY';
                break;
            case 4:
                $currency = 'HKD';
                break;
            case 5:
                $currency = 'EUR';
                break;
            default:
                $currency  ='AUD';
        }
        $postFields->requestedShipment->customsClearanceDetail->totalCustomsValue->currency = $currency;

        $postFields->requestedShipment->customsClearanceDetail->dutiesPayment = new stdClass;
        $postFields->requestedShipment->customsClearanceDetail->dutiesPayment->paymentType = "RECIPIENT";

        $postFields->requestedShipment->customsClearanceDetail->commodities = [];
        $items = json_decode($shipment->items);
        for ($i = 0; $i < count($items->g); $i++) {
            $commodity = new stdClass;
            $commodity->unitPrice = new stdClass;
            $commodity->customsValue = new stdClass;
            $commodity->weight = new stdClass;
            //$commodity->description = $items->g[$i];
            $commodity->description = 'Commodity description: ' . $items->g[$i];
            $commodity->name = $items->g[$i];
            $commodity->quantity = $items->q[$i];
            $commodity->quantityUnits = "PCS";
            $commodity->countryOfManufacture = "AU";
            $commodity->unitPrice->amount = $items->v[$i];
            $commodity->unitPrice->currency = $currency;
            $commodity->customsValue->amount = $items->v[$i];
            $commodity->customsValue->currency = $currency;
            $commodity->weight->units = "KG";
            $commodity->weight->value = 1;
            array_push($postFields->requestedShipment->customsClearanceDetail->commodities, $commodity);
        }
        
        // $commodity->description = "Commodity description";
        // $commodity->countryOfManufacture = "US";
        // $commodity->quantity = 1;
        // $commodity->quantityUnits = "PCS";
        // $commodity->unitPrice = new stdClass;
        // $commodity->unitPrice->amount = 100;
        // $commodity->unitPrice->currency = "USD";
        // $commodity->customsValue = new stdClass;
        // $commodity->customsValue->amount = 100;
        // $commodity->customsValue->currency = "USD";
        // $commodity->weight = new stdClass;
        // $commodity->weight->units = "KG";
        // $commodity->weight->value = 20;

        // array_push($postFields->requestedShipment->customsClearanceDetail->commodities, $commodity);


        // $postFields->requestedShipment->preferredCurrency = 'AUD';


        $packages = json_decode($shipment->packages);
        $postFields->requestedShipment->requestedPackageLineItems = [];
        foreach ($packages as $package) {
            $item = new stdClass;
            $item->weight = new stdClass;
            $item->dimensions = new stdClass;
            $item->weight->units = 'KG';
            $item->weight->value = $package->weight;
            $item->dimensions->units = "CM";
            $item->dimensions->length = $package->length;
            $item->dimensions->width = $package->width;
            $item->dimensions->height = $package->height;

            array_push($postFields->requestedShipment->requestedPackageLineItems, $item);
        }
        

        $postFields->accountNumber->value = $accNo;

        $postFields = json_encode($postFields);

        //echo $postFields;

        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://apis-sandbox.fedex.com/ship/v1/shipments/packages/validate',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => array(
            'Authorization:  Bearer ' . $token,
            'X-locale: en_US',
            'Content-Type: application/json',
        ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        print_r($response);
    }

    public function createShipment($shipment)
    {
        // Get FedEx account number
        $fedExKey = SystemSetting::model()->findByAttributes(['key'=>'fedex_api_key']);
		$accNo = $fedExKey->mdata['accNo'];
        // Get access token
        $fedexToken = Service::getCacheData('Fedex_Token');
		if (!empty($fedexToken)) {
			$fedexToken = json_decode($fedexToken);
			$token = $fedexToken->token;
		} else {
			$token = $this->Authorization();
			Service::setCacheData('Fedex_Token', '{"token": "'. $token . '"}', 3590);   // save token to redis, token expires in 1 hour
		}

        $postFields = new stdClass;
        $postFields->mergeLabelDocOption = "LABELS_ONLY";
        $postFields->requestedShipment = new stdClass;
        $postFields->requestedShipment->shipper = new stdClass;
        $postFields->requestedShipment->shipper->address = new stdClass;
        $postFields->requestedShipment->shipper->contact = new stdClass; 
        $postFields->requestedShipment->shippingChargesPayment = new stdClass;
        $postFields->requestedShipment->customsClearanceDetail = new stdClass;
        $postFields->requestedShipment->labelSpecification = new stdClass;
        $postFields->requestedShipment->shipmentSpecialServices = new stdClass;
        $postFields->requestedShipment->shipmentSpecialServices->etdDetail = new stdClass;
        $postFields->requestedShipment->shippingDocumentSpecification = new stdClass;
        $postFields->accountNumber = new stdClass;

        $postFields->requestedShipment->shipDatestamp = date('Y-m-d');
        $postFields->requestedShipment->pickupType = 'USE_SCHEDULED_PICKUP';
        $postFields->requestedShipment->serviceType = 'FEDEX_INTERNATIONAL_PRIORITY_EXPRESS';
        $postFields->requestedShipment->packagingType = 'YOUR_PACKAGING';
        $postFields->requestedShipment->totalWeight = $shipment->weight;
        $postFields->requestedShipment->shipper->address->streetLines = ['47-53 Moxon Road', 'Dock 3'];
        $postFields->requestedShipment->shipper->address->city = 'Punchbowl';
        //$postFields->requestedShipment->shipper->address->stateOrProvinceCode = 'NSW';
        $postFields->requestedShipment->shipper->address->postalCode = '2196';
        $postFields->requestedShipment->shipper->address->countryCode = 'AU';
        $postFields->requestedShipment->shipper->contact->personName = 'John Taylor';
        $postFields->requestedShipment->shipper->contact->phoneNumber = '0400000000';
        $postFields->requestedShipment->shipmentSpecialServices->etdDetail->requestedDocumentTypes = ["COMMERCIAL_INVOICE"];

        $recipient = new stdClass;
        $recipient->address = new stdClass;
        $recipient->contact = new stdClass;

        $recipient->address->streetLines = [$shipment->cnee->address];
        $recipient->address->city = $shipment->cnee->city;
        $recipient->address->stateOrProvinceCode = $shipment->cnee->state;
        $recipient->address->postalCode = $shipment->cnee->postcode;
        $recipient->address->countryCode = $shipment->cnee->country;
        //$recipient->address->residential = false;
        $recipient->contact->personName = $shipment->cnee->name;
        $recipient->contact->emailAddress = !empty($shipment->cnee->email)?$shipment->cnee->email:'';
        //$recipient->contact->phoneExtension = '000';
        $recipient->contact->phoneNumber = $shipment->cnee->tel;
        $recipient->contact->companyName = !empty($shipment->cnee->company)?$shipment->cnee->company:'';
        $postFields->requestedShipment->recipients = [];
        array_push($postFields->requestedShipment->recipients, $recipient);

        $postFields->requestedShipment->shippingChargesPayment->paymentType = "SENDER";

        // $postFields->requestedShipment->customsClearanceDetail->regulatoryControls = 'NOT_IN_FREE_CIRCULATION';
        $postFields->requestedShipment->shipmentSpecialServices->specialServiceTypes = ["ELECTRONIC_TRADE_DOCUMENTS"];
        $postFields->requestedShipment->shippingDocumentSpecification->shippingDocumentTypes = ['COMMERCIAL_INVOICE'];
        $postFields->requestedShipment->shippingDocumentSpecification->commercialInvoiceDetail = new stdClass;
        $postFields->requestedShipment->shippingDocumentSpecification->commercialInvoiceDetail->documentFormat = new stdClass;
        $letterHead = new stdClass;
        $letterHead->id = "IMAGE_1";
        $letterHead->type = "LETTER_HEAD";
        $letterHead->providedImageType = "LETTER_HEAD";

        $postFields->requestedShipment->shippingDocumentSpecification->commercialInvoiceDetail->customerImageUsages = [$letterHead];
        $postFields->requestedShipment->shippingDocumentSpecification->commercialInvoiceDetail->documentFormat->stockType = "PAPER_LETTER";
        $postFields->requestedShipment->shippingDocumentSpecification->commercialInvoiceDetail->documentFormat->locale = "en_US";
        $postFields->requestedShipment->shippingDocumentSpecification->commercialInvoiceDetail->documentFormat->docType = "PDF";

        $postFields->requestedShipment->labelSpecification->labelFormatType = 'COMMON2D';
        $postFields->requestedShipment->labelSpecification->labelOrder = 'SHIPPING_LABEL_FIRST';
        $postFields->requestedShipment->labelSpecification->labelStockType = 'PAPER_4X6';
        //$postFields->requestedShipment->labelSpecification->labelRotation = 'UPSIDE_DOWN';
        $postFields->requestedShipment->labelSpecification->imageType = 'PDF';

        $postFields->requestedShipment->customsClearanceDetail->totalCustomsValue = new stdClass;
        $postFields->requestedShipment->customsClearanceDetail->totalCustomsValue->amount = $shipment->dvalue;
        $currency = '';
        switch ($shipment->currency) {
            case 1:
                $currency = 'AUD';
                break;
            case 2:
                $currency = 'USD';
                break;
            case 3:
                $currency = 'CNY';
                break;
            case 4:
                $currency = 'HKD';
                break;
            case 5:
                $currency = 'EUR';
                break;
            default:
                $currency  ='AUD';
        }
        $postFields->requestedShipment->customsClearanceDetail->totalCustomsValue->currency = $currency;

        $postFields->requestedShipment->customsClearanceDetail->dutiesPayment = new stdClass;
        $postFields->requestedShipment->customsClearanceDetail->dutiesPayment->paymentType = "RECIPIENT";

        $postFields->requestedShipment->customsClearanceDetail->commodities = [];
        $items = json_decode($shipment->items);
        for ($i = 0; $i < count($items->g); $i++) {
            $commodity = new stdClass;
            $commodity->unitPrice = new stdClass;
            $commodity->customsValue = new stdClass;
            $commodity->weight = new stdClass;
            //$commodity->description = $items->g[$i];
            $commodity->description = 'Commodity description: ' . $items->g[$i];
            $commodity->name = $items->g[$i];
            $commodity->quantity = $items->q[$i];
            $commodity->quantityUnits = "PCS";
            $commodity->countryOfManufacture = "AU";
            $commodity->unitPrice->amount = $items->v[$i];
            $commodity->unitPrice->currency = $currency;
            $commodity->customsValue->amount = $items->v[$i];
            $commodity->customsValue->currency = $currency;
            $commodity->weight->units = "KG";
            $commodity->weight->value = 1;
            array_push($postFields->requestedShipment->customsClearanceDetail->commodities, $commodity);
        }

        $packages = json_decode($shipment->packages);
        $postFields->requestedShipment->requestedPackageLineItems = [];
        foreach ($packages as $package) {
            $item = new stdClass;
            $item->weight = new stdClass;
            $item->dimensions = new stdClass;
            $item->weight->units = 'KG';
            $item->weight->value = $package->weight;
            $item->dimensions->units = "CM";
            $item->dimensions->length = $package->length;
            $item->dimensions->width = $package->width;
            $item->dimensions->height = $package->height;

            array_push($postFields->requestedShipment->requestedPackageLineItems, $item);
        }

        $postFields->accountNumber->value = $accNo;

        $postFields->labelResponseOptions = "URL_ONLY";

        $postFields = json_encode($postFields);

        //echo $postFields;
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://apis-sandbox.fedex.com/ship/v1/shipments',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => array(
            'Authorization:  Bearer ' . $token,
            'X-locale: en_US',
            'Content-Type: application/json',
        ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
        $response = json_decode($response);
        //print_r($response);
        return $response;
    }
    
    public function cancelShipment($trackingNum)
    {
        // Get FedEx account number
        $fedExKey = SystemSetting::model()->findByAttributes(['key'=>'fedex_api_key']);
		$accNo = $fedExKey->mdata['accNo'];
        // Get access token
        $fedexToken = Service::getCacheData('Fedex_Token');
		if (!empty($fedexToken)) {
			$fedexToken = json_decode($fedexToken);
			$token = $fedexToken->token;
		} else {
			$token = $this->Authorization();
			Service::setCacheData('Fedex_Token', '{"token": "'. $token . '"}', 3590);   // save token to redis, token expires in 1 hour
		}

        $postFields = new stdClass;
        $postFields->accountNumber = new stdClass;

        $postFields->accountNumber->value = $accNo;
        $postFields->senderCountryCode = "AU";
        $postFields->deletionControl = "DELETE_ONE_PACKAGE";
        $postFields->trackingNumber = $trackingNum;

        $postFields = json_encode($postFields);

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://apis-sandbox.fedex.com/ship/v1/shipments/cancel',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => array(
            'Authorization:  Bearer ' . $token,
            'X-locale: en_US',
            'Content-Type: application/json',
        ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        // $response = json_decode($response);
        // print_r($response);
        echo $response;
    }

    public function trackShipment($trackingNum)
    {
        // Get FedEx account number
        $fedExKey = SystemSetting::model()->findByAttributes(['key'=>'fedex_api_key']);
		$accNo = $fedExKey->mdata['accNo'];
        // Get access token
        $fedexToken = Service::getCacheData('Fedex_Token');
		if (!empty($fedexToken)) {
			$fedexToken = json_decode($fedexToken);
			$token = $fedexToken->token;
		} else {
			$token = $this->Authorization();
			Service::setCacheData('Fedex_Token', '{"token": "'. $token . '"}', 3590);   // save token to redis, token expires in 1 hour
		}

        $postFields = new stdClass;
        $postFields->trackingInfo = [];
        $trackingInfoObj = new stdClass;
        $trackingInfoObj->trackingNumberInfo = new stdClass;

        $postFields->includeDetailedScans = true;
        $trackingInfoObj->trackingNumberInfo->trackingNumber = $trackingNum;
        array_push($postFields->trackingInfo, $trackingInfoObj);

        $postFields = json_encode($postFields);
        echo $postFields;
        echo "\n";
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://apis-sandbox.fedex.com/track/v1/trackingnumbers',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => array(
            'Authorization:  Bearer ' . $token,
            'X-locale: en_US',
            'Content-Type: application/json',
        ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    // TODO: finish upload document function
    public function uploadImage()
    {	
        // Get access token
        $fedexToken = Service::getCacheData('Fedex_Token');
		if (!empty($fedexToken)) {
			$fedexToken = json_decode($fedexToken);
			$token = $fedexToken->token;
		} else {
			$token = $this->Authorization();
			Service::setCacheData('Fedex_Token', '{"token": "'. $token . '"}', 3590);   // save token to redis, token expires in 1 hour
		}

        $postFields = [];
        $field = new stdClass;
        $field->document = new stdClass;
        $field->rules = new stdClass;
        $field->document->meta = new stdClass;

        $field->document->referenceId = "tla_logo_081122";
        $field->document->name = "tla_logo.png";
        $field->document->contentType = "image/png";
        $field->rules->workflowName = "LetterheadSignature";
        $field->document->meta->imageType = "LETTERHEAD";
        $field->document->meta->imageIndex = "IMAGE_1";
        
        $document = json_encode($field);
        echo $document;
        echo "\n";

        $filePath = 'D:\xampp\htdocs\hvlv\images\tla_logo.png';
        $fileName = 'tla_logo.png';
        $postFields = [
            'document' => $document,
            'attachment' => new CurlFile($filePath, 'image/png', $fileName),
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://documentapitest.prod.fedex.com/sandbox/documents/v1/lhsimages/upload',
        CURLOPT_HEADER => true,
        CURLOPT_POST => 1,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => array(
            'Authorization:  Bearer ' . $token,
            'x-customer-transaction-id: 9129407-1',
            'Content-Type: multipart/form-data',
        ),
        ));

        $response = curl_exec($curl);
        echo curl_error($curl);
        curl_close($curl);

        echo $response;
    }

    public function uploadDocument()
    {
        // Get access token
        $fedexToken = Service::getCacheData('Fedex_Token');
		if (!empty($fedexToken)) {
			$fedexToken = json_decode($fedexToken);
			$token = $fedexToken->token;
		} else {
			$token = $this->Authorization();
			Service::setCacheData('Fedex_Token', '{"token": "'. $token . '"}', 3590);   // save token to redis, token expires in 1 hour
		}


    }

}
