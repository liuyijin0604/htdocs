<?php

class EdiController extends Controller{

	protected $nonAjax=array('export');

    /**
     * Lists and search.
     */
    public function actionList(){
        $model = new EdiAwbConsol('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['EdiAwbConsol']))
            $model->attributes=$_GET['EdiAwbConsol'];
        $this->render('list',array(
            'model'=>$model,
        ));
    }

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id){
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

    /**
     * export searched Edi Awb Consoles
     */
    public function actionExport(){
        if(!empty($_POST)){
            parse_str($_POST['q'], $q);
            $model=new EdiAwbConsol('search');
            $model->unsetAttributes();  // clear any default values
            if(!empty($q['EdiAwbConsol']))	$model->attributes=$q['EdiAwbConsol'];
            $criteria = new CDbCriteria;
            $criteria->condition = "t.created >= '".$_POST['date']['from']."' AND t.created < DATE_ADD('".$_POST['date']['to']."', INTERVAL 1 DAY)";
            $dp = $model->search(false, null, $criteria);
            $tt = $dp->totalItemCount;
            $xls = new oExcel;
            $i = 1;
            $xls->addRow($i++, array('Consol#', 'AWB#', 'Owner', 'Status', 'Depot', 'Created'));

            for($pg = 0; $pg < ceil($tt/1000); $pg++){
                $_GET['ExParcel_page'] = $pg+1;
                $dp = $model->search(true, 1000, $criteria);
                foreach($dp->data as $r){
                    $xls->addRow($i++, array( $r->no, $r->awb,empty($r->owner)? '' : $r->owner->name,$r->getStatus(),$r->depot->name,$r->created));
                }
            }
            $xls->output('ediawb_search_export_'.time().'.xlsx');
            Yii::app()->end();
        }
        $this->render('export_search');
    }

    /**
     * remove EDI awb line
     * @param $id
     */
    public function actionRemoveAwb($id){
        $awb = EdiAwbConsol::model()->findByPk($id);
        if ( $awb ) {
           $awb->delete();
        }
        echo 'done';
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreateAWB(){
        $model = new EdiAwbConsol();
        if(isset($_POST['EdiAwbConsol'])){

            // check to see if same awb existing or not
            $awb =  $_POST['EdiAwbConsol']['awb'];
         //   $existingAwb = EdiAwbConsol::model()->find('awb = :awb',[':awb' => $awb] );
          //  if ( empty($existingAwb) )
            { // not existing
                $model->attributes = $_POST['EdiAwbConsol'];

                // save leg one data into meta
                $model->mdata['leg2'] = array(
                    'airline' => $_POST['airline'],
                    'flight' => $_POST['flight'],
                    'etd' => $_POST['etd2'],
                    'eta' => $_POST['eta2'],
                );
                $model->mdata['goods'] = isset($_POST['selected_goods']) ? $_POST['selected_goods'] : '';

                if (!empty($_POST['notes'])) {
                    $model->custom_log_note = $_POST['notes'];
                }

                $model->save();
            }
            //else {
                // existing already
               // $model->addError('id','The same AWB No. existing already!');
            //}
            $this->ajaxResult($model, ['id']);
        }

        $this->render('create',array(
            'model'=>$model,'goods' => self::$GOODS
        ));
    }

    /**
     * @param $id
     * @throws CHttpException
     */
    public function actionNotes($id){
        $model = $this->loadModel($id);
        if(!empty($_POST['notes'])){
            $log = Log::add($model, 6, array('notes' => $_POST['notes']));
            $this->ajaxResult($log);
        }
    }


    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id){
        $model = $this->loadModel($id);
        if ( !empty($_POST) ) {
            if(!empty($_POST['EdiAwbConsol'])){
                $model->attributes=$_POST['EdiAwbConsol'];
            }
            // save leg one data into meta
            $model->mdata['leg2'] = array(
                'airline' =>  $_POST['airline'],
                'flight' =>  $_POST['flight'],
                'etd' =>  $_POST['etd2'],
                'eta' =>  $_POST['eta2'],
            );
            $model->mdata['goods'] = $_POST['selected_goods'];

            if ( !empty( $_POST['notes']) ) {
                $model->mdata['custom_log_note'] = $_POST['notes'];
            }

            $model->save();
            $this->ajaxResult($model);
        }

        if(isset($_GET['tab'])){
            Acl::hasAccess($this->CaName.'/'.$_GET['tab'], true);
            $this->render('tab_'.$_GET['tab'], array('model'=>$model));
        }else{
            $this->render('update',array('model'=>$model));
        }

    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model = EdiAwbConsol::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

}
