<?php

class PickupBookingController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new PickupBooking;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['PickupBooking']))
		{
			$model->attributes=$_POST['PickupBooking'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['PickupBooking']))
		{
			$model->attributes=$_POST['PickupBooking'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('PickupBooking');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

		public function actionEditBookingInfo()
	{
		$service = new Service();
		$bookingId = $_GET['id'];
		$availableDate = null;

		$pb = PickupBooking::model()->findByPk($bookingId);
		if (empty($_POST))
		{

			$model = $pb->shipment;
			if (!empty($model->consol->mdata['available_date']) && !empty($model->mdata['amzon_pallet']) && ($model->status == ImParcel::STATE_CLEAR || $model->status == ImParcel::STATE_CLEAR_WAIT) && $model->status != ImParcel::DELIVERED) {
				$availableDate = date('Y-m-d H:i:s', strtotime($model->consol->mdata['available_date']));
				$today = date('Y-m-d H:i:s', strtotime('today'));
				if ($availableDate < $today) {
					$availableDate = $today;
				}
				$endDate = date('Y-m-d H:i:s', strtotime($availableDate . '+8 day'));
				$depot="";
				switch($model->consol->dpt_id) {
					case Org::TLA_DEPARTMENT_SYDNEY:
						$depot="Sydney";
						$sql = 'SELECT * FROM pickup_booking WHERE `booking_time` >= "' . $availableDate . '" AND `booking_time` < "' . $endDate . '" AND `status` != 3 AND `booking_number` LIKE "S%"';
						break;
					case Org::TLA_DEPARTMENT_MELBOURNE:
						$depot="Melbourne";
						$sql = 'SELECT * FROM pickup_booking WHERE `booking_time` >= "' . $availableDate . '" AND `booking_time` < "' . $endDate . '" AND `status` != 3 AND `booking_number` LIKE "M%"';
						break;
					case Org::TLA_DEPARTMENT_BRISBANE:
						$depot="Brisbane";
						$sql = 'SELECT * FROM pickup_booking WHERE `booking_time` >= "' . $availableDate . '" AND `booking_time` < "' . $endDate . '" AND `status` != 3 AND `booking_number` LIKE "B%"';
						break;
				}
				$bookings = PickupBooking::model()->findAllBySql($sql);
				$days = [];
				for ($i = 0; $i < 8; $i++) {
					array_push($days, date('Y-m-d', strtotime($availableDate . '+' . $i . ' day')));
				}

				$occupiedSlots = [];
				if (!empty($bookings)) {
					foreach ($bookings as $booking) {
						$bookingDate = explode(" ", $booking->booking_time)[0];
						$bookingTime = explode(" ", $booking->booking_time)[1];

						if (empty($occupiedSlots[$bookingDate])) {
							$occupiedSlots[$bookingDate] = [$bookingTime];
						} else {
							array_push($occupiedSlots[$bookingDate], $bookingTime);
						}
					}
				}

				$bookingDate = explode(" ",$pb->booking_time)[0];
				$bookingTimeArr = explode(":",explode(" ",$pb->booking_time)[1]);
				$bookingTime=$bookingTimeArr[0].":".$bookingTimeArr[1];
				
				$staticTimeSlot = ["7"=>["7:00","7:15","7:30","7:45"],"8"=>["8:00","8:15","8:30","8:45"],"9"=>["9:00","9:15","9:30","9:45"],"10"=>["","","10:00","10:30"],"11"=>["","","11:00","11:30"],"12"=>["","","12:00","12:30"],"13"=>["","","","13:00"],"14"=>["","","","14:00"],"15"=>["15:00","15:15","15:30","15:45"],"16"=>["16:00","16:15","16:30","16:45"],"17"=>["","","","17:30"]];
				$timeSlot = [];

				$slots = PickupBookingSlot::model()->findAll(['condition'=>'depot = :depot and status = 1',"params"=>[":depot"=>$model->consol->dpt_id],'order'=>" slot_time "]);
				foreach ($slots as $key => $value) {
					$vr = explode(":",$value->slot_time);
					$v1 = intval($vr[0]);
					$timeSlot[$v1][] = $v1.":".$vr[1];
				}

				foreach ($timeSlot as $key => $value) {
					$less = 6-count($value);
					if($less>0)
					{
						$createArr = [];
						for ($i=0; $i < $less; $i++) { 
							$createArr[] = "";
						}
						foreach ($value as $key2 => $value2) {
							$createArr[] = $value2;
						}
						$timeSlot[$key] = $createArr;
					}
				}

				$this->render('edit_booking_info', array(
					'availableDate' => $availableDate,
					'endDate' => $endDate,
					'occupiedSlots' => $occupiedSlots,
					'container_no' => $model->consol->container_no,
					'house_bl' => $model->hbn,
					'packages' => $model->pkg,
					'pallets' => $model->mdata['amzon_pallet'],
					'weight' => $model->weight,
					'volume' => $model->cbm,
					'driver_name'=>$pb->driver_name,
					'rego'=>$pb->rego,
					'company_name'=>$pb->company_name,
					'company_email'=>$pb->company_email,
					'depot' => $depot,
					'id'=>$pb->id,
					'booking_date'=>$bookingDate,
					'booking_time'=>$bookingTime,
					'staticTimeSlot'=>$timeSlot
				));
			}else
			{
					echo "Booking information can not be adjusted.";

			}
		}else
		{
			$oldBookingTime = $pb->booking_time;
			$pb->booking_time = $_POST['booking_date'] . ' ' . $_POST['booking_time'] . ':00';
			$pb->driver_name = $_POST['name'];
			$pb->company_name = $_POST['company_name'];
			$pb->company_email = $_POST['email'];
			$pb->rego = $_POST['rego'];

			if($pb->booking_time==$oldBookingTime)
			{
				$pb->save();
				echo $pb->booking_number;
				// Send confirmation email to customer
				$emailService = new EmailService();
				$emailService->sendBookingConfirmationEmail($pb, $pb->shipment->mdata['amzon_pallet']);
			}else
			{
				// Check if the choosen slot has already been occupied, only save this record when the slot is available.
				switch($pb->shipment->consol->dpt_id) {
					case Org::TLA_DEPARTMENT_SYDNEY:
						$sql = 'SELECT `booking_time` FROM pickup_booking WHERE `status` != 3 AND `booking_number` LIKE "S%"';
						//$dependency = new CDbCacheDependency('SELECT MAX(id) FROM pickup_booking WHERE `status` != 3 AND `booking_number` LIKE "S%"');
						break;
					case Org::TLA_DEPARTMENT_MELBOURNE:
						$sql = 'SELECT `booking_time` FROM pickup_booking WHERE `status` != 3 AND `booking_number` LIKE "M%"';
						//$dependency = new CDbCacheDependency('SELECT MAX(id) FROM pickup_booking WHERE `status` != 3 AND `booking_number` LIKE "M%"');
						break;
					case Org::TLA_DEPARTMENT_BRISBANE:
						$sql = 'SELECT `booking_time` FROM pickup_booking WHERE `status` != 3 AND `booking_number` LIKE "B%"';
						//$dependency = new CDbCacheDependency('SELECT MAX(id) FROM pickup_booking WHERE `status` != 3 AND `booking_number` LIKE "B%"');
						break;
				}
				//$slotCheck = Yii::app()->db->cache($depot."54321", $dependency)->createCommand($sql)->queryAll();
				$slotCheck = Yii::app()->db->createCommand($sql)->queryAll();
				if (empty($slotCheck)||!in_array(['booking_time' => $pb->booking_time], $slotCheck)) {
					$pb->save();		// save record to database
					$pb->shipment->mdata['pickup_booking_time'] = $pb->booking_time;
					$pb->shipment->updateMeta();
					echo $pb->booking_number;
					// Send confirmation email to customer
					$emailService = new EmailService();
					$emailService->sendBookingConfirmationEmail($pb, $pb->shipment->mdata['amzon_pallet']);
				}else {
					// Selected slot is occupied, return error message
					echo 'Slot has been occupied, please pick another time slot.';
				}
			}
		}
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new PickupBooking('search');
		$model->unsetAttributes();  // clear any default values
		$model->status = 2;
		if(isset($_GET['PickupBooking']))
			$model->attributes=$_GET['PickupBooking'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return PickupBooking the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=PickupBooking::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param PickupBooking $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='pickup-booking-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
