<?php

class GeneralCostSplitController extends Controller
{
	public function actionList()
	{
        $model = new GeneralCostSplit('search');
        $model->unsetAttributes();

        if ( isset($_GET['GeneralCostSplit']) ) {
            $model->attributes = $_GET['GeneralCostSplit'];
        }

        $this->render('list',['model' => $model]);
	}

    public function actionDelete($id){
        if(Yii::app()->request->isPostRequest)
        {
            // we only allow deletion via POST request
            GeneralCostSplit::model()->findByPk($id)->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if(!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        } else {
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionChargecodeList(){

        $rs = Chargecode::model()->findAll(array(
            'condition' => 'code LIKE :n OR name LIKE :n',
            'params' => array(':n' => '%'.$_GET['term'].'%'),
            'limit'=>20
        ));
        $a = array();
        foreach($rs as $r){
            $a[] = array(
                'value' => $r->code,
                'label' => $r->code.': '.$r->name,
            );
        }

        echo json_encode($a);
    }

    /**
     *
     */
    public function actionGrid(){
        if(isset($_POST['GeneralCostSplit'])){
            if ( empty($_POST['GeneralCostSplit']['id']) ){
                $model = new GeneralCostSplit();
                $chargeCode = Chargecode::model()->find('status = 1 AND code = :code',[':code' => $_POST['GeneralCostSplit']['chargecode']]);
                if ( !empty($chargeCode) ) {
                    $model->chargecode_id = $chargeCode->id;
                } else {
                    $model->addError('id','Charge Code : ' .$_POST['GeneralCostSplit']['chargecode'] . ' not existing' );
                    $this->ajaxResult($model);
                }
            }else{
                $model = GeneralCostSplit::model()->findByPk($_POST['GeneralCostSplit']['id']);
            }
            $data = $_POST['GeneralCostSplit'];
            $model->mdata['pim'] = isset($data['pim']) ? intval($data['pim']) : 0;
            $model->mdata['pex'] = isset($data['pex']) ? intval($data['pex']): 0;
            $model->mdata['paf'] = isset($data['paf']) ? intval($data['paf']) : 0;
            $model->mdata['p3pl'] = isset($data['p3pl']) ? intval($data['p3pl']) : 0;

            if (  $model->mdata['pim'] + $model->mdata['pex'] + $model->mdata['paf'] + $model->mdata['p3pl'] != 100 ) {
                $model->addError('id','all percents added should be equal to 100' );
            } else {
                $model->save();
            }
            $this->ajaxResult($model);
        }
    }

}