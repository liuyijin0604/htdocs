<?php
class ProdController extends Controller{

	public function actionList(){
		if(!empty($_GET)){
			$pg = empty($_GET['WmsProd_page'])? 1 : $_GET['WmsProd_page'];
			$q = ['condition' => 't.status = 1 AND (t.name LIKE :name OR t.name_zh LIKE :name OR t.ean = :ean OR packs.barcode = :ean)', 'params' => [':name' => '%'.$_GET['prod'].'%', ':ean' => $_GET['prod']]];
			$tot =  WmsProd::model()->with('packs')->together()->count($q);
			$q['limit'] = 10;
			$q['offset'] = ($pg-1) * 10;
			$rs = WmsProd::model()->with('packs')->together()->findAll($q);

			$more_url = $pg * 10 < $tot? $this->createUrl('list', ['prod' => $_GET['prod'], 'WmsProd_page' => $pg + 1]) : '';
			echo json_encode(['done' => true, 'data' => $this->renderPartial('search_result', ['rs' => $rs], true), 'url' => $more_url]);
			return;
		}
		$this->render('list');
	}

	public function actionCarton($id){
		$prod = WmsProd::model()->findByPk($id);
		$model = empty($_GET['pid'])? new WmsProdPack : WmsProdPack::model()->findByPk($_GET['pid']);
		if(empty($model) || empty($model)) return;
		$model->type = 10;
		$model->prod_id = $id;

		if(!empty($_POST['WmsProdPack'])){
			$model->attributes=$_POST['WmsProdPack'];
			if(!empty($_POST['dim'])){
				$model->dims = $_POST['dim'];
			}
			$model->save();
			$this->ajaxResult($model);
		}

		$this->renderPartial('carton', ['prod' => $prod, 'model' => $model]);
	}

	public function actionCreate(){
		$model = new WmsProd('create');
		$model->type = 10;
		$model->status = 1;

		if(isset($_POST['WmsProd'])){
			$model->attributes=$_POST['WmsProd'];
			if(!empty($_POST['dim'])) $model->dims = $_POST['dim'];
			$model->save();
			$this->ajaxResult($model);
		}

		$this->renderPartial('create', ['model' => $model]);
	}

	public function actionUpdate($id){
		$model = WmsProd::model()->findByPk($id);

		if(isset($_POST['WmsProd'])){
			$model->attributes=$_POST['WmsProd'];
			if(!empty($_POST['dim'])) $model->dims = $_POST['dim'];
			$model->save();
			$this->ajaxResult($model);
		}
		$this->renderPartial('update', ['model' => $model]);
	}
}