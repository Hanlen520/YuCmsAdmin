<?php
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name' => 'YuCMS后台管理系统',

	'charset'=>'UTF-8',
	'preload'=>array('log'),
    'sourceLanguage'=>'zh_cn',//
    'timeZone'=> 'PRC',
    //'theme'=>'classic',
    'language'=>'zh_cn',
    'defaultController' => 'login',
    //'layout'=>'main',

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
        'application.extensions.*',
		'application.vendors.*',
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
		),*/
        'srbac' => array(  'userclass'=>'AdminUser', //可选,默认是 User  
            'userid'=>'admin_user_id', //可选,默认是 userid   
            'username'=>'admin_user_name', //可选，默认是 username   
            'delimeter'=>'  ',//模块中添加operation时，插入Srbac之后的字段   
            'debug'=>true, //可选,默认是 false，只有当debug为false时，模块才能生效  
            'pageSize'=>10, //可选，默认是 15   
            'superUser' =>'admin', //可选，建议将此名称改为超级管理员名称，有利于角色的统一  
            'css'=>'srbac.css', //可选，默认是 srbac.css  
            'layout'=>   'application.views.layouts.main', //可选,默认是   // application.views.layouts.main, 必须是一个存在的路径别名   
            'notAuthorizedView'=>'srbac.views.authitem.unauthorized', // 可选,默认是 unauthorized.php   //srbac.views.authitem.unauthorized, 必须是一个存在的路径别名  
            'alwaysAllowed'=>array(    //可选,默认是 gui  
                'LoginLogin','BorderIndex','SiteError',),   
            'userActions'=>array(//可选,默认是空数组  
                'Show','View','List'),  
                 'listBoxNumberOfLines' => 15, //可选,默认是10   
                 'imagesPath' => 'srbac.images', //可选,默认是 srbac.images  
                 'imagesPack'=>'noia', //可选,默认是 noia  
                 'iconText'=>true, //可选,默认是 false   
                 'header'=>'srbac.views.authitem.header', //可选,默认是  // srbac.views.authitem.header, 必须是一个存在的路径别名  
                 'footer'=>'srbac.views.authitem.footer', //可选,默认是  // srbac.views.authitem.footer, 必须是一个存在的路径别名  
                 'showHeader'=>true, //可选,默认是false  
                 'showFooter'=>true, //可选,默认是false   
                 'alwaysAllowedPath'=>'srbac.components', 
             ),
	),
	
	'behaviors' => array('application.behaviors.ApplicationConfigBehavior'),

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
        
        'authManager'=>array(
            'class'=>'CDbAuthManager',
            'connectionID'=>'db',
            'itemTable'=>'authitem',
            'itemChildTable'=>'authitemchild',
            'assignmentTable'=>'authassignment',
        ),
        /*
		'db2'=>array(
			'connectionString' => DBDRIVER2.':host='.DBHOST2.';dbname=' . DBNAME2,
			'emulatePrepare' => true,
			'username' => DBUSER2,
			'password' => DBPASSWORD2,
			'charset'  => CHARSET2,
			'tablePrefix' => TABLEPREX2,
		),
		*/
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
		    'timeout' => 3600,
			'sessionName' => 'cms_',
		 ),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'qianxfu@gmail.com',
	),
		
	//编辑器
	'controllerMap' => array(
			'ueditor' => array(
					'class' => 'ext.editor.UeditorController'
			),
	),
);