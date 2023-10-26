<?php

class EmailPipeCommand extends CConsoleCommand {
	private $db;
	public $td, $err, $debug, $fromName, $fromEmail, $subject, $body, $atts;

	public function run($args) {
		$this->db = Yii::app()->getDb();
		$this->td = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR;
		$this->err = [];
		$this->debug = isset($args[0]) && $args[0] == '-d';
		require_once(Yii::app()->basePath.'/extensions/mimeparser/rfc822_addresses.php');
		require_once(Yii::app()->basePath.'/extensions/mimeparser/mime_parser.php');
		include_once('PHPMailer/class.phpmailer.php');

		// read from stdin
		$fd = fopen("php://stdin", "r");
		$email = "";
		while (!feof($fd)) {
			$email .= fread($fd, 1024);
		}
		fclose($fd);

		//create the email parser class
		$mime=new mime_parser_class;
		$mime->ignore_syntax_errors = 1;
		$parameters=array(
			'Data'=>$email,
		);

		$mime->Decode($parameters, $decoded);

		//get the name and email of the sender
		$this->fromName = $decoded[0]['ExtractedAddresses']['from:'][0]['name'];
		$this->fromEmail = $decoded[0]['ExtractedAddresses']['from:'][0]['address'];

		//get the subject
		$this->subject = trim($decoded[0]['Headers']['subject:']);
		
		//get body & atts
		$this->body = '';
		foreach($decoded[0]['Parts'] as $i => $part){
			//check for attachments
			if(isset($part['FileDisposition']) && $part['FileDisposition'] == 'attachment'){
				//add file to attachments array
				$tf = tempnam($this->td, "epa");
				file_put_contents($tf, $part['Body']);
				$this->atts[$i.$part['FileName']] = $tf;
			}else{
				if(isset($part['Body'])){
					$this->body .= $part['Body'];
				}elseif(isset($part['Parts'])){
					foreach($part['Parts'] as $pp){
						if(isset($pp['Body'])){
							$this->body .= $pp['Body'];
						}
					}
				}
			}
		}

		if(preg_match('/ID:\s*([^\s]+)/i', $this->subject, $m)){
			$this->cnID($m[1]);
		}elseif(preg_match('/CMT:\s*([\d\-]+)/i', $this->subject, $m)){
			$this->cmt($m[1]);
		}elseif(preg_match('/OTR:BWW:([$\s]+)$/i', $this->subject, $m)){
			$this->bwwOuturn($m[1]);
		}elseif(preg_match('/^RCPT:\s*([\d\-]+)/i', $this->subject, $m)){
			$this->rcptDownload($m[1]);
		}elseif(strtoupper(trim($this->subject)) == 'CNIDS'){
			$this->cnids();
		}elseif(strtoupper(trim($this->subject)) == 'SFFGXX'){
			$this->sffg();
		}

		//remove temp atts
		foreach($this->atts as $n => $f){
			unlink($f);
		}
	}

	public function respond($subj, $body){
		$mail = new PHPMailer();
		$mail->IsHTML(false);
		$mail->From     = "mp@toplogistics.com.au";
		$mail->FromName = "PCAE Mail Processor";
		$mail->Subject  = $subj;
		$mail->Body = $body;
		$mail->AddAddress($this->fromEmail, $this->fromName);
		$mail->send();
	}

	public function cnID($no){
		if($this->debug) echo 'ID: '.$no."\n";
		if(sizeof($atts) < 2){
			$this->err[] = 'Missing Photo';
		}
		$phone = '';
		$hbn = '';

		if(preg_match('/Name:\s*([^\s]+)/i', $this->body, $m)){
			$name = $m[1];
		}else{
			$this->err[] = 'Name not found';
		}

		if(preg_match('/HBN:\s*([^\s]+)/i', $this->body, $m)){
			$hbn = $m[1];
		}
		
		if(preg_match('/Phone:\s*([^\s]+)/i', $this->body, $m)){
			$phone = $m[1];
		}

		if(empty($this->err)){
			$cnid = new CnID;
			$cnid->status = 15;
			$cnid->name = $name;
			$cnid->mobile = $phone;
			$cnid->no = $no;
			$cnid->mdata['connote'] = $hbn;
			$cnid->mdata['fmp'] = 1;
			
			//resize
			$fids = [];
			foreach($this->atts as $n => $f){
				if(sizeof($fids) > 1) break;
				$img = AppHelper::resizeImg($f, 600);
				if($img) @imagejpeg($img, $f);
				$fids[] = FileRepo::storeFile($f, $n, 60, $cnid->id);
			}

			$cnid->front = $fids[0];
			$cnid->back = $fids[1];
			$cnid->joinPhoto();
			$cnid->save();
			if($this->debug) echo 'ID '.$no." saved\n";
		}else{
			if($this->debug){
				echo implode("\n", $this->err)."\n\n----------------------\n".$this->body;
			}else{
				$this->respond("Problem: ".$this->subject, implode("\n", $this->err)."\n\n----------------------\n".$this->body);
			}
		}
	}

	public function bwwOuturn($awb){
		$con = ExcoConsol::model()->find('awb = :a', [':a' => $awb]);
		if(empty($con)){
			$this->err[] = $awb.' Not Found!';
		}else{
			$xls = new oExcel;
			foreach($this->atts as $n => $f){
				$pi = pathinfo($n);
				break;
			}
			$ps = [];
			if(!$xls->supported($f.'.'.$pi['extension'])){
				foreach($xls->getError() as $e){
					$this->err[] = $e;
				}
			}else{
				$xls->load($f);
				$data = $xls->getAll();
				ini_set('precision', 12);
				$hl2 = implode(',', array_slice($data[1],0,2));
				unset($data[1]);
				if($hl2 == "Consignment,Count"){
					$st = time() - rand(2000, 6000);
					foreach($data as $r){
						$r[1] = trim($r[1]);
						$r[2] = trim($r[2]);
						if(empty($r[1])) continue;
						$p = ExParcel::model()->find('consol_id = :id AND hbn = :h', array(':id' => $con->id, ':h' => $r[1]));
						if(empty($p)){
							$this->err[] = $r[1].' not found';
						}else{
							for($j = 1; $j <= $r[2]; $j++){
								if(!empty($p->scan_data[50][$p->hbn.'-'.$j])) continue;
								$pt = $st + rand(10, 100 * $i);
								$p->scan_data[50][$p->hbn.'-'.$j] = date('Y-m-d H:i:s', $pt);
							}
							if(empty($p->mdata['scan_time']))	$p->mdata['scan_time'] = date('Y-m-d H:i:s', $st);
							$p->save();
						}
					}
				}else{
					$this->err[] = 'Manifest Column Mismatch! Need: Consignment,Count; Found: '.$hl2;
				}
			}
		}

		if(!empty($this->err)){
			if($this->debug){
				echo implode("\n", $this->err)."\n";
			}else{
				$this->respond("Problem: ".$this->subject, implode("\n", $this->err)."\n");
			}
		}else{
			$this->respond("Succssful: ".$this->subject, "OTR processed successfully.\n");
		}
	}

	public function rcptDownload($awb){
		$rs = ExcoConsol::model()->findAll('awb = :a', [':a' => $awb]);

		$www_path = realpath(Yii::app()->basePath.'/../../public_html/rcpt/');
		foreach(glob($www_path.DIRECTORY_SEPARATOR.'*.zip') as $f){
			if(filectime($f) < time() - 432000) unlink($f);
		}

		if(empty($rs)){
			$this->err[] = $awb.' Not Found!';
		}else{
			$zfs = [];
			foreach($rs as $model){
				$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.$model->no.'-'.$model->awb.'-RCPT'.date('YmdHi').'.zip';
				$zip = new ZipArchive;
				$zip->open($zf, ZipArchive::CREATE);
				$td = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
				mkdir($td);
				$tpl = $model->getLabelTpl();
				foreach($this->atts as $n => $f){
					if(preg_match('/xls|xlsx$/i', $n)){
						$pi = pathinfo($n);
						break;
					}
				}
				$xls = new oExcel;
				$ps = [];
				if(!$xls->supported($f.'.'.$pi['extension'])){
					foreach($xls->getError() as $e){
						$this->err[] = $e;
					}
				}else{
					$xls->load($f);
					$odata = $xls->getAll();
					ini_set('precision', 12);
					$pdow = [];
					foreach($odata as $odr){
						$pdow[$odr[4]][] = $odr;
					}
				}
				foreach($model->shipments as $p){//gen receipt
					$d = $p->receiptData();
					if(!empty($pdow[$p->ref])){
						if(in_array($model->poc, ['CNJMN', 'CNJM2'])){
							foreach($pdow[$p->ref] as $oi => $odr){
								if(preg_match('/^(.+)([1234]{1}段)婴儿奶粉$/', $odr[5], $m)){
									$pd = ExProdb::model()->find('type = 10 AND brand = :b AND model = :m', [':b' => $m[1], ':m' => str_replace(['1', '2', '3', '4'], ['一', '二', '三', '四'], $m[2])]);
									$odr[12] = $odr[12] * $pd->weight;
								}else{
									$pd = ExProdb::model()->findByPk($p->eitems['pid'][$oi]);
								}

								if(!empty($pd)){
									$p->eitems['pid'][$oi] = $pd->id;
									if($pd->mdata['price_CNJMN'] != $odr[12]){
										$pd->mdata['price_CNJMN'] = $odr[12];
										$pd->noaup = true;
										$pd->save();
									}
								}
								if(preg_match('/休闲鞋/', $odr[5], $m)){
									$p->eitems['gen'][$oi] = 'Sports/Leisure Footware';
								}
							}
						}elseif(in_array($model->poc, ['CNXIA'])){
							foreach($pdow[$p->ref] as $oi => $odr){
								if(preg_match('/^([1234]{1}段)婴儿奶粉,(.+)$/', $odr[2], $m)){
									$pd = ExProdb::model()->find('type = 10 AND brand = :b AND model = :m', [':b' => $m[2], ':m' => str_replace(['1', '2', '3', '4'], ['一', '二', '三', '四'], $m[1])]);
								}else{
									$pd = ExProdb::model()->findByPk($p->eitems['pid'][$oi]);
								}
								$up = $odr[7] / $odr[23];

								if(!empty($pd)){
									$p->eitems['pid'][$oi] = $pd->id;
									if($pd->mdata['price_CNXIA'] != $up){
										$pd->mdata['price_CNXIA'] = $up;
										$pd->noaup = true;
										$pd->save();
									}
								}
								if(preg_match('/休闲鞋/', $odr[5], $m)){
									$p->eitems['gen'][$oi] = 'Sports/Leisure Footware';
								}
								$p->eitems['q'][$oi] = $odr[23];
							}
						}
					}

					$tf = tempnam($td, "rcpt");
					if(in_array($model->poc, ['CNJMN', 'CNJM2'])){
						oPDF::renderPDF('receipt_'.$d['tpl'], array('r' => $p, 'd' => $d), 2, $tf);
						$zip->addFile($tf, (empty($p->ref)? $p->hbn : $p->ref).'.pdf');
					}elseif(in_array($model->poc, ['CNXIA'])){
						if(!empty($p->mdata['AltCnee']) && empty($_GET['real'])){
							$alcn = Addr::model()->findByPk($p->mdata['AltCnee']);
							$p->cnee->name = $alcn->name;
						}

						//label
						$html = oPDF::renderHTML('../expLabel/'.$tpl, array('p'  => $p));
						$tf = tempnam($td, "clabel");
						oPDF::html2image($html, 2, $tf);
						$fn = empty($p->ref)? $p->hbn : $p->ref;

						//id
						$cnid = $p->cnee->cnid;
						$idp = '';
						if(!empty($cnid)){
							if(empty($cnid->joint)){
								$cnid->joinPhoto();
								$cnid->refresh();
							}
							$idp = empty($cnid->joint)? '' : $cnid->photo_joint->getFile();
						}
						
						//rcpt
						$d = $p->receiptData();
						$tf2 = tempnam($td, "rcpt");
						$html = oPDF::renderHTML('../_pdf/receipt_'.$d['tpl'], array('r' => $p, 'd' => $d), true);
						oPDF::html2image($html, 2, $tf2);
						$magick = '/usr/bin/convert';
						AppHelper::exec($magick.' '.$tf.' '.$idp.' '.$tf2.' +append '.$tf);
						$zip->addFile($tf, $fn.'.jpg');
						echo $fn." added\n";
					}
				}
				$zip->close();
				rename($zf, $www_path.DIRECTORY_SEPARATOR.basename($zf));
				AppHelper::unlinkRecursive($td);
				$zfs[] = $zf;
			}
		}

		if(!empty($this->err)){
			if($this->debug){
				echo implode("\n", $this->err)."\n";
			}else{
				$this->respond("Problem: ".$this->subject, implode("\n", $this->err)."\n");
			}
		}else{
			$body = "Recipts have been prepared, please download from\n";
			foreach($zfs as $zf){
				$body .= "https://www.pcaexpress.com.au/rcpt/".basename($zf)."\n";
			}

			$this->respond("Succssful: ".$this->subject, $body);
		}
	}

	public function cnids(){
		foreach($this->atts as $n => $f){
			if(preg_match('/zip$/i', $n)) break;
		}

		$zip = new ZipArchive;
		$zip->open($f);
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR;
		$td = $tmp.'zip'.time();
		mkdir($td);
		for($i = 0; $i < $zip->numFiles; $i++) {
			$filename = $zip->getNameIndex($i);
			if(substr($filename, -1) != '/'){
				$tn = iconv('UTF-8', 'cp437', basename($filename));
				$tn = mb_convert_encoding($tn, 'UTF-8', 'GB18030');
				copy("zip://".$f."#".$filename, $td.DIRECTORY_SEPARATOR.$tn);
			}
		}
		$zip->close();

		$c = 0;
		$msg = '';
		foreach(glob($td.'/*1.jpg') as $ff){
			if(!preg_match('/^([\dXx]{18})(.+)1\.jpg$/', basename($ff), $m)){
				$msg .= $ff." file name format incorrect\n";
				continue;
			}

			$no = $m[1];
			$name = $m[2];
			$bf = dirname($ff).DIRECTORY_SEPARATOR.$no.$m[2].'2.jpg';

			$cnid = CnID::model()->find('no = :no AND status IN (10, 15, 18, 20)', array(':no' => $no));
			if(empty($cnid)){
				if(is_file($bf)){
					$cnid = new CnID;
					$cnid->status = 14;
					$cnid->no = strtoupper($no);

					$cnee = Addr::model()->find('cnid_no = :idn', array(':idn' => $no));
					if(empty($cnee)){
						$cnid->name = $name;
					}else{
						$cnid->name = $cnee->name;
						$cnid->mobile = $cnee->tel;
						$cnid->city = $cnee->city;
					}

					if(!$cnid->save()) $msg .= 'Saving error, '.print_r($cnid->getErrors(), true);
				
					//save images
					foreach(['front' => $ff, 'back' => $bf] as $side => $f){
						$img = AppHelper::resizeImg($f, 600);
						$tf = tempnam($tmp, "idp");
						if($img) @imagejpeg($img, $tf);
						$cnid->{$side} = FileRepo::storeFile($tf, $cnid->no.'_'.($side == 'front'? 1 : 2).'.jpg', 60, $cnid->id);
						unlink($tf);
					}

					$cnid->nolog = true;
					$cnid->save();
					$cnid->refresh();

					if($cnid->front > 0 && $cnid->back > 0){
						$cnid->nolog = false;
						$cnid->joinPhoto();
						$cnid->status = 15;
						$cnid->save();
						$cnid->matchCnee();
					}
					$msg .= $no." added\n";
					$c++;
				}else{
					$msg .= $ff." no back photo\n";
				}
			}else{
				$msg .= $no." already uploaded\n";
			}
		}
		$msg .= $c." ID added\n";
		$this->respond("Succssful: ".$this->subject, $msg);
		AppHelper::unlinkRecursive($td);
	}

	public function cmt($awb){
		$con = ExcoConsol::model()->find('awb = :a', [':a' => $awb]);
		if(empty($con)){
			$this->err[] = $awb.' Not Found!';
		}else{
			$xls = new oExcel;
			foreach($this->atts as $n => $f){
				$pi = pathinfo($n);
				break;
			}
			$ps = [];
			if(!$xls->supported($f.'.'.$pi['extension'])){
				foreach($xls->getError() as $e){
					$this->err[] = $e;
				}
			}else{
				$xls->load($f);
				$data = $xls->getAll();
				ini_set('precision', 12);
				$hl2 = implode(',', array_slice($data[1],0,2));
				unset($data[1]);
				if($hl2 == "运单号,转运单号"){
					foreach($data as $r){
						$r[1] = trim($r[1]);
						$r[2] = trim($r[2]);
						if(empty($r[1])) continue;
						$p = ExParcel::model()->find('consol_id = :id AND hbn = :h', array(':id' => $con->id, ':h' => $r[1]));
						if(empty($p)){
							$this->err[] = $r[1].' not found';
						}elseif($p->status < 90){
							$p->ref = $r[2];
							$ps[] = $p;
						}
					}
				}else{
					$this->err[] = 'Manifest Column Mismatch! Need: 运单号,转运单号; Found: '.$hl2;
				}
			}

			if(empty($this->err)){
				foreach($ps as $p){
					if($p->status < 25) $p->status = 25;
					$p->update(['ref', 'status']);
				}
				if($con->status < 20){
					$cc = ExParcel::model()->count('consol_id = :id AND status = 20', array(':id' => $id));
					if(empty($cc)){
						$con->status = 20;
						$con->update(['status']);
					}
				}
			}
			unset($xls);
		}

		if(!empty($this->err)){
			if($this->debug){
				echo implode("\n", $this->err)."\n";
			}else{
				$this->respond("Problem: ".$this->subject, implode("\n", $this->err)."\n");
			}
		}else{
			$this->respond("Succssful: ".$this->subject, "CMT processed successfully.\n");
		}
	}
}
