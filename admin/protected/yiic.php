<?php

// change the following paths if necessary
$yiic=dirname(__FILE__).'/../../framework/yiic.php';
$config=dirname(__FILE__).'/config/console.php';
$lang_config=dirname(__FILE__).'/config/lang.php';
$config_settings=dirname(__FILE__).'/config/config.inc.php';

define('KFW_COMMANDS',true);

@putenv('YII_CONSOLE_COMMANDS='. dirname(__FILE__).'/commands' );

require_once($config_settings);
require_once($lang_config);
require_once($yiic);
