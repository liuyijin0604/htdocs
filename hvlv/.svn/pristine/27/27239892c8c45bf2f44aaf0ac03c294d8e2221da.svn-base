<?php
class DeconsolidationController extends Controller
{
    protected $nonAjax = ['warehouseProcessDone'];
	protected $skipAcl = ['warehouseProcessDone'];

	protected $org;
	protected $type;

    private function setLoginTime()
	{
		$dataArr = [date('Y-m-d H:i:s')];
		Service::setCacheData("loginTime".date("Ymd").User::currentUserID(),$dataArr,3800);
		// print_r(Service::getCacheData("loginTime".date("Ymd").User::currentUserID()));		
	}
	
    public function beforeAction($action)
	{
		if (!Yii::app()->user->isGuest) {
			$this->org = Org::model()->findByPk(Yii::app()->user->org);
			$this->type = 'sc';
		}

		$date = Service::getCacheData("loginTime".date("Ymd").User::currentUserID());
		if(!empty($date)){
			// print_r($date[0]);
			$cacheTime = strtotime($date[0]);
			$nowTime = strtotime("-1 hour",strtotime(date('Y-m-d H:i:s')));
			// echo $cacheTime."|".$nowTime;
			// Yii::app()->end();
			if($cacheTime>=$nowTime){
				// echo "The difference is within 1 hour";
				$this->setLoginTime();
			} else {
			    // echo "The difference is not within 1 hour";
			    Yii::app()->user->logout();
				$this->redirect('../');
			}
		}
		else{
			$this->setLoginTime();
		}



		return parent::beforeAction($action);
	}

    public function actionDeconsolidationList()
    {
        $uid = Yii::app()->user->id;
        //$list = Deconsolidation::model()->findAllByAttributes(array('status' => Deconsolidation::STATUS_WAREHOUSE_PROCESSING, 'assigned_user' => $uid));
		$list = Deconsolidation::model()->findAll('(assigned_user = :user AND status = :status) OR (error_check_user = :user AND (error_check_complete_time IS NULL OR error_check_complete_time = ""))', array(':user' => $uid, 'status' => Deconsolidation::STATUS_WAREHOUSE_PROCESSING));
        $this->render('list', array('list' => $list));
    }

    public function actionWarehouseProcessDone()
    {
        if (!empty($_GET['id'])) {
            $model = Deconsolidation::model()->findByPk($_GET['id']);
            if (!empty($model)) {
                $model->status = Deconsolidation::STATUS_WAITING_SCAN_ALL;
                $model->warehouse_complete_time = date('Y-m-d H:i:s');
                $model->save();
                echo "Done";
            }
        }
    }

	public function actionErrorReport()
	{
		$res = new stdClass;
		$res->success = false;
		if (!empty($_POST['deconsolidation_id'])) {
			$model = Deconsolidation::model()->findByPk($_POST['deconsolidation_id']);
			if (!empty($model)) {
				$model->status = Deconsolidation::STATUS_WAITING_SCAN_ALL;
				$model->warehouse_complete_time = date('Y-m-d H:i:s');
				$model->mdata['errors'] = $_POST['error_content'];
				$model->save();
				$res->success = true;
			}
		}
		echo json_encode($res);
	}

	public function actionErrorCheckReport()
	{
		$res = new stdClass;
		$res->success = false;
		if (!empty($_POST['error_check_deconsolidation_id'])) {
			$model = Deconsolidation::model()->findByPk($_POST['error_check_deconsolidation_id']);
			if (!empty($model)) {
				//$model->status = Deconsolidation::STATUS_WAITING_SCAN_ALL;
				$model->error_check_complete_time = date('Y-m-d H:i:s');
				$model->mdata['error_check_result'] = $_POST['error_check_content'];
				$model->save();
				$res->success = true;
			}
		}
		echo json_encode($res);
	}

	public function actionErrorCheckDone()
	{
		if (!empty($_GET['id'])) {
			$strId = $_GET['id'];
			$pattern = '/\d+$/';
			if (preg_match($pattern, $strId, $matches)) {
				$id = $matches[0];
			}
            $model = Deconsolidation::model()->findByPk($id);
            if (!empty($model)) {
                //$model->status = Deconsolidation::STATUS_WAITING_SCAN_ALL;
                $model->error_check_complete_time = date('Y-m-d H:i:s');
				//$model->task_complete_time = date('Y-m-d H:i:s');
                $model->save();
                echo "Done";
            }
        }
	}
}