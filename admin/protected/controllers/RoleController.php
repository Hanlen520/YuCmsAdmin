<?php
/**
 * 后台角色控制器
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class RoleController extends Controller
{
	// 列表
	public function actionIndex()
	{
		$model = new AdminRole();
		
		$search = array(
		    'name'         => isset($_GET['name']) ? trim($_GET['name']) : '',
			'description'  => isset($_GET['description']) ? trim($_GET['description']) : '',
		);
		
		if (isset($_POST['AdminRole'])) $search = $_POST['AdminRole'];
		$model->attributes = $search;
		
		$data = AdminRole::model()->getList($search);
		
		$this->render('list', array('model' => $model, 'search' => $search, 'list' => $data['list'],'totalNums'=>$data['totalNums'], 'returnUrl' => $data['returnUrl'], 'multipage' => $data['multipage']));
	}
	
	// 新增
	public function actionAdd()
	{
		$model = new AdminRole();
		$modelT = new AdminTask();
		$modelR = new Authitemchild();
		
		if (isset($_POST['AdminRole']))
		{
			$model->attributes = $_POST['AdminRole'];
			$modelT->attributes = $_POST['AdminRole'];
			
			if ($model->validate() && $modelT->validate())
			{
				$result = $model->saveData();
				
				if ($result)
				{
					// 添加任务
					$resultTask = $modelT->saveData();
					if (!$resultTask) $this->redirectMsg('角色添加失败：' . $this->getError($modelT));
					
					// 添加角色与任务的关系
					$resultR = $modelR->saveData($model->name, $modelT->name);
					if (!$resultR) $this->redirectMsg('角色添加失败：' . $this->getError($modelR));
					
					// 添加系统日志
					$title = '新增角色['.$model->name.']';
					$content = '新增角色：角色代码为['.$model->name.']，角色描述为['.$model->description.']';
					$log = new AdminLog();
					$log->saveData($title, $content);

					$this->ajaxMsg('角色添加成功！', 1,array('url'=>'index'));
				}
				else 
					$this->ajaxMsg('角色添加失败：' . $this->getError($model),0);
			} else
				$this->ajaxMsg('角色添加失败：' . $this->getError(array($model, $modelT)),0);
		}
		
		$this->renderPartial('edit', array('model' => $model, 'returnUrl' => 'index'));
	}

	// 编辑
	public function actionEdit()
	{
		$returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'role/index';
		
		$model = $this->loadModel();
		
		if (isset($_POST['AdminRole']))
		{
			$model->attributes = $_POST['AdminRole'];
			
			if ($model->validate())
			{
				$result = $model->saveData();
				
				if ($result)
				{
					// 添加系统日志
					$title = '编辑角色['.$model->name.']';
					$content = '编辑角色：角色代码为['.$model->name.']，角色描述更改为['.$model->description.']';
					$log = new AdminLog();
					$log->saveData($title, $content);
					
					$this->ajaxMsg('角色保存成功！', 1,array('url'=>$returnUrl));
				}
				else 
					$this->ajaxMsg('角色保存失败：' . $this->getError($model),0);
			} else
				$this->ajaxMsg('角色保存失败：' . $this->getError($model),0);
		} else 
		{
			$returnUrl = str_replace('/admin', '', $returnUrl);
		}
		
		$this->renderPartial('edit', array('model' => $model, 'returnUrl' => $returnUrl));
	}
	
	// 删除
	public function actionDelete()
	{
		$returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'role/index';
		
		$model = $this->loadModel();
		$result = $model->deleteData($model->name);
		
		if ($result)
		{
			// 添加系统日志
			$title = '删除角色['.$model->name.']';
			$content = '删除角色：角色代码为['.$model->name.']，角色描述为['.$model->description.']';
			$log = new AdminLog();
			$log->saveData($title, $content);
					
		    $this->ajaxMsg('角色删除成功！',1,array('url'=>$returnUrl));
		}
		else
		    $this->ajaxMsg('角色删除失败：' . $this->getError($model),0);
	}
	
	// 权限
	public function actionPurview()
	{
		$returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'role/index';
		
		$ac = new Authitemchild();
		$model = $this->loadModel();
		
		// 取所有模块下的所有操作权限
		$AllPurview = Authitem::model()->getAllPurview();
		// 当前角色的所有权限
		$RolePurview = Authitem::model()->getAllPurviewByRole($model->name);
		
		if (isset($_POST['RolePurview']))
		{
			$ac->purview = $_POST['RolePurview'];
			$ac->roleName = $model->name;
			if ($ac->validate())
			{
				$result = $ac->setPurview();

				if ($result)
				{
					// 添加系统日志
					$title = '角色分配权限['.$model->name.']';
					$content = '角色分配权限：角色代码为['.$model->name.']，角色描述为['.$model->description.']';
					$log = new AdminLog();
					$log->saveData($title, $content);
			
					$this->ajaxMsg('权限分配成功！', 1,array('url'=>$returnUrl));
				}
				else 
					$this->ajaxMsg('权限分配失败！'. $this->getError($ac),0);
			} else
				$this->ajaxMsg('权限分配失败1！'. $this->getError($ac) ,0);
		} else 
		{
			$returnUrl = str_replace('/admin', '', $returnUrl);
		}
		
		$this->renderPartial('purview', array('model' => $model, 'modelAC' => $ac, 'AllPurview' => $AllPurview, 'RolePurview' => $RolePurview, 'returnUrl' => $returnUrl));
	}
	
	// 加载数据模型
	public function loadModel()
	{
		$name = isset($_GET['name']) ? trim($_GET['name']) : '';
		
		$model = AdminRole::model()->find("name=:name AND type=2", array(':name' => $name));
		if (!$model) $this->redirectMsg('角色不存在！');
		
		return $model;
	}
}