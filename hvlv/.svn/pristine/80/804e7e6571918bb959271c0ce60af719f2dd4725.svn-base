<?php

class ApiFheAction extends CAction
{
    public $ctlr;
    public $debug;
    public $user;

    private $privateKey = "MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBANB46DwiQeE9ttsJFGUpxa0Fu/JDwtBiiZiG/rd66wdAqfnyzBzFlrFGYSgVner0xYrts3hrurj3Rk10RNT01Mm5w+BVbZ42oVqLCsMydzejGDJbG6IM3SBOjyJIN89ixT774UeNq9gTp5krYBMFSJDrGweGl8tNgc6sjo2NdbnPAgMBAAECgYBE+FtM2cCV9kbyvFRFC8bccVM22XgwXQlMrwzCQyZSpfAWQ1+H/U7Xo4MtMcmnHAfm6LFBm9KQsy5NHbRQCBgFc67XtY81X+QY2OjSxmni4PQs9rRfHMm7nEZjd03axGwnKEdrZlfp28kN+f6vYYZQqa/mKHjsZi91UbHpe22YYQJBAP1ef/1mRGiUyLyG8gw9TRgfID21LXGxUL1Q+hhZDfwGtyNrkOEhgLjEAAPgnKfw+kwX0L/MzWoX/zGZDv1giIkCQQDSoxA9f9GWBOjuQY9GbZGIFF6dLwkT4E/cyMA91YWDSGT8cNpVgdBtiIuWYbdNATrDLsKXStP11X8VndM6HWmXAkBBtw7vRGUd0uk1rLJ+5i9mwDv2hVViFaFhWO1k/0QXSA6cCzwqiCwAwCVY3BsFnATvU4X7GT119P9ld9NheHYxAkEAto2KgrJnk6xXsD5zjSdi7Nwyj+n25RoQPRpjunN2ziwNEdhA8cCbQoMH72Jq+bsqEYVSMsswXqwVA0gQjBp3qwJBAJ0oZOzDMXlpwIBZHvBF6JcOSjr1gzm2S0fmWwPjwadPwAdsQ9mCUzE8xX1+pZzqrn4VzpktC8B+T2yeQ3NdNmw=";
    private $publicKey = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDQeOg8IkHhPbbbCRRlKcWtBbvyQ8LQYomYhv63eusHQKn58swcxZaxRmEoFZ3q9MWK7bN4a7q490ZNdETU9NTJucPgVW2eNqFaiwrDMnc3oxgyWxuiDN0gTo8iSDfPYsU+++FHjavYE6eZK2ATBUiQ6xsHhpfLTYHOrI6NjXW5zwIDAQAB";
    public function run()
    {
        $this->ctlr = $this->getController();
        $jsonString = file_get_contents('php://input');
        if (!empty($_POST['method']) && method_exists($this, $_POST['method'])) {
			if (!in_array($_POST['method'], ['get'])) {
				$this->log(json_encode($_POST));
			}
			$this->{$_POST['method']}();
		} else {
            $this->log($jsonString);
            $this->fheTracking($jsonString);
			//throw new CHttpException(400, 'API method not found!');
		}
    }

    public function log($l)
    {
        $tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'fheApi' . DIRECTORY_SEPARATOR;
        if (!is_dir($tmp)) {
            mkdir($tmp);
        }
		file_put_contents($tmp.'fasthorse_api_'  . date('Y-m-d') . '.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
    }

    public function fheTracking($jsonString)
    {
        $requestBody = new stdClass;
        $jsonObject = json_decode($jsonString);
        $requestBody->data = $jsonObject->data;
        $javaApi = new HvlvJavaAPI("SYD", "fasthorse");
        $result = $javaApi->decryptFastHorseTracking($requestBody);
        $response = new stdClass;
        if (!empty($result['success']) && $result['success'] == 1) {
            $data = $result['data'];
            $ref = $data['orderNo'];
            $shipment = ImParcel::model()->findByAttributes(array('ref' => $ref));
            if (empty($shipment)) {
                $response->code = 500;
                $response->message = 'Shipment not fount';
            } else {
                $trackingType = 0;
                switch ($data['action']) {
                    case "collect_order":
                    case "in_warehorse":
                    case "deliver_in_warehose":
                        $trackingType = 75;
                        break;
                    case "sign_in":
                    case "modify_sign_in":
                        $trackingType = 90;
                        break;
                    case "exception_order":
                    case "damage_order":
                    case "exception_reject":
                    case "exception_redeliver":
                    case "exception_withdrawing_re_in_warehouse":
                    case "navigation_or_address_error":
                    case "recipient_address_pobox":
                    case "no_safe_no_way_contact":
                        $trackingType = 101;
                        break;
                    case "out_warehorse_2":
                        $trackingType = 80;
                        break;
                    case "exception_change_data_1":
                    case "user_request_for_time_change":
                    case "reassign":
                        $trackingType = 87;
                        break;
                    case "exception_lose":
                    case "lost":
                        $trackingType = 85;
                        break;
                    case "transport_order":
                    case "transport_order_v2":
                    case "to_be_delivered":
                    case "allocate_order":
                    case "block_remove_package":
                    case "block_remove_package_abnormal_remark":
                    case "allocate_wait_out":
                    case "allocate_out":
                    case "allocate_in":
                    case "order_sorting_finish":
                        $trackingType = 70;
                        break;
                }
                if ($trackingType == 0) {
                    $response->code = 500;
                    $response->message = 'Cannot identify tracking action type';
                } else {
                    $tracking = Tracking::model()->findByAttributes(array('pid' => $shipment->id, 'type' => $trackingType, 'activity' => $data['routingDescription']));
                    if (!empty($tracking)) {
                        $tracking->dt = date('Y-m-d H:i:s', strtotime($data['operatorTime']));
                        $tracking->depot = $data['occurLocation'];
                        $tracking->update();
                    } else {
                        $tracking = new Tracking();
                        $tracking->pid = $shipment->id;
                        $tracking->dt = date('Y-m-d H:i:s', strtotime($data['operatorTime']));
                        $tracking->type = $trackingType;
                        $tracking->activity = $data['routingDescription'];
                        $tracking->depot = $data['occurLocation'];
                        $tracking->save();
                        if ($trackingType == 75 && $shipment->status < ImParcel::STATE_COURIER) {
                            $shipment->status = ImParcel::STATE_COURIER;
                            $shipment->update('status');
                        } else if ($trackingType == 90) {
                            $shipment->status = ImParcel::DELIVERED;
                            $shipment->update('status');
                        }

                    }
                    $response->code = 200;
                    $response->message = 'SUCCESS';
                }
            }   
        } else {
            $response->code = 500;
            $response->message = "Tracking info decryption error.";
        }
        echo json_encode($response);
    }
}