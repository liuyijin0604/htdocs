<?php

class HcController extends Controller{
	/**
	 * Declares class-based actions.
	 */
	protected $nonAjax = array();
    protected $skipAcl = [];

	protected $org, $type;


    /**
     * @param CAction $action
     * @return bool
     */
	public function beforeAction($action){

        if(!Yii::app()->request->isAjaxRequest) $this->layout = 'pos';

		if(!Yii::app()->user->isGuest){
			$this->org = Org::model()->findByPk(Yii::app()->user->org);
            $this->type = 'sc';
		}
		return parent::beforeAction($action);
	}

    /**
     * @param $id
     */
    public function actionDetails($id) {
        $this->forward('/cg/order/details/id/'.$id);
        // $this->redirect($this->createUrl('/cg/order/details', array('id' => $id, 'redirect' => 'pos')));
    }

    // show order history
    public function actionHistory() {
        $this->forward('/cg/order/history');
        // $this->redirect($this->createUrl('/cg/order/history', array('redirect' => 'pos')));
    }

    /**
     * show all awb related console scan statistic information
     */
    public function actionMake() {
        $this->forward('/cg/order/make');
        // $this->redirect($this->createUrl('/cg/order/make', array('redirect' => 'pos')));
    }

}