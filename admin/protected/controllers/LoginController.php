<?php
/**
 * 后台用户登录控制器
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class LoginController extends Controller
{
	// 设置默认方法
	public $defaultAction = 'login';
	
	public function beforeAction($action)
	{
		return true;
	}
	
	public function actions()
    { 
    	return array( 
            // 显示验证码
        	'captcha' => array(
            	'class'           => 'CCaptchaAction',
            	'backColor'       => 0xFFFFFF, 
            	'maxLength'       => '4',       // 最多生成几个字符
            	'minLength'       => '4',       // 最少生成几个字符
            	'width'           => 60,
             	'height'          => 24,
    			'padding'         => 0,         //文字周边填充大小
            	'offset'          => -1,        //设置字符偏移量
            	'testLimit'       => 999,
    		    //'transparent'     => true,
            ), 
    	); 
    }
	
	public function actionIndex()
	{
		die(1);
	}
	
	// 用户登录
	public function actionLogin()
	{
		$this->layout='//layouts/login';

		$model = new AdminUser();
		
        if(isset($_POST['Login']))
        {
        	$login = $_POST['Login'];
        	
         	$scode = $this->createAction('captcha')->getVerifyCode();
         	if($scode != trim($login['scode']))
         		$this->redirectMsg('验证码错误！');
        	
            $model->_attributes = array(
                'admin_user_name'  => trim($login['user_name']),
                'password'         => trim($login['password']),
            );
            
            $result = $model->login();
            if($result === true)
            {
               $this->redirectMsg('登录成功！', array('border/index'));
               
            }
            else
            {
            	$msg = '用户名或密码错误！';
            	if ($result === 10)
            		$msg = '账号IP受限！';
            	else if ($result === 20)
            		$msg = '账号已被锁定！';
            	
            	$this->redirectMsg($msg);
            }
        }
        
        $this->render('login',array('model' => $model));
	}

	// 用户退出
	public function actionLogout()
	{
		$model = new AdminUser();
		$model->logout();
		
		$this->redirect(array('login/login'));
	}
}