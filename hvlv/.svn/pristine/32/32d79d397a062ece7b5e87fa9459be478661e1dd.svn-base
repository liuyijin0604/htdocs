<?php
/* 
 * Xero integration API actions
 *
 * client should get authorize by ID and KEY which are from api table
 * for example
 *  user : pca_xero
 *  key : 4534052cfb29215f99edfc47f52e10c426fcaa82
 */
class ApiXeroAction extends CAction {

	public $ctlr, $debug, $user;

	public function run() {
		$this->ctlr = $this->getController();
		$this->debug = !empty($_POST['test']);
		$this->user = empty($this->ctlr->user)? false : User::model()->findByPk($this->ctlr->user);

		if(!empty($_POST['method']) && method_exists($this, $_POST['method'])){
			$this->log(json_encode($_POST));
			$this->{$_POST['method']}();
		}else{
            $this->log('API method not found! : '. $_POST['method'] );
            $this->log('row post data  : '. json_encode($_POST) );
			throw new CHttpException(400, 'API method not found!');
		}
	}

	public function log($l){
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		file_put_contents($tmp.'xero_api.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
	}

    /**
     * add a new contact
     *
     *  request data format :
     *  {
     *      'name' : name,'id' : id, 'tax':taxNumber,'email':email,'address' : address,'city' : city,
     *      'state' : state,'postcode' : postcode,'phone':phone,'fax':fax
     *  }
     */
	private function addContact() {

        $name = '';
        $errors = array();
        if ( isset($this->ctlr->data->name) ) $name = $this->ctlr->data->name;
        if ( empty($name) ) {
            $errors[] = 'Contact name is missing';
        }
        $id = '';
        if ( isset($this->ctlr->data->id) ) $id = $this->ctlr->data->id;
        if ( empty($id) ) {
            $errors[] = 'Contact ID is missing';
        }
        $tax = '';
        if ( isset($this->ctlr->data->tax) ) $tax = $this->ctlr->data->tax;
        $email = '';
        if ( isset($this->ctlr->data->email) ) $email = $this->ctlr->data->email;
        $addr = '';
        if ( isset($this->ctlr->data->address) ) $addr = $this->ctlr->data->address;
        $city = '';
        if ( isset($this->ctlr->data->city) ) $city = $this->ctlr->data->city;
        $state = '';
        if ( isset($this->ctlr->data->state) ) $state = $this->ctlr->data->state;
        $postcode = '';
        if ( isset($this->ctlr->data->postcode) ) $postcode = $this->ctlr->data->postcode;
        $phoneNumber = '';
        if ( isset($this->ctlr->data->phone) ) $phoneNumber = $this->ctlr->data->phone;
        $faxNumber = '';
        if ( isset($this->ctlr->data->fax) ) $faxNumber = $this->ctlr->data->fax;

        $resp = array('status' => 0);
        if ( empty($errors) ) {
            $contact = new XeContact();
            $contact->name = $name;
            $contact->contactNumber = 'PORG-'. $id;
            $contact->accountNumber = 'PORG-' . $id;
            $contact->taxNumber = $tax;
            $contact->emailAddress = $email;
            $address = new XeAddress();
            $address->addressType = XeAddress::AT_POBOX; //billing address
            $address->addressLine1 = $addr;
            $address->city = $city;
            $address->region = $state;
            $address->postalCode = $postcode;
            $contact->addresses->add($address);
            if ( !empty($phoneNumber) ) {
                $phone = new XePhone();
                $phone->phoneCountryCode = '+61';
                $phone->phoneNumber = $phoneNumber;
                $contact->phones->add($phone);
            }
            if ( !empty($faxNumber) ) {
                $fax = new XePhone();
                $fax->phoneType = 'FAX';
                $fax->phoneNumber = $faxNumber;
                $contact->phones->add($fax);
            }
            try {
                $rt = $contact->save();
                if ( !$rt ) {
                    $resp['status'] = 0;
                    $this->log('Failed to save contact - ' . $id );
                    $errors[] = 'Failed to save contact - Unknown Error';
                    $resp['errors'] = $errors;
                } else {
                    $resp['status'] = 1;
                    $resp['msg'] = 'Save contact successfully';
                }
            } catch ( Exception $ecp ) {
                $msg = '[ERROR] - Failed to save contact - ' . $ecp->getMessage();
                $errors[] =  $msg;
                $resp['errors'] = $errors;
                $msg .= PHP_EOL;
                $msg .= 'Org Data : ' . json_encode( $contact ,JSON_PRETTY_PRINT);
                $this->log( $msg );
            }
        } else {
            $resp['status'] = 0;
            $resp['errors'] = $errors;
        }
        // return results
        echo json_encode($resp);
	}


    /**
     * add a new invoice
     * request data format:
     * {
     *      'id':id,'currency':currency,'receiver':receiver,'receiverId':receiverId,'date':date,'dueDate':dueDate,
     *      'taxType':taxType,'items':items[{'name':name,'qty':qty,'amount':amount},{'name':name,'qty':qty,'amount':amount},...]
     * }
     */
    private function addInvoice() {

        $id = '';
        if ( isset($this->ctlr->data->id) ) $id = $this->ctlr->data->id;
        if ( empty($id) ) {
            $errors[] = 'Invoice ID is missing';
        }
        $items = array();
        if ( isset($this->ctlr->data->items) ) $items = $this->ctlr->data->items;
        if ( empty($items) ) {
            $errors[] = 'Invoice items are missing';
        }
        $currency = 'AUD'; // default as Australia dollar
        if ( isset($this->ctlr->data->currency) ) $tax = $this->ctlr->data->currency;
        $receiver = '';
        if ( isset($this->ctlr->data->receiver) ) $receiver = $this->ctlr->data->receiver;
        if ( empty($receiver) ) {
            $errors[] = 'Invoice Receiver is missing';
        }
        $receiverId = '';
        if ( isset($this->ctlr->data->receiverId) ) $receiverId = $this->ctlr->data->receiverId;
        $date = '';
        if ( isset($this->ctlr->data->date) ) $date = $this->ctlr->data->date;
        $dueDate = '';
        if ( isset($this->ctlr->data->dueDate) ) $dueDate = $this->ctlr->data->dueDate;

        $taxType = 3;
        if ( isset($this->ctlr->data->taxType) ) $taxType = $this->ctlr->data->taxType;

        $resp = array('status' => 0);
        if ( empty($errors) ) {
            $invoice = new XeInvoice('ACCREC');
            $invoice->status = 'AUTHORISED'; // approved , waiting for pay
            $invoice->invoiceNumber = $id;
            $invoice->currencyCode = $currency;

            $contact = new XeContact();
            $contact->name = $receiver;
            $contact->accountNumber = 'PORG-' . $receiverId;
            $invoice->contact = $contact;
            $invoice->date = $date;
            $invoice->dueDate = $dueDate;

            // Exclusive - exclude GST
            // Inclusive - include GST
            // NoTax
            switch ( $taxType ) {
                case 1:
                    $invoice->lineAmountTypes = 'Exclusive';
                    break;
                case 2:
                    $invoice->lineAmountTypes = 'Inclusive';
                    break;
                case 3:
                default:
                    $invoice->lineAmountTypes = 'NoTax';
                    break;
            }

            // get all items
            $invoiceAccountCode = AppHelper::getXeroSetting('import_invoice_glcode');
            foreach ($items as $item) {
                // add each item by name,quantity,amount
                $itemData = array();
                $itemData['quantity'] = $item->qty;
                $itemData['accountCode'] = $invoiceAccountCode;
                $itemData['description'] = $item->name;
                $itemData['unitAmount'] = $item->amount;
                $tracking = Invoice::getTrackingInfo($this->ctlr->data);
                $itemData['trackingName'] = $tracking['name'];
                $itemData['trackingValue'] = $tracking['value'];
                $invoice->addLineItem($itemData);
            }
            try {
                $rt = $invoice->save();
                if ( $rt ) {
                    $resp['status'] = 1;
                    $resp['msg'] = 'Save invoice successfully';
                } else {
                    // log error message
                    $errorMsg = '[ERROR] - failed to save invoice to xero for : ' . $id;
                    $this->log($errorMsg);
                    $errors[] = $errorMsg;
                    $resp['errors'] = $errors;
                }
            } catch ( Exception $mye ) {
                $msg = '[ERROR] - failed to save invoice - ' . $mye->getMessage();
                $msg .= PHP_EOL;
                $msg .= 'Invoice Data : ' . json_encode( $invoice ,JSON_PRETTY_PRINT);
                $this->log( $msg );
            }
        } else {
            $resp['status'] = 0;
            $resp['errors'] = $errors;
        }

        // return results
        echo json_encode($resp);
    }


}
