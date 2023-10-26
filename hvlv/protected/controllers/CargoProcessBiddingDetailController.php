<?php

class CargoProcessBiddingDetailController extends Controller
{
	public function loadModel($id)
	{
		$model= CargoProcessBiddingDetail::model()->findByPk($id);
		if ($model===null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	public function actionIndex()
	{
		if(!empty($_GET['id'])){
			$objBidding = CargoProcessBidding::model()->findByPk($_GET['id']);
			$ref = $objBidding->shipment->ref;
			$objBiddingDetail = new CargoProcessBiddingDetail('search');
			$objBiddingDetail->unsetAttributes();
			$objBiddingDetail->bid_id = $_GET['id'];

			$this->render('index',['model'=>$objBiddingDetail,'ref'=>$ref]);
			return;
		}
	}

	public function actionAjaxAcceptDetail(){
		if(!empty($_POST['id'])){
			//CargoProcessBiddingDetail
			$model = self::loadModel($_POST['id']);
			$objCargoprocess = $model->cargo_process_bidding->cargo_process;
			if(empty($objCargoprocess->job_relation->job)){
				if(!empty($objCargoprocess)){
					$objCargoProcessJob = new CargoProcessJob();
					$strDriverName = Org::model()->findByPk($model->driver_id)->name;
					$objCargoProcessJob->job_name = substr($strDriverName,0,6)." ".$model->choosedate." Bid";
					$objCargoProcessJob->vehicle_id = 2;
					$objCargoProcessJob->status = CargoProcessJob::STATE_NEW;
					$objCargoProcessJob->type = $objCargoprocess->type;
					$objCargoProcessJob->dpt_id = $objCargoprocess->dpt_id;
					$objCargoProcessJob->user_id = User::currentUserID();
					$objCargoProcessJob->created = $model->choosedate;
					$objCargoProcessJob->driver_id = $model->driver_id;
					$objCargoProcessJob->save();
	
					$re = new CargoProcessJobRelations();
					$re->cargo_process_id = $objCargoprocess->id;
					$re->sequence = 1;
					$re->created = date('Y-m-d H:i:s');
					$re->job_id = $objCargoProcessJob->id;
					$re->save();
	
					$objCargoprocess->status = 40;
					$objCargoprocess->mdata['inputcost'] = $model->cost;
					$objCargoprocess->mdata['biddingdetail_id'] = $model->id;
					$objCargoprocess->save();

					$objCargoprocess->sendSuccessfulBidding($objCargoProcessJob->driver_id,$objCargoProcessJob->created,$model->cargo_process_bidding->end_date);

					echo json_encode([
						'isSuccess' => true,
					]);
				}	
			}
		}
	}

	public function actionAjaxGetCargoProcessJobDetail(){
		if(!empty($_POST['id'])){
			$model = self::loadModel($_POST['id']);
			$objCargoprocess = $model->cargo_process_bidding->cargo_process;
			if(!empty($objCargoprocess)){
				$objCargoProcessJob = $objCargoprocess->job_relation->job;
				if(!empty($objCargoProcessJob)){
					$this->renderPartial('cargoProcessjob_detail',array('model'=>$objCargoProcessJob));
				}
			}
		}
	}
}