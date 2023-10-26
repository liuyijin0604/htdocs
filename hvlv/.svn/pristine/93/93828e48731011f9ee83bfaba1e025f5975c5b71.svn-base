<?php
class automaticCommand extends CConsoleCommand
{
    private $db;
    private $args;
    private $debug = false;
    public $ts;
    public $week_day;
    public $hr;
    public $mn;
    public $day;

    public function run($args)
    {   
        Yii::app()->name = 'TLA';
		$this->db = Yii::app()->getDb();
		$this->args = $args;
		$this->ts = time();
		$this->week_day = intval(date('N'));
		$this->hr = intval(date('G'));
		$this->mn = intval(date('i'));
		$this->day = intval(date('d'));
		Yii::app()->request->hostInfo = 'https://os.toplogistics.com.au';
        Yii::import('application.commands.*');
        if (!empty($args[0]) && method_exists($this, $args[0])) {
            $this->{$args[0]}();
            return;
        }
        //$sql = "SELECT * FROM auto_commands WHERE (`day` = " . $this->d . " OR `day` = 0) AND (`week_day` = " . $this->day . " OR `week_day` = 0) AND `hour` = " . $this->hr . " AND `minute` = " . $this->mn;
        $commands = AutoCommands::model()->findAll();
        $scheduledFunctions = [];
        if (!empty($commands)) {
            foreach ($commands as $function) {
                /* switch ($function->command_name) {
                    case 'cron':
                        $command = new CronCommand('cron', 'runner');
					    break;
                    case 'mq':
                        $command = new MQCommand('mq', 'runner');
                        break;
                    case 'az':
                        $command = new azCommand('az', 'runner');
                        break;
                }
                $commandArgs = [];
                array_push($commandArgs, $function->func_name);
                $this->log($function->command_name, $function->func_name, AutoCommandLog::TYPE_START);
                $command->run($commandArgs);
                $this->log($function->command_name, $function->func_name, AutoCommandLog::TYPE_COMPLETE); */
                if ($function->type == AutoCommands::TYPE_TIME_FIXED
                    && (empty($function->day) || $function->day == $this->day)
                    && (empty($function->week_day) || $function->week_day == $this->week_day)
                    && ($function->hour == $this->hr)
                    && ($function->minute == $this->mn)) {
                    array_push($scheduledFunctions, $function);
                }
                if ($function->type == AutoCommands::TYPE_TIME_INTERVAL
                    && ($this->hr % $function->hour_interval == 0)
                    && ($this->mn % $function->minute_interval == 0)) {
                        array_push($scheduledFunctions, $function);
                }
            }
            foreach ($scheduledFunctions as $functionToBeExecute) {
                switch ($functionToBeExecute->command_name) {
                    case 'cron':
                    case 'CRON':
                        $command = new CronCommand('cron', 'runner');
					    break;
                    case 'mq':
                    case 'MQ':
                        $command = new MQCommand('mq', 'runner');
                        break;
                    /* case 'az':
                        $command = new azCommand('az', 'runner');
                        break; */
                }
                $commandArgs = [];
                array_push($commandArgs, $functionToBeExecute->func_name);

                $funcStartLogId = $this->log($functionToBeExecute->command_name, $functionToBeExecute->func_name, AutoCommandLog::TYPE_START);
                $command->run($commandArgs);
                $this->log($functionToBeExecute->command_name, $functionToBeExecute->func_name, AutoCommandLog::TYPE_COMPLETE, $funcStartLogId);
            }
        }
    }

    private function log($command_name, $function_name, $type, $funcStartLogId = 0)
    {
        if ($type == AutoCommandLog::TYPE_START) {
            $commandLog = new AutoCommandLog();
            $commandLog->command_name = $command_name;
            $commandLog->function_name = $function_name;
            $commandLog->type = $type;
            $commandLog->time = date('Y-m-d H:i:s');
            $commandLog->save();
            return $commandLog->id;
        } else if ($type == AutoCommandLog::TYPE_COMPLETE) {
            $startLog = AutoCommandLog::model()->findByPk($funcStartLogId);
            // Update the start log to complete
            $startLog->type = $type;
            $startLog->time = date('Y-m-d H:i:s');
            $startLog->save();
        }
    }

    public function test()
    {
       Yii::app()->name = 'TLA';
        $this->db = Yii::app()->getDb();
        $this->ts = time();
        $this->week_day = intval(date('N'));
        $this->hr = intval(date('G'));
        $this->mn = intval(date('i'));
        $this->day = intval(date('d'));
        Yii::app()->request->hostInfo = 'https://os.toplogistics.com.au';
        Yii::import('application.commands.*');
        //$sql = "SELECT * FROM auto_commands WHERE (`day` = " . $this->d . " OR `day` = 0) AND (`week_day` = " . $this->day . " OR `week_day` = 0) AND `hour` = " . $this->hr . " AND `minute` = " . $this->mn;
        $commands = AutoCommands::model()->findAll('id = 133');
        $scheduledFunctions = [];
        if (!empty($commands)) {
            foreach ($commands as $function) {
                /* switch ($function->command_name) {
                    case 'cron':
                        $command = new CronCommand('cron', 'runner');
                        break;
                    case 'mq':
                        $command = new MQCommand('mq', 'runner');
                        break;
                    case 'az':
                        $command = new azCommand('az', 'runner');
                        break;
                }
                $commandArgs = [];
                array_push($commandArgs, $function->func_name);
                $this->log($function->command_name, $function->func_name, AutoCommandLog::TYPE_START);
                $command->run($commandArgs);
                $this->log($function->command_name, $function->func_name, AutoCommandLog::TYPE_COMPLETE); */
                array_push($scheduledFunctions, $function);
            }
            foreach ($scheduledFunctions as $functionToBeExecute) {
                switch ($functionToBeExecute->command_name) {
                    case 'cron':
                    case 'CRON':
                        $command = new CronCommand('cron', 'runner');
                        break;
                    case 'mq':
                    case 'MQ':
                        $command = new MQCommand('mq', 'runner');
                        break;
                    /* case 'az':
                        $command = new azCommand('az', 'runner');
                        break; */
                }
                $commandArgs = [];
                array_push($commandArgs, $functionToBeExecute->func_name);

                $funcStartLogId = $this->log($functionToBeExecute->command_name, $functionToBeExecute->func_name, AutoCommandLog::TYPE_START);
                $command->run($commandArgs);
                $this->log($functionToBeExecute->command_name, $functionToBeExecute->func_name, AutoCommandLog::TYPE_COMPLETE, $funcStartLogId);
            }
        }

    }
}
