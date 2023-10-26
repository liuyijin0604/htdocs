<?php

class ThreadCommand extends CConsoleCommand {

	public function run($args) {
		if(empty($args[0])) die("Usage: thread WorkerID [-d]\n");

		$worker = ThreadWorker::model()->findByPk($args[0]);
		if(empty($worker)) die("Worker record not found\n");

		$debug = false;
		foreach($args as $i=>$ag){
			if($ag == '-d') $debug = true;
		}

		$worker->run($debug);
	}
}
