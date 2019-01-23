<?php
/**
 * 后台用户控制器
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class UserController extends Controller
{
	// 基础数据
	public $initData = array();

	// 获取基础数据
	public function getInitData()
	{
		// 省份
		//$this->initData['Province'] = Province::model()->getProvince();

		// 部门
		$this->initData['Department'] = Department::model()->getDepartment();
	}

	// 列表
	public function actionIndex()
	{
		$model = new AdminUser();

		$search = array(
			'admin_user_name'  => isset($_GET['admin_user_name']) ? trim($_GET['admin_user_name']) : '',
			'real_name'        => isset($_GET['real_name']) ? trim($_GET['real_name']) : '',
			'mobile'           => isset($_GET['mobile']) ? trim($_GET['mobile']) : '',
			'status'           => isset($_GET['status']) ? (int) $_GET['status'] : '',
		);

		if (isset($_POST['AdminUser'])) $search = $_POST['AdminUser'];
		$model->attributes = $search;

		$data = AdminUser::model()->getList($search);

		$this->render('list', array('model' => $model,'totalNums'=>$data['totalNums'] ,'search' => $search, 'list' => $data['list'], 'returnUrl' => $data['returnUrl'], 'multipage' => $data['multipage']));
	}

	// 新增
	public function actionAdd()
	{
		// 载入基础数据
		$this->getInitData();

		$model = new AdminUser();
		$modelA = new AuthAssignment();

		//  获取所有角色
		$roles = Authitem::model()->getRoles();
		$model->scenario = 'adduser';

		if (isset($_POST['AdminUser'])&& $_POST['AuthAssignment'])
		{
			$model->attributes = $_POST['AdminUser'];
			$modelA->attributes = $_POST['AuthAssignment'];

			if ($model->validate() && $modelA->validate())
			{
				$result = $model->saveData($modelA);

				if ($result)
				{
					// 添加系统日志
					$title = '新增用户['.$model->admin_user_name.']';
					$log = new AdminLog();
					$log->saveData($title);

					$this->ajaxMsg('用户添加成功！', 1,array('url'=>'user/index'));
				}
				else
					$this->ajaxMsg('用户添加失败：' . $this->getError(array($model,$modelA)),0);
			} else
				$this->ajaxMsg('用户添加失败：' . $this->getError(array($model,$modelA)),0);
		}

		$this->renderPartial('edit', array('initData' => $this->initData, 'model' => $model, 'roles' => $roles,'modelA' => $modelA,'returnUrl' => 'user/index'));
	}

	// 编辑
	public function actionEdit()
	{
		// 载入基础数据
		$this->getInitData();

		$returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'user/index';

		$model = $this->loadModel();
        $modelA = new AuthAssignment();
		$model->scenario = 'edituser';

        //  获取所有角色
        $roles = Authitem::model()->getRoles();

        //  获取每个用户当前的角色
        $role = $modelA->getRole($model->admin_user_id);

		if (isset($_POST['AdminUser']) && isset($_POST['AuthAssignment']))
		{
			$model_OLD = $model->attributes;
			$model->attributes = $_POST['AdminUser'];
            $modelA->attributes = $_POST['AuthAssignment'];

			if ($model->validate()&& $modelA->validate())
			{
				$result = $model->saveData($modelA);

				if ($result)
				{
					$logArr = array();
					$logArr[] = array($model, $model_OLD, $model->attributes);
					$LogContent = $this->getOperateLog($logArr);

					// 添加系统日志
					$title = '编辑用户['.$model->admin_user_name.']';
					$log = new AdminLog();
					$log->saveData($title, $LogContent);

					$this->ajaxMsg('用户保存成功！', 1,array('url'=>$returnUrl));
				}
				else
					$this->ajaxMsg('用户保存失败：' . $this->getError(array($model,$modelA)),0);
			} else
				$this->ajaxMsg('用户保存失败1：' . $this->getError(array($model,$modelA)),0);
		} else
		{
			$returnUrl = str_replace('/admin', '', $returnUrl);
		}

		$this->renderPartial('edit', array('initData' => $this->initData, 'model' => $model, 'role'=>$role, 'roles' => $roles,'modelA' => $modelA, 'returnUrl' => $returnUrl));
	}

	// 删除
	public function actionDelete()
	{
		$returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'user/index';

		$model = $this->loadModel();
		$result = $model->deleteData($model->admin_user_id);

		if ($result)
		{
			// 添加系统日志
			$title = '删除用户['.$model->admin_user_name.']';
			$log = new AdminLog();
			$log->saveData($title);

			$this->ajaxMsg('用户删除成功！', 1,array('url'=>$returnUrl));
		}
		else
			$this->ajaxMsg('用户删除失败！',0);
	}

	// 启用/禁用
	public function actionSetState()
	{
		$returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'user/index';

		$status = isset($_GET['status']) ? (int) $_GET['status'] : 0;
		$status = $status == 1 ? 1 : 0;

		$msgStr = $status == 1 ? '启用' : '禁用';

		$model = $this->loadModel();
		$result = $model->SetStatus($model->admin_user_id, $status);

		if ($result)
		{
			// 添加系统日志
			$title = $msgStr . '用户['.$model->admin_user_name.']';
			$log = new AdminLog();
			$log->saveData($title);

			$this->ajaxMsg('用户'.$msgStr.'成功！', 1,array('url'=>$returnUrl));
		}
		else
			$this->ajaxMsg('用户'.$msgStr.'失败！',0);
	}

	// 加载数据模型
	public function loadModel()
	{
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

		$model = AdminUser::model()->findByPk($id);
		if (!$model) $this->ajax('用户数据不存在！',0);

		$depart = Department::model()->findByPk($model->depart_id);
		$model->depart_name = $depart ? $depart->depart_name : '';

		$model->password = '';

		return $model;
	}
}