<?php
     class ImportsPayController extends  PController{
    
         /**
          * @param type $pid the shipment ID
          * @param type $act the pay ways
          */
        public function actionMakeSupay($id, $act="Poli") {
            $shipment=ImParcel::model()->findByPk($id);
            if(isset($shipment->process)&&$shipment->process->status<ShipmentProcess::PAYMENT_RECEIVED){
                    $invoices = Invoice::model()->findAll('pid=:pid AND status!=10',array(':pid'=>$id));
                        if(empty($invoices)){
                            echo json_encode(array('success'=>false,'msg'=>'Not Related Invoice Found!','url'=>''));
                        }else{
                            $data=[
                                'invoice_ids'=>[],
                            ];
                            foreach($invoices as $inv){
                                $data['invoice_ids'][]=$inv->id;
                            }
                             if ($act == 'Poli') {
                                 $url = $this->createPaylink($data);
                              } else{ 
                                  $callback_notification_url = Yii::app()->request->hostInfo . $this->createUrl('importsPay/supay');
                                  $callback_return_url = Yii::app()->request->hostInfo . $this->createUrl('importsPay/supayReturn');
                                 
				  $supay = new SupayAPI(
						$callback_notification_url,
                                                $callback_return_url
                                
				  );
                                  $invDetails= $this->findInvoiceAmountForShipment($shipment->id);
                                  if($invDetails[0]<=0){
                                    echo json_encode(array('success' => false, 'msg' => 'Not found Invoice to Pay', 'url' =>''));
                                    return;
                                  }
                                  if ($act == 'Wechat') {
                                      $url = $supay->checkoutByWechatPay($shipment->ref." ".$invDetails[1], $shipment->id, 'AUD', round($invDetails[0] * 1.01, 2));
                                   } else if ($act == 'Alipay') {
                                      $url = $supay->checkoutByAliPay($shipment->ref." ".$invDetails[1], $shipment->id, 'AUD', round($invDetails[0] * 1.014, 2));
                                  }
                              }
                echo json_encode(array('success' => true, 'msg' => 'success', 'url' => $url));
                        }
            }else{
                echo json_encode(array('success' => false, 'msg' => 'The Parcel Not found or has been paid!', 'url' => ''));
            }        
        }
        
        public function actionSupayReturn(){
            $result=isset($_GET['result'])?$_GET['result']:'';
            if($result=="SUCCESS"){
               $trading_no=isset($_GET['trade_no'])?$_GET['trade_no']:'';
               $trading_trn=isset($_GET['out_trade_no'])?$_GET['out_trade_no']:'';
               $amount=isset($_GET['total_fee'])?$_GET['total_fee']:0;
               $currency=isset($_GET['currency'])?$_GET['currency']:'AUD';
               echo "<h1>".$result."</h1>";
               echo "<p>Trading No:".$trading_no."</p>";
               echo "<p>Trading Token:".$trading_trn."</p>";
               echo "<p>Paid:".$currency." ".$amount."</p>";
                return;
            }else{
                echo "<h1>$result</h1>";
            }
        }
        /**
         * @param Integer $pid the shipment id
         * @return [$amount,$ref] the amount of money need to pay for this shipment,the ref of invoice
         */
         private function findInvoiceAmountForShipment($pid){
            $invoices= Invoice::model()->findAll('type in (41,45) AND status not in (8,9,10,11) AND pid=:pid',array(':pid'=>$pid));
            $totalAmount=0;
            $ref='Invoice:';
            if(!empty($invoices)){
                foreach($invoices as $inv){
                $totalAmount += ($inv->total - $inv->paid() > 0 ? $inv->total - $inv->paid() : 0);
                $ref.=$inv->no.";";
               }
            }
          return [$totalAmount, substr($ref, 0,255)];  
        }
        public function actionSupay() {
            Yii::log(json_encode($_SERVER['QUERY_STRING']), 'warning', 'trace');
            Log::log2file(json_encode($_GET), 'suppay','account');
            $supay = new SupayAPI();
            $veri_result = $supay->verifyNotification($_GET);
            $shipment= ImParcel::model()->findByPk(trim($_GET['merchant_trade_no']));
            $invDetails= $this->findInvoiceAmountForShipment($shipment->id);
            $invoices=Invoice::model()->findAll('type in (41,45) AND status not in (8,9,10,11) AND pid=:pid',array(':pid'=>trim($_GET['merchant_trade_no'])));
            if (!empty($invoices)&&!empty($shipment)&& $veri_result) {
                $result = $supay->checkoutResult($shipment->id);
                if ($result) {
                    if($this->afterPaymentPaid($shipment, array('notice_id' => $_GET['notice_id'], 'amount' => $invDetails[0]), 'SUPAY')){
                    }
                } else {
                  echo "invoice is wrong";
                }
            } else {
                echo "can't find the invoice";
            }
        }      
   private function afterPaymentPaid($shipment, $trnInfo, $type) {
        if (!empty($trnInfo['notice_id'])) {
            $trn = $trnInfo['notice_id'];
            $amount = $trnInfo['amount'];
        }

        $payment_gateway_history_model = PaymentGatewayHistory::model()->find('trn = :trn', array(':trn' => $trn));
        if ( !isset($payment_gateway_history_model) ) {
            $payment_gateway_history_model = new PaymentGatewayHistory();
            $payment_gateway_history_model->trn = $trn;
            $payment_gateway_history_model->pid = $shipment->id;
            $payment_gateway_history_model->type = constant('PaymentGatewayHistory::PAYMENT_GATEWAY_TYPE_'. $type);
            $payment_gateway_history_model->token = $trn;
            $payment_gateway_history_model->meta = json_encode($trnInfo);
            $payment_gateway_history_model->result = 'success';
            $payment_gateway_history_model->payed_time = new CDbExpression('NOW()');
            $payment_gateway_history_model->save();
        } else {
            if ( $payment_gateway_history_model->result == 'success' && !empty($payment_gateway_history_model->payed_time) ) {
                echo 'you have paid the invoice successfully already before!';
                return false;
            }
        }
        $invoices = Invoice::model()->findAll('pid=:pid AND status!=10',array(':pid'=>$shipment->id));
        $paidAmount=$amount;
        if(!empty($paidAmount)){
             $transaction=Yii::app()->db->beginTransaction();
             try{
                $shipment->receivedPaymentBySubPay($paidAmount,$trn);
                $transaction->commit();
                return true;
             }catch(Exception $ex){
                 $transaction->rollback();
                 throw  $ex;
             }
        }
        return false;
    }
        /**
	 * pay link clicked will call this API
	 * @param $token
	 */
	public function actionPay($token){
		// get token
		$token = $this->safe_b64decode($token);
		$iv = str_repeat("\x00", openssl_cipher_iv_length('aes-256-cbc'));
		$data = rtrim(openssl_decrypt(base64_decode($token), 'AES-128-CBC', md5(PoliPaymentApi::PAYLINK_KEY), 0, $iv), "\0");
		$invoiceData = json_decode($data);

		if ( !empty($invoiceData) && isset($invoiceData->invoice_ids) ) {
			// valid paylink
			$this->payInvoiceDirect($invoiceData->invoice_ids);

		} else {
			// invalid pay link
			echo 'invalid pay link';
		}

	}
    	/**
	 * create invoice paylink algorithm as below:
	 * app_url/invoice/pay.app?token=[tokenvaue]
	 * @param $data
	 * @return string
	 */
	private function createPaylink($data){
		$paylink = Yii::app()->createAbsoluteUrl('/') .'/importsPay/pay.app?token=';
		$iv = str_repeat("\x00", openssl_cipher_iv_length('aes-256-cbc'));
		$token = base64_encode(openssl_encrypt(json_encode($data), 'AES-128-CBC', md5(PoliPaymentApi::PAYLINK_KEY), 0, $iv));
		$paylink .= trim($this->safe_b64encode($token));
		return $paylink;
	}
    
      /**
	 * remove special string for URL paramters
	 * @param $string
	 * @return mixed|string
	 */
	private  function safe_b64encode($string) {
		$data = base64_encode($string);
		$data = str_replace(array('+','/','='),array('-','_',''),$data);
		return $data;
	}
	private function safe_b64decode($string) {
		$data = str_replace(array('-','_'),array('+','/'),$string);
		$mod4 = strlen($data) % 4;
		if ($mod4) {
			$data .= substr('====', $mod4);
		}
		return base64_decode($data);
	}
         /**
	 * pay by POLI web service
	 * @param $invoiceIds an array of invoice ids;
	 */
	private function payInvoiceDirect($invoiceIds){
            $pid='';
            $totalToPay=0;
            $invoiceRef='';
            foreach ($invoiceIds as $inv_id){
                $inv= Invoice::model()->findByPk($inv_id);
                if(!empty($inv)){
                    if(in_array($inv->status,[8,9,10]))  continue;
                    $pid=$inv->pid;
                    $totalToPay+=($inv->total-$inv->paid()>0?$inv->total-$inv->paid():0);
                    $invoiceRef.=$inv->no.' ';
                }
            }
            if(!empty($pid)){
                  $shipment= ImParcel::model()->findByPk($pid);
            }
           if(!empty($pid)&&!empty($shipment)){
               $ref=$shipment->ref." Invoice ".$invoiceRef;
               if(!empty($totalToPay)){
                 $home_page = Yii::app()->createAbsoluteUrl('');
                 $call_back_root = Yii::app()->createAbsoluteUrl('/') . '/importsPay/poli/' . $pid . '.app?op=';
                 $callback_success_url = $call_back_root . 'success';
                 $callback_fail_url = $call_back_root . 'fail';
                 $callback_cancel_url = $call_back_root . 'cancel';
                 $callback_notiry_url = $call_back_root . 'notify';
                 $totalToPay += min($totalToPay * 0.01, 3); 
                 $json_builder = '{
					"Amount":"' . $totalToPay . '",
					"CurrencyCode":"AUD",
					"MerchantReference":"' . $ref . '",
					"MerchantHomepageURL":"' . $home_page . '",
					"SuccessURL":"' . $callback_success_url . '",
					"FailureURL":"' . $callback_fail_url . '",
					"CancellationURL":"' . $callback_cancel_url . '",
					"NotificationURL":"' . $callback_notiry_url . '"
				}'; 
                 $auth = base64_encode(Invoice::POLI_MERCHCODE . ':' . Invoice::POLI_AUTHCODE);
				$header = array();
				$header[] = 'Content-Type: application/json';
				$header[] = 'Authorization: Basic ' . $auth;

				$ch = curl_init("https://poliapi.apac.paywithpoli.com/api/Transaction/Initiate");
				//See the cURL documentation for more information: http://curl.haxx.se/docs/sslcerts.html
				//We recommend using this bundle: https://raw.githubusercontent.com/bagder/ca-bundle/master/ca-bundle.crt
				//curl_setopt( $ch, CURLOPT_CAINFO, "ca-bundle.crt");
				//curl_setopt( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);

				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

				curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json_builder);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($ch);
				curl_close($ch);

				$json = json_decode($response, true);
				if ($json['Success'] == true && !empty($json["NavigateURL"])) {

					// we should save this in DB related with invoice
					$trn = $json['TransactionRefNo'];
                                
					$poli_model = new PaymentGatewayHistory();
					$poli_model->pid = $pid;
                                        $poli_model->type= PaymentGatewayHistory::PAYMENT_GATEWAY_TYPE_POLI;
					$poli_model->trn = $trn;
					if(!$poli_model->save()){
                                            throw new CHttpException(500,'Internal Errors');
                                        }
					// redirect to POLI payment page directly
					header('Location: ' . $json["NavigateURL"]);

				} else {
					// log error message
					$error = $json['ErrorMessage'] . '(' . $json['ErrorCode'] . ')';

					// redirect to pay error page
					echo $error;
				}
                   
               }else{
                   echo "Sorry, Not found Invoice to Pay!";
               }
              }else{
               echo 'Sorry, Not found the Parcel In Our System, Please contact us!'; 
            }   
	}
        /**
	 * process poli payment callback logic
	 * @param $id
	 * @param $op
	 */
	public function actionPoli($id,$op){  //we need to valid if double reload.
		$model = ImParcel::model()->findByPk($id);
		// transaction token returned by poli , we should saved this if need check status again
		$poli_token = $_GET['token'];
		// get trn by token
		$trn_info = $this->getPoliTrn($poli_token); 
                
                //*****************************
//                  $file= Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'a.txt';
//                  $content= file_get_contents($file);   
//                  $trn_info= json_decode($content,true);          
               //******************************
//               var_dump($trn_info);
//               return;
                
		if ( !empty($trn_info['ErrorCode'])||!isset($trn_info['TransactionRefNo'])) {
			// payed failed save log
			$poli_model = new PaymentGatewayHistory();
			$poli_model->pid = $id;
			$poli_model->trn = 'invalid';
			$poli_model->token = $poli_token;
			$poli_model->result = 'failed';
			$poli_model->payed_time = new CDbExpression('NOW()');;
			$poli_model->meta = json_encode($trn_info);
			$poli_model->save();
			echo $trn_info['ErrorMessage'];
			exit();
		}
		// record history from POLI call back
		$poli_model = PaymentGatewayHistory::model()->find('trn = :trn', array(':trn' => $trn_info['TransactionRefNo']));
		if ( !isset($poli_model) ) {
			$poli_model = new PaymentGatewayHistory();
			$poli_model->trn = $trn_info['TransactionRefNo'];
		} else {
                        if(!empty($poli_model->result)){ //we need to avoid reload the page!
                            echo '<h2>The Payment already been processed</h2>';
                            echo  '<p>'.$poli_model->result.'</p>';
                            echo  '<p>Transaction Number:'.$poli_model->trn.'</p>';
                            exit();
                        }
//			if ( $poli_model->result == 'Completed' && !empty($poli_model->payed_time) ) {
//				echo 'you have paid the invoice successfully already before!';
//				exit();
//			}      
		}
		$poli_model->pid = $id;
		$poli_model->token = $poli_token;
		$poli_model->meta = json_encode($trn_info);
		$poli_model->result = isset($trn_info['TransactionStatusCode'])?$trn_info['TransactionStatusCode']:'Unknow';
		$poli_model->payed_time = new CDbExpression('NOW()');
		if ( !empty($model) ) {
			switch ($op) {
				case 'success' :
                                    if(isset($trn_info['TransactionStatusCode'])&&$trn_info['TransactionStatusCode']=="Completed"){
                                          $this->afterPayedByPoli($id,$trn_info);
                                         if($model->getUnpaidInvoiceAmount()<=0){
                                            echo '<h3>'.$poli_model->result.'</h3>';
                                            echo '<p>Amount Paid: <b>'.$trn_info['AmountPaid'].'</b></p>';
                                            echo '<p>Fully Paid</p>';
                                            echo '<p>Transaction Number:'.$trn_info['TransactionRefNo'].'</p>';
                                          }else{
                                            echo '<h3>'.$poli_model->result.'</h3>';
                                            echo '<p>Amount Paid: <b>'.$trn_info['AmountPaid'].'</b></p>';
                                            echo '<p>Partly Paid</p>';
                                            echo '<p>UnPaid Amount:<b>'.$model->getUnpaidInvoiceAmount().'</b></p>';
                                            echo '<p>Transaction Number:'.$trn_info['TransactionRefNo'].'</p>';
                                          }
                                          $poli_model->save();
                                          return;
                                      }
			            break;
				case 'fail' :
					break;
				case 'cancel' :
					break;
				case 'notify':
					break;
				default:
                                echo 'unknown error occurred !';
                                break;
			}
		} else {
			// important , we should check why this id not existing
			// because client has payed for this invoice
			// we should record this error with invoice id and pay result information
			$poli_model->result = $op . " can't find the Imparcel";
		}
		$poli_model->save();
		echo $poli_model->result;
	}
        
        /**
	 * get trn by token
	 * @param $token
	 * @return string
	 */
	private function getPoliTrn($token){
		$trn = '';
		$auth = base64_encode(Invoice::POLI_MERCHCODE . ':' . Invoice::POLI_AUTHCODE);
		$header = array();
		$header[] = 'Content-Type: application/json';
		$header[] = 'Authorization: Basic ' . $auth;

		// https://poliapi.apac.paywithpoli.com/api/v2/Transaction/GetTransaction?token={transactionToken}
		$requst_url = 'https://poliapi.apac.paywithpoli.com/api/v2/Transaction/GetTransaction?token='.$token;
		try {
			$ch = curl_init($requst_url);

			//See the cURL documentation for more information: http://curl.haxx.se/docs/sslcerts.html
			//We recommend using this bundle: https://raw.githubusercontent.com/bagder/ca-bundle/master/ca-bundle.crt
			//curl_setopt($ch, CURLOPT_CAINFO, "/ca-bundle.crt");
			// curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);

			// currently just disable ssl
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);

			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($ch);

			if ($response == false) {
				throw new Exception(curl_error($ch), curl_errno($ch));
			}

			curl_close($ch);
		} catch( Exception $e) {
			trigger_error( sprintf(
				'Curl failed with error #%d: %s',
				$e->getCode(), $e->getMessage()),
				E_USER_ERROR);

			return false;
		}

		$json = json_decode($response, true);
		// get url token save them for query soon
		if ( is_array($json) ) {
			$trn = $json;
		} else {
		}

		return $trn;
	}
        /**
	 * process after payed by POLI successfully
	 * we should do :
	 * 1. create a payment
	 * 2. link payment with invoice by pay_inv table
	 * @param $id
	 * @param $trnInfo
	 */
	private function afterPayedByPoli($pid,$trnInfo){
		// get original invoice data
	         $invoices = Invoice::model()->findAll('pid=:pid AND status!=10',array(':pid'=>$pid));
                 $paidAmount=$trnInfo['AmountPaid'];
                 if(!empty($paidAmount)){
                     $shipment= ImParcel::model()->findByPk($pid);
                     if (strtotime($shipment->consol->eta) >= strtotime('2020-08-01')) [$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
                     $transaction=Yii::app()->db->beginTransaction();
                     try{
                        $shipment->receivedPaymentByPoli($paidAmount,$trnInfo);
                        $transaction->commit();
                     }catch(Exception $ex){
                         $transaction->rollback();
                         throw  $ex;
                     }
               }
	}
}

