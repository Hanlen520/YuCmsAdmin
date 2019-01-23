<?php
/**
 * 后台菜单控制器
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class MenuController extends Controller
{
	// 列表
	public function actionIndex()
	{
		// 载入基础数据
		$model = new AdminMenu();
		
		$search = array(
		    'menu_name'     => isset($_GET['menu_name']) ? trim($_GET['menu_name']) : '',
			//'module_id'     => isset($_GET['module_id']) ? (int) $_GET['module_id'] : '',
		);
	    
		if (isset($_POST['AdminMenu'])) $search = $_POST['AdminMenu'];
		$model->attributes = $search;
		
		$data = $model->getList($search);
        $menuList =AdminMenu::model()->getMenuOptions();

		$this->render('list', array('model' => $model, 'search' => $search, 'list' => $data['list'], 'menuList'=>$menuList, 'returnUrl' => $data['returnUrl'], 'multipage' => $data['multipage'], 'totalNums'=>$data['totalNums']));
	}
	
	// 新增
	public function actionAdd()
	{
		// 载入基础数据
		//$this->getInitData();
		
		$model = new AdminMenu();
		
		if (isset($_POST['AdminMenu']))
		{
			$model->attributes = $_POST['AdminMenu'];
			
			if ($model->validate())
			{
				$result = $model->saveData();
				
				if ($result)
				{
					// 添加系统日志
					$title = '新增菜单['.$model->menu_name.']';
					$log = new AdminLog();
					$log->saveData($title);
					
					$this->ajaxMsg('菜单添加成功！', 1, array('url'=>'index'));
				}
				else 
					$this->ajaxMsg('菜单添加失败：' . $this->getError($model),0);
			} else
				$this->ajaxMsg('菜单添加失败：' . strip_tags($this->getError($model)),0);
		}

        $menuList =AdminMenu::model()->getMenuOptions();
        $itemNames = Authitem::model()->getOperateOptions();

		$this->renderPartial('edit', array('model' => $model, 'menuList' => $menuList, 'itemNames'=>$itemNames, 'returnUrl' => 'index'));
	}
	
	// 编辑
	public function actionEdit()
	{
		$returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'menu/index';
		
		$model = $this->loadModel();
		
		//if ($model->is_sys == 1) $this->ajaxMsg('系统菜单无法编辑！',0);
		
		if (isset($_POST['AdminMenu']))
		{
			$model_OLD = $model->attributes;
			$model->attributes = $_POST['AdminMenu'];
			
			if ($model->validate())
			{
				$result = $model->saveData();
				
				if ($result)
				{
					$logArr = array();
					$logArr[] = array($model, $model_OLD, $model->attributes);
					$LogContent = $this->getOperateLog($logArr);
					
					// 添加系统日志
					$title = '编辑菜单';
					$log = new AdminLog();
					$log->saveData($title, $LogContent);
					
					$this->ajaxMsg('菜单编辑成功！', 1,array('url'=>$returnUrl));
				}
				else 
					$this->ajaxMsg('菜单编辑失败：' . $this->getError($model),0);
			} else
				$this->ajaxMsg('菜单编辑失败：' . strip_tags($this->getError($model)),0);
		} else 
		{
			$returnUrl = str_replace('/admin', '', $returnUrl);
		}

        $menuList =AdminMenu::model()->getMenuOptions();
        $itemNames = Authitem::model()->getOperateOptions();
		$this->renderPartial('edit', array('model' => $model, 'menuList'=>$menuList, 'itemNames'=>$itemNames,'returnUrl' => $returnUrl));
	}
	
	// 更新排序
	public function actionUpdate()
	{
		$returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'menu/index';
		
		$model = new AdminMenu();
		
		$Menu = isset($_POST['Menu']) ? $_POST['Menu'] : array();
		if (!$Menu) $this->ajaxMsg('没有可操作的对象！',0);

		foreach ($Menu as $id => $val)
		{
			$id = isset($id) ? (int) $id : 0;
			$sort = isset($val['sort']) ? (int) $val['sort'] : 0;
			
			$dataArr = array(
			    'sort'     => $sort,
			);

			$model->updateByPk($id, $dataArr);
		}
		
		// 添加系统日志
		$title = '更新菜单排序';
		$log = new AdminLog();
		$log->saveData($title);
					
		$this->ajaxMsg('菜单排序更新成功！', 1,array('url'=>$returnUrl));
	}
	
	// 删除
	public function actionDelete()
	{
		$returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'menu/index';
		
		$model = $this->loadModel();
		
		if ($model->is_sys == 1) $this->ajaxMsg('系统菜单无法删除！',0);
		
		$result = $model->deleteData($model->menu_id);
		
		if ($result)
		{
			// 添加系统日志
			$title = '删除菜单['.$model->menu_name.']';
			$log = new AdminLog();
			$log->saveData($title);
					
		    $this->ajaxMsg('菜单删除成功！',1, array('url'=>$returnUrl));
		}
		else
		    $this->ajaxMsg('菜单删除失败：' . strip_tags($this->getError($model)),0);
	}
	
	// 加载数据模型
	public function loadModel()
	{
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		
		$model = AdminMenu::model()->findByPk($id);
		if (!$model) $this->ajaxMsg('菜单数据不存在！',0,array('url'=>'index'));

		return $model;
	}
}