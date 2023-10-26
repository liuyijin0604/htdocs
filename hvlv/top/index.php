<?php
// change the following paths if necessary
$yii='YiiFramework/yii.php';
$config=include(dirname(__FILE__).'/../protected/config/top.php');

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);

$components=[];
$config['components'] = array_merge($config['components'], $components);
$app = Yii::createWebApplication($config);
//$app->user->setStateKeyPrefix('2662822f01376a9b00b645e609130d43');
//$app->defaultController ='site';
$app->user->getStateKeyPrefix();
$app->name = 'TLA';
$app->run();
?>