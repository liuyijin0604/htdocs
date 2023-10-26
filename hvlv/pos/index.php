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
			'shipment/bulkPrint/<s:.+>/<fn:\w+>.pdf'=>'pos/shipment/bulkPrint',
			'shipment/print/<id:\d+>/<fn:\w+>.pdf'=>'pos/shipment/print',
			'client/connote/<id:\d+>/<fn:\w+>.pdf'=>'pos/client/connote',
			'client/<action:\w+>/<id:\d+>/<fn:\w+>.jpg'=>'pos/client/<action>',
			'qr/<action:\w+>/<hash:\w+>'=>'pos/qr/<action>',
			'<controller:\w+>/<action:\w+>/<id:\d+>'=>'pos/<controller>/<action>',
			'<controller:\w+>/<action:\w+>'=>'pos/<controller>/<action>',
		 ),
	),
	'clientScript' => array(
		'scriptMap' => array(
			'jquery.js'=>false,
			'jquery.min.js'=>false,
			'jquery-ui.js'=>false,
			'jquery-ui.min.js'=>false,
			'jquery.yiigridview.js'=>false,
			'jquery.ba-bbq.js'=>false,
			'jquery-ui.css'=>false,
		),
	),
);
$config['name'] = 'PCAE POS';
$config['components'] = array_merge($config['components'], $components);
$app = Yii::createWebApplication($config);
$app->defaultController ='pos/site';
$app->run();