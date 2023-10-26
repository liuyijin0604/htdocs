<?php
// change the following paths if necessary
$yii='YiiFramework/yii.php';
$config=include(dirname(__FILE__).'/../protected/config/main.php');

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);

$components=array(
	'urlManager'=>array(
		'urlFormat' => 'path',
		'showScriptName' => false,
		'urlSuffix' => '.html',
		'caseSensitive' => true,
		'rules' => array(
			'/<ref:\w+>'=>'otrace/index',
			'qr/<ref:\w+>'=>'otrace/qr',
		 ),
	),
);
$config['components'] = array_merge($config['components'], $components);
$app = Yii::createWebApplication($config);
$app->defaultController ='otrace';
$app->run();