<?php 
/**
 * 自动载入配置等
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-08-16
 */
class ApplicationConfigBehavior extends CBehavior 
{ 
    public function events() 
    { 
        return array_merge(parent::events(), array( 
            'onBeginRequest' => 'beginRequest', 
        )); 
    } 
      
    public function beginRequest() 
    { 
    	// 自动装载模块的配置
        // $this->loadModules();
    }
    
    // 自动装载模块的配置
    public function loadModules()
    {
    	$modules = array(); 
        $model = AdminModule::model()->findAll("status=1 AND module_code!='' ORDER BY sort, module_id"); 
        foreach ($model as $item) 
        { 
            $modules[$item->module_code] = array();
        }
        
        $modules = $this->scanModuleDir($modules);
        Yii::app()->setModules($modules); 
    }
    
    // 搜索模块目录
    public function scanModuleDir($modules)
    {
    	$modulePath = Yii::app()->getModulePath();
    	$handle = opendir($modulePath);
	    
		while (($file = readdir($handle)) !== false) 
		{
	      $filePath = $modulePath . DS . $file;
		  if (is_dir($filePath) && $file != "." && $file != "..") 
	      {
	        $modules[$file] = array();
	      }
	    }
	    
	    return $modules;
    }
} 
