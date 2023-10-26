<?php

class TaskController extends Controller
{

	public function actionList()
	{
		$this->render('list');
	}

    /**
     * save related agent task information
     */
    public function actionAjaxSave(){

        $driver_id = $_POST['did'];
        $agent_id = $_POST['aid'];
        $from_time = $_POST['tf'];
        $to_time = $_POST['tt'];
        $repeat = $_POST['r'];
        $notes = $_POST['n'];

        // save them now
        $task = Tasks::model()->find('agent_id = :aid AND driver_id = :did ' , array(':aid' => $agent_id, ':did' => $driver_id));
        if ( isset($task) ) {
            $task->setAttributes(array(
                'pickup_time_from' => $from_time,
                'pickup_time_to' => $to_time,
                'repeat' => $repeat,
                'notes' => $notes
            ));
        } else {
            $task = new Tasks();
            $task->setAttributes(array(
                'driver_id' => $driver_id,
                'agent_id' => $agent_id,
                'pickup_time_from' => $from_time,
                'pickup_time_to' => $to_time,
                'repeat' => $repeat,
                'notes' => $notes
            ));
        }
        $task->save();

        $rt = array('success' => 1,
            'task' => array(
                'id' => $task->id,
                'agent_id' => $task->agent_id,
                'agent_name' => $task->agent->name,
                'from_time' => $task->pickup_time_from,
                'to_time' => $task->pickup_time_to,
                'notes' => $task->notes,
                'repeat' => $task->repeat
        )
        );
        echo ( json_encode( $rt ));

    }

    /**
     * remove task for one agent
     * called by ajax
     */
    public function actionAjaxRemove(){

        $driver_id = $_POST['did'];
        $agent_id = $_POST['aid'];

        // save them now
        $task = new Tasks();
        $rt = $task->find('agent_id = :aid AND driver_id = :did', array(
            ':aid' => $agent_id,
            ':did' => $driver_id
        ));
        $rt->delete();

        echo ( json_encode( array('success' => 1)));

    }

    /**
     * get task information based on driver id
     * called by Ajax api
     */
    public function actionAjaxGetTaskInfo(){
        $driver_id = $_POST['did'];

        $tasks = Tasks::model()->findAll('driver_id = :did',array(
            ':did' => $driver_id
        ));

        // convert to array
        $tasks_array = array();
        foreach ( $tasks as $task ) {
              $tasks_array[] = array(
                  'id' => $task->id,
                  'agent_id' => $task->agent_id,
                  'agent_name' => $task->agent->name,
                  'from_time' => $task->pickup_time_from,
                  'to_time' => $task->pickup_time_to,
                  'notes' => $task->notes,
                  'repeat' => $task->repeat
              );
        }

        $rt = json_encode(
            array('success' => 1,
                'tasks' => $tasks_array)
        );

        echo $rt;

    }

    // Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}