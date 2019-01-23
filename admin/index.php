<?php
// change the following paths if necessary
$yii=dirname(__FILE__).'/../framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';
$lang_config=dirname(__FILE__).'/protected/config/lang.php';
$config_settings=dirname(__FILE__).'/protected/config/config.inc.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG', false);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($config_settings);
require_once($lang_config);
require_once($yii);
Yii::createWebApplication($config)->run();

