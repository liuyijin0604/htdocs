<?php
// change the following paths if necessary
$yii='YiiFramework/yii.php';
$config=dirname(__FILE__).'/../protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);

$app = Yii::createWebApplication($config);
$app->defaultController ='dplatform/site';
$components=array(
	'urlManager'=>array(
		'urlFormat' => 'path',
		'showScriptName' => false,
		'urlSuffix' => '.app',
		'caseSensitive' => true,
		'rules' => array(
			'shipment/bulkPrint/<s:.+>/<fn:\w+>.pdf'=>'dplatform/shipment/bulkPrint',
			'shipment/print/<id:\d+>/<fn:\w+>.pdf'=>'dplatform/shipment/print',
			'client/connote/<id:\d+>/<fn:\w+>.pdf'=>'dplatform/client/connote',
			'client/<action:\w+>/<id:\d+>/<fn:\w+>.jpg'=>'dplatform/client/<action>',
			'qr/<action:\w+>/<hash>'=>'dplatform/qr/<action>',
			'<controller:\w+>/<action:\w+>/<id:\d+>'=>'dplatform/<controller>/<action>',
			'<controller:\w+>/<action:\w+>'=>'dplatform/<controller>/<action>',
		 ),
	),
	'clientScript' => array(
		'scriptMap' => array(
			'jquery.js'=>false,
			'jquery.min.js'=>false,
			'jquery-ui.js'=>false,
			'jquery-ui.min.js'=>false,
			'jquery.ba-bbq.js'=>false,
			'jquery-ui.css'=>false,
		),
	),
);
$app->setComponents($components);
$app->run();
