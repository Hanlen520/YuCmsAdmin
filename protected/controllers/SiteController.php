<?php
/**
 * 错误处理 控制器
 * @author yuzhijie<sysionyzj@sysion.com>
 * @date 2014-08-30
 */
class SiteController extends Controller
{
    // 设置默认方法
    public $defaultAction = 'error';

	public function actionError()
	{
        die('没有找到相应的内容！');
	}
}