<?php
/**
 * 函数库
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-06-24
 */
class CMyfunc
{
	/**
	 * 截取字符串
	 * @param string $string  字符串
	 * @param string $length  截取长度
	 * @param string $etc     省略号
	 * @param string $code    编码
	 */
	public static function truncate_string($string, $length = 20, $etc = '...', $code = 'UTF-8')
	{
		if ($length == 0) return '';
		if ($code == 'UTF-8') 
		{
			$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
		}
		else {
			$pa = "/[\x01-\x7f]|[\xa1-\xff][\xa1-\xff]/";
		}

		preg_match_all($pa, $string, $t_string);
		if (count($t_string[0]) > $length)
		{
			return join('', array_slice ( $t_string [0], 0, $length ) ) . $etc;
		} else {
			return $string;
		}
	}
	
	/**
	 * 验证手机号码
	 * @param string $mobile 手机号码
	 */
	public static function checkMobile($mobile)
	{
		if (!$mobile) return false;
		
		$pattern = "/^13[0-9]{1}[0-9]{8}$|14[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/";
		
		if (preg_match($pattern, $mobile))
			return true;
		else
			return false;
	}
	
	/**
	 * 验证身份证号码
	 * @param string $idcard 身份证号码
	 */
	public static function checkIdCard($idcard)
	{
		if (!$idcard) return false;
		
		// 15位
		$pattern1 = "/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/";
		
		// 18位
		$pattern2 = "/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|x|X)$/";
		
		if (preg_match($pattern1, $idcard) || preg_match($pattern2, $idcard))
			return true;
		else
			return false;
	}
	
	/**
	 * 手机号中间四位替换为*
	 * @param string $mobile 手机号码
	 */
	public static function replaceMobile($mobile)
	{
		if (!$mobile) return '';
		
		$pattern = "/(1\d{1,2})(\d{1,4})(\d{1,4})/";
		$replacement = "\$1****\$3";

		return preg_replace($pattern, $replacement, $mobile);
	}
	
	/**
	 * 创建目录
	 * @param string  $subdir 目录地址
	 */
	public static function createFolder($dir)
	{
		if(!file_exists($dir))
		{
            if(!@mkdir($dir, 0777, true)) 
            {
				return false;
            }
        }
        
    	CMyfunc::chmodr($dir, 0777);
    	
    	return true;
	}
	
	/**
	 * 修改目录权限
	 * @param string  $dir       目录
	 * @param string  $filemode  权限
	 */
	public static function chmodr($dir, $filemode)
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') return false;
		
		$arrDir	= explode(DS, $dir);

        $m = strtoupper(substr($dir, 0, 1)) === DS ? DS : '';

		$subDir	= '';

		foreach( $arrDir as $t )
		{
			if (trim($t))
			{
				$subDir	.= $m . $t;

				if(!file_exists($subDir))
				{
					if(!@mkdir($subDir, 0777, true) )
					{
						die('目录创建失败！');
					}
				}
				@chmod($subDir, $filemode);

				$m = DS;
			}			
		}
	}
	
	/**
	 * 删除文件
	 * @param string or array  $files
	 */
	public static function removeFiles($files)
	{
		if (!$files) return false;
		
		if (is_array($files))
		{
			foreach ($files as $file)
			{
				$file = DATA_ROOT . $file;
				
				if (is_file($file) && file_exists($file))
				{
					@unlink($file);
				}
			}
			
		} else {
			$files = DATA_ROOT . $files;
			
			if (is_file($files) && file_exists($files))
			{
				@unlink($files);
			}
		}
		
		return true;
	}
	
	//过滤SQL
	public static function strip_sql($string) 
	{
		$pattern_arr = array("/ union /i", "/ select /i", "/ update /i", "/ outfile /i", "/ or /i", "/'/");
		
		$replace_arr = array('&nbsp;union&nbsp;', '&nbsp;select&nbsp;', '&nbsp;update&nbsp;',
		'&nbsp;outfile&nbsp;', '&nbsp;or&nbsp;', "''");

		return preg_replace($pattern_arr, $replace_arr, $string);
	}
	
	/**
	 * 替换模板内容
	 * @param string  $condition  条件
	 * @param array   $param      替换内容
	 */
	public static function replaceTemplate($condition, $param = array()) 
	{
		if (!$condition) return '';
		
		if($param && is_array($param))
		{
			foreach ($param as $k => $v) 
			{
				$rk = $k + 1;
				$condition = str_replace('\\'.$rk, $v, $condition);
			}
		}
		
		return $condition;
	}
	
	/**
	 * 将临时文件复制到正式目录下
	 * @param string  $tempFile            临时文件
	 * @param string  $folder              上传到的目录
	 * @param int     $isDeleteTempFile    是否删除临时文件: 1 是，0 否
	 * @param array   $thumb               创建的缩略图
	 * @param int     $thumb_mod           缩略图方式：1 等比例缩放，2 按比例裁剪
	 */
	public static function copyTempFile($tempFile, $folder, $isDeleteTempFile = 1, $thumb = array(), $thumb_mod = 1)
	{
		if (!$tempFile || !$folder) return false;
		
		// 定义上传目录名(data 目录下)
		$uploadFolder = 'uploads';
		
		$filePath = DATA_ROOT.$tempFile;
		if (!file_exists($filePath)) return false;
		
		// 获取文件扩展名
		$fileinfo = pathinfo($tempFile);
        $fileExt = strtolower($fileinfo["extension"]);
		
		// 创建目录
		$timestamp = time();
		$fileFolder = $uploadFolder.DS.$folder.DS.date('Y', $timestamp).DS.date('m', $timestamp).DS.date('d', $timestamp);
		$m = CMyFunc::createFolder(DATA_ROOT . $fileFolder);
		if (!$m) return false;
		
		// 复制文件
		$srcfile = $fileFolder.DS.rand(0,1000).'_' .$timestamp.'.' . $fileExt;
		$result = copy(DATA_ROOT.$tempFile, DATA_ROOT.$srcfile);
		
		if ($result)
		{
			$file = str_replace('\\', '/', $srcfile);
			
			if ($thumb && is_array($thumb))   // 创建缩略图
			{
				$im = new Images_Class(DATA_ROOT.$srcfile);
				$im->add_thumb($thumb);         // 添加缩略图
				$im->set_thumb_mod($thumb_mod); // 设置缩略图方式
				$im->process();                 // 处理图片
				if (!$im->thumb_filenames) return false;
				
				$file = array(
					'picture'  => $file,
					'thumb'    => $im->thumb_filenames,
				);
			}
			
			// 删除临时文件
			if ($isDeleteTempFile == 1) CMyFunc::removeFiles($tempFile);

			return $file;
		} else 
			return false;
	}
	
	/**
	 * 获取不同尺寸的缩略图
	 * @param string  $srcFile   源图地址
	 * @param int     $width     缩略图的宽
	 * @param int     $height    缩略图的高
	 */
	public static function getThumbPath($srcFile, $width, $height)
	{
		if (!$srcFile || !$width || !$height) return '';
		
		// 图片信息
		$fileinfo = pathinfo($srcFile);
		$basename = $fileinfo['basename'];
		$path = str_replace('\\', '/', $fileinfo['dirname'] . DS);
		$_ext = strrchr($basename, '.');
		
		// 缩略图地址
		$_fileName = str_replace($_ext, '', $basename);
        $_sizeImg  = $_fileName . '_thumb_' . $width . '_' . $height;
        $thumbFile = $path . $_sizeImg . $_ext;
		
		if (file_exists(DATA_ROOT . $thumbFile))
			return $thumbFile;
		else
			return $srcFile;
	}
	
	/**
	 * 拼接搜索条件的URL
	 * @param string  $url     链接地址
	 * @param array   $search  搜索条件
	 */
	public static function mergeSearchUrl($url, $search = array())
	{
		if (!$url) return false;
		if (!$search || !is_array($search)) return $url;
		
		if (substr($url, -1) === '/') $url = rtrim($url, '/');
		
		foreach ($search as $key => $val)
		{
			if (is_array($val))
			{
				foreach ($val as $k => $v)
				{
					if (trim($v) !== '') 
						$url .= '/' . $key . '[]/' . urlencode(trim($v));
				}
			} else
			{
				if (trim($val) !== '') 
					$url .= '/' . $key . '/' . urlencode(trim($val));
			}
		}
		
		return $url;
	}
	
	/**
	 * 获取当前url
	 * @param string $url url地址
	 */
	public static function getCurrentUrl($url)
	{
		if ($url) return $url;
		
		$_url = array();
		
		if (isset(Yii::app()->controller->module))  // 模块
		{
			$_url[] = Yii::app()->controller->module->id;
		}
		$_url[] = Yii::app()->controller->id;             // 控制器
		$_url[] = Yii::app()->controller->action->id;     // 方法

		$url = '/' . implode('/', $_url);

		return $url;
	}
	
	/**
	 * 
	 * 生成图片的HTML标签
	 * @param string  $images   图片地址
	 * @param int     $width    图片宽度
	 * @param int     $height   图片高度
	 */
	public static function getHtmlByImages($images = '', $width = null, $height = null)
	{
		if (!$images) return '';
		
		$img = '<img src="'.$images.'"';
		if ($width) $img .= ' width="'.$width.'"';
		if ($height) $img .= ' height="'.$height.'"';
		$img .= ' />';
		
		return $img;
	}
	
	/**
	 *  使用特定function对数组中所有元素做处理
	 *  @param  string  &$array                 要处理的字符串
	 *  @param  string  $function               要执行的函数
	 *  @param  boolean $apply_to_keys_also     是否也应用到key上
	 */
	public static function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
	{
	    static $recursive_counter = 0;
	    
	    if (++$recursive_counter > 1000) die('error!');
	        
	    foreach ($array as $key => $value) 
	    {
	        if (is_array($value))
	            CMyfunc::arrayRecursive($array[$key], $function, $apply_to_keys_also);
	        else
	            $array[$key] = $function($value);
	  
	        if ($apply_to_keys_also && is_string($key)) 
	        {
	            $new_key = $function($key);
	            
	            if ($new_key != $key) 
	            {
	                $array[$new_key] = $array[$key];
	                unset($array[$key]);
	            }
	        }
	    }
	    
	    $recursive_counter--;
	}
	
	/**
	 *  将数组转换为JSON字符串（兼容中文）
	 *  @param  array   $array      要转换的数组
	 */
	public static function converJson($array) 
	{
	    CMyfunc::arrayRecursive($array, 'urlencode', true);
	    $json = CJSON::encode($array);
	    
	    return urldecode($json);
	}
}