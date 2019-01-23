<?php
/**
* 文件上传类
* @author qianxfu<qianxfu@gmail.com>
* @date 2013-07-05
*/
class Upload_File {

    var $folder = 'uploads';
    var $max_size;
    var $limit_ext;
    var $lock_name = '';

    // only read
    var $size;
    var $ext;
    var $filename;
    var $path;
    var $src;

    //private
    var $_file;
    
    var $msg = '上传失败！';   // 提示信息

    function __construct($name, $exts='') { //PHP 5
    	if(empty($_FILES[$name])) {
    	    $this->redirect('没有文件被上传。');
    	}
        $this->_file =& $_FILES[$name];
        if($this->_file['error'] > 0) {
            $this->redirect('文件上传失败！');
        }
        $this->size = $this->_file['size'];
        
        $this->max_size = $this->size_bytes(ini_get('upload_max_filesize'));
        if(!$exts) $exts = 'rar zip 7z txt';
        $this->set_ext($exts);
    }

    function Upload_File($name, $etxs='') { //PHP 4
        $this->__construct($name,$etxs);
    }

    function set_max_size($size) {
        $this->max_size = min($this->max_size, $this->size_bytes($size . 'k'));
    }

    function set_ext($exts) {
        if(!$exts) return '';
        
        $exts = explode(' ', $exts);
        foreach($exts as $k => $v) {
            if(!$v) {
                unset($exts[$k]);
            } else {
                $exts[$k] = strtolower($v);
            }
        }
        if($exts) $this->limit_ext = $exts;
    }

    function _check() {
        if(!is_uploaded_file($this->_file['tmp_name'])) {
            $this->redirect('无法确认或识别的上传文件。');
        } elseif(!$this->is_upfile($this->_file['name'])) {
            @unlink($this->_file['tmp_name']);
            $this->redirect("文件格式不正确，只允许上传以 ".implode('，', $this->limit_ext)." 为后缀的文件。");
        } elseif($this->_file['size'] > $this->max_size) {
            @unlink($this->_file['tmp_name']);
            $this->redirect(sprintf("上传的文件大小不能大于 %d %s。", floor($this->max_size/1024) ,'KB'));
        }
        return TRUE;
    }

    function upload($folder, $subdir = 'DAY', $filename = '') {
        
    	$timestamp = time();
    	
        $this->_check();
        
        $this->folder = $this->folder ? $this->folder . DS . $folder : $folder;
        $path = DATA_ROOT . $this->folder;
        if(!@is_dir($path)){
            if(!@mkdir($path, 0777)) {
                $this->redirect(sprintf("没有权限建立 %s 目录。", $this->folder));
            }
        }
			
		if($filename == '') {
			if($subdir == 'MONTH') {
				$subdir = date('Y-m', $timestamp);
			} elseif($subdir == 'DAY') {
				$subdir = date('Y', $timestamp) . DS . date('m', $timestamp) . DS . date('d', $timestamp);
			}
		}else{
			$subdir = '';
		}

		
        if($subdir) {
            $dirs = explode(DS, $subdir);
            foreach ($dirs as $val) {
                $path .= DS . $val;
                if(!@is_dir($path)) {
                    if(!@mkdir($path, 0777)) {
                        $this->redirect(sprintf("没有权限建立 %s 目录。", str_replace(DATA_ROOT, '', $path)));
                    }
                }
            }
        }

        $fileinfo = pathinfo($this->_file['name']);
        $this->ext = strtolower($fileinfo["extension"]);

        if(!$this->lock_name || $this->lock_name == "" || ! empty($this->lock_name)) {
			if($filename!=""){
				$name = $filename.".".  $this->ext;
			}
			else{
				PHP_VERSION < '4.2.0' && srand();
				$rand = rand(1, 100);
				$name = $rand . '_' . $timestamp . '.' .  $this->ext;
				unset($rand);
			}
        } else {
            $name = $this->lock_name . '.' .  $this->ext;
        }

        $sorcuefile = $path . DS . $name;

        if (@move_uploaded_file($this->_file['tmp_name'], $sorcuefile)) {
            $this->filename = $name;
            $this->path = str_replace(DATA_ROOT, '', $path);
            $this->src = str_replace(DS, '/', $this->path);
            $this->delete_tmpfile();
            return TRUE;
        } else {
            $this->redirect('文件上传失败，请检查上传权限。');
        }
    }

    function delete_tmpfile() {
        @unlink(str_replace("\\\\", "\\", $this->_file['tmp_name']));
    }

    function delete_file() {
        @unlink(DATA_ROOT . $this->path.'/'.$this->filename);
    }

    function is_upfile($filename) {
        $this->ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if(!$this->ext) return FALSE;
        return in_array($this->ext, $this->limit_ext);
    }

    // 取得字节单位
    function size_bytes($val) {
    	$val = trim($val);
    	$last = strtolower($val{strlen($val)-1});
    	switch($last) {
    		case 'g':
    			$val *= 1024;
    		case 'm':
    			$val *= 1024;
    		case 'k':
    			$val *= 1024;
    	}
    	return $val;
    }
    
	function redirect($msg)
	{
		$isAjax = Yii::app()->getRequest()->getIsAjaxRequest();
		
		if ($isAjax)
		{
			$this->msg = $msg;
			
			exit;
		}
		else 
		{
			$c = new Controller('Controller');
			$c->layout = 'main2';
			$c->redirectMsg($msg);
            
			exit;
		}
	}
}