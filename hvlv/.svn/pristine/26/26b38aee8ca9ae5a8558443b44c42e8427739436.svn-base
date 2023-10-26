<?php

class ToolsController extends Controller
{
	public $nonAjax = ['dgLabel','letterLabel','getDFEAndTntCost'];
	public $skipAcl=['dgLabel'];

	public function actionDgLabel(){
		if(!empty($_FILES['file'])){
			$data = oExcel::getAllData($_FILES['file']['tmp_name'], 'abc.xlsx', false);
			$lbls = [];
			foreach($data as $i => $r){
				if($i == 4 || empty($r[13]) || empty($r[1])) continue;
				$lbls[] = ['un' => $r[13], 'prod' => $r[1]];
			}
			echo oPDF::renderHTML('dg_label', ['labels' => $lbls, 'shipper' => $data[1][2], 'consignee' => $data[2][2]], 1, 'dg_labels.pdf');
			exit(0);
		}

		echo '<h2>DG Label</h2>
		<form action="" method="POST" enctype="multipart/form-data">
			<input type="file" name="file" /><br />
			<input type="submit" value="Submit" />
		</form>';
	}

	public function actionLetterLabel($id){
		$t = WmsTask::model()->findByPk($id);
		$dt = $t->deliveryTask();

		foreach($t->items as $itm){
			$stk = WmsStock::model()->findByPk($itm->mdata['si']);
			//label
			if($itm->mdata['uq'] < 5 && !empty($itm->mdata['si'])){
				$o = ['agent_id' => $oid, 'pkg' => $itm->mdata['uq'], 'ref' => $t->getNo(), 'cref' => $t->ref, 'hbn' => $t->getNo(), 'cnee' => $dt->mdata['cnee']];
				$o = json_decode(json_encode($o));
				$o->mdata = ['show_sku' => true, ''];
				$o->eitems = ['sku' => ['<span style="font-size:1.2em">'.$itm->mdata['sn'].' '.$stk->prod->ean.' x 1</span>']];
				$lbls[] = $o;
			}
		}
		oPDF::renderPDF('label_A6', ['rs' => $lbls, 'tpl' => '_label_letter'], 1, 'letter_labels.pdf');
	}

	public function actionUpdateShipmentMemo()//2020-01-07 Gero1
	{
		$model = new ShipmentUpdateHistory();
		$mdata = [];
		if (isset($_POST) && !empty($_POST)) {
			$inv_file = empty($_FILES['inv_file']) ? [] : $_FILES['inv_file'];
			if (empty($inv_file['tmp_name']) || !is_uploaded_file($inv_file['tmp_name'])) 
			{
				$this->ajaxResult($model, ['id'], 'Invalid template file');
			} else 
			{
				$xls = new oExcel();
				$xls->load($inv_file['tmp_name']);
				$data = $xls->getAll();
				$errors = [];
				$shipments = [];
				$memos = [];
				unset($data[1]);
				foreach ($data as $k => $line)
				{
					$hbn = trim($line[1]);
					$memo = trim($line[2]);
					if(empty($hbn))
					{
						continue;
					}
					$p = ImParcel::model()->find("hbn = :hbn",[":hbn"=>$hbn]);
					if(empty($p))
					{
						$errors[]=" line".$k." shipment {$hbn} is not exist";
					}
					if(empty($memo))
					{
						$errors[]=" line".$k." memo is empty";
					}
					$shipments[] = $p;
					$memos[] = $memo;
				}

				if($errors!= null)
				{
					$model->addError('id', implode("; ", $errors));
					$this->ajaxResult($model);
				}

				$transaction = Yii::app()->db->beginTransaction();
				try
				{
					$oldRecord = [];
					foreach ($shipments as $key => $shipment) 
					{
						$oldRecord[]=[$shipment->hbn=>$shipment->can];
						$shipment->can = $memos[$key];
						$shipment->update(['can']);
					}
					$model->mdata = $oldRecord;
					$model->operator = User::currentUserID();
					$model->created = date('Y-m-d H:i:s');
					$model->save();
					$transaction->commit();

				}catch(Exception $ex)
				{
					$transaction->rollback();
					Log::log2file("Update Shipment Memo".$ex->getMessage(), "transaction_err_log", "transaction");
					throw $ex;
				}
				$this->ajaxResult($model);

			}
		}else
		{
			$this->render('update_shipment_memo');
		}
	}

	
}
