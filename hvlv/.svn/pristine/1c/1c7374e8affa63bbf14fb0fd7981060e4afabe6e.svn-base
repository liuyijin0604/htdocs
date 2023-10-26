<?php
class WmsLocationController extends Controller{
	protected $nonAjax = ['export','report','label','genLabel', 'labelLarge', 'reLabel'];

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id){
		$model=$this->loadModel($id);
		if(isset($_GET['tab'])){
			Acl::hasAccess($this->CaName.'/'.$_GET['tab'], true);
            $this->render('tab_' . $_GET['tab'], array('model' => $model));
		}else{
			$this->render('view',array('model'=>$model));
		}
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate(){
		$model=new WmsLocation;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['WmsLocation'])){
			$model->attributes=$_POST['WmsLocation'];
			$model->save();
			$this->ajaxResult($model);
		}
		$model->status = 1;

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id){
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['WmsLocation'])){
			$model->attributes=$_POST['WmsLocation'];
			$model->save();
			$this->ajaxResult($model);
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionGenLabel(){
		if(!empty($_POST['q'])){
			$cs = WmsLocation::genCodes($_POST['q'], $_POST['t'], $_POST['wid']);
			if ($_POST['d'] == 'vertical') {
				oPDF::renderPDF('label_plt', array('cs' => $cs, 'dup' => false));
			} else {
				oPDF::renderPDF('label_plt_large', array('cs' => $cs, 'dup' => false));
			}
		}
		$this->render('plt_label');
	}

	public function actionReLabel()
	{
		if (!empty($_POST['q'])) {
			$q = preg_split('/[\n\r\s]+/', $_POST['q']);
			$q = array_filter($q);

			if (!empty($q)) {
				$cs = [];
				$plts = WmsLocation::model()->findAll('name in ("' . implode('","', $q) . '")');
				foreach ($plts as $plt) {
					$cs[] = $plt->name;
				}

				if (!empty(array_diff($q, $cs))) {
					echo 'Pallet No.: ' . implode(', ', array_values(array_diff($q, $cs))) . ' not exist in system';
				} else {
					if (!empty($cs)) {
						oPDF::renderPDF('label_plt', array('cs' => $cs, 'dup' => true));
					} else {
						echo 'empty';
					}
				}
			} else {
				echo 'empty';
			}
		} else {
			$this->render('reprint_plt_label');
		}
	}

	public function actionLabel($id){
		$model=$this->loadModel($id);
		oPDF::renderPDF('label_plt', array('cs' => [$model->code], 'dup' => true));
	}

	public function actionLabelLarge($id)
	{
		$model = $this->loadModel($id);
		oPDF::renderPDF('label_plt_large', array('cs' => [$model->code], 'dup' => true));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id){
		if(Yii::app()->request->isPostRequest){
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists and search.
	 */
	public function actionList(){
		$model=new WmsLocation('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['WmsLocation']))
			$model->attributes=$_GET['WmsLocation'];

		$model->setAttribute('archived', 0);
		$this->render('list',array(
			'model'=>$model,
		));
	}

	/**
	 * Archived pallets list
	 */
	public function actionArchivedPalletList()
	{
		$model=new WmsLocation('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['WmsLocation']))
			$model->attributes=$_GET['WmsLocation'];

		$model->setAttribute('archived', 1);
		$this->render('list',array(
			'model'=>$model,
		));
	}

	public function actionReport($id){
		$xls = new oExcel;
		$i = 1;
		$xls->setColWidth(array(15,15,25,50,20,20,15,15));
		$xls->addRow($i++, array('Location', 'Owner', 'PLT', 'Product', 'EAN', 'Brand', 'Expiry', 'Batch', 'Qty', 'Stock In', 'Days'));
		$ss = WmsStockLocation::model()->findAll('qty > 0 AND location_id > 99');
		foreach($ss as $p){
			$sit = WmsStockLedger::stockInTime($p->location_id);
			$xls->addRow($i++, array(empty($p->loc->pid)? '' : $p->loc->parent->code, $p->stock->customer->name, $p->loc->code, $p->stock->prod->name, '="'.$p->stock->prod->ean.'"', $p->stock->prod->brand, $p->stock->expiry, $p->stock->batch, $p->qty, substr($sit, 0, 10), round((time() - strtotime($sit)) / 86400)));
		}

		$org = Org::model()->findByPk($id);

		$xls->output('WmsLocation_report_'.str_replace(' ', '_', $org->name).'_'.date('YmdHis').'.xlsx');
	}

	public function actionExport($id){
		$model=$this->loadModel($id);

		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, array('Location','WBN','Status','Name','Address','State'));

		foreach($model->items as $p){
			$m = new $p->model;
			$r = $m::model()->findByPk($p->fid);
			$xls->addRow($i++, [$model->name, $r->hbn, $r->getStatus(), $r->cnee->name, $r->cnee->address, $r->state]);
		}

		$xls->output($model->name.'_list.xlsx');
	}

	public function actionSuggest(){
		$rs = WmsLocation::model()->findAll(array(
			'condition' => "wid = :wid AND (t.name LIKE :t OR t.code LIKE :t)",
			'params' => array(':t' => $_GET['term'].'%', ':wid' => 106),
			'limit' => 20,
		));
		$a = array();
		foreach($rs as $r){
            $a[] = array(
                'value' => $r->id,
                'label' => $r->name,
            );
		}
		echo json_encode($a);
	}

	public function actionParentSuggest(){
		$rs = WmsLocation::model()->findAll(array(
			'condition' => "wid = :wid AND (t.name LIKE :t OR t.code LIKE :t)",
			'params' => array(':t' => $_GET['term'].'%', ':wid' => $_GET['wid']),
			'limit' => 20,
		));
		$a = array();
		foreach($rs as $r){
            $a[] = array(
                'value' => $r->id,
                'label' => $r->name,
            );
		}
		echo json_encode($a);
	}

	public function actionManageLocationRecommendation(){
		$this->render('parcel_priority');
	}

	public function actionEditParcelPrioristyByDptId(){
		$model = new ParcelPriority('search');
		$parcelPrioritys = $model->findAll("id>0 and dpt_id =:dptId",[":dptId"=>$_GET['dptId']]);
		$parcelPrioritysData = [];
		foreach ($parcelPrioritys as $key => $value) {
			$parcelPrioritysData[] = $value->getAttributes();
		}
		$recommendation = new WarehouseLocationRecommendation('search');
		$recommendation->dpt_id = $_GET['dptId'];
		if(!empty($_GET['WarehouseLocationRecommendation']))
		{
			$recommendation->setAttributes($_GET['WarehouseLocationRecommendation']);
			$this->render('_recommendation',array(
			'recommendation'=>$recommendation
			));
			return;
		}

		$this->render('manage_location_recommendation',array(
			'parcelPrioritys'=>$parcelPrioritysData,
			'recommendation'=>$recommendation
		));
	}

		/**
	 * save all Parcel Priority for specified charge code
	 */
	public function actionSaveParcelPriority()
	{

		$data = $_POST['data'];
		$data = json_decode($data, true);
		if (!empty($data)) {
			$trans = Yii::app()->db->beginTransaction();
			try {
				
				ParcelPriority::model()->deleteAll('dpt_id = :dptId',[":dptId"=>$_POST['dptId']]);

				// step 2 - update all with new zone rate data
				foreach ($data as $wRates) {
					if(empty($wRates['parcel_type'])) continue;
					$parcelType = $wRates['parcel_type'];
					$areaType = $wRates['area_type'];
					$isOversize = $wRates['is_oversize'];
					$priority = $wRates['priority'];
					
					$parcelPriority = new ParcelPriority();
					$parcelPriority->parcel_type = $parcelType;
					$parcelPriority->area_type = $areaType;
					$parcelPriority->is_oversize = $isOversize;
					$parcelPriority->priority = $priority;
					$parcelPriority->dpt_id = $_POST['dptId'];
					$parcelPriority->save();
				}
				$trans->commit();
			} catch (Exception $ex) {
				$trans->rollback();
				throw $ex;
			}
		}

		$resp = array('success' => 1);
		echo json_encode($resp);

	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=WmsLocation::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='storage-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
