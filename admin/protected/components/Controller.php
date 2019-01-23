<?php
/**
 * 基础控制器
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-06-18
 */
class Controller extends CController
{
	public $layout='//layouts/column2';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	
	public $_config;
	
	public $subMenu = array();
	
	public $moduleList;
	
	function init()
	{
		global $_config;
		
		// 获得配置文件
		$this->_config = $_config;
		
		Yii::app()->clientScript->registerCoreScript('jquery');
	}

	function beforeAction($action)
    {    
        // 用户未登录 则跳转
        if(Yii::app()->user->isGuest)
        {
           $this->redirect(array('login/login'));
        }
        
        // 获取当前的模块、控制器、方法
		 $moduleName     = $this->getModule() ? $this->getModule()->getId() : '';
		 $controllerName = ucfirst(strtolower($this->getId()));
		 $actionName     = ucfirst(strtolower($this->getAction()->getId()));
		
		// 检测权限
		// $isPurview = $this->checkAccess($moduleName, $controllerName, $actionName);
		// if (!$isPurview) $this->redirectMsg('对不起，您没有该操作权限！', '', 1, false);
		
		// 获取有权限的模块
        // $this->moduleList = AdminModule::model()->getModuleName();
        
        // 获取面包屑
        // $this->breadcrumbs = $this->getBreadcrumbs($moduleName, $controllerName, $actionName);
      
        return true;
    }
    
    /**
     * 检测权限
     * @param string  $moduleName      模块
     * @param string  $controllerName  控制器
     * @param string  $actionName      方法
     */
    public function checkAccess($moduleName, $controllerName, $actionName)
    {
    	// 当前用户如为超级管理员则无需验证
    	$is_founder = Yii::app()->user->getState('SYS');
    	if ($is_founder == 1) return true;
    	
    	$_module = $moduleName ? $moduleName . '-' : '';
    	$power = $_module . $controllerName .$actionName;
    	
        // 不需要权限控制的模块+控制器
        $controllersArr = array(
        	'Login', 'Site', 'Ajax', 'Border'
        );
        
        // 不需要权限控制的方法
        $actionsArr = array(
       	);
      
       	//$c = $_module . $controllerName;
       	$c = $controllerName;
        if(!in_array($c, $controllersArr) && !Yii::app()->user->checkAccess($power))
        {
	    	return false;
        }
        
        return true;
    }
    
    /**
     * 获取面包屑
     * @param string  $moduleName      模块
     * @param string  $controllerName  控制器
     * @param string  $actionName      方法
     */
    public function getBreadcrumbs($moduleName, $controllerName, $actionName)
    {
    	if (!$moduleName && !$controllerName && !$actionName) return array();

    	$links = array();

    	$_module = $moduleName ? $moduleName . '-' : '';
    	$power = $_module . $controllerName .$actionName;

    	// 获取模块
    	if ($moduleName)
    	{
    		$moduleInfo = AdminModule::model()->find('module_code=:module_code', array(':module_code' => $moduleName));
    		if ($moduleInfo) $links[] = $moduleInfo->module_name;
    	} else
    	{
    		$_moduleName= Authitem::model()->getModuleByItem($power);
    		if ($_moduleName) $links[] = $_moduleName;
    	}

    	// 获取具体操作
    	$m = Authitem::model()->getBreadcrumbs($moduleName, $controllerName, $power);
    	if ($m) $links = array_merge($links, $m);

    	return $links;
    }
	
	// 页面跳转
	public function redirectMsg($message, $url = "", $delay = 3, $isrefresh = true)
	{
		$this->layout = false;
		
		$isAjax = Yii::app()->getRequest()->getIsAjaxRequest();
		
		if($isAjax)
		{
			if (!is_array($message))
			{
				$message = array(
					'status'  => 0,
					'msg'     => $message,
				);
			}
			
			echo CJSON::encode($message);
			exit;
		}
		
		if (is_array($message))
		{
			$message = isset($message['msg']) && $message['msg'] ? $message['msg'] : '操作失败！';
		}

		if(is_array($url))
		{
			$route = isset($url[0]) ? $url[0] : '';
			$url = $this->createUrl($route, array_splice($url,1));
		}

		Yii::app()->clientScript->registerMetaTag('no-cache', null, 'Pragma');
		
		$url = $url ? $url : Yii::app()->request->getUrlReferrer();
		$url = str_replace(SITE_URL, '', $url);
		$url = SITE_URL .  str_replace('/admin', '', $url);
		$returnUrl = isset($_REQUEST['returnUrl']) && trim($_REQUEST['returnUrl']) ? trim($_REQUEST['returnUrl']) : '';
		$returnUrl = str_replace(SITE_URL, '', $returnUrl);
		$returnUrl = SITE_URL . str_replace('/admin', '', $returnUrl);

		if ($url)
		{
			$meta = "$delay;url=$url";
			//$meta .= $target ? ";target=$target" : '';
			//Yii::app()->clientScript->registerMetaTag($meta, null, 'refresh');
		}

		$this->render('//message', array(
		    'message' => $message,
		    'url'     => $url,
		    'delay'   => $delay,
			'returnUrl'  => $returnUrl,
            'refresh'  =>$isrefresh,
		));

		die();
	}

	// 获取错误信息
	public function getError($data)
	{
		if (!is_array($data)) 
			$error = $this->getErrorValue($data);
		else {
			foreach ($data as $v)
			{
				$error = $this->getErrorValue($v);
				
				if ($error) break;
			}
		}
		
		$error = $error ? $error : '未知错误！';

		return '<font color=red>' . $error . '</font>';
    }
    
    
    // 获取错误值
    public function getErrorValue($model)
    {
        if(is_null($model)) return '';
        
         $errors = $model->getErrors();
         if (!$errors) return '';

         foreach($errors as $key => $value)
         {
         	if(is_array($value))
         	{
         		foreach($value as $k => $v)
         		{
         			$_err = $v;
         			break 2;
         		}
         	}
         	else
         	{
         		$_err = $value;
         		break;
         	}
         }
         
         return $_err;
    }
    
    /**
     * 获取操作日志
     * @param array $data
     */
    public function getOperateLog($data)
    {
    	if (!$data || !is_array($data)) return '';
    	
    	$LogArr = array();    	
    	foreach ($data as $m)
    	{
    		$model   = isset($m[0]) ? $m[0] : null;    // 模型
    		$oldData = isset($m[1]) ? $m[1] : null;    // 旧的数据
    		$newData = isset($m[2]) ? $m[2] : null;    // 新的数据
    		
    		if (!$model || (!$oldData && !$newData)) continue;
    		
    		// 转换批量的数组数据：如需转换，则在相应的模型中加入 converOperateLog 方法
	    	if (method_exists($model, 'converOperateLog'))
	    	{
	    		$converArr = $model->converOperateLog($oldData, $newData);
	    		if (!isset($converArr[0]) || !isset($converArr[1]) || !$converArr[0] || !$converArr[1]) continue;
	    		
	    		$oldData = $converArr[0];
	    		$newData = $converArr[1];
	    	}

    		// 验证不同值
	    	$diffArr = array_diff_assoc($oldData, $newData);
	    	if (!$diffArr) continue;
	    	
	    	// 过滤更改操作项：如需过滤，则在相应的模型中加入 filterOperateLog 方法
	    	if (method_exists($model, 'filterOperateLog'))
	    	{
	    		$diffArr = $model->filterOperateLog($diffArr);
	    	}
	    	
	    	foreach ($diffArr as $key => $val)
	    	{
	    		// 获得标签名称
	    		$Name = $model->getAttributeLabel($key);
	    		$oldValue = $val;
	    		$newValue = isset($newData[$key]) ? $newData[$key] : '';
	    		
	    		// 设置更改操作项：如需设置，则在相应的模型中加入 setOperateValue 方法
	    		if (method_exists($model, 'setOperateValue'))
	    		{
	    			$oldValue = $model->setOperateValue($key, $oldValue);
	    			$newValue = $model->setOperateValue($key, $newValue);
	    		}
	    		
	    		$LogArr[] = $Name . ' 由 [' . $oldValue . '] 修改为 [' . $newValue . ']'; 
	    	}
    	}
    	
    	return $LogArr ? implode('、', $LogArr) : '未更改任何值';
    }
	
	// 数组显示
    function dump($var)
    {
    	echo "<div style=\"border:1px solid #ddd;background:#F7F7F7;padding:5px 10px;\">\r\n";
    	echo "<pre style=\"font-family:Arial,Vrinda;font-size:14px;\">\r\n";
    	print_r($var);
    	echo "\r\n</pre>\r\n";
    	echo "</div>";
    	exit;
    }

    /**
     * 输出AJAX提示信息
     * @param string  $msg     提示信息
     * @param int     $status  状态
     * @param array   $data    其他数据
     */
    public function ajaxMsg($msg, $status = 1, $data = array())
    {
        $callback = Yii::app()->request->getParam('callback', null);

        $arr = array(
            'msg'      => $msg,
            'status'   => $status,
            'data'     => $data,
        );

        $json = CJSON::encode($arr);
        if ($callback === null)
            echo $json;
        else
        {
            echo $callback . "($json)";
        }

        exit;
    }

    /**
     * 输出JS提示框
     * @param string  $text  提示文本
     */
    public static function jsAlert($text)
    {
        echo '<script>alert("'.$text.'")</script>';
        exit;
    }

    /**
     * 输出js内容
     * @param string  $content js内容
     */
    public static function js($content)
    {
        echo '<script>'.$content.'</script>';
        exit;
    }
}