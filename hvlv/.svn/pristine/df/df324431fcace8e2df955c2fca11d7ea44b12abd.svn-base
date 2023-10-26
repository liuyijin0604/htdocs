<?php

class ShipmentErrRecordController extends Controller
{

	/**
	 * Lists all models.
	 */
    public function actionIndex() {
        $filtersForm = new FiltersForm;
        $provide = [];
        if (isset($_GET['FiltersForm']))
            $filtersForm->filters = $_GET['FiltersForm'];
        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $start_time = $_GET['start_date'];
            $end_time = $_GET['end_date'];
            $end_time=date('Y-m-d',strtotime('+1 day',strtotime($end_time)));
            $sql="SELECT err_op FROM `shipment_err_record` WHERE record>0 AND err_time<:end AND err_time > :start  GROUP BY err_op";
            $opIds=Yii::app()->db->createCommand($sql)->bindValues(array(':end'=>$end_time,':start'=>$start_time))->queryAll();
            foreach ($opIds as $op) {
                $opid = $op['err_op'];
                $temp = [];
                foreach (array_keys(ExParcel::$errTypes) as $key){
                $sql="SELECT COUNT(*) AS number FROM `shipment_err_record` WHERE record&:flag>0 AND err_time<:end AND err_time > :start AND err_op=:opid";
                ${'number'.$key}=Yii::app()->db->createCommand($sql)->bindValues(array(':flag'=>$key,':end'=>$end_time,':start'=>$start_time,':opid'=>$opid))->queryScalar();
                $temp['number'.$key]=${'number'.$key};
                }
                $opUser= User::model()->findByPk($opid);
                $provide[]=['id'=>$opid,'user'=>$opUser->getFullName(),'']+$temp;
            }
        }
        $filteredData = $filtersForm->filter($provide);
        $dataprovider = new CArrayDataProvider($filteredData);
        $sort = new CSort();
        $sort_array['user']=array(
                'asc' => 'user ASC',
                'desc' => 'user DESC',
            );
        foreach(ExParcel::$errTypes as $key=>$value){
            $sort_array['number'.$key]=array(
                'asc'=>'number'.$key. ' ASC',
                'desc'=>'number'.$key. ' DESC',
           );
        }
        $sort->attributes = $sort_array;
        $sort->defaultOrder = "user DESC";
        $dataprovider->sort = $sort;
        $dataprovider->pagination = array('pageSize' => 30,);
       if (!empty($_GET['partial'])) {
            $this->renderPartial('_search1', array(
                'dataProvider' => [$dataprovider, $filtersForm]
            ));
            return;
        }
        $this->render('index', ['dataProvider' => [$dataprovider, $filtersForm]]);
    }
       /**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return ShipmentErrRecord the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=ShipmentErrRecord::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}


}
