<?php
/**
 * 后台首页控制器
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class IndexController extends Controller
{
	// 首页
	public function actionIndex()
	{
		$s = function_exists('gd_info') ? gd_info() : '<span class="font_1"><strong>Not Support</strong></span>';
		
		if (function_exists('memory_get_usage'))
			$memory = round(memory_get_usage()/1024,2);    // KB
		
		// 登录次数
		$count = AdminLogin::model()->count('admin_user_name =:name AND status =:status' ,array(':name'=>Yii::app()->user->getState('USER_NAME'),'status'=>'1'));
		
		// 系统信息
		$server = array(
			'os'         	   => php_uname('v'),
			'ip'         	   => $_SERVER['REMOTE_ADDR'],
			'servename'        => $_SERVER['SERVER_NAME'],
			'port'        	   => $_SERVER['REMOTE_PORT'],
			'document_root'    => $_SERVER['DOCUMENT_ROOT'],
			'time'             => date('Y-m-d H:i:s'),
			'software'         => $_SERVER['SERVER_SOFTWARE'],
			'phpver'           => phpversion(),
			'mysqlver'         => CMyfunc::getMysqlVersion(),
			'upfile'           => (ini_get('file_uploads')) ? '允许 ' . ini_get('upload_max_filesize') : '关闭',
			'register_globals' => (ini_get('register_globals')) ? '允许' : '关闭',
			'safe_mode'        => (ini_get('safe_mode')) ? '允许' : '关闭',
			'gd'               => is_array($s) ? ($s['GD Version']) : $s,
			'memory'           => $memory,
			'count'            => $count,
		);
		
		$this->render('index', array('server' => $server));
	}
	
	// 修改密码
	public function actionEditPass()
	{
		$model = $this->loadModel();
		$model->scenario = 'editpassword';
		
		if(isset($_POST['AdminUser']))
		{
			$adminUser = $_POST['AdminUser'];
			$model->attributes = $adminUser;
			
			if ($model->validate())
			{
				$model->password = md5(md5($adminUser['new_password']));
				$result = $model->save(false);
				
				if ($result)
				{
					// 添加系统日志
					$title = '用户修改密码['.$model->admin_user_name.']';
					$log = new AdminLog();
					$log->saveData($title);

                    $this->ajaxMsg('密码修改成功！', 1, array('url' => 'index'));
				}
				else 
					$this->ajaxMsg('密码修改失败：'. $this->getError($model),0);

			} else
                $this->ajaxMsg('密码修改失败：'. $this->getError($model),0);
		}
		
		$this->renderPartial('password', array('model' => $model));
	}
	
	// 加载主建模型数据
	public function loadModel()
	{
		$uid = Yii::app()->user->getState('USER_ID');
		$model = AdminUser::model()->findByPk((int) $uid);

		if(!$model) $this->redirectMsg('参数错误！');
		
		return $model;
	}
}