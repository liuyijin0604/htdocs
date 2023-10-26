<?php

class ExParcelController extends Controller{

	protected $nonAjax = array('export', 'elabel', 'label', 'transLabel', 'authLetter', 'receipt', 'idReport', 'dupReport', 'ecreate','newTag', 'refMap');

	/**
	* Displays a particular model.
	* @param integer $id the ID of the model to be displayed
	*/
	public function actionView($id){
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}
	
	public function actionRemoveWarn($id){
		$model=$this->loadModel($id);
		if(!empty($_GET['b'])){
			$model->bwf = $model->bwf & (~(int) $_GET['b']);
			$model->custom_log_note = 'Warning ['.ExParcel::$bwfs[$_GET['b']].'] removed';
			$model->save();
		}
		echo 'done';
	}

	public function actionNotes($id){
		$model=$this->loadModel($id);
		if(!empty($_POST['notes'])){
			$log = Log::add($model, 6, array('notes' => $_POST['notes']));
			$this->ajaxResult($log);
		}
	}

	/**
	* Creates a new model.
	* If creation is successful, the browser will be redirected to the 'view' page.
	*/
	public function actionCreate(){
		$model=new ExParcel;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ExParcel'])){
			$model->setScenario('create');
			$model->attributes=$_POST['ExParcel'];
			$model->hbn = strtoupper(trim($model->hbn));

			if(!Acl::hasAccess('C:org/exAgentSuggest')) $model->agent_id = Yii::app()->user->org;
			$model->eitems = $_POST['items'];
			$cnor = new Addr;
			$cnor->attributes = $_POST['Cnor'];
			$cnor->save();
			$model->cnor_id = $cnor->id;
			$cnee = new Addr;
			$cnee->attributes = $_POST['Cnee'];
			if(!empty($cnee->cnid_id)) $cnee->cnid_no = '';
			$acc = $cnee->checkCnAddr();
			if(empty($cnee->postcode)){
				$cnee->postcode = CnArea::getZip($cnee->state, $cnee->city);
			}
			$cnee->save();
			$model->cnee_id = $cnee->id;
			$model->state = $cnee->state;
			$model->postcode = $cnee->postcode;
			$model->save();
			$this->ajaxResult($model);
		}

		$model->pkg = 1;

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	* Updates a particular model.
	* If update is successful, the browser will be redirected to the 'view' page.
	* @param integer $id the ID of the model to be updated
	*/
	public function actionUpdate($id){
		$model=$this->loadModel($id);

		if(!Acl::hasAccess('B:Export/SeeAllShipments') && !empty($model->agent_id) && $model->agent_id != Yii::app()->user->org) Acl::denied403();

		if(isset($_POST['ExParcel'])){
			$oldStatus = $model->status;
			if($model->status > 18 && !Acl::hasAccess('B:Export/UpdateParcelAfterConsolidation')) Acl::denied403();
			if(!empty($_POST['ExParcel']['status']) && !Acl::hasAccess('B:Export/StatusOverride')) Acl::denied403();
			if(!Acl::hasAccess('C:org/exAgentSuggest') && empty($model->agent_id)) $model->agent_id = Yii::app()->user->org;
			if(empty($model->cnor)) $model->cnor = new Addr;
			if(empty($model->cnee)) $model->cnee = new Addr;
			$model->cnor->attributes = $_POST['Cnor'];
			$model->cnor->save();
			//reset CnID if name changes
			if($model->cnee->name != $_POST['Cnee']['name'] && $model->cnee->cnid_id == $_POST['Cnee']['cnid_id']) $_POST['Cnee']['cnid_id'] = 0;
			$model->cnee->attributes = $_POST['Cnee'];
			if(!empty($model->cnee->cnid_id)) $model->cnee->cnid_no = '';
			$model->cnee->save();
			$model->attributes=$_POST['ExParcel'];
			if(empty($model->cnor_id)) $model->cnor_id = $model->cnor->id;
			if(empty($model->cnee_id)) $model->cnee_id = $model->cnee->id;
			if($model->status > 20){
				foreach($_POST['items'] as $k => $v){
					$model->eitems[$k] = $v;
				}
			}else{
				$model->eitems = $_POST['items'];
			}
			if(!empty($_POST['meta'])){
				foreach($_POST['meta'] as $k => $v){
					$model->mdata[$k] = $v;
				}
			}
			if($model->status == 14 && !empty($model->cnee->name)) $model->status = 15;
			$model->state = $model->cnee->state;
			$model->postcode = $model->cnee->postcode;
			$model->save();
			if(!empty($_POST['confirm'])){
				$model->checkInfoReady(true);
			}

			// for returned item create returned credit note
			// 104 : returned item
			if($oldStatus != 104 && !empty($_POST['ExParcel']['status']) && intval($_POST['ExParcel']['status']) == 104 ) {
				Payment::createReturnedCreditNote($model);
			}

			$this->ajaxResult($model);
		}
		
		if(isset($_GET['tab'])){
			Acl::hasAccess($this->CaName.'/'.$_GET['tab'], true);
			if ( $_GET['tab'] == 'crm' ) {
				$this->render('/crm/tab_' . $_GET['tab'], array('model' => $model));
			} else {
				$this->render('tab_' . $_GET['tab'], array('model' => $model));
			}
		}else{
			$this->render('update',array('model'=>$model));
		}
	}

	public function actionFetchTracking($id){
		$model=$this->loadModel($id);
		include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR.'commands'.DIRECTORY_SEPARATOR.'trackingCommand.php');
		$tc = new trackingCommand('tracking', 'run');
		$tc->run(['update', $model->hbn]);
		echo json_encode('done');
	}
	
	public function actionItemsGrid($id){
		$model=$this->loadModel($id);
		if(!empty($_POST['ExItem'])){
			if(empty($_POST['ExItem']['id'])){
				$it = &$model->eitems[];
			}else{
				$it = &$model->eitems[$_POST['ExItem']['id']];
				unset($_POST['ExItem']['id']);
			}
			foreach($_POST['ExItem'] as $k => $v){
				$it[$k] = $v;
			}
			$model->save();
			$this->ajaxResult($model);
		}
	}

	public function actionCnorSuggest(){
		$rs = ExParcel::model()->with('cnor')->findAll([
				'condition' => 't.agent_id = :a AND (cnor.name LIKE :t OR cnor.tel LIKE :t) AND LENGTH(cnor.tel) > 9',
				'order' => 'cnor.name,cnor.tel',
				'group' => 'cnor.name,cnor.tel',
				'params' => [':a' => $_GET['agt'], ':t' => $_GET['term'].'%']
			]);
		$a = [];
		foreach($rs as $r){
			$attr = $r->cnor->attributes;
			unset($attr['id'], $attr['cnid_id'], $attr['owner_id'], $attr['acc']);
			$a[] = $attr + ['label' => $r->cnor->name.'/'.$r->cnor->tel];
		}
		echo json_encode($a);
	}

	public function actionCneeSuggest($id=0){
		$rs = Addr::model()->findAll(array(
			'condition' => "t.id IN (SELECT MAX(id) FROM addr a WHERE a.city != '' AND a.tel != '' AND a.id != :id AND (a.name LIKE :t OR a.tel LIKE :t) GROUP BY name,tel)",
			'params' => array(':t' => $_GET['term'].'%', ':id' => $id),
			'limit' => 20,
			'order' => 't.id DESC',
		));

		$a = array();
		foreach($rs as $r){
			$attr = $r->attributes;
			unset($attr['id'], $attr['cnid_id'], $attr['owner_id'], $attr['acc']);
			$a[] = $attr + array(
				'value' => $r->name,
				'label' => $r->name.' ('.$r->city.'/'.$r->tel.')',
				'cnid' => $r->cnid_id,
				'cnid_no' => $r->cnid_id > 0? $r->cnid->no : '',
			);
		}
		echo json_encode($a);
	}
	
	public function actionTrackingGrid($id){
		if(empty($_POST['Tracking']['id'])){//add
			$pt = new Tracking;
			$pt->pid = $id;
		}else{
			$pt = Tracking::model()->findByPk($_POST['Tracking']['id']);
		}
		$pt->attributes = $_POST['Tracking'];
		$pt->save();
		$this->ajaxResult($pt);
		
	}

	public function actionRefMap(){
		$f = Yii::app()->basePath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'ref_map.php';
		if(!empty($_POST['map'])){
			$rs = explode("\n", $_POST['map']);
			$map = [];
			foreach($rs as $r){
				$m = preg_split('/[\s,;|]+/', $r);
				$map[$m[0]] = $m[1];
			}
			file_put_contents($f, "<?php\nreturn ".var_export($map, true).';');
			$this->redirect('refMap');
		}
		$rmap = include($f);
		$this->render('refmap', ['rmap' => $rmap]);
	}

	public function actionCheckHbn(){
		$m = ExParcel::model()->find('hbn = :hbn', [':hbn' => $_GET['hbn']]);
		echo empty($m)? 0 : $m->id;
	}

	/**
	* Lists and search.
	*/
	public function actionList(){
		$model=new ExParcel('search');
		$model->unsetAttributes(); // clear any default values
		if(isset($_GET['ExParcel'])) {
			if (!empty($_GET['ExParcel']['prod'])) {
				$model->prod = $_GET['ExParcel']['prod'];
				unset($_GET['ExParcel']['prod']);
			}
			$model->attributes=$_GET['ExParcel'];
		}

		if(!Acl::hasAccess('B:Export/SeeAllShipments')) $model->agent_id = Yii::app()->user->org;
		
		if(Yii::app()->user->grp == 40 && $model->odpt_id == -1) $model->odpt_id = 0;
		elseif(Yii::app()->user->grp == 40 && !Acl::hasAccess('B:Export/AllDepots')) $model->odpt_id = Yii::app()->user->org;

		if(!Acl::hasAccess('B:Export/UpdateParcelAfterConsolidation')){
			if(empty($model->status) || $model->status > 20) $model->status = '<25';
		}
		$this->render('list',array(
			'model'=>$model,
		));
	}

	public function actionLsx(){
		$this->render('lsx');
	}

	public function actionElabel(){
		if(!empty($_POST)){
			$nos = [];
			$b = substr($_POST['start'], 0,6);
			$n = (int) substr($_POST['start'], 6);
			for($i = 0; $i < $_POST['qty']; $i++){
				$o = new StdClass;
				$o->hbn = $b.sprintf('%06d', $n+$i);
				$o->pkg = 1;
				$o->weight = '';
				$o->cbm = '';
				$o->note = '';
				$o->cnee = new Addr;
				$o->cnor = new Addr;
				$nos[] = $o;
			}
			oPDF::renderPDF('label_'.$_POST['size'], array('tpl' => '_label-ex', 'empty' => true, 'rs' => $nos));
			Yii::app()->end();
		}
		$this->render('elabel');
	}
	
	public function actionLabel($id){
		$model=$this->loadModel($id);
		oPDF::renderPDF('label_A6', array('rs' => [$model], 'tpl' => '_label-ex', 'empty' => false));
		Yii::app()->end();
	}
	
	public function actionAuthLetter($id){
		$model=$this->loadModel($id);
		oPDF::renderPDF('auth', array('p' => $model));
		Yii::app()->end();
	}
	
	public function actionTransLabel($id){
		$p=$this->loadModel($id);
		$p->altGoods();
		$p->altCnee();
		//if(!empty($p->mdata['AltCnee']) && empty($_GET['real'])){
			//$p->cnee = Addr::model()->findByPk($p->mdata['AltCnee']);
		//}
		
		if(empty($p->consol)) $p->consol = new ExcoConsol;
		$tpl = $p->consol->getLabelTpl();
		if(in_array($p->agent_id, [672]) && empty($p->consol_id)) $tpl = 'label_yto';
		$p->labelGoods();
		$html = $this->renderPartial('../expLabel/'.$tpl, array('p' => $p), true);
		
		if(empty($_GET['type'])){
			oPDF::html2image($html);
		}elseif($_GET['type'] == 'pdf'){
			oPDF::html2pdf($html, 1, $p->hbn.'.pdf');
		}elseif($_GET['type'] == '3in1'){
			$td = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime";

			//label
			$html = $this->renderPartial('../expLabel/'.$tpl, array('p' => $p), true);
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
			$html = $this->renderPartial('../_pdf/receipt_'.$d['tpl'], array('r' => $p, 'd' => $d), true);
			oPDF::html2image($html, 2, $tf2);
			$magick = '/usr/bin/convert';
			AppHelper::exec($magick.' '.$tf.' '.$idp.' '.$tf2.' +append '.$tf);
			header("Cache-Control: maxage=1");
			header('Content-Type: image/jpeg');
			header('Content-Disposition: inline; filename="'.$p->ref.'_3in1.jpg"');
			readfile($tf);
			unlink($tf2);
			unlink($tf);
			Yii::app()->end();
		}elseif($_GET['type'] == 'zpl-vip'){
			Meta::getAll($p);
echo '^XA
^PW812
^LL640
~SD07
^PR4

^FO8,200^GB488,1,1^FS
^FO8,416^GB796,1,1^FS
^FO8,464^GB796,1,1^FS
^FO8,568^GB796,1,1^FS
^FO300,304^GB502,1,1^FS
^FO440,500^GB364,1,1^FS

^FO496,8^GB0,192,1^FS
^FO300,200^GB0,216,1^FS
^FO320,464^GB0,104,1^FS
^FO440,464^GB0,104,1^FS

^CWZ,E:WQYHEI.TTF^FS^CI28

^FO48,72^AZN,28,28^TBN,460,60^FH^FD收货人：',$p->cnee->name,'_0D_0A单号：',$p->ref,'^FS

^FO512,40^AZN,24,24^TBN,280,160^FH^FD客户要求：',(empty($p->_mkv['transportDay'])? '': $p->_mkv['transportDay']),'_0D_0A邮编：',$p->cnee->postcode,'_0D_0A包装日期：',date('y年m月d日'),'^FS
^FO512,276^AZN,24,24^FD单号: ',$p->ref,'^FS
^FO64,272^AZN,96,96^FD^FS
^FO308,208^AZN,24,24^TBN,200,60^FD',(empty($p->_mkv['carrieresName'])? '': $p->_mkv['carrieresName']),'^FS
^FO308,312^AZN,24,24^TBN,500,96^FH^FD',$p->cnee->getCnFullAddress(),'^FS
^FO32,432^AZN,28,28^TBN,500,48^FD电话：',$p->cnee->tel,'^FS
^FO548,432^AZN,28,28^TBN,260,48^FD',$p->cnee->name,'^FS
^FO32,480^AZN,56,56^FD',substr($p->cnee->state,0,6),' ', ((!empty($p->cnee->city) && $p->cnee->city != $p->cnee->state)? substr($p->cnee->city, 0 ,6) : ''),'^FS
^FO328,480^AZN,56,56^FD',(empty($p->_mkv['pickCode'])? '': $p->_mkv['pickCode']),'^FS
^FO448,472^AZN,24,24^FD金额应收：0.00^FS
^FO448,508^AZN,24,24^TBN,500,50^FH^FD客户签收：_0D_0A   年 月 日 时 分^FS
^FO32,576^AZN,20,20^TBN,300,50^FH^FD箱号：',$p->ref,'^FS
^CI0

^BY2,1
^FO512,168^AZN,32,32^BCN,100,N^FD>;',$p->ref,'^FS
^FO288,572^AZN,32,32^BCN,80,N^FD',$p->ref,'^FS
^XZ';

		}
	}

	public function actionReceipt($id){
		$model=$this->loadModel($id);
		$model->altGoods();
		$td = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime";

		if(empty($_POST)){
			$d = $model->receiptData(!empty($_GET['rand']), false);
			if(!empty($model->consol) && $model->consol->poc == 'CNCA3'){
				foreach($model->eitems['pid'] as $i => $t){
					$ep = ExProdb::model()->findByPk($model->eitems['pid'][$i]);
				}
			}
			if(empty($_GET['photo'])){
				oPDF::renderImage('receipt_'.$d['tpl'], array('r' => $model, 'd' => $d));
			}else{
				$tf = tempnam($td, "rcpt");
				oPDF::renderImage('receipt_'.$d['tpl'], array('r' => $model, 'd' => $d), 2, $tf);
				RcptMaker::makeReal($tf, $tf, true);
				unlink($tf);
			}
		}else{
			switch($_POST['type']){
				case 'aur':
					$d = $model->receiptData(true, false);
					foreach($_POST['item'] as $i => $p){
						$model->eitems['pid'][$i] = 0;
						foreach($p as $k=>$v){
							$model->eitems[$k][$i] = $v;
						}
					}
					if(empty($_POST['photo'])){
						oPDF::renderImage('receipt_'.$d['tpl'], array('r' => $model, 'd' => $d));
					}else{
						$tf = tempnam($td, "rcpt");
						oPDF::renderImage('receipt_'.$d['tpl'], array('r' => $model, 'd' => $d), 2, $tf);
						if(!empty($_POST['photo'])) RcptMaker::makeReal($tf, $tf, true);
						unlink($tf);
					}
				break;
				case 'imo':
					oPDF::renderImage('receipt_imo', array('r' => $model, 'd' => $_POST));
				break;
			}
		}
	}

	public function actionCusReceipt($id){
		$model=$this->loadModel($id);
		$this->render('cus_receipt', ['model' => $model]);
	}

	public function actionExport(){
		if(!empty($_POST)){
			parse_str($_POST['q'], $q);
			$model=new ExParcel('search');
			$model->unsetAttributes(); // clear any default values
			if(!empty($q['ExParcel']))	$model->attributes=$q['ExParcel'];

			if(!Acl::hasAccess('B:Export/SeeAllShipments')) $model->agent_id = Yii::app()->user->org;
			if(Yii::app()->user->grp == 40 && $model->odpt_id == -1) $model->odpt_id = 0;
			elseif(Yii::app()->user->grp == 40 && !Acl::hasAccess('B:Export/AllDepots')) $model->odpt_id = Yii::app()->user->org;
			
			if(!Acl::hasAccess('B:Export/UpdateParcelAfterConsolidation')){
				if(empty($model->status) || $model->status >20) $model->status = '<25';
			}
			$criteria = new CDbCriteria;
			$criteria->condition = "t.created >= '".$_POST['date']['from']."' AND t.created < DATE_ADD('".$_POST['date']['to']."', INTERVAL 1 DAY)";

			switch($_POST['typ']){
				case 'xls':	
					$dp = $model->search(false, null, $criteria);
					$tt = $dp->totalItemCount;
					$xls = new oExcel;
					$i = 1;
					//'Location', 
					$xls->addRow($i++, array('Service', 'WBN', 'Ref', 'Consol', 'Status', 'Agent', 'Weight', 'Check Wt','Charge Wt', 'Sender', 'Sender Tel', 'Cnee Name', 'Cnee Tel', 'Address', 'State', 'Date', 'Delay', 'Type', 'Goods', 'Rating Code','Entry Goods'));

					for($pg = 0; $pg < ceil($tt/1000); $pg++){
						$_GET['ExParcel_page'] = $pg+1;
						$dp = $model->search(true, 1000, $criteria);
						foreach($dp->data as $r){
							if(empty($r->cnee)) continue;
							$ln = $r->getLastLog();
							$dd = empty($ln)? 0 : ceil((time() - strtotime($ln->time)) / 86400);
							$rate = $r->getAgentRate();
							$temp='';
							if(!empty($r->mdata['client_entry'])){
								$cltent = $r->mdata['client_entry'];
								$items= $cltent['items'];
								if(isset($items['g'])&&is_array($items['g'])){
									foreach($items['g'] as $index=>$name){
										$temp.=$items['q'][$index].'X'.$name."   \n";
									}
								}
							}
							// $r->getLocation(), 
							$xls->addRow($i++, array($r->getStyp(), '="'.$r->hbn.'"', $r->ref, empty($r->consol_id)? '': $r->consol->no, $r->getStatus(), empty($r->agent)? '' : $r->agent_id.':'.$r->agent->name, $r->weight, $r->wtck, $r->chargeWeight(), $r->cnor->name, '="'.$r->cnor->tel.'"', $r->cnee->name, '="'.$r->cnee->tel.'"', $r->cnee->getCnFullAddress(), $r->cnee->state, $r->pickupDate(), $dd, $r->goodsType(), $r->GoodsNames(false, ', ', true), $rate[0],$temp));
						}
					}
					$xls->output('exp_search_export_'.time().'.xlsx');
				break;
				case 'exlabel':
					$dp = $model->search(true, 200); //, $criteria);
					foreach($dp->data as $r){
						if(!empty($r->mdata['client_entry'])){
							$cltent = $r->mdata['client_entry'];
							if(!empty($cltent['items'])){
								$r->eitems = $cltent['items'];
							}
						}
					}
					oPDF::renderPDF('label_A6', array('rs' => array_slice($dp->data,0,200), 'tpl' => '_label-ex', 'empty' => false));
				break;
				case 'colabel':
					$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'courier_labels_'.date('YmdHi').'.zip';
					$zip = new ZipArchive;
					$zip->open($zf, ZipArchive::CREATE);
					$dp = $model->search(true, 200); //, $criteria);
					$td = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
					mkdir($td);
					$i = 0;
					foreach($dp->data as $p){
						if($i >= 199) continue;
						$p->altGoods();
						$tpl = $p->consol->getLabelTpl();
						$html = $this->renderPartial('../expLabel/'.$tpl, array('p' => $p), true);
						$tf = tempnam($td, "clabel");
						oPDF::html2pdf($html, 2, $tf);
						$zip->addFile($tf, (empty($p->ref)? $p->hbn : $p->ref).'.pdf');
						$i++;
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
				break;
				case 'receipt':
					$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'shipment_receipts_'.date('YmdHi').'.zip';
					$i = 0;
					foreach($dp->data as $p){//gen receipt
						if($i >= 199) continue;
						$p->altGoods();
						$d = $p->receiptData();
						$tf = tempnam($td, "rcpt");

						$html = $this->renderPartial('../_pdf/receipt_'.$d['tpl'], array('r' => $p, 'd' => $d), true);

						oPDF::html2image($html, 2, $tf);
						if(!in_array($p->consol->poc, ['CNJMN', 'CNJM2'])){
							$real = RcptMaker::makeReal($tf, $tf);
						}
						//iconv('utf-8', 'gb2312', '小票');
						$fn = ($p->consol->poc == 'CNKMG'? $p->goodsType().'/' : '').(empty($p->ref)? $p->hbn : $p->ref).($real? '' : '_NR');
						if($p->consol->poc == 'CNXM2') $fn .= '.x';
						if(is_file($tf)) $zip->addFile($tf, $fn.'.jpg');
						else Yii::log('Problem creating receipt for '.$fn, 'error');
						$i++;
					}
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
				break;
			}
			Yii::app()->end();
		}
		$this->render('export_search');
	}

	public function actionIdReport(){
		$criteria = new CDbCriteria();
		$criteria->addInCondition("status", [12, 15]);
		
		//$model->odpt_id = $id;
		if(!Acl::hasAccess('B:Export/SeeAllShipments')) $criteria->compare('agent_id', Yii::app()->user->org);
		$rs = ExParcel::model()->findAll($criteria);

		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, array('Depot', 'WBN', 'Status', 'Agent', 'Sender', 'Sender Tel', 'Cnee Name', 'Cnee Tel', 'Address', 'State', 'Weight', 'Type', 'Date', 'Delay', 'Multiple ID', 'Problem Upload', 'ID Number', 'Qty'));

		foreach($rs as $r){
			if(empty($r->cnee) || $r->cnee->cnid_id > 0 || empty($r->cnee->name)) continue;
			$dd = empty($ln)? 0 : ceil((time() - strtotime($r->pickupDate())) / 86400);
			$xls->addRow($i++, array($r->getOdpt(), $r->hbn, $r->getStatus(), empty($r->agent)? '' : $r->agent->name, $r->cnor->name, '="'.$r->cnor->tel.'"', $r->cnee->name, '="'.$r->cnee->tel.'"', $r->cnee->address, $r->cnee->state, $r->weight, $r->goodsType(), $r->pickupDate(), $dd, (($r->bwf & 32) > 0? 'Y' : ''), (CnID::hasProblemUpload($r->cnee->name)? 'Y' : ''), (empty($r->cnee->cnid_no)? '' : '="'.$r->cnee->cnid_no.'"'), '=COUNTIF(G:G,G'.($i-1).')'));
		}
		$xls->output('exp_id_report'.time().'.xlsx');
	}

	public function actionDupReport(){
		$criteria = new CDbCriteria();
		$criteria->addInCondition("status", [15, 18]);
		$rs = ExParcel::model()->findAll($criteria);
		
		$dp = ['idno' => [], 'tel' => [], 'fa' => []];
		$acs = [];

		$isDup = function($p) use(&$dp){
			$vks = ['idno' => empty($p->cnee->cnid_id)? $p->cnee->cnid_no : $p->cnee->cnid->no, 'tel' => $p->cnee->tel, 'fa' => md5($p->cnee->fullAddress())];
			foreach($vks as $k => $v){
				if(empty($v)) continue;
				$dp[$k][] = $v;
			}
		};

		$getDup = function($p) use(&$acs){
			$vks = ['idno' => empty($p->cnee->cnid_id)? $p->cnee->cnid_no : $p->cnee->cnid->no, 'tel' => $p->cnee->tel, 'fa' => md5($p->cnee->fullAddress())];
			$vcs = [];
			foreach($acs as $k => $ac){
				$vcs[$k] = isset($ac[$vks[$k]])? $ac[$vks[$k]] : 0;
			}

			return $vcs;
		};

		foreach($rs as $r) $isDup($r);

		foreach($dp as $k => $l) $acs[$k] = array_count_values($l);

		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, array('Depot', 'WBN', 'Status', 'Agent', 'Sender', 'Sender Tel', 'Cnee Name', 'Cnee Tel', 'Address', 'State', 'Weight', 'Type', 'Goods', 'Date', 'ID Dup', 'Tel Dup', 'Address Dup', 'Max Dup'));

		foreach($rs as $r){
			$dups = $getDup($r);
			if(max($dups) < 2) continue;
			$xls->addRow($i++, array($r->getOdpt(), $r->hbn, $r->getStatus(), empty($r->agent)? '' : $r->agent->name, $r->cnor->name, '="'.$r->cnor->tel.'"', $r->cnee->name, '="'.$r->cnee->tel.'"', $r->cnee->address, $r->cnee->state, $r->weight, $r->goodsType(), $r->GoodsNames(false, ', ', true), $r->pickupDate(), $dups['idno'], $dups['tel'], $dups['fa'], max($dups['idno'], $dups['tel'], $dups['fa'])));
			if($dups['fa'] > $dups['tel'] * 2 && $dups['fa'] > $dups['idno'] * 2 && !empty($r->cnee->name) && strpos($r->cnee->address, $r->cnee->name) === false){
				$r->cnee->address .= ' '.$r->cnee->name;
				$r->cnee->save();
			}
		}
		$xls->output('exp_dup_report'.time().'.xlsx');
	}

	/**
	* Returns the data model based on the primary key given in the GET variable.
	* If the data model is not found, an HTTP exception will be raised.
	* @param integer the ID of the model to be loaded
	*/
	public function loadModel($id){
		$model=ExParcel::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	* Performs the AJAX validation.
	* @param CModel the model to be validated
	*/
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='ex-parcel-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
		
	public function findhbn($hbn){
		$r= ExImage::model()->find('hbn like :hbn',array(':hbn'=>"%$hbn%"));
		if(empty($r)){
			return FALSE;
		} else {
			foreach (preg_split('/[\s,;]+/', trim($r->hbn)) as $h){
				if(empty(trim($h))) continue;
				$e= ExParcel::model()->find('hbn=:hbn',array(':hbn'=>$h));
				if(empty($e)){
					$r->islinked=2;
					$r->save();
					return TRUE;
				}
			}
			$r->islinked=1;
			$r->save();
			return TRUE;
		}
	}

	public function actionImage(){
		$model=new ExImage('search');
		$model->unsetAttributes(); // clear any default values
		if(isset($_GET['ExImage'])) $model->attributes=$_GET['ExImage'];
		$model->agent_id=$_GET['agent_id'];
		if(!empty($_GET['unlink'])) $model->hbn=$_GET['fhbn'];
		$this->render('show_image',array('model'=>$model));
	}

	public function actionUimage(){
		$model=new ExImage('search');
		$model->unsetAttributes(); // clear any default values
		if(isset($_GET['ExImage'])) $model->attributes=$_GET['ExImage'];
		if(!empty($_GET['file'])){
			$model->pdf_number=$_GET['file'];
		}
		$this->render('check_unknown_image',array('model'=>$model));
	}
		
	   public function actionLink() {
			$resp = array('msg' => 'Link successfully', 'done' => true);
			$r = ExImage::model()->find("id=:id", array(':id' => $_GET['id']));
			if (!empty($r)) {
				if (empty(trim($_GET['hbn']))) {
					$resp['msg'] = 'Please input the barcode';
					$resp['done'] = false;
					echo json_encode($resp);
					return;
				}
				$r->hbn = '';
				$isLink = TRUE;
				foreach (preg_split('/[\s,;]+/', trim($_GET['hbn'])) as $h) {
					if (preg_match('/' . $h . '/i', $r->hbn))
						continue;
					if (!preg_match('/PE\d+|PV\d+/i', $h)) {// this is for not label image
						$r->hbn .= (empty($r->hbn) ? '' : ',') . $h;
						$r->islinked = 1;
						if ($r->save()) {
							echo json_encode($resp);
							return;
						}
					}
					$r->hbn .= (empty($r->hbn) ? '' : ',') . $h;
					$e = ExParcel::model()->find('hbn=:hbn', array(':hbn' => $h));
					if (empty($e)) {
						$isLink = FALSE;
					}else{
						$resp['id']=$e->id;
					}
				}
				if ($isLink) {
					$r->islinked = 1;
					$resp['create']=1; //means load to the created shipment
				} else {
					$r->islinked = 2;
					$resp['create'] = 2; //means load to create new shipment
				}

				if ($r->save()) {
					echo json_encode($resp);
				}
			}
	 }

		public function actionLink1(){
		$r=ExImage::model()->find("id=:id",array(':id'=>$_GET['id']));
		if(preg_match('/'.$_GET['hbn'].'/i', $r->hbn)) return;
		$r->hbn.=','.$_GET['hbn'];
		$r->islinked=1;
		$r->save();
		echo "succesfully";	
	}
		
	public function actionUnlink(){
		$r=ExImage::model()->find("id =:id",array(':id'=>$_GET['id']));
		if(!empty($r)){
			$r->hbn=preg_replace('/\,?'.$_GET['hbn'].'/i', '', $r->hbn);
			if(strlen($r->hbn)<2){
				$r->islinked=0;
			} else {
				if(preg_match_all('/PE\d+/i',$r->hbn,$match)){
					foreach ($match[0] as $h){
						$e= ExParcel::model()->find('hbn=:hbn',array(':hbn'=>$h));
						if(empty($e)){
							$r->islinked=2;
							break;
						}
						$r->islinked=1;
					}
				}else{
					$r->islinked=0;
				}
			}
			
			$r->save();
			echo "succesfully";
		}
	}

	public function gethbn($hbn){
		$r= ExImage::model()->find('hbn like :hbn order by id DESC',array(':hbn'=>"%$hbn%"));
		return $r->file_adr;			
	}

	public function get_url_hbn($hbn){
		$r= ExImage::model()->find('hbn like :hbn order by id DESC',array(':hbn'=>"%$hbn%"));
		return $r->getfilename();		
	}
		
		public function getExparcelid($hbn){
			foreach (preg_split('/[\n;,]/i', $hbn) as $h){
				if(empty($h)) continue;
				$p=ExParcel::model()->find('hbn=:hbn',array(':hbn'=>trim($h)));
			   if(!empty($p)) return $p->id;
			 }
		 }




		public function get_url_id($id){
		$r= ExImage::model()->find('id=:id',array(':id'=>$id));
		return $r->getfilename();
	}

	public function gethbnbyid($id){
		$r= ExImage::model()->find('id=:id',array(':id'=>$id));
		return $r->hbn;
	}

	public function geteid($hbn){
		$r=ExImage::model()->find("hbn like :hbn order by id DESC",array(':hbn'=>"%$hbn%"));
		if(!empty($r)) return $r->id;
	}
		public function getAgent($hbn){
			$r=ExImage::model()->find("hbn like :hbn order by id DESC",array(':hbn'=>"%$hbn%"));
		   if(!empty($r)) return $r->agent_id;
		}
		
	public function actionEcreate(){
		if(empty($_GET['id'])){
			echo json_encode(['No Image To Link']);
			return;
		}
		$r= ExImage::model()->find('id=:id',array(':id'=>$_GET['id']));
		
		$this->render('check_new',array('model'=>$r));
		
	}
		
	public function actionTcreate(){
			$model=new ExParcel;
		if(isset($_POST['ExParcel'])){
			$model->setScenario('create');
			$model->attributes=$_POST['ExParcel'];
			$model->hbn = strtoupper(trim($model->hbn));
						
						$r=ExImage::model()->find('id=:id',array(':id'=>$_GET['id']));
						
						//give err message
						if(!preg_match("/$model->hbn/i", $r->hbn)){
							$resp=array('success'=>0,'msg'=>'The Code is Wrong');
							echo json_encode($resp);
							return;
					}

			if(!Acl::hasAccess('C:org/exAgentSuggest')) $model->agent_id = Yii::app()->user->org;
			$model->eitems = $_POST['items'];
			$cnor = new Addr;
			$cnor->attributes = $_POST['Cnor'];
			$cnor->save();
			$model->cnor_id = $cnor->id;
			$cnee = new Addr;
			$cnee->attributes = $_POST['Cnee'];
			if(!empty($cnee->cnid_id)) $cnee->cnid_no = '';
			$acc = $cnee->checkCnAddr();
			if(empty($cnee->postcode)){
				$cnee->postcode = CnArea::getZip($cnee->state, $cnee->city);
			}
			$cnee->save();
			$model->cnee_id = $cnee->id;
			$model->state = $cnee->state;
			$model->postcode = $cnee->postcode;
			$model->save();
						
			$r=ExImage::model()->find("hbn like :hbn order by id DESC",array(':hbn'=>"%$model->hbn%"));
		
			if(!empty($r)){
				foreach (preg_split('/[\s,;]+/', trim($r->hbn)) as $h){
				$e= ExParcel::model()->find('hbn=:hbn',array(':hbn'=>$h));
			if(empty($e)){
				$r->islinked=2;
				if($r->save()){
				}
				$this->ajaxResult($model);
				return;
				}
			}

			$r->islinked=1;
			$r->save();
			$this->ajaxResult($model);
			
			}else{
				//rollback save()!
				$this->ajaxResult($model);
			}
		}

		$r=ExImage::model()->find("id=:id",array(':id'=>$_GET['id']));
					if (!empty($r)) {
						foreach (preg_split('/[\s,;]+/', trim($r->hbn)) as $h) {
							if (preg_match('/PE|PV/i', $h)) {
								$e = ExParcel::model()->find('hbn=:hbn', array(':hbn' => $h));
								if (empty($e)) {
									$model->pkg = 1;
									$m['id'] = $r->id;
									$model->hbn=trim($h);
									$model->agent_id = $r->agent_id;
									$this->render('create', array('model' => $model, 'm' => $m));
									break;
								} else {
									echo '<h1>The shipment already Exist</h1>';
								}
							} else {
								echo '<h1>The Image is not a valid label</h1>';
							}
						}
					}
	}
	
	public function actionCheckHbnImage(){
		$m=ExImage::model()->find("id!=:id AND hbn like :hbn",array(':hbn'=>"%".$_GET['hbn']."%",':id'=>$_GET['id']));
			
		echo empty($m)? 0 : $m->id;
	}
	
	public function actionNewTag(){
		if(empty($_GET['id'])){
			echo json_encode(['No Image To Open']);
			return;
		}
		$r= ExImage::model()->find('id=:id',array(':id'=>$_GET['id']));
		
		if(!empty($r)){
			$model['file_adr']=$r->file_adr;
			$model['id']=$r->id;
		}
			
		$this->render('new_tag',array('model'=>$model));
	}
         public function actionUpdateErrorRecord($id){
         $model= $this->loadModel($id);
         if(!empty($_POST)){ 
             if(!empty($_POST['err_reasons'])){
                 $err=0;
                 foreach ($_POST['err_reasons'] as $value){
                     $err=$err|$value;
                  }
               $resp=$model->newShipmentErrRecord($err);
             }else{
                 $resp= $model->newShipmentErrRecord(0);
             }
             if(!$resp['status']){
                 $model->addError('id',$resp['msg']);
             }
              $this->ajaxResult($model);
         }
        $this->render('err_record',array('model'=>$model));
     }
		
		
	public function actionToCheck(){
		$resp=array('success'=>1,'msg'=>'link successfully');
		$all= ExImage::model()->findAll('islinked= 2 and date>:date',array(':date'=>date("Y-m-d",strtotime("-360 days"))));
			
		foreach ($all as $r){
			$iSlink=TRUE;
			foreach (preg_split('/[\s,;]+/i', trim($r->hbn)) as $n){
				if(!preg_match('/PE\d+/i', $n)){
					continue;
				}
				$s= ExParcel::model()->find('hbn=:hbn',array(':hbn'=>$n));
				if(empty($s)){
					$iSlink=false;
					break;
				}
			}
				
			if($iSlink){
				$r->islinked=1;
				$r->save();
			}
			
		}
	
		echo json_encode($resp);		
	}
	
}
