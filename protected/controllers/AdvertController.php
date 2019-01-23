<?php
/**
 * 广告详情 控制器
 * @author yuzhijie<sysionyzj@sysion.com>
 * @date 2014-08-30
 */
class AdvertController extends Controller
{
	public function actionInfo()
	{
		Yii::app()->name = '泰策咨询-制造行业培训咨询引领者';
	
		// 导航
		$data['nav'] = Catalog::model()->getNav();
		
		// 新闻详情
		$id = Yii::app()->request->getParam('id',0);
		$model = Ad::model()->findByPk($id);
		$data['model'] = $model;
		$data['title'] = '泰策动态';
		// 引入页面
		$this->render('info',$data);
	}
}