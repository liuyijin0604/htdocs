<?php

class ToolsController extends Controller{
	
	protected $nonAjax=array('downloadManifest', 'downloadLabels');

	public function actionIndex(){
		$this->render('index');
	}

	public function actionManifest(){
		if(!empty($_FILES)){
			$model=new Manifest('upload');
			$model->fwd_id = Yii::app()->user->org;
			$model->by_id = Yii::app()->user->id;
			$model->type = 110;
			if(SellRate::hasExRate(Yii::app()->user->org, 'EC') && empty($_POST['cfm']) && $model->validFile()){
				$o = new StdClass;
				$o->err = [];
				$o->duty = [];
				$o->vdt = true;
				$o->tw = 0;
				$o->tp = 0;
				$xls = new oExcel;
				$xls->load($model->file['tmp_name']);
				$data = $xls->getAll();
				ini_set('precision', 12);
				unset($xls);
				unset($data[1]);
				foreach($data as $l=>$r){
					if(empty($r[1])) continue;
					if(empty($r[22]) && empty($r[23])) break;
					$tduty = 0;
					$gs = [];
					for($j = 0; $j < 100; $j++){
						if($j > 0 && (empty($data[$l+$j]) || !empty($data[$l+$j][1]))) break;
						if($model->mtype == '110n' && !empty($data[$l+$j][33])){
							$pd = ExProdbPrice::model()->with('prod')->find('sku = :sku', [':sku' => $data[$l+$j][33]]);
						}else{
							$pd = ExProdbPrice::model()->with('prod')->find('name_zh LIKE :n OR sku = :nt', [':n' => '%'.$data[$l+$j][23].'%', ':nt' => $data[$l+$j][23]]);
						}
						if(empty($pd)){
							$o->err[$l+$j] = 'Unable to rate product, SKU: '.(empty($data[$l+$j][33])? '' : $data[$l+$j][33]).' Name: '.$data[$l+$j][23];
						}else{
							$v = $pd->getPrice();
							$exc = ExChannel::model()->find('code = :c', [':c' => 'CNCA3']);
							$vr = HsPoc::getTariff($pd->prod->hs, $exc, $v, $pd->prod->weight);
							$tr = $vr[1];
							$v = $vr[0];
							$tduty += $tr * $v * $data[$l+$j][28];
							$gs[] = $pd->getName().'x'.$data[$l+$j][28];
						}
					}
					$tduty = $tduty > 50? $tduty : 0;
					$wt = round($r[20]*100)/100;
					if($tduty > 0){
						$o->duty[$l] = [$r[2], implode(', ', $gs), $wt, $tduty];
					}
					$o->tw += $wt;
					$o->tp++;
				}
				echo json_encode($o);
			}else{
				if(!empty($_POST['exclude'])) $model->exclude_lines = $_POST['exclude'];
				$model->save();
				$this->ajaxResult($model, array('id', 'warns'));
			}
		}
	}

	public function actionManiHistory(){
		$rs = Manifest::model()->findAll([
			'condition' => 'fwd_id = :o AND type = 110', 
			'params' => [':o' => Yii::app()->user->org],
			'order' => 'id DESC',
			'limit' => '20',
			]);
		foreach($rs as $r){
			if(Yii::app()->user->grp == 82 && $r->by_id != Yii::app()->user->id) continue;
			echo '<p><a href="'.$this->createUrl('tools/downloadManifest', ['id' => $r->id]).'">'.$r->getFileName().'</a> '.$r->totPacks().' shipments ('.$r->created.') <a href="'.$this->createUrl('tools/downloadLabels', ['id' => $r->id, 'type' => 'pdf']).'" target="_blank">PDF Labels</a> / <a href="'.$this->createUrl('tools/downloadLabels', ['id' => $r->id, 'type' => 'jpg']).'" target="_blank">JPG Labels</a></p>';
		}
	}

	public function actionProducts(){
		$model = new ExProdbPrice('search');
		$model->status = 1;
		$model->type = 20;
		$model->agt_id = Yii::app()->user->org;
		$this->render('products', ['model' => $model]);
	}

	public function actionBulkID(){
		if(!empty($_FILES['file'])){
			$no = null;
			if(preg_match('/^([\dX]{18})[_\-]*([^_\-]*)[_\-]*([abAB12]{1})\.(jpg|jpeg)$/', $_FILES['file']['name'], $m)){
				$no = $m[1];
				$name = $m[2];
			}elseif(preg_match('/^([^_\-]*)[_\-]*([\dX]{18})[_\-]*([abAB12]{1})\.(jpg|jpeg)$/', $_FILES['file']['name'], $m)){
				$no = $m[2];
				$name = $m[1];
			}

			if(!empty($no)){
				$cnid = CnID::model()->find('no = :no AND status IN (10,14,15,18,20) AND bwf < 3', array(':no' => $no));
				if(empty($cnid)){
					$cnid = new CnID;
					$cnid->status = 14;
					$cnid->no = strtoupper($no);
					$cnid->name = empty($name)? 'EMPTY' : $name;
					$cnid->save();
				}

				if($cnid->status < 15){
					$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR;
					$img = AppHelper::resizeImg($_FILES['file']['tmp_name'], 600);
					$tf = tempnam($tmp, "idp");
					if($img) @imagejpeg($img, $tf);
					$side = in_array($m[3], ['1','A','a'])? 'front' : 'back';

					$ocrSuccess = function($r)use(&$cnid, $tf){
						if(!empty($r->id)){
							$cnid->name = $r->name;
							$cnid->no = $r->id;
							$cnid->save();
							$bin = $r->frontimage;
						}else{
							$bin = $r->backimage;
						}
						file_put_contents($tf, base64_decode($bin));
					};

					$r = CnID::youtuOCR($side == 'front'? 0 : 1, $tf);
					if($r->errorcode === 0){
						$ocrSuccess($r);
					}else{//flip side and try
						$r = CnID::youtuOCR($side == 'back'? 0 : 1, $tf);
						if($r->errorcode === 0){
							$side = $side == 'front'? 'back' : 'front';
							$ocrSuccess($r);
						}
					}

					$cnid->{$side} = FileRepo::storeFile($tf, $cnid->no.'_'.($side == 'front'? 1 : 2).'.jpg', 60, $cnid->id);
					unlink($tf);
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
				}
			}
			echo 'DONE';
			Yii::app()->end();
		}
		$this->render('bulk_id');
	}

	public function actionProdUp($id){
		$model = ExProdbPrice::model()->findByPk($id);
		if($model===null || $model->agt_id != Yii::app()->user->org)
			throw new CHttpException(404,'The requested page does not exist.');
		if(!empty($_POST['ExProdbPrice'])){
			$model->sn = $_POST['ExProdbPrice']['sn'];
			$model->name = $_POST['ExProdbPrice']['name'];
			$model->save();
			$this->ajaxResult($model);
		}
		$this->render('prod_update', ['model' => $model]);
	}

	public function actionDownloadManifest($id){
		$m = Manifest::model()->findByPk($id);
		if($m->fwd_id != Yii::app()->user->org) throw new CHttpException(404, 'Not Found!');
		$rs = ExParcel::model()->findAll('man_id = :id', [':id' => $id]);
		$xls = new oExcel;
		$xls->setColWidth([10,15,15,12,12,40,10,10,10,10,12,12,40,10,10,10,10,10,10,10,10,10,40,15,15,10,10,10,15,10,30]);
		$i = 1;
		$xls->addRow($i++, ['序号','运单号','参考号','发货人','电话','地址','市/区','洲/省','邮编','国家','收货人','电话','地址','区','市','洲/省','邮编','国家','包裹数量','毛重(kg)','体积(m3)','分类','中文品名','品牌','规格','申报货币','申报单价','件数','HS编码','保费','备注']);
		foreach($rs as $ri => $r){
			foreach($r->eitems['g'] as $ii => $g){
				$row = [$r->eitems['type'][$ii], $g, $r->eitems['b'][$ii], '="'.$r->eitems['m'][$ii].'"', '', '', $r->eitems['q'][$ii], '="'.$r->eitems['hs'][$ii].'"'];
				if($ii == 0){
					$xls->addRow($i++, array_merge([$ri+1, $r->hbn, empty($r->cref)? '' : $r->cref, $r->cnor->name, '="'.$r->cnor->tel.'"', $r->cnor->address, $r->cnor->suburb, $r->cnor->state, '="'.$r->cnor->postcode.'"', $r->cnor->country, $r->cnee->name, '="'.$r->cnee->tel.'"', $r->cnee->address, $r->cnee->suburb, $r->cnee->city, $r->cnee->state, '="'.$r->cnee->postcode.'"', $r->cnee->country, $r->pkg, $r->weight, $r->cbm], $row, [$r->insurance, $r->note]));
				}else{
					$xls->addRow($i++, array_merge(['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''], $row, ['', '']));
				}
			}
		}
		$f = preg_replace('/\..+$/', '', $m->getFileName());
		$xls->output(urlencode($f.'.xlsx'));
	}

	public function actionDownloadLabels($id){
		$m = Manifest::model()->findByPk($id);
		if($m->fwd_id != Yii::app()->user->org) throw new CHttpException(404, 'Not Found!');
		$rs = ExParcel::model()->findAll('man_id = :id', [':id' => $id]);
		if(empty($_GET['type']) || $_GET['type'] == 'pdf'){
			oPDF::renderPDF('label_A6', array('tpl' => '_label-ex', 'empty' => false, 'rs' => $rs));
		}elseif($_GET['type'] == 'jpg'){
			$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.date('Ymd', strtotime($m->created)).'_labels.zip';
			$zip = new ZipArchive;
			$zip->open($zf, ZipArchive::CREATE);
			$copied = [];
			$td = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
			if(!file_exists($td)) mkdir($td);

			foreach($rs as $p){
				$tf = tempnam($td, "clabel");
				oPDF::renderImage('label_A6', array('tpl' => '_label-ex', 'empty' => false, 'rs' => [$p]), 2, $tf);
				$zip->addFile($tf, $p->hbn.'.jpg');
			}
			$zip->close();
			
			header("Cache-Control: maxage=1");
			header("Content-Description: File Transfer");
			header("Content-type: application/octet-stream");
			header('Content-Disposition: attachment; filename="'.basename($zf).'"');
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($zf));
			readfile($zf);
			unlink($zf);
			AppHelper::unlinkRecursive($td);
			Yii::app()->end();
		}
	}

}