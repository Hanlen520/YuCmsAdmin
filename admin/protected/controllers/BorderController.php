<?php
/**
 * 顶部菜单控制器
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class BorderController extends Controller
{
	public $layout = '//layouts/column1';
	
	public $firstUrl = '';
	
	public function actionIndex()
	{
		$menuData = AdminMenu::model()->getMenu();
		// 获取子菜单
		$this->subMenu = isset($menuData['menu']) ? $menuData['menu'] : array();
		
		// 获取菜单引导链接
		$this->firstUrl = isset($menuData['firstUrl']) ? $menuData['firstUrl'] : '';

		$this->render('index');
	}
}