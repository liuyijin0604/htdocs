<?php

class ApiInboundAction extends CAction
{
    public $ctlr;
    public $debug;
    public $user;

    public function run()
    {
        $this->ctlr = $this->getController();
        if (!empty($_POST['method']) && method_exists($this, $_POST['method'])) {
			if (!in_array($_POST['method'], ['get'])) {
				$this->log(json_encode($_POST));
			}
			$this->{$_POST['method']}();
		} else {
			throw new CHttpException(400, 'API method not found!');
		}
    }

    public function log($l)
    {
        $tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'inboundapi' . DIRECTORY_SEPARATOR ;
        if (!is_dir($tmp)) {
            mkdir($tmp);
        }
		file_put_contents($tmp.'inbound_api_'  . date('Y-m-d') . '.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
    }

    public function checkContainer()
    {
        $data = json_decode($_POST['data']);
        $response = new stdClass;

        if (is_object($data)) {
            if (!empty($data->containerNumber)) {
                $consol = Consol::model()->findByAttributes(['container_no'=>$data->containerNumber]);
                if (!empty($consol)) {
                    $containerNumber = $consol->container_no;
                    $vessel = !empty($consol->airline)?$consol->airline:'';
                    $voyage = !empty($consol->flight)?$consol->flight:'';
                    if(!empty($consol->eta) && date('Y-m-d H:i:s', strtotime($consol->eta)) != date('Y-m-d H:i:s', strtotime('0000-00-00'))) {
                        $estimateArrival = date('Y-m-d H:i:s', strtotime($consol->eta));
                    } else {
                        $estimateArrival = '';
                    }
                    $unpackingDate = !empty($consol->mdata['ContainerUnloadDate'])?date('Y-m-d H:i:s', strtotime($consol->mdata['ContainerUnloadDate'])):'';
                    $availabilityDate = !empty($consol->mdata['available_date'])?date('Y-m-d H:i:s', strtotime($consol->mdata['available_date'])):'';
                    $storageStartDate = !empty($consol->mdata['input_storage_date'])?date('Y-m-d H:i:s', strtotime($consol->mdata['input_storage_date'])):'';
                    $facilityName = '';
                    $depot = $consol->dpt_id;
                    switch ($depot) {
                        case 106:
                            $facilityName = 'Sydney';
                            break;
                        case 218:
                            $facilityName = 'Melbourne';
                            break;
                        case 530:
                            $facilityName = 'Brisbane';
                            break;
                    }

                    $response->containerNumber = $containerNumber;
                    $response->vessel = $vessel;
                    $response->voyage = $voyage;
                    $response->estimateArrival = $estimateArrival;
                    $response->unpackingDate = $unpackingDate;
                    $response->availabilityDate = $availabilityDate;
                    $response->storageStartDate = $storageStartDate;
                    $response->facilityName = $facilityName;

                    echo json_encode($response);
                } else  {
                    $response->status = 'Error';
                    $response->message = 'We cannot find this shipment in our system.';
                    echo json_encode($response);
                }
            } elseif (empty($data->checkContainer)) {
                $response->status = 'Error';
                $response->message = 'Container number is mandatory.';
                echo json_encode($response);
            } elseif (empty($data->houseBill)) {
                $response->status = 'Error';
                $response->message = 'House bill is mandatory.';
                echo json_encode($response);
            }
        } else {
            $response->status = 'Error';
            $response->message = 'Json data malformatted.';
            echo json_encode($response);
        }
    }

    public function handleInboundBooking()
    {
        
    }
}