<?php
class PickupBookingService extends Service
{
    private $apiKeySyd = 'alex.zhang@toplogistics.com.au:638064134848387967.NjM4MDY0MTM0ODQ4Mzg3OTY3ODBlMlZiTTUtWjM1OTBAMjczNA==';
    private $apiKeyMel = 'alex.zhang@toplogistics.com.au:638063999923749972.NjM4MDYzOTk5OTIzNzQ5OTcyQG5AZk53MTBaQjM1OTJAMjczMw==';
    private $apiKeyBne = 'alex.zhang@toplogistics.com.au:638064135782017028.NjM4MDY0MTM1NzgyMDE3MDI4Sj8zN2tqQEMyVjM1OTFAMjczNQ==';
    public function completeInboundBooking($bookingNo, $dptId)
    {
        $curl = curl_init();
        $apiKey = '';
        switch($dptId){
            case 106:
                $apiKey = $this->apiKeySyd;
                break;
            case 218:
                $apiKey = $this->apiKeyMel;
                break;
            case 530:
                $apiKey = $this->apiKeyBne;
                break;
        }
        $postfields = [];
        array_push($postfields, $bookingNo);
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.inboundconnect.com/api/services/app/externalintegration/completebookings',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postfields),
        CURLOPT_HTTPHEADER => array(
            'Inbound-API-Key: ' . $apiKey,
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        if ($response->success) {
            return true;
        } else {
            return false;
        }
    }
}