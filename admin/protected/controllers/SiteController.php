<?php
  /**
  * 错误处理控制器
  */
class SiteController extends Controller
{
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		//$this->render('index');
        die();
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			/*
            if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
              */  
            $this->redirectMsg($error,'',1,false);   
		}
        
        
        
        
	}

}