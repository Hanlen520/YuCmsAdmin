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
	//无限分类
	public static $treeList = array();
	
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
	 * 后台分页函数
	 * @param int    $num      总记录数
	 * @param int    $perpage  每页页码
	 * @param int    $curpage  当前页码
	 * @param string $mpurl    URL地址
	 */
	public static function pagination($num, $perpage, $curpage, $mpurl)
	{
		$multipage = '';
		$mpurl .= substr($mpurl, -1)!=='/' ? '/' : '';

	    if($num > $perpage) 
	    {
        	$page = 10;
        	$offset = 5;
        	$pages = @ceil($num / $perpage);
        	if($page > $pages) {
            	$from = 1;
            	$to = $pages;
        	} else {
            	$from = $curpage - $offset;
            	$to = $curpage + $page - $offset - 1;
            	if($from < 1) {
                	$to = $curpage + 1 - $from;
                	$from = 1;
                	
                	if(($to - $from) < $page && ($to - $from) < $pages) {
                    	$to = $page;
                	}
            	} elseif($to > $pages) {
                	$from = $curpage - $pages + $to;
                	$to = $pages;
                	
                	if(($to - $from) < $page && ($to - $from) < $pages) {
                   	 $from = $pages - $page + 1;
                	}
            	}
        	}
        	
        	$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a title="首页" href="'.$mpurl.'page/1" class="p_redirect">&lt;&lt;</a>' : '').($curpage > 1 ? '<a title="上一页"  href="'.$mpurl.'page/'.($curpage - 1).'" class="p_redirect">&lt;</a>' : '');
        	
        	for($i = $from; $i <= $to; $i++) {
           		$multipage .= $i == $curpage ? '<span class="p_curpage">'.$i.'</span>' : '<a href="'.$mpurl.'page/'.$i.'" class="p_num">'.$i.'</a>';
        	}
        	
        	$multipage .= ($curpage < $pages ? '<a title="下一页"  href="'.$mpurl.'page/'.($curpage + 1).'" class="p_redirect">&gt;</a>' : '').($to < $pages ? '<a title="尾页"  href="'.$mpurl.'page/'.$pages.'"class="p_redirect">&gt;&gt;</a>' : '');
        	$multipage = $multipage ? '<div class="p_bar"><span class="p_info" title="共'.$num.'条记录">'.$num.'</span>'.$multipage.'</div>' : '';
    	}
		
		return $multipage;
	}
	
	/**
	 * 读取EXCEL数据
	 * @param string $path 文件路径
	 */
	public static function readExcel($path)
	{
		/**
		 * 关闭yii的自动装载，因为yii要求类名必须和文件名相同
		 * 而phpexcel中会把包名加上，如PHPExcel_IOFactory => IOFactory.php
		 * 不符合这一规则，如果不关闭yii的自动装载，系统将会报错
		 */
		spl_autoload_unregister(array('YiiBase','autoload'));
		
		include Yii::getPathOfAlias('ext.phpexcel.Classes'). DIRECTORY_SEPARATOR.'PHPExcel.php';
		
		$PHPExcel = new PHPExcel();
		//默认xlsx
		$PHPReader = new PHPExcel_Reader_Excel2007();
		
		if (!$PHPReader->canRead($path))
		{
		    $PHPReader = new PHPExcel_Reader_Excel5();
		    if (!$PHPReader->canRead($path))
		    {
		        echo 'no Excel';
		        return ;
		    }
		}

		$objExcel = $PHPReader->load($path);
		$sheetData = $objExcel->getSheet(0);
        //获取总行数
        $maxRows = $sheetData->getHighestRow();
        //获取总列数
        $maxColumns = $sheetData->getHighestColumn();
        
        $excelData = array();
        
        for ($i = 2; $i <= $maxRows; $i++)
        {
        	for ($j = 'A'; $j <= $maxColumns; $j++)
        	{
        		$excelData[$i][] = $sheetData->getCell($j.$i)->getFormattedValue();
        	}
        }
        
		//恢复yii的自动装载
		spl_autoload_register(array('YiiBase','autoload'));
		return $excelData;
	}
	
	/**
	 * 根据省、市、区ID获得相应的地区
	 * @param int  $province_id   省ID
	 * @param int  $city_id       市ID
	 * @param int  $zone_id       区ID
	 */
	public static function getAreaName($province_id, $city_id, $zone_id)
	{
		if (!$province_id || !$city_id || !$zone_id) return '';
		
		$areaName = '';
		
		// 获取省
		$province = Province::model()->findByPk((int) $province_id);
		$areaName .= $province ? $province->province_name : '';
		
		// 获取市
		$city = City::model()->findByPk((int) $city_id);
		$areaName .= $city ? $city->city_name : '';
		
		// 获取区
		$zone = Zone::model()->findByPk((int) $zone_id);
		$areaName .= $zone ? $zone->zone_name : '';
		
		return $areaName;
	}
	
	/**
	 * 上传缩略图
	 * @param string  $name            表单名称
	 * @param string  $folder          上传目录
	 * @param string  $subdir          上传子目录类型
	 * @param array   $mment           生成的原图大小
	 * @param boolen  $delete_source   是否删除原文件
	 * @param string  $filename        自定义上传文件名
	 */
	public static function uploadImage($name, $folder, $subdir='DAY', $mment = array(), $delete_source = false, $filename = '')
	{
		if (isset($_FILES[$name]) && $_FILES[$name] && $_FILES[$name]['error'] == 0)
		{
			$img = new Upload_Image($name);
			
			// 最大上传 KB
			$img->set_max_size(10 * 1024);
			
			// 图片质量 0-100
			$img->set_thumb_level(95);
			
			$img->upload($folder, $subdir, $mment, $delete_source, $filename);
			
			if (!$img->filename) return null;
			
			$fileinfo = pathinfo($img->filename);
			$imgFile = str_replace(DS, '/', $img->path . DS . $fileinfo['filename'] . '.' . $fileinfo['extension']);
			
			return $imgFile;
			
		} else 
			return NULL;
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
	 * 获取操作者姓名
	 * @param int  $userID  用户ID
	 */
	public static function getOperateName($userID)
	{
		if (!$userID) return '';
		
		$row = AdminUser::model()->findByPk($userID);
		if (!$row) return '';
		
		return $row['real_name'] . '[' . $row['admin_user_name'] . ']';
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
	 * 生成图片缩略图
	 * @param string  $srcFile             源文件
	 * @param array   $thumb               创建的缩略图
	 * @param int     $thumb_mod           缩略图方式：1 等比例缩放，2 按比例裁剪
	 */
	public static function createThumb($srcFile, $thumb = array(), $thumb_mod = 1)
	{
		if (!$srcFile || !$thumb || !is_array($thumb)) return false;
		
		$filePath = DATA_ROOT.$srcFile;
		if (!file_exists($filePath)) return false;
		
        // 创建缩略图
        $im = new Images_Class(DATA_ROOT.$srcFile);
		$im->add_thumb($thumb);                       // 添加缩略图
		$im->set_thumb_mod($thumb_mod);               // 设置缩略图方式
		$im->process();                               // 处理图片
		
		if (!$im->thumb_filenames) return false;
		
		return $im->thumb_filenames;
	}
	
	/**
	 * 获取不同尺寸的缩略图
	 * @param string  $srcFile   源图地址
	 * @param int     $width     缩略图的宽
	 * @param int     $height    缩略图的高
	 * @param int     $flag      如当前尺寸的图片不存在，是否使用源图：0 不使用，1 使用
	 */
	public static function getThumbPath($srcFile, $width, $height, $flag = 1)
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
		
        if ($flag === 0) return $thumbFile;
        
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
	
	// 获取MYSQL版本
	public static function getMysqlVersion()
	{
		$sqlString = "SELECT VERSION() AS version";
		$row = Yii::app()->db->createCommand($sqlString)->queryRow();
		
		return $row['version'];
	}
	
	/**
	 * 删除缓存文件
	 * @param string  $cachename  缓存名称
	 */
	public static function delete_cache_file($cachename)
	{
		if (!$cachename) return false;
		
		// 缓存目录
	    $cachedir = DATA_ROOT . 'cache' . DS;
	    
	    // 缓存文件路径
	    $filename = 'cache_' . $cachename . '.php';
	    $cachefile = $cachedir . $filename;
	    
	    if(!file_exists($cachefile)) return false;
	    @unlink($cachefile);
	    
	    return true;
	}
	
	/**
	 * 读取缓存文件
	 * @param string  $cachename  缓存名称
	 */
	public static function read_cache_file($cachename)
	{
		if (!$cachename) return false;
		
		// 缓存目录
	    $cachedir = DATA_ROOT . 'cache' . DS;
	    
	    // 缓存文件路径
	    $filename = 'cache_' . $cachename . '.php';
	    $cachefile = $cachedir . $filename;
	    
	    if(!file_exists($cachefile)) return false;
	    
	    return include $cachefile;
	}
	
	/**
	 * 写入缓存文件
	 * @param string  $cachename  缓存名称
	 * @param string  $cachedata  缓存数据
	 */
	public static function write_cache_file($cachename, $cachedata)
	{
		if (!$cachename || !$cachedata) return false;
		
		// 缓存目录
	    $cachedir = DATA_ROOT . 'cache' . DS;
	    CMyfunc::createFolder($cachedir);
	    
	    $filename = 'cache_' . $cachename . '.php';
	    $cachefile = $cachedir . $filename;
	    
	    $cachedata = is_array($cachedata) ? var_export($cachedata, true) : $cachedata;
	    
	    $fp = @fopen($cachefile, 'wb');
	    if($fp)
	    {
	        @fwrite($fp, "<?php \r\nreturn $cachedata; \r\n?>");
	        
	        @fclose($fp);
	        @chmod($cachefile, 0777);
	        
	        return true;
	        
	    } else 
	    {
	    	@fclose($fp);
	    	
	    	return false;
	    }
	}
	
	/**
	 * 设置缓存数据
	 * @param string  $key         键名
	 * @param string  $value       值
	 */
	public static function setCacheData($key, $value)
	{
		if (!$key) return false;
		
		if (CACHE_TYPE == 'File')              // 文件缓存
		{
			$r = CMyfunc::write_cache_file($key, $value);
			if (!$r) die('缓存数据生成失败！');
			
		} else if (CACHE_TYPE == 'Memcache')   // 内存缓存
		{
			// 有效时间(秒)
			$expire = 600;
		
			//Yii::app()->memcache
		}
		
		return true;
	}
	
	/**
	 * 获取缓存数据
	 * @param string  $key         键名
	 */
	public static function getCacheData($key)
	{
		if (!$key) return false;
		
		if (CACHE_TYPE == 'File')              // 文件缓存
		{
			$r = CMyfunc::read_cache_file($key);
			return $r;
			
		} else if (CACHE_TYPE == 'Memcache')   // 内存缓存
		{
			
		}
	}
	
	/**
	 * 删除缓存数据
	 * @param string  $key         键名
	 */
	public static function deleteCacheData($key)
	{
		if (!$key) return false;
		
		if (CACHE_TYPE == 'File')              // 文件缓存
		{
			$r = CMyfunc::delete_cache_file($key);
			return $r;
			
		} else if (CACHE_TYPE == 'Memcache')   // 内存缓存
		{
			
		}
	}
	
	/**
	 * 获取指定的分类数组
	 * @param array      $array    分类数组
	 * @param integer    $cat_id   分类id, 0取所有
	 * @param integer    $level    获取级数
	 * @param boolean    $is_show  是否包含隐藏分类
	 */
	public static function getCatList($array, $cat_id = 0, $level = 0, $is_show = true) 
	{
		if (!$array) return array();
		
	    $instance = array();
	    $hash = md5($cat_id . $level . $is_show);
	    
	    if (!isset($instance[$hash])) 
	    {       
	        $options = $array;
	        if ($cat_id) 
	        {
	            if (empty($options[$cat_id])) return array();
	            $pid_level = $options[$cat_id]['level'];
	
	            foreach ($options AS $key => $value) 
	            {
	                if ($key != $cat_id)
	                    unset($options[$key]);
	                else
	                    break;
	            }
	            
	            $pid_array = array();
	            foreach ($options AS $key => $value) 
	            {
	                if (($pid_level == $value['level'] && $value['cat_id'] != $cat_id) || ($pid_level > $value['level']))
	                    break;
	                else
	                    $pid_array[$key] = $value;
	            }
	            
	            $options = $pid_array;
	        }
	        
	        $children_level = 99999;  // 大于这个分类的将被删除
	        if ($is_show == false) 
	        {
	            foreach ($options as $key => $val) 
	            {
	                if ($val['level'] > $children_level)
	                    unset($options[$key]);
	                else 
	                {
	                    if (isset($val['is_show']) && $val['is_show'] == 0) 
	                    {
	                        unset($options[$key]);
	                        
	                        if ($children_level > $val['level'])   // 标记一下，这样子分类也能删除
	                            $children_level = $val['level'];
	                    } else
	                        $children_level = 99999;   // 恢复初始值
	                }
	            }
	        }
	
	        // 截取到指定的缩减级别
	        if ($level > 0) 
	        {
	            if ($cat_id == 0)
	                $end_level = $level;
	            else 
	            {
	                $first_item = reset($options);     // 获取第一个元素
	                $end_level  = $first_item['level'] + $level;
	            }
	
	            // 保留level小于end_level的部分
	            foreach ($options AS $key => $val) 
	            {
	                if ($val['level'] >= $end_level)
	                    unset($options[$key]);
	            }
	        }
	        
	        $instance[$hash] = $options;
	    }
	    
	    return $instance[$hash];
	}
	
	/**
	 * 无限级分类
	 * @param Array $data
	 * @param int $pid
	 * @param int $count
	 */
	public static function tree(&$data,$parent_id = 0,$count = 1) {
		foreach ($data as $key => $val){
			if($val['parent_id']==$parent_id){
				$val['Count'] = $count;
				self::$treeList []=$val;
				unset($data[$key]);
				self::tree($data,$val['id'],$count+1);
			}
		}
		return self::$treeList ;
	}
	
	/**
	 * [格式化图片列表数据]
	 *
	 * @return [type] [description]
	 */
	public static function imageListSerialize( $data ) {
	
		foreach ( (array)$data['file'] as $key => $row ) {
			if ( $row ) {
				$var[$key]['fileId'] = $data['fileId'][$key];
				$var[$key]['file'] = $row;
			}
	
		}
		return array( 'data'=>$var, 'dataSerialize'=>empty( $var )? '': serialize( $var ) );
	
	}
}