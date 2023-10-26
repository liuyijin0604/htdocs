<?php
switch($model->type){
	case 1010: //Pallets In
	case 1020: //Bulk In
	case 1030: //Container Unload
		echo $this->renderPartial('_task_10', array('model'=>$model));
	break;
	case 2030: //Container Load
	case 2040: //Pack PMC
		echo $this->renderPartial('_task_20', array('model'=>$model));
	break;
	case 3010: //Pick Pallet
	case 3020: //Pick Carton
	case 3030: //Pick Unit
	case 5010: //CG Delivery
		echo $this->renderPartial('_task_30', array('model'=>$model));
	break;
	case 3210: //Pack Order
		echo $this->renderPartial('_task_32', array('model'=>$model));
	break;
	case 2110: //pick up
		echo $this->renderPartial('_task_211', array('model'=>$model));
	break;
	case 2120: //delivery
		echo $this->renderPartial('_task_212', array('model'=>$model));
	break;
	case 4010: //Stock Take
	case 4030: //Stock Discard
	break;
	case 6010: // Adhoc Task
	case 6020: // Request
	case 6030: // Process
	case 6040: // QA
		echo $this->renderPartial('_task_60', array('model'=>$model));
	break;
	case WmsTask::TYPE_Split_Delivery:
		echo $this->renderPartial('_task_split_sorting', array('model'=>$model));
		break;
}
?>
