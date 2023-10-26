<?php
class StockController extends Controller{

	public function actionList(){
		if(!empty($_GET)){
			$model = new WmsStock('search');
			$model->unsetAttributes();
			$pg = empty($_GET['WmsStock_page'])? 1 : $_GET['WmsStock_page'];
			$ec = new CDbCriteria;
			$loc = false;
			$plts = false;
			if(!empty($_GET['org'])) $model->cust_name = $_GET['org'];
			if(!empty($_GET['loc'])){
				$model->loc_code = $_GET['loc'];
				$loc = WmsLocation::model()->find('code = :n', [':n' => $_GET['loc']]);
				if($pg == 1) $plts = WmsLocation::model()->findAll('status = 1 AND type = 50 AND pid = :pid', [':pid' => $loc->id]);
			}
			if(!empty($_GET['prod'])){
				$ec->with  = ['prod'];
				$ec->addCondition('(prod.ean = :ean OR prod.name LIKE :name OR prod.name_zh LIKE :name)');
				$ec->params = [':name' => '%'.$_GET['prod'].'%', ':ean' => $_GET['prod']];
			}
			$model->qty = '>0';
			$dp = $model->search(true, 10, $ec);

			if($dp->totalItemCount > 0){
				$more_url = $pg * 10 < $dp->totalItemCount? $this->createUrl('list', ['prod' => $_GET['prod'], 'loc' => $_GET['loc'], 'org' => $_GET['org'], 'WmsStock_page' => $pg + 1]) : '';
				echo json_encode(['done' => true, 'data' => $this->renderPartial('search_result', ['dp' => $dp, 'loc' => $loc, 'plts' => $plts], true), 'url' => $more_url]);
			}elseif(!empty($_GET['loc']) && empty($_GET['org']) && empty($_GET['prod'])){
				$ec = new CDbCriteria;
				$ec->with = ['location'];
				$ec->addCondition('location.code LIKE :code');
				$ec->params = [':code' => '%'.$_GET['loc'].'%'];
				$ec->group = 'shipment_id';
				$ec->select = 'shipment_id, COUNT(sno) AS qty';
				$dp = new CActiveDataProvider('WmsRackShipment', ['criteria' => $ec]);
				echo json_encode(['done' => true, 'data' => $this->renderPartial('search_result_shipments', ['dp' => $dp], true), 'url' => '']);
			}
			return;
		}

		$this->render('list');
	}
}


