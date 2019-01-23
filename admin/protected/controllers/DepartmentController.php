<?php
/**
 * 部门控制器
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class DepartmentController extends Controller
{
	// 列表
	public function actionIndex()
	{
		$model = new Department();
		
		$search = array(
		    'depart_name'  => isset($_GET['depart_name']) ? trim($_GET['depart_name']) : '',
			'status'       => isset($_GET['status']) ? (int) $_GET['status'] : '',
		);
		
		if (isset($_POST['Department'])) $search = $_POST['Department'];
		$model->attributes = $search;
		
		$data = Department::model()->getList($search);
		
		$this->render('list', array('model' => $model, 'search' => $search, 'list' => $data['list'], 'returnUrl' => $data['returnUrl'], 'multipage' => $data['multipage'], 'totalNums'=>$data['totalNums']));
	}
	
	// 新增
	public function actionAdd()
	{
		$model = new Department();
		
		if (isset($_POST['Department']))
		{
			$model->attributes = $_POST['Department'];
			
			if ($model->validate())
			{
				$result = $model->saveData();
				
				if ($result)
				{
					// 添加系统日志
					$title = '新增部门['.$model->depart_name . ']';
					$log = new AdminLog();
					$log->saveData($title);
					
					$this->ajaxMsg('部门添加成功！', 1,array('url'=>'department/index'));
				}
				else 
					$this->ajaxMsg('部门添加失败：' . $this->getError($model),0);
			} else
				$this->ajaxMsg('部门添加失败：' . $this->getError($model),0);
		}
		
		$this->renderPartial('edit', array('model' => $model, 'returnUrl' => 'index'));
	}

	// 编辑
	public function actionEdit()
	{
		$returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'department/index';
		
		$model = $this->loadModel();
		
		if (isset($_POST['Department']))
		{
			$model_OLD = $model->attributes;
			$model->attributes = $_POST['Department'];
			
			if ($model->validate())
			{
				$result = $model->saveData();
				
				if ($result)
				{
					$logArr = array();
					$logArr[] = array($model, $model_OLD, $model->attributes);
					$LogContent = $this->getOperateLog($logArr);
			
					// 添加系统日志
					$title = '编辑部门';
					$log = new AdminLog();
					$log->saveData($title, $LogContent);
					
					$this->ajaxMsg('部门保存成功！', 1,array('url'=>$returnUrl));
				}
				else 
					$this->ajaxMsg('部门保存失败：' . $this->getError($model),0);
			} else
				$this->ajaxMsg('部门保存失败：' . $this->getError($model),0);
		} else 
		{
			$returnUrl = str_replace('/admin', '', $returnUrl);
		}

		$this->renderPartial('edit', array('model' => $model, 'returnUrl' => $returnUrl));
	}
	
	// 删除
	public function actionDelete()
	{
		$returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'department/index';
		
		$model = $this->loadModel();
		$result = $model->deleteData($model->depart_id);
		
		if ($result)
		{
			// 添加系统日志
			$title = '删除部门['.$model->depart_name . ']';
			$log = new AdminLog();
			$log->saveData($title);
					
		    $this->ajaxMsg('部门删除成功！',1 ,array('url'=>$returnUrl));
		}
		else
		    $this->ajaxMsg('部门删除失败：' . $this->getError($model),0);
	}
	
	// 更新排序
	public function actionUpdate()
	{
		$returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'department\index';
		
		$model = new Department();
		
		$Department = isset($_POST['Depart']) ? $_POST['Depart'] : array();
		if (!$Department) $this->ajaxMsg('没有可操作的对象！',0);

		foreach ($Department as $id => $val)
		{
			$id = isset($id) ? (int) $id : 0;
			$sort = isset($val['sort']) ? (int) $val['sort'] : 0;
			
			$dataArr = array(
			    'sort'     => $sort,
			);

			$model->updateByPk($id, $dataArr);
		}
		
		// 添加系统日志
		$title = '更新部门排序';
		$log = new AdminLog();
		$log->saveData($title);
			
		$this->ajaxMsg('部门排序更新成功！',1 ,array('url'=>$returnUrl));
	}
	
	// 启用/禁用
	public function actionSetState()
	{
		$returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'department/index';
		
		$status = isset($_GET['status']) ? (int) $_GET['status'] : 0;
		$status = $status == 1 ? 1 : 0;
		
		$msgStr = $status == 1 ? '启用' : '停用';
		
		$model = $this->loadModel();
		$result = $model->SetStatus($model->depart_id, $status);
		
		if ($result)
		{
			// 添加系统日志
			$title = $msgStr . '部门['.$model->depart_name . ']';
			$log = new AdminLog();
			$log->saveData($title);
			
		    $this->ajaxMsg('部门'.$msgStr.'成功！', 1,array('url'=>$returnUrl));
		}
		else
		    $this->ajaxMsg('部门'.$msgStr.'失败！',0);
	}
	
	// 加载主建模型数据
	public function loadModel()
	{
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		
		$model = Department::model()->findByPk($id);
		if (!$model) $this->redirectMsg('部门不存在！');
		
		return $model;
	}
}