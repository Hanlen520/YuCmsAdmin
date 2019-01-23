<?php
/**
 * 后台日志控制器
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class LogController extends Controller
{
	// 列表
	public function actionIndex()
	{
		$model = new AdminLog();

		$search = array(
			'title'                 => isset($_GET['title']) ? trim($_GET['title']) : '',
		    'admin_user_name'       => isset($_GET['admin_user_name']) ? trim($_GET['admin_user_name']) : '',
		    'startDate'             => isset($_GET['startDate']) ? trim($_GET['startDate']) : '',
		    'endDate'               => isset($_GET['endDate']) ? trim($_GET['endDate']) : '',
		    'ip'                    => isset($_GET['ip']) ? trim($_GET['ip']) : '',
		);
		
		if (isset($_POST['AdminLog'])) $search = $_POST['AdminLog'];
		$model->attributes = $search;

		if (($search['startDate'] && !strtotime($search['startDate'])) || ($search['endDate'] && !strtotime($search['endDate']))) 
		    $this->ajaxMsg('操作时间格式不对！',0);
		    
		if ($search['startDate'] && $search['endDate'] && $search['startDate'] > $search['endDate']) 
		    $this->ajaxMsg('操作时间格式不对！',0);
		    
		$data = AdminLog::model()->getList($search);

		$this->render('list', array('model' => $model, 'search' => $search, 'list' => $data['list'], 'totalNums'=>$data['totalNums'],'returnUrl' => $data['returnUrl'], 'multipage' => $data['multipage']));
	}
	
	// 查看
	public function actionView()
	{
		$returnUrl = isset($_POST['returnUrl']) && trim($_POST['returnUrl']) ? $_POST['returnUrl'] : 'index';
		$returnUrl = str_replace('/admin', '', $returnUrl);
		
		$model = $this->loadModel();
		
		$this->renderPartial('view', array('model' => $model, 'returnUrl' => $returnUrl));
	}
	
	// 删除
	public function actionDelete()
	{
		$model = new AdminLog();
		
		$ids = isset($_POST['ids']) ? $_POST['ids'] : (isset($_GET['id']) ? (int) $_GET['id'] : 0);
		if (!$ids) $this->redirectMsg('没有可操作的对象！');
		
		$result = $model->deleteLog($ids);
		
		if ($result)
		    $this->ajaxMsg('日志删除成功！',1,array('url'=>'index'));
		else
		    $this->ajaxMsg('日志删除失败！',0);
	}
	
	// 加载模型数据
	public function loadModel()
	{
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		
		$model = AdminLog::model()->findByPk($id);
		if(!$model) $this->redirectMsg('日志记录不存在！');
		
		return $model;
	}
}