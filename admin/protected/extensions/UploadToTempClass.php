<?php
/** 上传到临时目录的类
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-08-06
 */
class UploadToTempClass extends Upload_File {	

	// 临时上传文件的路径
	var $srcFullFile = '';
	
	
	/**
	 * 初始化 PHP5
	 * @param string  $name  表单中的名称
	 * @param string  $exts  支持的文件上传格式
	 */
	function __construct($name, $exts = '') { //PHP 5
		if (!$exts) $exts = 'txt doc docx xls xlsx ppt pptx jpg gif bmp png pdf swf zip rar';
		
		parent::__construct($name, $exts);
	}
	
	/**
	 * 初始化 PHP4
	 * @param string  $name  表单中的名称
	 * @param string  $exts  支持的文件上传格式
	 */
	function uploadToTempClass($name, $exts = '') { //PHP 4
        $this->__construct($name, $exts);
    }
     
	/**
	 * 上传文件
	 * @param string  $folder      上传的主目录
	 * @param string  $subdir      上传的子目录
	 * @param int     $FileFlag    是否需要重命名文件
	 * @param string  $prefix      文件名前缀
	 */
    function upload($folder, $subdir = 'DAY', $FileFlag = 1, $prefix = '') {
    	
    	$filenameSrc = $this->getNewFileName($prefix, $FileFlag);

    	parent::upload($folder, $subdir, $filenameSrc);
    	if (!$this->filename) return FALSE;
    	
    	// 源文件保存路径
    	$this->srcFullFile = $this->path.DS.$this->filename;
    	
    	if ($this->srcFullFile)
    		return true;
    	else
    		return false;
    }  
    
	// 生成新的文件名
	function getNewFileName($prefix = '', $FileFlag = 1)
	{
		$timestamp = time();
		$fileName = '';
		
		if ($prefix) $fileName .= $prefix . '@';
		$fileName .= date('Y', $timestamp) . "_" . date('m', $timestamp) . '_'. date('d', $timestamp);
		
		PHP_VERSION < '4.2.0' && srand();
		
		$rand = rand(1, 100);
		
		if ($FileFlag == 1)
			$fileName .= '@' . $rand . '_' . $timestamp;
		else 
		{
			$fileinfo = pathinfo($this->_file['name']);
			$fileName .= '@' . $fileinfo['filename'];
		}
		
		unset($rand);
		
		return $fileName;
	}
}