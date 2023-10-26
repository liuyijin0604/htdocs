<?php

class CargoProcessBiddingController extends Controller
{
	public function loadModel($id)
	{
		$model= CargoProcessBidding::model()->findByPk($id);
		if ($model===null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	public function actionIndex()
	{
		if(!empty($_GET['depot'])){
			$objBidding = new CargoProcessBidding('search');
			$objBidding->unsetAttributes();
			$objBidding->dpt_id = $_GET['depot'];

			if(!empty($_GET['CargoProcessBidding'])){
				$objBidding->setAttributes($_GET["CargoProcessBidding"]);
			}
			$this->render('index',['model'=>$objBidding]);
			return;
		}
		$this->render('cargo_manage',['type'=>1]);
	}

	public function actionAjaxCreate(){
		if(!empty($_POST["CargoProcessBidding"])){
			$objBiddingOld = CargoProcessBidding::model()->find('shipment_id = :shipment_id and cargo_process_id = :cargo_process_id',[':shipment_id'=>$_POST["CargoProcessBidding"]["shipment_id"],':cargo_process_id'=>$_POST["CargoProcessBidding"]["cargo_process_id"]]);
			if(empty($objBiddingOld)){
				$objBidding = new CargoProcessBidding;
				$objBidding->setAttributes($_POST["CargoProcessBidding"]);
				$objBidding->status = 10;
				$objBidding->createtime = date('Y-m-d H:i:s');
				$objBidding->creater = Yii::app()->user->id;

				if($objBidding->save()){
					if(!empty($_POST["CargoProcessBidding"]['cargo_process_id'])){
						$objCargoProcess = CargoProcess::model()->findByPk($_POST["CargoProcessBidding"]['cargo_process_id']);
						if(!empty($objCargoProcess)){
							$objCargoProcess->status = 22;
							$objCargoProcess->save();
							$objCargoProcess->sendBiddingInfoToDriver();
						}
					}
					echo json_encode([
						'isSuccess' => true,
					]);
				}
			}
		}
		echo json_encode([
			'isSuccess' => false,
		]);
	}

	public function actionAjaxUpdate(){
		if(!empty($_POST["CargoProcessBidding"]['id'])){
			$objBidding = CargoProcessBidding::model()->findByPk($_POST["CargoProcessBidding"]['id']);
			if(!empty($objBidding)){
				$objBidding->setAttributes($_POST["CargoProcessBidding"]);
				if($objBidding->save()){
					echo json_encode([
						'isSuccess' => true,
					]);
				}
			}
		}
	}

	public function actionAjaxRecreate(){
		if(!empty($_POST["CargoProcessBidding"]['id'])){
			$objBidding = CargoProcessBidding::model()->findByPk($_POST["CargoProcessBidding"]['id']);
			if(!empty($objBidding)){
				$objBidding->status = 100;
				if($objBidding->save()){
					$objNewBidding = new CargoProcessBidding;
					$objNewBidding->setAttributes($_POST["CargoProcessBidding"]);
					$objNewBidding->status = 10;
					$objNewBidding->createtime = date('Y-m-d H:i:s');
					$objNewBidding->creater = Yii::app()->user->id;
					if($objNewBidding->save()){
						echo json_encode([
							'isSuccess' => true,
							'newBiddingId' => $objNewBidding->id,
						]);
					}
				}
			}
		}
	}

	public function actionUpdate(){
		if(!empty($_GET['id'])){
			$objBidding = $this->loadModel($_GET['id']);
			$model = $objBidding->cargo_process;
			$this->render('update',['objBidding'=>$objBidding,'model'=>$model]);
		}
	}


}