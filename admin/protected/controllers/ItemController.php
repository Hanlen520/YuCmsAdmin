<?php
/**
 * 操作项目 控制器
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class ItemController extends Controller
{
	// 模块分隔符
	public $delimeter = '-';
	
	// 列表
	public function actionIndex()
	{
		$controllers = $this->_getControllers();
		sort($controllers);
		
		$this->render('index', array('list' => $controllers));
	}
	
	// 授权
	public function actionAdd()
	{
		$model = new Authitem();
		
		$data = $this->loadModel();
		
		$actions = $this->_getControllerInfo($data['name']);
		sort($actions);
		
		if (isset($_POST['items']))
		{
			//var_dump($_POST['items']);exit();
			if ($model->validateData($model, $_POST['items']))
			{
				$result = $model->saveData();
				
				if ($result)
				{
					// 控制器
					if (substr_count($data['name'], $this->delimeter)) 
	    			{
	     				$c = explode($this->delimeter, $data['name']);
	      				$controller = $c[1];
	    			} else 
	    				$controller = $data['name'];
	    			
	    			// 方法
	    			$action = implode('、', array_keys($model->itemList));
	    			
	    			// 添加系统日志
					$title = '控制器授权['.$controller.']';
					$content = '控制器授权：模块为['.$model->module_name.']，控制器为['.$controller.']，方法为['.$action.']';
					$log = new AdminLog();
					$log->saveData($title, $content);
					
				    $this->ajaxMsg('授权成功！', 1, array('index'));
				}
				else 
					$this->ajaxMsg('授权失败：' . $this->getError($model),0);
			} else
				$this->ajaxMsg('授权失败：' . strip_tags($this->getError($model)),0);
		}
		
		$this->renderPartial('edit', array('data' => $data, 'actions' => $actions));
	}
	
	// 删除某控制器下的授权项
	public function actionDelete()
	{
		$model = new Authitem();
		$data = $this->loadModel();
		
		$actions = $this->_getControllerInfo($data['name'], 1);
		sort($actions);

		if (isset($_POST['items']))
		{
			$item = $_POST['items'];
			$result = $model->deleteData($item);

            if ($model->validateData($model, $_POST['items']))
            {
                if ($result)
                {
                    // 方法
                    $action = implode('、', $item);

                    // 添加系统日志
                    $title = '控制器删除授权['.$data['name'].']';
                    $content = '控制器删除授权：控制器为['.$data['name'].']，方法为['.$action.']';
                    $log = new AdminLog();
                    $log->saveData($title, $content);

                    $this->ajaxMsg('授权项删除成功！',1, array('index'));
                }
                else
                    $this->ajaxMsg('授权项删除失败：' . $this->getError($model),0);
            }
            $this->ajaxMsg('授权项删除失败：' . strip_tags($this->getError($model)),0);
        }
	    $this->renderPartial('delete', array('data' => $data, 'actions' => $actions));
	}
	
	// 更新
	public function actionUpdate()
	{
		$model = new Authitem();
		$data = $this->loadModel();
		
		$actions = $this->_getControllerInfo($data['name'], 1);
		// 获取描述
		$descArr = Authitem::model()->getDescription(array_keys($actions));
		
		$Description = isset($descArr['description']) ? $descArr['description'] : array();

		if (isset($_POST['description']))
		{
			$description = $_POST['description'];

			foreach ($description as $name => $v)
			{
				$description = trim($v);
				
				$dataArr = array(
					'description'   => $description,
				);
	
				if ($name && $description) $model->updateByPk($name, $dataArr);
				
			}
			
			// 添加系统日志
			$title = '更新授权描述['.$data['name'].']';
			$log = new AdminLog();
			$log->saveData($title);
					
			$this->ajaxMsg('更新成功！',1, array('index'));
		}
		
		$this->renderPartial('update', array('data' => $data, 'Description' => $Description));
	}
	
	// 获取所有控制器
	private function _getControllers() 
	{
	    $contPath = Yii::app()->getControllerPath();
		$controllers = $this->_scanDir($contPath);
		
	    // 扫描模块下的控制器
	    $modules = Yii::app()->getModules();
	    $modControllers = array();
	    
	    foreach ($modules as $mod_id => $mod) 
	    {
	    	// 已安装的模块才可以显示权限
	    	//$isInstalled = AdminModule::model()->isInstalled($mod_id);
	    	
	    	//if ($isInstalled == true)
	    	//{
	    		$moduleControllersPath = Yii::app()->getModule($mod_id)->controllerPath;
	    		$modControllers = $this->_scanDir($moduleControllersPath, $mod_id, "", $modControllers);
	    	//}
	    }
	    
	    return array_merge($controllers, $modControllers);
  	}
  	
  	// 扫描控制器所在目录
    private function _scanDir($contPath, $module = '', $subdir = '', $controllers = array()) 
    {
	    $handle = opendir($contPath);
	    $del = $this->delimeter;
	    
		while (($file = readdir($handle)) !== false) 
		{
	      $filePath = $contPath . DS . $file;
		  if (is_file($filePath)) {
	        if (preg_match("/^(.+)Controller.php$/", basename($file))) 
	        {
	            $controllers[] = (($module) ? $module . $del : "") .
	              (($subdir) ? $subdir . "." : "") .
	              str_replace(".php", "", $file);
	        }
	      } else if (is_dir($filePath) && $file != "." && $file != "..") 
	      {
	        $controllers = $this->_scanDir($filePath, $module, $file, $controllers);
	      }
	    }
	    
	    return $controllers;
    }
    
    // 获取当前控制器下的方法
    private function _getControllerInfo($controller, $isDelete = 0) 
    {
    	$del = $this->delimeter;
    	$actions = array();
    	$auth = Yii::app()->authManager;
    	
	    if (substr_count($controller, $del)) 
	    {
	      $c = explode($del, $controller);
	      
	      $controller = $c[1];
	      $module = $c[0] .$del;
	      $contPath = Yii::app()->getModule($c[0])->getControllerPath();
	      $control = $contPath . DS . str_replace(".", DS, $controller) . ".php";
	    } else 
	    {
	      $module = '';
	      $contPath = Yii::app()->getControllerPath();
	      $control = $contPath . DS . str_replace(".", DS, $controller) . ".php";
	    }
	    
	    $h = file($control);
	    for ($i = 0; $i < count($h); $i++) 
	    {
	      $line = trim($h[$i]);
	      if (preg_match("/^(.+)function( +)action*/", $line)) {
	        $posAct = strpos(trim($line), "action");
	        $posPar = strpos(trim($line), "(");
	        $action = trim(substr(trim($line),$posAct, $posPar-$posAct));
	        $patterns[0] = '/\s*/m';
	        $patterns[1] = '#\((.*)\)#';
	        $patterns[2] = '/\{/m';
	        $replacements[2] = '';
	        $replacements[1] = '';
	        $replacements[0] = '';
	        $action = preg_replace($patterns, $replacements, trim($action));
	        $itemId = $module . ucfirst(strtolower(str_replace("Controller", "", $controller))) .
	        ucfirst(strtolower(preg_replace("/action/", "", $action, 1)));
	        
	        $itemUrl = strtolower(str_replace("Controller", "", $controller)) . '/' .
	        strtolower(preg_replace("/action/", "", $action, 1));
	        
	        $itemUrl = ($module ? str_replace($del, '/', $module) : '') . $itemUrl;
	        
	        if ($action != "actions") 
	        {
	        	$au = $auth->getAuthItem($itemId);
	        	if ( $au!== null && $isDelete == 1)   // 删除
	        	{
	        		$actions[$itemId] = array(
	        			'itemId'      => $itemId,
	        			'url'         => $itemUrl,
	        			'description' => $au->description,
	        		);
	        		
	        	} else if ($au === null && !$isDelete)
	        	{
	        		$actions[$itemId] = array(
	        			'itemId'       => $itemId,
	        			'url'          => $itemUrl,
	        			'description'  => '',
	        		);
	        	}
	        }
	      }
	    }
	    
	    return $actions;
    }
    
    // 加载数据
    public function loadModel()
    {
    	$name = isset($_GET['name']) ? trim($_GET['name']) : '';
		if (!$name) $this->ajaxMsg('参数错误！',0);
		
		$arr = explode($this->delimeter, $name);
		if (count($arr) == 1)
		{
			$module     = '-';
            $controller = $arr[0];
		} else 
		{
			$module     = $arr[0];
            $controller = $arr[1];
		}

        $data = array(
			'name'           => $name,
			'module'         => $module,
			'controller'     => $controller,
		);
		
		return $data;
    }

}