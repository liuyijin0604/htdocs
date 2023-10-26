<?php

class CrmController extends Controller
{
    protected $nonAjax = array('ajaxSearchReport');

	public function actionList()
	{
        $model = new Crm('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Crm']))
            $model->attributes=$_GET['Crm'];

        // check to see if we should show operator's alert tickets
        if ( isset($_GET['alert']) && $_GET['alert'] === 'true' ) {
            $this->render('crm_list', array(
                'model' => $model,
                'showMyTicket' => $_GET['alert']
            ));
        } else {
            $this->render('crm_list', array(
                'model' => $model,
            ));
        }
	}

    /**
     * update or show crm details
     * @param $id
     */
    public function actionUpdateCrm($id){
        $model= new CrmLog();

        $rt = $model->findAllByAttributes(array('crm_id' => $id));

        $this->render('update_crm',array('model'=>$model));
    }

    /**
     * create a new CRM ticket
     * @param $id
     */
    public function actionCrmNotes($id){
        $model = NULL;
        if ( $id > 0 ) {
            $model = Shipment::model()->findByPk($id);
        }
        if(!empty($_POST['notes'])){
            $crm = Crm::addLog($model, $_POST);
            $this->ajaxResult($crm);
        }
    }

    /**
     * create a new CRM ticket
     * @param $id
     */
    public function actionCrmMoreNotes($id){
        if(!empty($_POST['notes'])){
            // $crm = Crm::addLog($model, $_POST);
            $crmlog_model = new CrmLog();
            $crmlog_model->attributes = array(
                'crm_id' => $id,
                'operator_id' => Yii::app()->user->id,
                'note' => $_POST['notes']
            );
            $crmlog_model->save();
            $this->ajaxResult($crmlog_model);
        }
    }

    /**
     * list all crm items
     */
    public function actionCrmList(){

        $model = new Crm('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Crm']))
            $model->attributes=$_GET['Crm'];

        $this->render('crm_list',array(
            'model'=>$model,
        ));
    }

    /**
     * show basic report information
     */
    public function actionReports(){
        $openCount = Crm::model()->count('status = :open',[':open' => 0]);
        $closedCount = Crm::model()->count('status = :closed',[':closed' => 1]);

        // get all type , level, source name lists
        $crm = new Crm();
        $types = $crm->getTypes();
        $types[0] = '其它';
        $levels = $crm->getLevels();
        $levels[0] = '其它';
        $sources = $crm->getSources();
        $sources[0] = '其它';

        // statistic by types
        $typesData = array();
        $command = Yii::app()->db->createCommand('SELECT type,COUNT(id) AS num FROM  crm GROUP BY type order by num asc');
        foreach($command->queryAll() as $row) {
            $typesData[$types[ $row['type'] ] ] = $row['num'];
        }

        // statistic by levels
        $levelsData = array();
        $command = Yii::app()->db->createCommand('SELECT level,COUNT(id) AS num FROM  crm GROUP BY level order by num asc');
        foreach($command->queryAll() as $row) {
            $levelsData[$levels[ $row['level'] ] ] = $row['num'];
        }

        // statistic by sources
        $sourcesData = array();
        $command = Yii::app()->db->createCommand('SELECT source,COUNT(id) AS num FROM  crm GROUP BY source order by num asc');
        foreach($command->queryAll() as $row) {
            $sourcesData[$sources[ $row['source'] ] ] = $row['num'];
        }

        $crm_search_data_provider = new CArrayDataProvider($this->searchTopCreatedReport(0,'',''),array(
            'id' => 'crm_search_report',
            'pagination'=>array(
                'pageSize'=>'30',
            ),
        ));
        $curdate = date("Y-m-d");// current date
        $olddate = strtotime(date("Y-m-d", strtotime($curdate)) . " -30 days");
        $model = array(
            'from_time' => date('Y-m-d',$olddate),
            'to_time' => date('Y-m-d'),
            'opid' => '',
            'crm_search_provider' => $crm_search_data_provider
        );

        $this->render('reports',array(
            'total' => $closedCount + $openCount,
            'totalOpen' => $openCount,
            'totalClosed' => $closedCount,
            'types' => $typesData,
            'levels' => $levelsData,
            'sources' => $sourcesData,
            'model' => $model
        ));
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionViewCrm($id){

        // get crm ticket id
        $crmlog_model = new CrmLog();
        $crmlog_model->crm_id = $id;

        $crm = Crm::model()->findByPk($id);
        $status = $crm->status;
        $this->render('view_crm',array(
            'model' => $crmlog_model,
            'status' => $status
        ));
    }

    /**
     * reopen one CRM ticket
     * @param $id
     */
    public function actionReopenCrm($id){
        $crm = Crm::model()->findByPk($id);

        if ( !empty($crm) ) {
            // close current ticket
            $crm->status = 10;
            $crm->save();
            $crm->updateCurrentUser();
            $rt = array('success' => 1);
        } else {
            $rt = array('success' => 0,'error' => 'Invalid ticket');
        }

        echo json_encode($rt);
    }
    /**
     * clost one CRM ticket
     * @param $id
     */
    public function actionCloseCrm($id){

        $crm = Crm::model()->findByPk($id);

        // save last note for close ticket
        if ( !empty($_POST['notes'])) {
            $crm_log = new CrmLog();
            $crm_log->attributes = array(
                'crm_id' => $id,
                'operator_id' => Yii::app()->user->id,
                'note' => $_POST['notes'],
                'process_method'=>$_POST['process_method'],
            );
            if($crm_log->save()){
            $crm->updateCurrentUser();
            }
        }

        // close current ticket
        $crm->status = 90;
        $crm->closed_by =  Yii::app()->user->id;
        $crm->close_time = date('Y-m-d h:i:s');
        $crm->save();
        if($crm->main_type==20&&$crm->type==30){
            //send msg to customer
            $crm->noticeTicketClose();
        }
        $rt = array('success' => 1);
        echo json_encode($rt);
      }
 

    /**
     * @throws CException
     */
    public function actionAjaxSearchReport(){

        $operator_id = 0;
        if ( !empty($_POST['opid']) ){
            $operator_id = $_POST['opid'];
        }

        $from_time = NULL;
        if ( isset($_POST['from_time'] ) ) {
            $from_time = $_POST['from_time'];
        }
        $to_time = NULL;
        if ( isset($_POST['to_time'] ) ) {
            $to_time = $_POST['to_time'];
        }

        $model = array();
        $model['opid'] = $operator_id;
        $model['from_time'] = $from_time;
        $model['to_time'] = $to_time;

        $allTickets = $this->searchTopCreatedReport($operator_id,$from_time,$to_time);

        $crm_search_data_provider = new CArrayDataProvider($allTickets,array(
            'id' => 'crm_search_report',
            'pagination'=>array(
                'pageSize'=>'30',
            ),
        ));
        $model['crm_search_provider'] = $crm_search_data_provider;

        $this->renderPartial('_op_reports',array('model' => $model),false, true);
    }

    /**
     * in case operator id is null or empty
     * we just search top 10 by created tickets in the specified time
     * @param $operator_id
     * @param $from_time
     * @param $to_time
     * @return array
     */
    private function searchTopCreatedReport($operator_id,$from_time,$to_time){
        $allTickets = array();

        if ( !empty($operator_id) ) {
            $opData = array();
            // get created number
            $user = User::model()->findByPk($operator_id);
            $opData['id'] = $operator_id;
            $opData['name'] = $user->getFullName();
            $opData['created'] = Crm::model()->count('operator_id = :oid and create_time >= :cftime and create_time <= :cttime',[':oid' => $operator_id,':cftime' => $from_time . ' 00:00:00',':cttime' => $to_time . ' 23:59:59']);

            // get closed number
            $opData['closed'] = Crm::model()->count('closed_by = :oid and close_time >= :cftime and close_time <= :cttime',[':oid' => $operator_id,':cftime' => $from_time . ' 00:00:00',':cttime' => $to_time . ' 23:59:59']);

            // get all processed number
            $opData['processed'] = CrmLog::model()->count('operator_id = :oid and time >= :cftime and time <= :cttime',[':oid' => $operator_id,':cftime' => $from_time . ' 00:00:00',':cttime' => $to_time . ' 23:59:59']);
            $allTickets[] = $opData;

        } else {
            // get top 10 operator's data

            $startTime = $from_time . ' 00:00:00';
            $endTime = $to_time . ' 23:59:59';
            $command = Yii::app()->db->createCommand("SELECT operator_id,COUNT(id) AS num FROM crm WHERE create_time >= '".$startTime."' AND create_time <= '". $endTime."' GROUP BY operator_id order by num asc limit 10");
            foreach($command->queryAll() as $row) {
                $user = User::model()->findByPk($row['operator_id']);
                $processed = CrmLog::model()->count('operator_id = :oid and time >= :cftime and time <= :cttime',[':oid' => $row['operator_id'],':cftime' =>$startTime,':cttime' => $endTime]);
                $closed = Crm::model()->count('closed_by = :oid and close_time >= :cftime and close_time <= :cttime',[':oid' => $row['operator_id'],':cftime' =>$startTime,':cttime' => $endTime]);
                $allTickets[]  =  array(
                    'id' => $row['operator_id'],
                    'name' => $user->getFullName(),
                    'created' => $row['num'],
                    'processed' => $processed,
                    'closed' => $closed
                );
            }
        }

        return $allTickets;
    }

    /**
     * return suggest from name
     */
    public function actionFromSuggest(){
        $this->operatorSuggest(array(0,10,20,30,35,40,45,120));
    }

    private function operatorSuggest($grp=''){
        if(empty($grp)){
            $rs = User::model()->findAll(array(
                'condition' => 'active = 1 AND (fname LIKE :n OR lname LIKE :n)',
                'params'=> array(':n' => '%'.$_GET['term'].'%'),
                'order' => 'fname',
                'limit' => 20,
            ));
        }elseif(is_array($grp)){
            $rs = User::model()->findAll(array(
                'condition' => 'active = 1 AND type IN ('.implode(',', $grp).') AND (fname LIKE :n OR lname LIKE :n)',
                'params' => array(':n' => '%'.$_GET['term'].'%'),
                'order' => 'fname',
                'limit' => 20,
            ));
        }else{
            $rs = User::model()->findAll(array(
                'condition' => 'active = 1 AND type = :grp AND (fname LIKE :n OR lname LIKE :n)',
                'params' => array(':n' => '%'.$_GET['term'].'%', ':grp' => $grp),
                'order' => 'fname',
                'limit' => 20,
            ));
        }
        $a = array();

        foreach($rs as $r){
            $a[] = array(
                'value' => $r->id,
                'label' => $r->fname . ' ' . $r->lname,
            );
        }
        echo json_encode($a);
    }

}