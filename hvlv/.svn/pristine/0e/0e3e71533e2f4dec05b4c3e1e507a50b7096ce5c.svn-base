<?php

class YtoGlobalAPI
{
    protected $testingUrl = 'http://test.edi.ytoglobal.com/global-edi-itf/api/outv1/track/receiveExtremityTrack';
    protected $productionUrl = 'http://api.ytoglobal.com/global-edi-itf/api/outv1/track/receiveExtremityTrack';
    protected $testingAccount = 'TLA';
    protected $productionAccount = 'TLA';
    protected $testingKey = 'YHT8CX5^4Z@!F^BZ';
    protected $productionKey = '!8I&#JXQW%$RYN@N';

    public function __construct()
    {
    }

    public function postCustomsStatus($shipment)
    {
        $data = [];
        $trackingObj = new stdClass;
        $trackingObj->trackingNumber = $shipment->hbn;
        $trackingObj->successList = [];
		$trackingObj->failList = [];
        $eventLocation = '';
        if (!empty($shipment->consol->dpt_id)) {
            switch($shipment->consol->dpt_id) {
                case 106:
                    $eventLocation = 'Sydney';
                    break;
                case 218:
                    $eventLocation = 'Melbourne';
                    break;
                case 530:
                    $eventLocation = 'Brisbane';
                    break;
                case 811:
                    $eventLocation = 'Perth';
                    break;
                default:
                    $eventLocation = 'Sydney';
            }
        }
        $timezone = new DateTimeZone(date_default_timezone_get());
		$datetime = new DateTime('now', $timezone);
		$offset = $timezone->getOffset($datetime);
		$hours = abs(intdiv($offset, 3600));
        $timezoneOffset = "+" . $hours . ":00";

        $heldRecord = Tracking::model()->findByAttributes(array('pid' => $shipment->id, 'type' => Tracking::TYPE_HELD));
        $clearRecord = Tracking::model()->findByAttributes(array('pid' => $shipment->id, 'type' => Tracking::TYPE_CLEARED));
        if (!empty($heldRecord)) {
            // First check if it has cleared record, if not, check held record timestamp against current time,
            if (empty($clearRecord)) {
                // if it has been held for more than 3 hours and not been cleared yet, add a fail record
                if ($this->timeDiff($heldRecord->dt) >= 3 && empty($heldRecord->mdata['yto_posted'])) {
                    $failRecord = new stdClass;
                    $failRecord->failCode = "C";
                    $failRecord->eventTime = $heldRecord->dt . '' . $timezoneOffset;
                    $failRecord->eventTimeZone = $this->currentTimezone();
                    $failRecord->eventLocation = !empty($heldRecord->depot) ? $heldRecord->depot : $eventLocation;
                    $failRecord->failDetail = $heldRecord->activity;
                    $failRecord->countryCode = "AU";
                    array_push($trackingObj->failList, $failRecord);
                    $heldRecord->mdata['yto_posted'] = 1;
                    $heldRecord->update('meta');
                }
            } else {
                if ($this->timeDiff($heldRecord->dt, $clearRecord->dt) >= 3 && empty($heldRecord->mdata['yto_posted'])) {
                    $failRecord = new stdClass;
                    $failRecord->failCode = "C";
                    $failRecord->eventTime = $heldRecord->dt . '' . $timezoneOffset;
                    $failRecord->eventTimeZone = $this->currentTimezone();
                    $failRecord->eventLocation = !empty($heldRecord->depot) ? $heldRecord->depot : $eventLocation;
                    $failRecord->failDetail = $heldRecord->activity;
                    $failRecord->countryCode = "AU";
                    array_push($trackingObj->failList, $failRecord);
                    $heldRecord->mdata['yto_posted'] = 1;
                    $heldRecord->update('meta');
                }
                // The shipment has been cleared, add a success record
                if (empty($clearRecord->mdata['yto_posted']) && !in_array($shipment->status, array(ImParcel::STATE_CLEAR_WAIT, ImParcel::STATE_HELD, ImParcel::STATE_DUTY_HELD, ImParcel::STATE_CONCLEAR))) {
                    $successRecord = new stdClass;
                    $successRecord->eventCode = "RC";
                    $successRecord->eventTime = $clearRecord->dt . '' . $timezoneOffset;
                    $successRecord->eventTimeZone = $this->currentTimezone();
                    $successRecord->eventLocation = !empty($clearRecord->depot) ? $clearRecord->depot : $eventLocation;
                    $successRecord->eventDetail = "Customs Cleared";
                    $successRecord->countryCode = "AU";
                    array_push($trackingObj->successList, $successRecord);
                    $clearRecord->mdata['yto_posted'] = 1;
                    $clearRecord->update('meta');
                }    
            }
        } else if (!empty($clearRecord) && empty($clearRecord->mdata['yto_posted']) && !in_array($shipment->status, array(ImParcel::STATE_CLEAR_WAIT, ImParcel::STATE_HELD, ImParcel::STATE_DUTY_HELD, ImParcel::STATE_CONCLEAR))) {
            // In case shipment doesn't have held record, and has cleared record
            $successRecord = new stdClass;
            $successRecord->eventCode = "RC";
            $successRecord->eventTime = $clearRecord->dt . '' . $timezoneOffset;
            $successRecord->eventTimeZone = $this->currentTimezone();
            $successRecord->eventLocation = !empty($clearRecord->depot) ? $clearRecord->depot : $eventLocation;
            $successRecord->eventDetail = "Customs Cleared";
            $successRecord->countryCode = "AU";
            array_push($trackingObj->successList, $successRecord);
            $clearRecord->mdata['yto_posted'] = 1;
            $clearRecord->update('meta');
        }

        if (count($trackingObj->successList) < 1 && count($trackingObj->failList) < 1) {
            return;
        }
        array_push($data, $trackingObj);
        $postData = json_encode($data);
        $sign = $this->calculateSign($postData);
        $header = [];
        $headerClientId = 'clientId: ' . $this->productionAccount;
        $headerSign = 'sign: ' . $sign;
        $headerContentType = 'Content-Type: text/plain';
        array_push($header, $headerClientId);
        array_push($header, $headerSign);
        array_push($header, $headerContentType);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->productionUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $log = $shipment->hbn . " " . $response;
        $this->log($log);
    }

    private function currentTimezone()
    {
        $timezone = new DateTimeZone(date_default_timezone_get());
		$datetime = new DateTime('now', $timezone);
		$offset = $timezone->getOffset($datetime);
		$hours = abs(intdiv($offset, 3600));
		$minutes = abs(intdiv($offset % 3600, 60));
		$timezoneFormatted = sprintf("GMT%s%02d:%02d", ($offset >= 0 ? '+' : '-'), $hours, $minutes);
		return $timezoneFormatted;
    }

    private function timeDiff($time, $clearTime = '')
    {
        $trackingTime = new DateTime($time);
        if (!empty($clearTime)) {
            $now = new DateTime($clearTime);
        } else {
            $now = new DateTime();
        }
		
		$diff = $trackingTime->diff($now);
		$hours = $diff->h + ($diff->days * 24);
		return $hours;
    }

    private function calculateSign($data)
    {
        $content = sprintf("%s%s", $this->productionKey, $data);
        $md5 = md5($content, true);
		$sign = base64_encode($md5);
        return $sign;
    }

    public function log($l)
    {
        $tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'yto_global_api' . DIRECTORY_SEPARATOR ;
        if (!is_dir($tmp)) {
            mkdir($tmp);
        }
		file_put_contents($tmp.'yto_global_api_'  . date('Y-m-d') . '.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
    }
}
