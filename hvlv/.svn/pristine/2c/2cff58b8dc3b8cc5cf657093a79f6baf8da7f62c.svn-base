<?php

class BwtrunkAPI{

  private $url='https://bwapp.jplms.com.au/api/jobs/info/8Oj588tWhqwzVaNJvJ4KjWrZScoepoR5HDlFKtTl';
  
  public function getData(){
  	    $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data, true);
        $data = $data['data'];
        return $data;

  }

}


// $api = new BwtrunkAPI();
// $data = $api->getData();
// var_dump($data);
