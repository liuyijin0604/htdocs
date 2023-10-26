<?php
/* 
 * support dxt auto weight
 */
class ApiDxtAction extends CAction {
	public function run() {

        if ( !isset($_GET['key'])  || $_GET['key'] != 'pcadxt168') {
            $resp['success'] = 0;
            $resp['msg'] = 'invalid request';
            echo json_encode($resp);
            return;
        }


        $resp = array(
            'success' => 1,
            'msg' => 'success'
        );

        $hbn = $_GET['hbn'];
        $weight = $_GET['w'];

        $shipment_model = ExAfs::model()->find('hbn = :hbn',array(
            ':hbn' => $hbn
        ));

        if ( !isset($shipment_model)) {
            $resp['success'] = 0;
            $resp['msg'] = 'Not Found Shippment';
            echo json_encode($resp);
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

        }

		echo json_encode($resp);
	}


}
