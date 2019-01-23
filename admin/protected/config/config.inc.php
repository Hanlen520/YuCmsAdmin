<?php
/**
 * 配置信息
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-06-18
 */

define('DS', DIRECTORY_SEPARATOR);

// 数据库配置
define('DBDRIVER', 'mysql');          // 数据库驱动
define('DBHOST', '127.0.0.1');    // 数据库IP
define('DBNAME', 'yucms');              // 数据库名称
define('DBUSER', 'root');             // 数据库用户名
define('DBPASSWORD','123456');       // 数据库密码
define('CHARSET', 'utf8');            // 数据库编码
define('TABLEPREX', 't_');            // 表前缀

// 域名相关
define('SITE_DOMAIN', 'http://www.yucms.com');
define('SITE_URL', SITE_DOMAIN.'/admin/');
define('IMAGE_URL', SITE_DOMAIN.'/data/');

// 站点目录
define('WEB_ROOT', substr(dirname(__FILE__), 0, -16));

// 缓存类型: File 文件缓存, Memcache 内存缓存
define('CACHE_TYPE', 'File');

// 图片/文件目录
define('DATA_ROOT', WEB_ROOT . '../data' . DS);

$_config = array(
	// 性别
	'gender'  => array(
		1  => '男',
		2  => '女',
	),

	// 用户状态
	'user_status' => array(
		0  => '已停用',
		1  => '已启用',
		2  => '已锁定'
	),
	
	// 用户详情状态
	'userinfo_status' => array(
		1  => '在职',
		2  => '离职'
	),
	
	// 普通状态
	'common_status' => array(
	    0  => '无效',
	    1  => '有效',
	),
	
	// 是/否
	'yes_no' => array(
	    0  => '否',
	    1  => '是',
	),
	
	// 审核状态
	'check_status' => array(
	    0  => '未审核',
	    1  => '审核通过',
	    2  => '审核不通过',
	),
);