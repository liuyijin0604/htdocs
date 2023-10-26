<?php
if(!empty($_POST['term'])){
  $data=['term'=>$_POST['term']];
  $a=request('hs',$data);
  echo $a;
}
/* for duty
*/
if(!empty($_POST['duty'])){
  if(empty($_POST['hs_code'])||!preg_match('/\d{4}\.\d{2}\.\d{2}/i',$_POST['hs_code']))  {
    echo json_encode(['msg'=>'please input the valid hs code xxxx.xx.xx']); 
    return;
  }
  if(empty($_POST['good_cost'])) {
    echo json_encode(['msg'=>'please input the good value']); 
    return;
  }
    $data=['currency'=>$_POST['currency'],'good_value'=>$_POST['good_cost'],'hs'=>$_POST['hs_code']];
    $b=request('rate',$data);
    echo $b;
}

/* for gst
*/
if(!empty($_POST['gst'])){
  if(empty($_POST['hs_code'])||!preg_match('/\d{4}\.\d{2}\.\d{2}/i',$_POST['hs_code']))  {
    echo json_encode(['msg'=>'please input the valid hs code xxxx.xx.xx']); 
    return;
  }
  if(empty($_POST['good_cost'])) {
    echo json_encode(['msg'=>'please input the good value']); 
    return;
  }
  if(empty($_POST['good_weight'])) {
    echo json_encode(['msg'=>'please input the good weight']); 
    return;
  }
  if(empty($_POST['delivery_rate'])) {
    echo json_encode(['msg'=>'please input the delivery rate']); 
    return;
  }
  if(empty($_POST['good_cbm'])) {
    echo json_encode(['msg'=>'please input the good volume']); 
    return;
  }
  $data=['currency'=>$_POST['currency'],'good_value'=>$_POST['good_cost'],'hs'=>$_POST['hs_code'],'weight'=>$_POST['good_weight'],
         'delivery_rate'=>$_POST['delivery_rate'],'delivery'=>$_POST['delivery'],'cbm'=>$_POST['good_cbm']
        ];
  $b=request('gst',$data);
  echo $b;
}



 function request($method='hs',$data){
  require_once('curl.php');
 $j=new stdClass();
  $j->data=$data;
  $d=[
    'api_id'=>'ddt', //API ID
    'method'=>$method,
    'data'=>json_encode($j),
  ];
  ksort($d);
  $key='c546f1f3777495b41707ec40ac1646b721221a5c'; //API KEY
  $s=$key;
  foreach($d as $k=>$v){
      if(empty($v)) continue;
      $s.=$k.$v;
  }
  $s.=$key;
  $d['sign']=strtoupper(md5($s));
  $c=new curl('http://api.pcaexpress.com.au/api/image');
  $data_string=$c->asPostString($d);
  $c->setopt(CURLOPT_CUSTOMREQUEST, "POST");
  $c->setopt(CURLOPT_POSTFIELDS, $data_string);
  $c->setopt(CURLOPT_RETURNTRANSFER, true);
  $c->exec();
  return $c->result;
}


?>