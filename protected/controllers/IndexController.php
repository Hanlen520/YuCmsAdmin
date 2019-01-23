<?php
/**
 * 泰策首页
 * @author yuzhijie<yuzj1113@163.com>
 * @date 2013-10-24
 */
class IndexController extends Controller
{
	public function actionIndex()
	{
		Yii::app()->name = '泰策咨询-制造行业培训咨询引领者';
		// 导航
		$data['nav'] = Catalog::model()->getNav();
		// 广告
		$data['ad']= Ad::model()->getAd(1);
		// 公告
		$data['notice'] = Ad::model()->getAd(2);
		// 动态
		$data['trends'] = Ad::model()->getAd(3,4);
		// 公司介绍
		$data['company'] = Course::model()->getNew(array(48),1);
		// 课程
		$data['course'] = Course::model()->getNew(array(43,44,45),1);
		// 现场风采
		$data['scene'] = Course::model()->getNew(array(46),4);
		// 师资力量
		$data['teacher'] = Course::model()->getNew(array(47),8);
		
		$this->render('index',$data);
	}
	
	public function actioninfo()
	{
		$cid = Yii::app()->request->getParam('cid',0);
		
		$model = Catalog::model()->findByPk($cid);
		
		Yii::app()->name = isset($model->catalog_name) && $model->catalog_name ? $model->catalog_name.'-泰策咨询' : '泰策咨询';
		// 导航
		$data['nav'] = Catalog::model()->getNav();
		
		// 新闻标题
		$data['course'] = Course::model()->getTitle($cid);
		
		// 新闻详情
		$id = Yii::app()->request->getParam('id',0);
		$model = Course::model()->findByPk($id);
		if($model)
		{
			$data['content'] = isset($model->content) ? $model->content : '暂无内容!';
			$data['id'] = $id;
			$data['title'] = isset($model->title) ? $model->title : '';
				
		}else
		{
			$info = $data['course'];
			$info = array_shift($info);
			$data['content'] = isset($info['content'])? $info['content'] : '暂无内容!';
			$data['id'] = 9;
			$data['title'] = isset($info['title']) ? $info['title'] : '';
		}
		
		// 引入页面
	   $this->render('info',$data);
	}
	
	public function actionNews()
	{
		Yii::app()->name = '泰策咨询-制造行业培训咨询引领者';
	
		// 导航
		$data['nav'] = Catalog::model()->getNav();
	
		// 新闻详情
		$id = Yii::app()->request->getParam('id',0);
		$model = Course::model()->findByPk($id);
		$data['model'] = $model;
		$cid = isset($model->catalog_id)&& $model->catalog_id ? $model->catalog_id : 0; 
		$Cmodel = Catalog::model()->findByPk($cid);
		$data['title'] = isset($Cmodel->catalog_name) && $Cmodel->catalog_name ? $Cmodel->catalog_name : '泰策咨询';;
		// 引入页面
		$this->render('news',$data);
	}
	
	public function actionAbout()
	{
		$cid = Yii::app()->request->getParam('cid',0);
	
		$model = Catalog::model()->findByPk($cid);
	
		Yii::app()->name = isset($model->catalog_name) && $model->catalog_name ? $model->catalog_name.'-泰策咨询' : '泰策咨询';
		// 导航
		$data['nav'] = Catalog::model()->getNav();
	
		// 新闻标题
// 		$data['course'] = Course::model()->getTitle($cid);
	
		$data['about'] = Course::model()->getAbout($cid);
		$data['title'] = $cid == 46 ? '实战讲堂' :'师资力量';
		
		$this->render('about',$data);
		
	}
}