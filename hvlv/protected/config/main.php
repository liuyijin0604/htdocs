<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'HVLV App',
	'sourceLanguage'=>'en',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.modules.REST.src.SmsNotice.*',
		'application.services.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'orite',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		'pos'=>array(
			'defaultController' => 'site',
		),
		'ims'=>array(
			'defaultController' => 'site',
		),
		'opcn'=>array(
			'defaultController' => 'site',
		),
		'client'=>array(
			'defaultController' => 'site',
		),
		'wma'=> array(
			'defaultController' => 'site',
		),
		'cg' => array(
			'defaultController' => 'site',
		),
		'mqx' => array(
			'defaultController' => 'site',
		),
		'cart' => array(
			'defaultController' => 'site',
		),
		'crmmsg' => array(
			'defaultController' => 'site',
		),
		'whscan' => array(
			'defaultController' => 'site',
		),
		'pcaw' => array(
			'defaultController' => 'site',
		),
		'pack' => array(
			'defaultController' => 'site',
		),
		'imtk' => array(
			'defaultController' => 'site',
		),
		'gapsig' => array(
			'defaultController' => 'site',
		),
		'szportal' => array(
			'defaultController' => 'site',
		),
		'dplatform' => array(
			'defaultController' => 'site',
		),
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		/*
		*/
		'urlManager'=>array(
			'urlFormat'=>'path',
			'urlSuffix'=>'.app',
			'caseSensitive' => true,
			'showScriptName' => false,
			'rules'=>array(
				'dash/widget/<name>'=>'dash/widget',
				'tracking/<code>'=>'tracking/index',
				'tracking/<action>/<code>'=>'tracking/<action>',
				'site/voice/<file>.mp3'=>'site/voice',
				'filerepo/upload/<hash>'=>'filerepo/upload',
				'filerepo/delete/<id>'=>'filerepo/delete',
				'filerepo/<hash>/<file>'=>'filerepo/download',
				'cargoProcess/uploadSignatureFile/<hash>'=>'cargoProcess/uploadSignatureFile',
				'cargoProcess/uploadPOD/<hash>'=>'cargoProcess/uploadPOD',
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		'assetManager' => array(
			'newDirMode' => 0755,
			'newFileMode' => 0644,
		),
		'clientScript' => array(
			'scriptMap' => array(
				'jquery.js'=>false,
				'jquery.min.js'=>false,
				'jquery-ui.js'=>false,
				'jquery-ui.min.js'=>false,
				'jquery.yiigridview.js'=>false,
				'jquery-ui.css'=>false,
			),
		),
		/*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),
		*/
		// uncomment the following to use a MySQL database
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=hvlv_db',//54.206.212.233;dbname=hvlv_db',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '123456',
			'charset' => 'utf8',
		),
		'db_tla'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=hvlv_db',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '123456',
			'charset' => 'utf8',
			'class' => 'CDbConnection',
		),
		// 'db'=>array(
		// 	'connectionString' => 'mysql:host=localhost;dbname=hvlv_staging',
		// 	'emulatePrepare' => true,
		// 	'username' => 'root',
		// 	'password' => '123456',
		// 	'charset' => 'utf8',
		// ),
		/*'db2'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=hvlv_dev',
			'emulatePrepare' => true,
			'username' => 'orite',
			'password' => '',
			'charset' => 'utf8',
			'class' => 'CDbConnection',
		),*/
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'session' => array(
			// 'class' => 'CDbHttpSession',
			// 'connectionID' => 'db',
			// 'autoCreateSessionTable' => false,
			'class' => 'CCacheHttpSession',
			'cacheID' => 'cache',
			'timeout' => 7200,
		),
        'cache'=>array(
                'class'=>'system.caching.CDbCache',
                'connectionID'=>'db',
        ),
        // 'cache'=>array(
        //         'class'=>'CRedisCache',
        //         'hostname'=>'localhost',
        //         'port'=>6379,
        //         'database'=>0,
        //         'options'=>STREAM_CLIENT_CONNECT,
        //         'keyPrefix'=>'hvlv_',
        // ),
        'cache_java'=>array(
                'class'=>'CRedisCache',
                'hostname'=>'127.0.0.1',
                'port'=>6379,
                'database'=>0,
                'options'=>STREAM_CLIENT_CONNECT,
                'keyPrefix'=>'java:',
                'hashKey'=>false,
                'serializer'=>false
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
					'except' => 'exception.CHttpException.404',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),

		// xero for product version settings
		'xero' => array(
			'class' => 'Xero',
			'appType' => 'private',
		   // 'oAuthCallback' => 'http://localhost/xero/xero/auth', //oauth callback field - not needed for private apps
			'signatures' => [
				// pcaexpress xero api keys
				'consumer_key' => 'XAP9LTZ5R43RQQ060XYVV5NELHXNBF',
				'shared_secret' => 'YH7LTXUC1JCQTDXSYPWYRGA79I2EDJ',
				//certificates
				'rsa_private_key' => dirname(__DIR__).DIRECTORY_SEPARATOR.'data/xero_key.pem',
				'rsa_public_key'  => dirname(__DIR__).DIRECTORY_SEPARATOR.'data/xero_cert.cer'
				],
			// 'pcaex' => [
			// 	'client_id' => '373C2A278D4E4925952F32CE86496B6A',
   //  			'client_secret' => 'Phr3mmkr4niISVM8zhCs6w781TTuGSJponolVaSZn0CvQ1xl',
			// ],
			// 'toplog' => [
			// 	'client_id' => '1468D9462C26416D8167FB1D13580260',
			// 	'client_secret' => 'xJA7TTsltDwJqon3yImlAgM4BY4oy6zbTZEHtO3A78cRz-Np',
			// ],
		),

	/*
		// xero for local testing settings
		'xero' => array(
			'class' => 'Xero',
			'appType' => 'private',
			// 'oAuthCallback' => 'http://localhost/xero/xero/auth', //oauth callback field - not needed for private apps
			'signatures' => array(
				// local
				'consumer_key' => 'KSTKEREGFA8JA1OPYVZ8CSCQPQPU9Q',
				'shared_secret' => 'QJA3IH6NFFQJZRH9ASHG8TZ8YOQL7O',

				// for pcaexpress another official account
		  //      'consumer_key' => '747LUE8TIFPPAQSDDVDYEPFNUYMGBI',
			//    'shared_secret' => 'KTCHVLPQHFSAXKDFVVCEYFBYH0LPXZ',

				//certificates
				'rsa_private_key' => dirname(__DIR__).DIRECTORY_SEPARATOR.'config/xero/privatekey.pem',
				'rsa_public_key'  => dirname(__DIR__).DIRECTORY_SEPARATOR.'config/xero/publickey.cer'
			)
		), */
),


	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'gero@toplogistics.com.au',
		'maxFileSize' => '10mb',
		'langs' => array('zh'=>'中文', 'en'=> 'English'),
		'fileRepoPath'=>dirname(__DIR__).DIRECTORY_SEPARATOR.'filerepo',
		'tmp'=>dirname(__DIR__).DIRECTORY_SEPARATOR.'runtime',
		'fileCacheTTL'=>2592000,
		'ics' => include(dirname(__FILE__).DIRECTORY_SEPARATOR.'ics.php'),
		'otherICS' => include(dirname(__FILE__).DIRECTORY_SEPARATOR.'other_ics.php'),
		'settings'=>include(dirname(__FILE__).DIRECTORY_SEPARATOR.'settings.php'),
	),
);