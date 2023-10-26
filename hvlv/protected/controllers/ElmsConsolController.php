<?php

class ElmsConsolController extends Controller
{
    protected $nonAjax = array();

	public function actionList()
	{
        $model = new ElmsConsol('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['ElmsConsol']))
            $model->attributes = $_GET['ElmsConsol'];

        $this->render('list', array(
            'model' => $model,
        ));
	}

    public function actionUpdateByNo($no){
        $consol = ElmsConsol::model()->find('no = :no',[':no' => $no]);
        if ( !empty($consol) ) {
            $this->actionUpdate($consol->id);
        } else {
            throw new CHttpException(404,'The requested page does not exist.');
        }
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        if ( !empty($_POST['mdata']) ) {
            foreach ( $_POST['mdata'] as $k=>$v ) {
                $model->mdata[$k] = $v;
            }
        }

        if ( !empty($_POST) ) {
            if (isset($_POST['ElmsConsol'])) {
                $model->attributes = $_POST['ElmsConsol'];
                if ( empty($model->eta) ) $model->eta = $model->created;
            }
            $model->save();

            $this->ajaxResult($model);
        }

        if ( isset($_GET['tab']) ) {
            Acl::hasAccess($this->CaName . '/' . $_GET['tab'], true);
            $this->render('tab_' . $_GET['tab'], array('model' => $model));
        } else {
            $this->render('update', array('model' => $model));
        }
    }


    public function actionNotes($id)
    {
        $model = $this->loadModel($id);
        if (!empty($_POST['notes'])) {
            $log = Log::add($model, 6, array('notes' => $_POST['notes']));
            $this->ajaxResult($log);
        }
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = ElmsConsol::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

}