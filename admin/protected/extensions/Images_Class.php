<?php
/**
 * 图片处理类（生成缩略图、水印）
* @author qianxfu<qianxfu@gmail.com>
* @date 2013-09-13
*/
class Images_Class
{
	public $imagePath              = '';       // 图片绝对地址
	public $thumb_mod              = 1;        // 缩略图方式，1 等比例缩放，2 按比例裁剪
	public $useWatermark           = false;    // 图片水印
    public $watermark_postion      = 0;        // 水印位置，默认为右下角，1：左上角，2：右上角，3：左下角，4右下角，5，剧中
    public $watermark_transparence = 50;       // 透明度 :未实现
    public $watermark_text         = 'www.kfw001.com';   // 文字水印
    public $thumbs                 = array();  // 需要缩略图的规格列表

    public $thumb_level            = 95;       // 图片质量 0-100
    public $width                  = 0;
    public $height                 = 0;
    public $type                   = 0;
    public $attr                   = '';
    public $thumb_filenames        = array();
    public $filename;
    public $path;
    public $file_ext;
    public $newfile;                          // 新的原始图
    
    
	public function __construct($imagePath)
	{
		if (!$imagePath) return false;
		
		$this->imagePath = $imagePath;
	}
	
	public function Image_Class($imagePath)
	{
		$this->__construct($imagePath);
	}
	
	/**
	 * 是否设置水印
	 * @param boolean $isUse  true or false
	 */
	public function set_use_watermark($isUse) 
	{
        $this->useWatermark = $isUse;
    }
    
	/**
	 * 设置文字水印
	 * @param string  $text  水印文字
	 */
    public function set_watermark_text($text) 
    {
        if(!$text) return false;
        
        $this->watermark_text = $text;
    }
    
    /**
     * 创建缩略图
     * @param array $thumb  缩略图
     */
	public function add_thumb($thumb = array()) 
	{
		if (!$thumb || !is_array($thumb)) return false;
		
        $this->thumbs = $thumb;
    }
    
    /**
     * 设置图片质量
     * @param int  $level  图片质量(0-100)
     */
	public function set_thumb_level($level) 
	{
        if($level > 0 && $level <= 100)
            $this->thumb_level = $level;
    }
    
	/**
     * 设置缩略图方式
     * @param int  $thumb_mod  缩略图方式：1 等比例缩放，2 按比例裁剪
     */
	public function set_thumb_mod($thumb_mod) 
	{
        if($thumb_mod == 1 || $thumb_mod == 2)
            $this->thumb_mod = $thumb_mod;
    }
    
    /**
     * 图片处理
     * @param array  $mment  原图新尺寸
     */
    public function process($mment = array()) 
    {
        if(function_exists('getimagesize') && !@getimagesize($this->imagePath)) return false;

        $thumbfunc = $this->thumb_mod == 1 ? 'create_thumb2' : 'create_thumb';

        $sorcuefile = $this->imagePath;
        list($this->width, $this->height, $this->type, $this->attr) = @getimagesize($sorcuefile);
        
		$fileinfo = pathinfo(str_replace(DATA_ROOT, '', $sorcuefile));
		$this->filename = $fileinfo['basename'];
		$this->path = str_replace('\\', '/', $fileinfo['dirname']);
		
        $_ext = strrchr($this->filename, '.');
        $this->file_ext = ltrim($_ext, '.');
        
        if($this->thumbs) foreach($this->thumbs as $key => $val) 
        {
        	$_fileName = str_replace($_ext, '', $this->filename);
        	$_sizeImg  = $_fileName . '_thumb_' . $val['width'] . '_' . $val['height'];
        	
            $savefile = DATA_ROOT . $this->path . DS . $_sizeImg . $_ext;
            $this->$thumbfunc($sorcuefile, $savefile, $val['width'], $val['height'], $this->thumb_level);

            $thumbFile = $this->path . DS . $_sizeImg . $_ext;
            $thumbFile = str_replace('\\', '/', $thumbFile);
            $this->thumb_filenames[$key]['filename'] = $thumbFile;
            list($this->thumb_filenames[$key]['width'], $this->thumb_filenames[$key]['height'], $this->thumb_filenames[$key]['type'], $this->thumb_filenames[$key]['attr']) = @getimagesize($savefile);
        }
        
    	// 原图新尺寸
        if($mment)
        {
            $res = $this->$thumbfunc($sorcuefile, $sorcuefile, $mment['width'], $mment['height'], $this->thumb_level);
            if ($res !== false)
            {
	            $this->getImagesSize($sorcuefile);   // 新图片的字节大小
	            $this->newfile = str_replace(DATA_ROOT, '', $sorcuefile);
            }
        }
        
        // 增加图片水印
        if(($this->useWatermark) && is_file($w = DATA_ROOT . 'watermark.png'))
            $this->watermark($sorcuefile, $sorcuefile, $w, $this->thumb_level);
        
        return true;
    }

    /**
     * 图片加水印
     * @param string  $srcimg     源图片
     * @param string  $destimg    目标图片
     * @param string  $waterimg   水印图片
     * @param int     $level      图片质量
     */
    public function watermark($srcimg, $destimg, $waterimg, $level = 80) 
    {
        if($this->is_anim($srcimg)) return; // 动画图片不加水印
        
        $simg = $this->imagecreatefromimg($srcimg);
        if(!$simg) return;
        
        $path_parts = pathinfo($srcimg);
        $ext_name = strtolower($path_parts['extension']);
        $sw = imagesx($simg);                           // 目标图片宽
        $sh = imagesy($simg);                           // 目标图片高
        imagealphablending($simg, true);                // 设定混合模式
        
        $wimg = $this->imagecreatefromimg($waterimg);   // 读取水印文件
        if(!$wimg) return;
        
        $ww = imagesx($wimg);    // 水印图片宽
        $wh = imagesy($wimg);    // 水印图片高

        $postion = $this->watermark_postion ? $this->watermark_postion : mt_rand(1, 5);
        
        if($postion==1)          // 右上
        {
            $sx = 5; 
            $sy = 10;
        } elseif($postion==2)    // 左下
        {
            $sx = $sw - $ww - 5; 
            $sy = 10;
        } elseif($postion==3)    // 右下
        {
            $sx = 5; 
            $sy = $sh - $wh - 10;
        } elseif($postion== 4)   // 右下角
        {
            $sx = $sw - $ww - 5; 
            $sy = $sh - $wh - 10;
        } elseif($postion==5)    // 居中
        {
            $sx = round($sw/2 - $ww/2); 
            $sy = round($sh/2 - $wh/2);
        } elseif($postion==6)    // 底部文字
        { 
            $fontfiles = DATA_ROOT . 'simsun.ttc';
            
            if(is_file($fontfiles) && $this->watermark_text)
            {
                $fontinfo = imagettfbbox(10, 0, $fontfiles, $this->watermark_text);
                $font_width = max($fontinfo[2],$fontinfo[4]);
                
                if($font_width < $sw) 
                {
                    $new_img = ImageCreateTrueColor ($sw , $sh + 20);
                    imagecopy($new_img, $simg, 0, 0, 0, 0, $sw, $sh);
                    
                    //if($ext_name == 'gif') {
                    //    $color = imagecolorallocate($new_img, 0, 0, 0);
                   // } else {
                        $color = imagecolorallocate($new_img, 255, 255, 255);
                    
                    imagettftext($new_img, 10, 0, 1, $sh + abs($fontinfo[7])+2, $color, $fontfiles, $this->watermark_text);
                    $fun = $this->imagecreatefun($ext_name);
                    
                    if($fun == 'imagejpeg') 
                    {
                        $fun($new_img, $destimg, $level);
                    } else {
                        if($fun == 'imagegif') 
                        {
                            //$bgcolor = ImageColorAllocate($new_img ,0, 0, 0);
                            //$bgcolor = ImageColorTransparent($new_img, $bgcolor);
                        }
                        $result = $fun($new_img, $destimg);
                    }
                    
                    ImageDestroy($simg);
                    ImageDestroy($new_img);
                    
                    return $result;
                } else
                    $mark_invalid = true;
            } else
                $mark_invalid = true;
        }

        if(!$mark_invalid)
            imagecopy($simg, $wimg, $sx, $sy, 0, 0, $ww, $wh);
        
        $fun = $this->imagecreatefun($ext_name);
        
		if($fun=='imagejpeg')
			$fun($simg, $destimg, $level);
		else 
		{
			if($fun=='imagegif') 
			{
				$bgcolor = ImageColorAllocate($new_img ,0, 0, 0);
				$bgcolor = ImageColorTransparent($new_img, $bgcolor);
			}
			
			$fun($simg, $destimg);
		}
		
        ImageDestroy($simg);
        ImageDestroy($wimg);
        
        return $result;
    }

    /**
     * 按比例裁剪图片
     * @param string  $source_img_file    源图片
     * @param string  $dest_img_file      目标图片
     * @param int     $new_width          新的图片宽度
     * @param int     $new_height         新的图片高度
     * @param int     $level              新的图片质量
     */
    public function create_thumb($source_img_file, $dest_img_file, $new_width, $new_height, $level = 80) 
    {
        $path_parts = pathinfo($source_img_file);
        $ext_name = strtolower($path_parts['extension']);
        
        $img = null;
        $img = $this->imagecreatefromimg($source_img_file);
        $src_x = $src_y = 0;
        
        if ($img) 
        {
            $imgcreate_fun = function_exists('ImageCreateTrueColor') ? 'ImageCreateTrueColor' : 'ImageCreate';
            $source_img_width = imagesx ($img);
            $source_img_height = imagesy ($img);
            
            if($source_img_width < $new_width || $source_img_height < $new_height) 
            {
                $new_img_width = min($new_width,$source_img_width);
                $new_img_height = min($new_height,$source_img_height);
                $src_x = $source_img_width > $new_width ? (int) (($source_img_width - $new_width) / 2) : 0;
                $src_y = $source_img_height > $new_height ? (int) (($source_img_height - $new_height) / 2) : 0;
                
                $new_img = $imgcreate_fun ($new_img_width , $new_img_height);
                imagecopyresized($new_img ,$img, 0 ,0 ,$src_x ,$src_y ,$new_img_width, $new_img_height, $new_img_width , $new_img_height);
            } else 
            {
                $w = $source_img_width / $new_width;
                
                if($source_img_height/$w >= $new_height) 
                {
                    $new_img_width = $new_width;
                    $new_img_height = (int)($source_img_height / $source_img_width * $new_width);
                } else 
                {
                    $new_img_height = $new_height;
                    $new_img_width = (int)($source_img_width / $source_img_height * $new_height);
                }
                
                $new_img = $imgcreate_fun ($new_width , $new_height);
                $new_img2 = $imgcreate_fun ($new_img_width , $new_img_height);
                ImageCopyResampled($new_img2, $img, 0, 0, 0, 0, $new_img_width, $new_img_height, $source_img_width, $source_img_height);
                
                $src_x = $src_y = 0;
                $src_x = $new_img_width > $new_width ? (int) (($new_img_width - $new_width) / 2) : 0;
                $src_y = $new_img_height > $new_height ? (int) (($new_img_height - $new_height) / 2) : 0;
                imagecopyresized($new_img ,$new_img2, 0 ,0 ,$src_x ,$src_y , $new_width, $new_height , $new_width , $new_height);
                
                imagedestroy($new_img2);
            }
            
            $fun = $this->imagecreatefun($ext_name);
            
            if($fun=='imagejpeg')
                $fun($new_img, $dest_img_file, $level);
            else 
            {
                if($fun=='imagegif') 
                {
                    $bgcolor = ImageColorAllocate($new_img ,0, 0, 0);
                    $bgcolor = ImageColorTransparent($new_img, $bgcolor);
                }
                
                $fun($new_img, $dest_img_file);
            }
            
            imagedestroy($img);
            imagedestroy($new_img);
            
        } else 
        	return false;
    }

    /**
     * 等比例缩放
     * 按比例，优先使用 new_width 生成，
     * new_height 在 $source_img_width > $source_img_height 的情况下 使用
     * @param string  $source_img_file  源图片
     * @param string  $dest_img_file    目标图片
     * @param int     $new_width        新的图片宽度
     * @param int     $new_height       新的图片高度
     * @param int     $level            新的图片质量
     */
    public function create_thumb2($source_img_file, $dest_img_file, $new_width, $new_height, $level = 80) 
    {
        $path_parts = pathinfo($source_img_file);
        $ext_name = strtolower($path_parts['extension']);
        
        $img = null;
        $img = $this->imagecreatefromimg($source_img_file);
        
        if ($img) 
        {
            $source_img_width = imagesx ($img);
            $source_img_height = imagesy ($img);
            
            if ($source_img_width > $source_img_height) 
            {
                $new_img_width = $new_width;
                $new_img_height = (int)($source_img_height / $source_img_width * $new_width);
            } else 
            {
                $new_img_height = $new_height;
                $new_img_width = (int)($source_img_width / $source_img_height * $new_height);
            }
            
            $imgcreate_fun = function_exists('ImageCreateTrueColor') ? 'ImageCreateTrueColor' : 'ImageCreate';
            $new_img = $imgcreate_fun($new_img_width, $new_img_height);
            ImageCopyResampled($new_img, $img, 0, 0, 0, 0, $new_img_width, $new_img_height, $source_img_width, $source_img_height);
            
            $fun = $this->imagecreatefun($ext_name);
            
            if($fun=='imagejpeg')
                $fun($new_img, $dest_img_file, $level);
            else 
            {
                if($fun=='imagegif') 
                {
                    $bgcolor = ImageColorAllocate($new_img ,0, 0, 0);
                    $bgcolor = ImageColorTransparent($new_img, $bgcolor);
                }
                
                $fun($new_img, $dest_img_file);
            }
            
            imagedestroy($img);
            imagedestroy($new_img);
        } else 
	        return false;
    }

    // 返回有效的img资源句柄
    public function imagecreatefromimg($img) 
    {
        $ext_name = strtolower(pathinfo($img, PATHINFO_EXTENSION));
        
        switch ($ext_name) 
        {
            case 'gif':
                return function_exists('imagecreatefromgif') ? @imagecreatefromgif($img) : false;
                break;
            case 'jpg':
            case 'jpe':
            case 'jpeg':
            	ini_set('gd.jpeg_ignore_warning', 1);
                return function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($img) : false;
                break;
            case 'png':
                return function_exists('imagecreatefrompng') ? @imagecreatefrompng($img) : false;
                break;
            default:
                return false;
        }
    }
    
    /**
     * 获取图片大小
     * @param string  $imagesFile  图片路径
     */
    public function getImagesSize($imagesFile)
    {
    	if (file_exists($imagesFile))
    	{
    		clearstatcache();
			$this->size = filesize($imagesFile);
    	}
    	
    	return true;
    }
    
    // 返回创建图片的函数
    public function imagecreatefun($ext) 
    {
        switch ($ext) 
        {
	        case 'gif':
	            return 'imagegif';
	        case 'png':
	            return 'imagepng';
	        default:
	            return 'imagejpeg';
        }
    }

    // 是否动态GIF
    public function is_anim($srcfile) 
    {
        $fp = fopen($srcfile, 'rb');
        $filecontent = fread($fp, filesize($srcfile));
        fclose($fp);
        
        return strpos($filecontent, 'NETSCAPE2.0');
    }
}