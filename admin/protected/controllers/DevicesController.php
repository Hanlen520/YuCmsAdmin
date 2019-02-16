<?php
/**
 * 后台菜单控制器
 * @author yuzhijie
 * @date 2015-1-25
 */
class DevicesController extends Controller
{
	// 申请列表
	public function actionIndex()
	{
		// 载入基础数据
		$model = new Devices();
		
		$search = array(
		    'device_name' => isset($_GET['device_name']) ? trim($_GET['device_name']) : '',
            'status'      => isset($_GET['status']) ? trim($_GET['status']) : '',
            'model'      => isset($_GET['model']) ? trim($_GET['model']) : '',
            'platform'    => isset($_GET['platform']) ? trim($_GET['platform']) : '',
            'brand'       => isset($_GET['brand']) ? trim($_GET['brand']) : '',
            'category'    => isset($_GET['category']) ? trim($_GET['category']) : '',
            'version'     => isset($_GET['version']) ? trim($_GET['version']) : '',
            'other'       => isset($_GET['other']) ? trim($_GET['other']) : '',
            'borrower'       => isset($_GET['borrower']) ? trim($_GET['borrower']) : '',
            'old_dev'       => isset($_GET['old_dev']) ? trim($_GET['old_dev']) : '0',
		);
	    
		if (isset($_POST['Devices'])) $search = $_POST['Devices'];
		$model->attributes = $search;
		
		$data = $model->getList($search);

		$this->render('list', array('model' => $model, 'search' => $search, 'list' => $data['list'], 'returnUrl' => $data['returnUrl'], 'multipage' => $data['multipage'], 'totalNums'=>$data['totalNums']));
	}

    // 设备列表
    public function actionList()
    {
        // 载入基础数据
        $model = new Devices();

        $search = array(
            'device_name' => isset($_GET['device_name']) ? trim($_GET['device_name']) : '',
            'status'      => isset($_GET['status']) ? trim($_GET['status']) : '',
            'model'      => isset($_GET['model']) ? trim($_GET['model']) : '',
            'platform'    => isset($_GET['platform']) ? trim($_GET['platform']) : '',
            'brand'       => isset($_GET['brand']) ? trim($_GET['brand']) : '',
            'category'    => isset($_GET['category']) ? trim($_GET['category']) : '',
            'version'     => isset($_GET['version']) ? trim($_GET['version']) : '',
            'other'       => isset($_GET['other']) ? trim($_GET['other']) : '',
            'borrower'       => isset($_GET['borrower']) ? trim($_GET['borrower']) : '',
            'old_dev'       => isset($_GET['old_dev']) ? trim($_GET['old_dev']) : '',
        );

        if (isset($_POST['Devices'])) $search = $_POST['Devices'];
        $model->attributes = $search;

        $data = $model->getList($search);

        $this->render('index', array('model' => $model, 'search' => $search, 'list' => $data['list'], 'returnUrl' => $data['returnUrl'], 'multipage' => $data['multipage'], 'totalNums'=>$data['totalNums']));
    }

    // 设备列表
    public function actionCheck()
    {
        // 载入基础数据
        $model = new Devices();

        $search = array(
            'device_name' => isset($_GET['device_name']) ? trim($_GET['device_name']) : '',
            'status'      => isset($_GET['status']) ? trim($_GET['status']) : '',
            'model'      => isset($_GET['model']) ? trim($_GET['model']) : '',
            'platform'    => isset($_GET['platform']) ? trim($_GET['platform']) : '',
            'brand'       => isset($_GET['brand']) ? trim($_GET['brand']) : '',
            'category'    => isset($_GET['category']) ? trim($_GET['category']) : '',
            'version'     => isset($_GET['version']) ? trim($_GET['version']) : '',
            'other'       => isset($_GET['other']) ? trim($_GET['other']) : '',
            'borrower'       => isset($_GET['borrower']) ? trim($_GET['borrower']) : '',
            'old_dev'       => isset($_GET['old_dev']) ? trim($_GET['old_dev']) : '',
            'check_dev'       => isset($_GET['check_dev']) ? trim($_GET['check_dev']) : '0',
        );

        if (isset($_POST['Devices'])) $search = $_POST['Devices'];
        $model->attributes = $search;

        $data = $model->getList($search);

        $this->render('check', array('model' => $model, 'search' => $search, 'list' => $data['list'], 'returnUrl' => $data['returnUrl'], 'multipage' => $data['multipage'], 'totalNums'=>$data['totalNums']));
    }

    // 新增
    public function actionAdd()
    {
        $model = new Devices();

        if (isset($_POST['Devices']))
        {
            $model->attributes = $_POST['Devices'];

            if ($model->validate())
            {
                $result = $model->saveData();

                if ($result)
                {
                    // 添加系统日志
                    $title = '新增设备['.$model->device_name . ']';
                    $log = new AdminLog();
                    $log->saveData($title);

                    $this->ajaxMsg('设备添加成功！', 1,array('url'=>'devices/list'));
                }
                else
                    $this->ajaxMsg('设备添加失败：' . $this->getError($model),0);
            } else
                $this->ajaxMsg('设备添加失败：' . $this->getError($model),0);
        }

        $this->renderPartial('edit', array('model' => $model, 'returnUrl' => 'list'));
    }

    // 编辑
    public function actionEdit()
    {
        $returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'devices/list';

        $model = $this->loadModel();

        if (isset($_POST['Devices']))
        {
            $model_OLD = $model->attributes;
            $model->attributes = $_POST['Devices'];

            if ($model->validate())
            {
                $result = $model->saveData();

                if ($result)
                {
                    $logArr = array();
                    $logArr[] = array($model, $model_OLD, $model->attributes);
                    $LogContent = $this->getOperateLog($logArr);

                    // 添加系统日志
                    $title = '编辑设备';
                    $log = new AdminLog();
                    $log->saveData($title, $LogContent);

                    $this->ajaxMsg('设备保存成功！', 1,array('url'=>$returnUrl));
                }
                else
                    $this->ajaxMsg('设备保存失败：' . $this->getError($model),0);
            } else
                $this->ajaxMsg('设备保存失败：' . $this->getError($model),0);
        } else
        {
            $returnUrl = str_replace('/admin', '', $returnUrl);
        }

        $this->renderPartial('edit', array('model' => $model, 'returnUrl' => $returnUrl));
    }
    
	// 加载数据模型
	public function loadModel()
	{
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		
		$model = Devices::model()->findByPk($id);
		if (!$model) $this->ajaxMsg('设备数据不存在！',0,array('url'=>'list'));

		return $model;
	}

    // 申请/取消
    public function actionSetState()
    {
        $returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'devices/index';

        $status = isset($_GET['status']) ? (int) $_GET['status'] : 0;
        $borrower = isset($_GET['borrower']) ? $_GET['borrower'] : '';

        $borrower  = $status == 1 ? $borrower : '';
        $status = $status == 1 ? 1 : 0;

        $msgStr = $status == 1 ? '申请' : '取消申请';

        $model = $this->loadModel();
        $result = $model->SetStatus($status, $borrower);

        if ($result)
        {
            // 添加系统日志
            $title = $msgStr . '设备['.$model->device_name . ']';
            $log = new AdminLog();
            $log->saveData($title);

            $this->ajaxMsg('设备'.$msgStr.'成功！', 1,array('url'=>$returnUrl));
        }
        else
            $this->ajaxMsg($this->getError($model),0);
    }

    // 申请/取消
    public function actionSetStatus()
    {
        $returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'devices/list';

        $status = isset($_GET['status']) ? (int) $_GET['status'] : 0;
        $borrower = isset($_GET['borrower']) ? $_GET['borrower'] : '';

        $borrower  = $status == 2 ? $borrower : '';
        $status = $status == 2 ? 2 : 0;

        $msgStr = $status == 2 ? '借出' : '归还';

        $model = $this->loadModel();
        $result = $model->SetStatus($status, $borrower);

        if ($result)
        {
            // 添加系统日志
            $title = $msgStr . '设备['.$model->device_name . ']';
            $log = new AdminLog();
            $log->saveData($title);

            $this->ajaxMsg('设备'.$msgStr.'成功！', 1,array('url'=>$returnUrl));
        }
        else
            $this->ajaxMsg($this->getError($model),0);
    }

    // 删除
    public function actionDelete()
    {
        $returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'devices/list';

        $model = $this->loadModel();
        $result = $model->deleteData();

        if ($result)
        {
            // 添加系统日志
            $title = '删除设备['.$model->device_name . ']';
            $log = new AdminLog();
            $log->saveData($title);

            $this->ajaxMsg('设备删除成功！',1 ,array('url'=>$returnUrl));
        }
        else
            $this->ajaxMsg('设备删除失败：' . $this->getError($model),0);
    }

    // 重置
    public function actionReset()
    {
        $returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'devices/list';

        $model = $this->loadModel();
        $model->check_dev = '0';
        $result = $model->save();
        if ($result)
        {
            // 添加系统日志
            $title = '重置设备['.$model->device_name . ']';
            $log = new AdminLog();
            $log->saveData($title);

            $this->ajaxMsg('设备重置成功！',1 ,array('url'=>$returnUrl));
        }
        else
            $this->ajaxMsg('设备重置失败：' . $this->getError($model),0);
    }


    // 编辑
    public function actionHandle()
    {
        $returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'devices/check';

        $model = $this->loadModel();

        if (isset($_POST['Devices']))
        {
            $model_OLD = $model->attributes;
            $model->attributes = $_POST['Devices'];

            if ($model->validate())
            {
                $result = $model->handleData();

                if ($result)
                {
                    $logArr = array();
                    $logArr[] = array($model, $model_OLD, $model->attributes);
                    $LogContent = $this->getOperateLog($logArr);

                    // 添加系统日志
                    $title = '盘点设备';
                    $log = new AdminLog();
                    $log->saveData($title, $LogContent);

                    $this->ajaxMsg('设备盘点成功！', 1,array('url'=>$returnUrl));
                }
                else
                    $this->ajaxMsg('设备保存失败：' . $this->getError($model),0);
            } else
                $this->ajaxMsg('设备盘点失败：' . $this->getError($model),0);
        } else
        {
            $returnUrl = str_replace('/admin', '', $returnUrl);
        }

        $this->renderPartial('handle', array('model' => $model, 'returnUrl' => $returnUrl));
    }

}