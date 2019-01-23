<?php
/**
 * Ajax控制器
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class AjaxController extends Controller
{
	public function beforeAction($action)
	{
		return true;
	}
	
	public function actionIndex()
	{
		die(1);
	}
	
	// 获取相关数据
	public function actionGetBaseData()
	{
		$typeList = array(
			'country',     // 国家
			'province',    // 省份
			'city',        // 城市
			'zone',		   // 区
			'menu',        // 菜单
			'operate',     // 权限操作
		);
		
		$type = isset($_GET['type']) ? trim($_GET['type']) : '';
		$type = in_array($type, $typeList) ? $type : '';
		$val = isset($_GET['val']) ? (int) $_GET['val'] : 0;
		// 当前值
		$currVal = isset($_GET['currVal']) ? trim($_GET['currVal']) : 0;
		
		switch ($type)
		{
			case 'country':   // 国家数据
				$data = Country::model()->getCountry();
				break;
				
			case 'province':   // 省份数据
				$data = Province::model()->getProvince($val);
				break;
				
			case 'city':       // 城市数据
				$data = City::model()->getCity($val);
				break;
				
			case 'zone':       // 区数据
				$data = Zone::model()->getZone($val);
				break;
				
			case 'menu':      // 上级菜单
				$data = AdminMenu::model()->getMenuOptions($val);
				break;
				
			case 'operate':      // 权限操作数据
				$data = Authitem::model()->getOperateOptions($val);
				break;
		}
		
		$label = $type == 'menu' ? '顶级' : ($type == 'operate' ? '无' : '请选择');
		
		$str = '<option value="0">--'.$label.'--</option>';
		
		if ($data)
		{
			foreach ($data as $k => $v)
			{
				$_select = $currVal == $k ? ' selected' : '';
				$str .= '<option value="'.$k.'"'.$_select.'>'.$v.'</option>';
			}
		}
		
		echo $str;
		exit;
	}
	
	// 通过SWFUPLOAD上传文件到临时目录
	public function actionUploadToTemp()
	{
		$name = isset($_POST['name']) && trim($_POST['name']) ? trim($_POST['name']) : 'file';
		$prefix = isset($_POST['folder']) && trim($_POST['folder']) ? trim($_POST['folder']) : '';
		$exts = isset($_POST['exts']) && trim($_POST['exts']) ? trim($_POST['exts']) : '';
		$fileFlag = isset($_POST['fileFlag']) ? (int) $_POST['fileFlag'] : 1;
		$isProcess = isset($_POST['isProcess']) ? (int) $_POST['isProcess'] : 1;   // 是否不处理图片文件
		
		$attachmentFile = array(
		    'result'  => false,
		    'msg'     => '上传失败！',
		);
		
		// 图片扩展名
		$attachmentExt = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
		
		if ($prefix && $_FILES[$name] && $_FILES[$name]['error'] == 0)
		{
			$attachment = new UploadToTempClass($name, $exts);
			$attachment->set_max_size(1024*10);                   // 最大上传20M
			$attachment->upload('temp', '', $fileFlag, $prefix);
			
			if ($attachment->srcFullFile) 
			{		
				$fileExt = strtolower($attachment->ext);
				if (in_array($fileExt, $attachmentExt) && $isProcess == 1)     // 如为图片的则需处理
				{
					// 生成新的图片
					$mment = array(
						'width'  => 1540,
						'height' => 360,
					);
					
			        $im = new Images_Class(DATA_ROOT.$attachment->srcFullFile);
					$im->set_thumb_mod(1);               // 设置缩略图方式
					$r = $im->process($mment);           // 处理图片
					
					if ($r === true)
					{
						if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
							$attachmentFile['srcFile'] = $im->newfile;
						else
							$attachmentFile['srcFile'] = str_replace('\\', '/', $im->newfile);
						
						$attachmentFile['fileType'] = $fileExt;
						$attachmentFile['fileSize'] = $im->size;
					}
					
				} else      // 非图片的不处理
				{
					if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
						$attachmentFile['srcFile'] = $attachment->srcFullFile;
					else
						$attachmentFile['srcFile'] = str_replace('\\', '/', $attachment->srcFullFile);
						
					$attachmentFile['fileType'] = $fileExt;
					$attachmentFile['fileSize'] = $attachment->size;
				}
				
				$attachmentFile['result'] = true;
				$attachmentFile['msg'] = '';
			} else {
				$attachmentFile['result'] = false;
				$attachmentFile['msg'] = $attachment->msg;
			}
		}
		
		echo CJSON::encode($attachmentFile);
		exit;
	}
}