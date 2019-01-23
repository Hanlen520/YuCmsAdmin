<?php
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'泰策咨询有限公司',

	'charset'=>'UTF-8',
	'preload'=>array('log'),
    'sourceLanguage'=>'zh_cn',//
    'timeZone'=> 'PRC',
    //'theme'=>'classic',
    'language'=>'zh_cn',
    'defaultController' => 'index',
    //'layout'=>'main',

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
        'application.extensions.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		/*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123456',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			//'ipFilters'=>array('127.0.0.1','::1'),
			'ipFilters' => false,
		),
		*/
	),
	
	// application components
	'components'=>array(
	    'request'=>array(
                 'enableCookieValidation'=>true,
           ),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
//			'rules'=>array(
//				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
//				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
//				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
//			),
		),
        /*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),
		*/
		// uncomment the following to use a MySQL database
		'db'=>array(
			'connectionString' => DBDRIVER.':host='.DBHOST.';dbname=' . DBNAME,
			'emulatePrepare' => true,
			'username' => DBUSER,
			'password' => DBPASSWORD,
			'charset'  => CHARSET,
			'tablePrefix' => TABLEPREX,
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error',
				),
				array(
				    'class' => 'CWebLogRoute',
				    'levels' => 'profile,trace',
				),
				array(
				    'class' => 'CProfileLogRoute',
				    'levels' => 'profile',
				),				
			),
		),
		
		'session' => array(
		    'class' => 'CHttpSession',
		    'timeout' => 600,
			'sessionName' => 'app_',
		 ),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params' => array(
		// this is used in contact page
		'adminEmail' => 'yuzj1113@163.com',
	),
);