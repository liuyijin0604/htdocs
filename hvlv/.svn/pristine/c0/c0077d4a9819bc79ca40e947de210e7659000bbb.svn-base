<?php
switch($model->type){
	case 1010: //Pallets In
	case 1020: //Bulk In
	case 1030: //Container Unload
		echo $this->renderPartial('_task_10', array('model'=>$model));
	break;
	case 3010: //Pick Pallet
	case 3020: //Pick Carton
	case 3030: //Pick Unit
		echo $this->renderPartial('_task_30', array('model'=>$model));
	break;
	case 3050:
		echo $this->renderPartial('_task_305', array('model' => $model));
	break;
	case 3210: // Pack Order
		echo $this->renderPartial('_task_32', array('model'=>$model));
	break;
    	case 2110: //pick up
		echo $this->renderPartial('_task_211', array('model'=>$model));
	break;
	case 2120: //delivery
		echo $this->renderPartial('_task_212', array('model'=>$model));
	break;
}
?>

