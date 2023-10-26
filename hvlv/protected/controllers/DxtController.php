<?php

class DxtController extends Controller
{

    public function actionAjaxShipment(){

        $shipment_hbn = $_POST['hbn'];
        $shipment_model = ExAfs::model()->find('hbn = :hbn',array(
            ':hbn' => $shipment_hbn
        ));

        // return update weight form
        if ( !isset($shipment_model)) {
            echo 'No Matched Shippment';
            return;
        }

        // check to see if we can modify weight
        // 只有在没有确认入库/发货状态才可以修改重量
        // 20 => 'Consolidated',
        //25 => 'Confirmed',
		//30 => 'Reported',
		//35 => 'Held',
		//40 => 'Cleared',
		//60 => 'Dispatched',
		//70 => 'Arrived',
		//80 => 'Clearance',
		//90 => 'Courier',
		//99 => 'Delivered',
		//100 => 'Cancelled',
        if ( $shipment_model->status != 20  ) {
            echo "parcel weight can't be changed now";
            return;
        }

        $this->renderPartial('_shipment', array('model' => $shipment_model), false, true);
    }

    public function actionAjaxWeight(){

        $shipment_hbn = $_POST['hbn'];
        $weight = $_POST['w'];

        $shipment_model = ExAfs::model()->find('hbn = :hbn',array(
            ':hbn' => $shipment_hbn
        ));

        if ( !isset($shipment_model)) {
            echo 'Not Found Shippment';
            return;
        }

        // save weight
        $shipment_model->setAttribute('weight',$weight);
        $shipment_model->save();

        // for DXT shipment we put the weight changed informaton in sync queue
        // check to see if this shipment belong to DXT
        $consol = ExacConsol::model()->findByPk($shipment_model->consol_id);
        include(Yii::app()->basePath.DIRECTORY_SEPARATOR .'commands/DxtCommand.php');
        if ( isset($consol) && $consol->owner_id == DxtCommand::DXT_ORG_ID ) {
            // put into sync DXT queue
            DxtPushQueue::addStockIn($shipment_model->id);

            // test only
            //DxtPushQueue::addStockOut($shipment_model->consol_id);
            //DxtPushQueue::addRoute($shipment_model->consol_id);
        }

        echo 'weight updated successfully';

    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id){
        $model=ExAfs::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}