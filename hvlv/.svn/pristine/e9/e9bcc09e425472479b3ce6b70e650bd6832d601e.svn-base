<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ApiImageAction extends CAction{
		
		public $ctr,$debug,$user;
		
		public function run(){
				$this->ctr=$this->getController();
				$this->user=empty($this->ctr->user)?FALSE:User::model()->findByPk($this->ctr->user);
				if(!empty($_POST['method'])&& method_exists($this, $_POST['method'])){
						$this->{$_POST['method']}();
					 
				}else{
						throw new CHttpException(400,"API method not found!");
				}
				
				
		}
		
		public function log($l){
				$tmp=Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'imageapi'.DIRECTORY_SEPARATOR;
				file_put_contents($tmp.'image_api_'.date('Y-m-d'),date('Y-m-d H:i:s').' '.$l."\n",FILE_APPEND);
				
				
		}
		
		public function import(){
				$data=json_decode(json_encode($this->ctr->data->data),TRUE);
				if(empty($data)) {
						echo 0;
						return;
				}
				foreach ($data as $key=>$d){
					$trans = Yii::app()->db->beginTransaction();
					try{
						$this->getJasonData($key,$d);
						$trans->commit();
					} catch (Exception $ex) {
						$trans->rollback();
						throw $ex;
					}
				}
				echo 1;
		 }
			 
		public function  get(){
				if(!empty($this->ctr->data->pdf)){
							 $r= ExImage::model()->find('pdf_number= :pdf_number',array(':pdf_number'=>$this->ctr->data->pdf));
							 if(!empty($r)){
									 
									 echo 1;
							 }else {
									 echo 0;
							 }
							
		}
	}
			public function hs(){
					 $term=$this->ctr->data->data->term;
				 CustomRate::suggestHs($term);
			}
			
			public function rate(){
					$data=$this->ctr->data->data;
					$o=new stdClass();
					$rate= CustomRate::getRate($data->hs);
					if($rate[0]==0){
							$o->status=0;
							$o->msg='the goods is prohibited!';
							$o->rate=$rate[1];
					}else{
							$currency=Currency::getExrate()[0];
							if(preg_match('/USD/i', $data->currency)){
									$goods_value=round($data->good_value/$currency,2);
							}else{
									$goods_value=$data->good_value;
							}
							$duty=$goods_value*$rate[1];
							$o->status=1;
							$o->rate=$rate[1];
							$o->duty=$duty;
							$o->msg="success";
							$o->good_value=$goods_value;
					}
					echo json_encode($o);
			}
			
			 public function gst(){
						$data=$this->ctr->data->data;
						$o=new stdClass();
						$currency=Currency::getExrate()[0];
						$rate= CustomRate::getRate($data->hs);
					if($rate[0]==0){
							$o->status=0;
							$o->msg='the goods is prohibited!';
							$o->rate=$rate[1];
							echo json_encode($o);
							return;
					}else{
							$currency=Currency::getExrate()[0];
							if(preg_match('/USD/i', $data->currency)){
									$good_value=round($data->good_value/$currency,2);
							}else{
									$good_value=$data->good_value;
							}
						 $duty=$good_value*$rate[1];
						 $insurance=$good_value*0.000025;
					 if($data->delivery=='air'){
								$weight=max($data->cbm*167,$data->weight);
								$delivery_fee=$data->delivery_rate*$weight/$currency;
							}else{
								$delivery_fee=$data->cbm*$data->delivery_rate/$currency;
							}
							$gst=round(($good_value+$duty+$delivery_fee+$insurance)*0.1,2);
							$o->status=1;
							$o->msg='success';
							$o->gst=$gst;
							$o->good_value=$good_value;
							echo json_encode($o);
					 }
		}
		
	 //deal with jsondata and save to our database.
		public function getJasonData($f, $d) {
				$lines = $d; //array
				$agent_id = '';
				foreach ($lines as $line_number => $line) {
						$data = json_decode($line, true);
						if ($data['err'] == FALSE) {
								$hbn = $data['symbols'][0]['code'];        // only consider one line one barcode in the json file.
						} else {
								$hbn = '';
								continue;
						}
						if (!empty($hbn)) {
								$s_id = ExParcel::model()->find('hbn=:hbn', array(':hbn' => $hbn));
								$agent_id = empty($s_id->hbn) ? '' : $s_id->agent_id;
						}
						if (!empty($agent_id))
								break;
				}
//       
				foreach ($lines as $line_number => $line) {
						$data = json_decode($line, true);
						$this->save2Data($data, $agent_id, $f);
//             
//        }
//    
				}
		}
	 
		public function save2Data($data,$aid,$f){
				// find agent_id at first     
				$e= ExImage::model()->find('file_adr=:file_adr',array(':file_adr'=>$data['file']));
				if(empty($e)){
						$p = new ExImage();
						$p->file_adr = $data['file'];
						$p->pdf_number=$f;  //need to change later as the file name need to be dynamic
						//$p->date= date('Y-m-d', strtotime(substr($p->pdf_number, -19,8)));
						preg_match('/^[\d]+_(\d{8})/', basename($p->pdf_number), $m);
						$p->date= date('Y-m-d', strtotime($m[1]));
						if($data['err']==FALSE){
							 $p->hbn=$data['symbols'][0]['code'];
							 $s_id= ExParcel::model()->find('hbn=:hbn',array(':hbn'=>$p->hbn));
							 $p->islinked=empty($s_id->hbn)?2:1;   
						}else{
								$p->hbn='';
								$p->islinked=0;
						}
						$p->agent_id=$aid;
						
						//need to change        //0 unregonize need human working 1:linked  2:has hbn, but not build Exparcel yet
						$p->save();
				}else{                                   //update the record;
					$e->pdf_number=$f;  //need to change later as the file name need to be dynamic
					//$e->date= substr($e->pdf_number, 0,4).'-'.substr($e->pdf_number,4,2).'-'.substr($e->pdf_number, 6,2);
					preg_match('/^[\d]+_(\d{8})/', basename($e->pdf_number), $m);
					$e->date= date('Y-m-d', strtotime($m[1]));
					if($data['err']==FALSE){  
						 $e->hbn=$data['symbols'][0]['code'];
						 $s_id= ExParcel::model()->find('hbn=:hbn',array(':hbn'=>$e->hbn));
						 $e->islinked=empty($s_id->hbn)?2:1;
					}else{
							$e->hbn='';
							$e->islinked=0;
					}
					$e->agent_id=$aid;
					$e->save();
				}
			 }
			 
	 
			 
			 
}