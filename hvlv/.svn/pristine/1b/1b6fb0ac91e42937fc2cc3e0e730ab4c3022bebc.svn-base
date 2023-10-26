<?php

class PostcodeController extends Controller{

    protected $nonAjax = array('ajaxAdd');

    protected $skipAcl = ['suggest'];


	public function actionSuggest(){
		$rs = Postcode::model()->findAll(array(
			'condition' => "`country` = 'AU' AND (`suburb` LIKE :t OR postcode LIKE :t2)", 
			'params' => array(':t' => '%'.$_GET['term'].'%', ':t2' => $_GET['term'].'%'), 
			'limit'=>20,
		));
		$a = array();
		foreach($rs as $r){
			$sub = ucwords(strtolower($r->suburb));
            $a[] = array(
                'value' => $sub,
                'label' => $sub.', '.$r->state.' '.$r->postcode,
                'st' => $r->state,
                'pc' => $r->postcode,
            );
		}
		echo json_encode($a);
	}

    /**
     * Lists and search.
     */
    public function actionList(){
        $model=new Postcode('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Postcode']))
            $model->attributes=$_GET['Postcode'];

        $this->render('list',array(
            'model'=>$model,
        ));
    }

}