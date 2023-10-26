<?php
class WhscanController extends Controller
{
	/**
	 * Declares class-based actions.
	 */

	public function beforeAction($action)
	{
		$date = Service::getCacheData("loginTime".date("Ymd").User::currentUserID());
		if(!empty($date)){
			// print_r($date[0]);
			$cacheTime = strtotime($date[0]);
			$nowTime = strtotime("-4 hour",strtotime(date('Y-m-d H:i:s')));
			// echo $cacheTime."|".$nowTime;
			// Yii::app()->end();
			if($cacheTime>=$nowTime){
				//echo "The difference is within 4 hours";
				$this->setLoginTime();
			} else {
			    //echo "The difference is not within 4 hours";
			    Yii::app()->user->logout();
				$this->redirect('../');
			}
		}
		else{
			$this->setLoginTime();
		}

		return parent::beforeAction($action);
	}

	private function setLoginTime()
	{
		$dataArr = [date('Y-m-d H:i:s')];
		Service::setCacheData("loginTime".date("Ymd").User::currentUserID(),$dataArr,14600);
		// print_r(Service::getCacheData("loginTime".date("Ymd").User::currentUserID()));		
	}

}
