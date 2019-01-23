<?php
/**
 * 泰策咨询
 * @author yuzhijie<yuzj1113@163.com>
 * @date 2015-8-12
 */
class SignController extends Controller
{
	//添加
	public function  actionAdd()
	{
		Yii::app()->name = '泰策咨询-欢迎咨询培训';
		$model  = new Sign();
		
		// 导航
		$data['nav'] = Catalog::model()->getNav();
		
		// 课程
		$data['course'] = Course::model()->getCourse();
		$data['model'] = $model;
		$data['title'] = '在线报名';
		$this->render('registration',$data);
	}
	
	
	public  function  actionDeal()
	{
		$model = new Sign();
		if($_POST)
		{
			$model->attributes = $_POST;
			$result = $model->saveData();
			if($result)
				echo 1;
			else
				echo 0;
		} 
			else
				echo 0;
	}
}