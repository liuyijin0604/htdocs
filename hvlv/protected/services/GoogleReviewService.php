<?php
class GoogleReviewService extends Service
{
    public function storeGoogleReviewRecords()
    {
        $numTimeNow = time();
		$numTimeFrom = strtotime('-1 day',$numTimeNow);

		$strTimeNow = date("Y-m-d H:i:s", $numTimeNow);
		$strTimeFrom = date("Y-m-d H:i:s", $numTimeFrom);
		
		$numLimitHours = 72;

		$objCGoogleReview = new CGoogleReview;
		$listRecord = $objCGoogleReview->funcCreateListRecord($strTimeFrom,$strTimeNow,$numLimitHours);
        $regExpPhonePattern = '/^(\d)\1+$/';        // For checking cnee_tel

        foreach ($listRecord as $record)
        {
            $attributes = ['hbn'=>$record->hbn];
            $temp = GoogleReview::model()->findByAttributes($attributes);
            if (!empty($temp)){
                continue;
            }
            if (preg_match($regExpPhonePattern, $record->cnee_tel) == 1){
                continue;
            }
            $googleReview = new GoogleReview();
            $googleReview->hbn = $record->hbn;
            $googleReview->ref = $record->ref;
            $googleReview->time_start = $record->time_start;
            $googleReview->time_done = $record->time_done;
            $googleReview->cnee_name = $record->cnee_name;
            $googleReview->cnee_email = $record->cnee_email;
            $googleReview->cnee_tel = $record->cnee_tel;
            $googleReview->cnor_city = $record->cnor_city;
            $googleReview->cnor_state = $record->cnor_state;
            $googleReview->pkg = $record->pkg;
            $googleReview->weight = $record->weight;
            $googleReview->time_arrived = $record->time_arrived;
            $googleReview->shipment_id = $record->shipment_id;
            $googleReview->cargo_process_id = $record->cargo_process_id;
            $googleReview->status = GoogleReview::DELIVERED;
            $googleReview->time_created = $strTimeNow;
            $googleReview->save();
        }
    }

    public function getNumCount($type, $period)
    {
        $numTimeToday = strtotime(date('Y-m-d 00:00:00'));
        if($period == 1){
            $numTimeFrom = $numTimeToday;
        }
        elseif($period == 7){
            $numTimeFrom = $this->last_monday($numTimeToday);
        } else {
            $numTimeFrom = strtotime(date('Y-m-01 00:00:00'));
        }
        $strTimeFrom = date("Y-m-d H:i:s", $numTimeFrom);

        if ($type == GoogleReview::DELIVERED) {
            $sql = 'SELECT COUNT(*) FROM google_review WHERE `time_created`>= "'.$strTimeFrom.'"';
        }
        elseif ($type == GoogleReview::CALLED_NO_ANSWER) {
            $sql = 'SELECT COUNT(*) FROM google_review WHERE `status` = 20 AND `time_processed` >= "'.$strTimeFrom.'"';
        }
        elseif ($type == GoogleReview::EMAIL_SENT) {
            $sql = 'SELECT COUNT(*) FROM google_review WHERE `status` = 30 AND `time_processed` >= "'.$strTimeFrom.'"';
        }
        elseif ($type == GoogleReview::REFUSED) {
            $sql = 'SELECT COUNT(*) FROM google_review WHERE `status` = 40 AND `time_processed` >= "'.$strTimeFrom.'"';
        }
		$count = Yii::app()->db->createCommand($sql)->queryScalar();
        return $count;
    }

    private function last_monday($date)
    {
        if (!is_numeric($date)) {
            $date = strtotime($date);
        }	
		if (date('w', $date) == 1) {
            return $date;
        } else {
            return strtotime(
				'last monday',
				$date
			);
        }		
    }

    public function sendReviewEmail($objGoogleReview)
    {
        $emailService = new EmailService;
        $strSubject = 'RE: Shipment NO. '.$objGoogleReview->ref.' from '.$objGoogleReview->cnor_state.'  China';
        $strHtml='';
        $strHtml .= 'Hi '. $objGoogleReview->cnee_name.',<br/><br/>';
        $strHtml .= 'As we truly value you as a customer, we are thrilled to know that you are happy with our service. Please take a moment to click the link below and give us a positive feedback on Google Review. Your support will help us go a long way in providing excellent delivery service.<br/>';
        $strHtml .= '[ <a href="https://g.page/top-logistics-australia/review?rc"><u>https://g.page/top-logistics-australia/review?rc</u></a> ]<br/><br/>';
        $strHtml .= 'TLA Customer Service';

        $emailService->funcSendGoogleReviewEmail($objGoogleReview->cnee_email,$strSubject,$strHtml);
    }

    public function sendReviewSMS($objGoogleReview)
    {
        $emailService = new EmailService();
        $strMessage1 = 'Hi '. $objGoogleReview->cnee_name.',
			As we truly value you as a customer, we are thrilled to know that you are happy with our service.';
        $strMessage2 = '
        Please take a moment to click the link below and give us a positive feedback on Google Review.
        [ https://g.page/top-logistics-australia/review?rc ]
        TLA Customer Service';
		$emailService->funcSendGoogleReviewSms($objGoogleReview->cnee_tel, $strMessage1);
        $emailService->funcSendGoogleReviewSms($objGoogleReview->cnee_tel, $strMessage2);
    }
}

?>