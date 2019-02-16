<?php
/**
 * 后台用户模型
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class AdminUser extends ActiveRecord
{
	public $_userInfo;
	private $_identity;
	
	public $old_password;
	public $new_password;
	public $re_password;
	public $op;
	public $scode;
	public $depart_name;
    public $role;
	
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return '{{admin_user}}';
    }

    public function rules()
    {
        return array(
        	array('scode', 'captcha', 'on' => 'login', 'allowEmpty'=> !extension_loaded('gd')),
        	array('old_password, new_password, re_password', 'required', 'on' => 'editpassword', 'message' => '{attribute}必须填写！'),
        	array('new_password', 'compare', 'compareAttribute' => 're_password', 'on' => 'editpassword', 'message' => '两次密码输入不一致！'),
        	array('old_password', 'checkOldPassword', 'on' => 'editpassword'),
        	
			array('admin_user_name, password, re_password, real_name, mobile, depart_id', 'required', 'on' => 'adduser', 'message' => '{attribute}必须填写！'),
			array('re_password', 'compare', 'on' => 'adduser', 'compareAttribute' => 'password', 'message' => '两次密码输入不一致！'),
			array('admin_user_name, real_name, mobile', 'length', 'on' => 'adduser', 'max' => 50, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过50个字符！'),
			array('depart_id', 'numerical', 'on' => 'adduser', 'integerOnly' => true, 'min' => 1,  'tooSmall' => '{attribute}必须选择！'),
			array('mobile', 'checkmobile', 'on' => 'adduser'),
			
			array('admin_user_name, real_name, mobile, depart_id', 'required', 'on' => 'edituser', 'message' => '{attribute}必须填写！'),
			array('re_password', 'compare', 'on' => 'edituser', 'compareAttribute' => 'password', 'message' => '两次密码输入不一致！'),
			array('depart_id', 'numerical', 'on' => 'edituser', 'integerOnly' => true, 'min' => 1,  'tooSmall' => '{attribute}必须选择！'),
			array('mobile', 'checkmobile', 'on' => 'edituser'),
			array('password', 'safe', 'on' => 'edituser'),
			
			array('status', 'safe'),
		);
    }

    public function relations()
    {
        return array();
    }
    
    public function attributeLabels()
	{
		return array(
		    'admin_user_name'  => '用户名',
		    'password'         => '密码',
            'real_name'        => '真实姓名',
			'scode'            => '验证码',
			'old_password'     => '旧密码',
			'new_password'     => '新密码',
			're_password'      => '密码重复',
			'mobile'           => '手机号码',
			'status'           => '状态',
			'dateline'      => '添加时间',
			'login_times'      => '登录次数',
			'last_login_time'  => '最后登录时间',
			'last_login_ip'    => '最后登录IP',
			'depart_id'        => '所属部门',
			'op'               => '操作',
        );
	}
	
	public function safeAttributes() 
	{ 
		return array('scode');
	}
	
	// 验证旧密码
	public function checkOldPassword($attribute, $params)
	{
		$old_password = md5(md5($this->old_password));

		$model = $this->find("admin_user_id=:admin_user_id AND password=:password AND status=1", array(':admin_user_id' => $this->admin_user_id, ':password' => $old_password));
		if (!$model)
			$this->addError('old_password','旧密码错误！');
	}
	
	// 验证手机号
	public function checkmobile($attribute, $params)
	{
		if (!CMyfunc::checkMobile($this->mobile))
			$this->addError('mobile','手机号码有误！');
	}
	
    // 用户登录
    public function login()
    {
    	if($this->_identity===null)
		{
			$this->_identity= new UserIdentity($this->admin_user_name, $this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			$this->_userInfo = $this->_identity->userInfoModel;
			Yii::app()->user->login($this->_identity);
			return true;
		}
		else
			return $this->_identity->errorCode;
    }
    
    /**
     * 安全登录
     * @param string $userName     用户名
     * @param string $password     密码
     */
    public function safeLogin($userName, $password)
    {
    	if (!$userName && !$password) return null;
    	
    	// 上次登录时间和IP
    	$loginModel = AdminLogin::model()->find('admin_user_name=:name AND status=:status ORDER BY login_time DESC', array(':name'=> $userName, ':status'=> '1'));
    	
    	$lastTime =  isset($loginModel->login_time) ? $loginModel->login_time : '-';
    	$lastIP =  isset($loginModel->login_ip) ? $loginModel->login_ip : '-';
    	// 当前登录IP
    	$loginIP = Yii::app()->request->getUserHostAddress();
    	
    	// 用户登录日志数据初始化
    	$adminLogin = new AdminLogin();    	
    	$loginData = array(
    		'user_name'  => $userName,
    		'ip'         => $loginIP,
    		'status'     => 0,
    	);
    	
    	$password = md5(md5($password));
    	$userLogin = $this->find("admin_user_name=:admin_user_name AND password=:password AND status>=1", array(':admin_user_name' => $userName, ':password' => $password));
    	if(!$userLogin) 
    	{
    		// 保存登录日志
			$adminLogin->saveData($loginData);
    		return null;
    	}

    	// 是否账户被锁定
    	if ($userLogin->status == 2)
    	{
    		// 保存登录日志
			$adminLogin->saveData($loginData);	
			
			if ($userLogin->end_lock_time > time())    // 还在锁定时间内
				return -2;
    		else 
    		{
    			// 如超过锁定时间则自动解锁
    			AdminUser::model()->unLockUser($userName);
    		}
			
    	}
    	
    	// 如有设置绑定IP则需要验证IP
    	if (trim($userLogin->bindip))
    	{
    		if (trim($userLogin->bindip) != $loginIP)
    		{
    			// 保存登录日志
    			$adminLogin->saveData($loginData);
    			return -1;
    		}
    	}

        // 记录当前用户SESSION.
        Yii::app()->user->setState('__id', $userLogin->admin_user_id);             // 用户ID
        Yii::app()->user->setState('__name', $userLogin->real_name);               // 用户名
        
        Yii::app()->user->setState('USER_ID', $userLogin->admin_user_id);          // 用户ID
        Yii::app()->user->setState('USER_NAME', $userLogin->admin_user_name);      // 用户名        
        Yii::app()->user->setState('REAL_NAME', $userLogin->real_name);            // 真实姓名
        Yii::app()->user->setState('IP', $loginIP);                           	   // 登录IP
        Yii::app()->user->setState('SYS', $userLogin->is_admin);             	   // 是否超级管理员
        Yii::app()->user->setState('lastIP', $lastIP);                           	   // 登录IP
        Yii::app()->user->setState('lastTime', $lastTime);
        // 保存当前登录信息
        $sqlString = "UPDATE " . $this->tableName() . " SET last_login_ip='{$loginIP}', login_times=login_times+1, last_login_time='".time()."' WHERE admin_user_name='{$userLogin->admin_user_name}'";
        Yii::app()->db->createCommand($sqlString)->query();
        
        // 保存登录日志
        $loginData['status'] = 1;
        $adminLogin->saveData($loginData);
        
        return  $userLogin;
    }
    
    // 用户退出
    public function logout()
    {
 	    Yii::app()->user->logout();
    }
    
    /**
     * 锁定账户
     * @param string   $userName   用户名
     * @param int      $lockTime   锁定时间(秒)
     */
    public function lockUser($userName, $lockTime = 3600)
    {
    	if (!$userName || !$lockTime) return false;
    	
    	$lockTime += time();
    	
    	$sqlString = "UPDATE " . $this->tableName() . " SET status=2, end_lock_time='$lockTime' WHERE admin_user_name='$userName'";
    	$result = Yii::app()->db->createCommand($sqlString)->query();
    	
    	return $result ? true :false;
    }
    
    /**
     * 解锁账户
     * @param string  $userName  用户名
     */
    public function unLockUser($userName)
    {
    	if (!$userName) return false;
    	
    	$sqlString = "UPDATE " . $this->tableName() . " SET status=1, end_lock_time=null WHERE admin_user_name='$userName'";
    	$result = Yii::app()->db->createCommand($sqlString)->query();
    	
    	return $result ? true :false;
    }
    
	/**
     * 禁用/启用账户
     * @param int  $userID  用户ID
     * @param int  $status  状态
     */
    public function SetStatus($userID, $status)
    {
    	if (!$userID) return false;
    	
    	$sqlString = "UPDATE " . $this->tableName() . " SET status='$status' WHERE admin_user_id='$userID'";
    	$result = Yii::app()->db->createCommand($sqlString)->query();
    	
    	return $result ? true :false;
    }
    
    /**
     * 修改密码
     * @param int     $userID       用户ID
     * @param string  $newPassword  新的密码
     */
    public function updatePass($userID, $newPassword)
    {
    	if (!$userID || !$newPassword) return false;
    	
    	$newPassword = md5(md5($newPassword));
    	$result = $this->updateByPk($userID, array('password' => $newPassword));
    	
    	return $result ? true : false;
    }
    
    /**
     * 获取当前部门下的员工
     * @param int  $depart_id  部门ID：默认 1 客服部
     */
    public function getAllAdminUsers($depart_id = 1)
    {
    	$criteria = new CDbCriteria();
    	
    	$criteria->select = "admin_user_id, admin_user_name, real_name";
    	$criteria->compare('status', '>=1');
    	$criteria->compare('depart_id', $depart_id);
    	$criteria->order = "real_name ASC";
    	
    	$adminUsers = $this->findAll($criteria);
    	
    	return $adminUsers;
    }
    
    /**
     * 根据id获取人员信息
     */
    public function getAdminUserByPK($id)
    {
    	$criteria = new CDbCriteria();
    	$criteria->select = "admin_user_id, admin_user_name, real_name";
    	$adminUser = $this->findByPk($id);
    	return $adminUser;
    }
    
	/**
	 * 获取相关账号
	 */
	public function getRealNameByTime()
	{
		$criteria = new CDbCriteria();
		
		$criteria->alias = "u";
		$criteria->select = "u.admin_user_id, u.admin_user_name, u.real_name";
		
		$criteria->addCondition("u.status>=1");
		$criteria->order = 'u.admin_user_id ASC';
		
		$model = $this->findAll($criteria);
		
		$list = array();
		foreach( $model as $val)
		{
			$list[$val['admin_user_id']] = $val['real_name'];
		}
		
		return $list;
	}
	
	/**
	 * 过滤操作更改项
	 * @param array  $data  已更改的数据项及值
	 */
	public function filterOperateLog($data)
	{
		if (isset($data['password'])) unset($data['password']);
		
		return $data;
	}
	
	/**
	 * 设置日志相应的值
	 * @param string  $key    键
	 * @param string  $value  值
	 */
	public function setOperateValue($key, $value)
	{
		if (!$key) return false;
		
		switch ($key)
		{
			// 部门
			case 'depart_id':
				$depart = Department::model()->findByPk($value);
				$value = $depart ? $depart['depart_name'] : $value;
				break;
		}
		
		return $value;
	}
	
	// 保存数据
	public function saveData($model)
	{
		if (!$model)
		{
			$this->addError('admin_user_id', '参数错误！');
			return false;
		}

		$transaction = Yii::app()->db->beginTransaction();
		try
		{
			if (!$this->admin_user_id)
			{
				$this->dateline = time();
				$this->status = 1;

				$total = $this->count('admin_user_name=:admin_user_name', array(':admin_user_name' => $this->admin_user_name));
			} else {
				$total = $this->count('admin_user_name=:admin_user_name AND admin_user_id!=:admin_user_id', array(':admin_user_name' => $this->admin_user_name, ':admin_user_id' => $this->admin_user_id));
			}

			if ($this->password)
				$this->password = md5(md5($this->password));
			else
				unset($this->password);

			if ($total) throw new Exception('用户名已存在！');

			$result = $this->save(false);
			if (!$result) throw new Exception('用户保存失败');

            $model->userid = $this->admin_user_id;
            $result =$model->saveData();
            if (!$result) throw new Exception('用户角色保存失败！');

			$transaction->commit();
			return true;
		} catch (Exception $e)
		{
			$transaction->rollBack();
			$this->addError('admin_user_id', $e->getMessage());
			return false;
		}
	}
	
	/**
     * 列表
     * @param array   $search       搜索条件
     * @param int     $pageSize     每页记录数
     * @param int     $page         当前页数
     * @param string  $url          URL地址
     */
	public function getList($search = array(), $pageSize = 20, $page = 1, $url = '/user/index')
	{
		// 当前页数
		$page = isset($_GET['page']) ? (int) $_GET['page'] : $page;
		$pageSize = isset($_GET['pageSize']) ? (int) $_GET['pageSize'] : $pageSize;
    	
		$criteria = new CDbCriteria();
		$criteria->alias = "t";
		$criteria->select = "d.depart_name,t.admin_user_id, t.admin_user_name, t.real_name, t.mobile, t.is_admin, t.status, t.dateline, t.login_times, t.last_login_time, t.last_login_ip";
		$criteria->join = "LEFT JOIN ".Department::model()->tableName(). " d on d.depart_id=t.depart_id ";
		$criteria->compare('t.admin_user_name', $search['admin_user_name']);
		$criteria->addSearchCondition('t.real_name', $search['real_name']);
		$criteria->compare('t.mobile', $search['mobile']);
		$criteria->compare('t.status', $search['status']);
		
		$criteria->order = 't.admin_user_id ASC';
		
		// 总记录数
		$totalNums = $this->count($criteria);
		$pages = new CPagination($totalNums);
		
		// 每页记录数
		$pages->pageSize = $pageSize;
		
		// 总页数
		$totalPage = $pages->getPageCount();
		$page = $page < 1 ? 1 : ($page > $totalPage ? $totalPage : $page);
		$pages->applyLimit($criteria);
		$model = $this->findAll($criteria);
		
		$list = array();
		foreach( $model as $val)
		{
			$list[] = $val;
		}
		
		// 拼接搜索条件
		$url = CMyfunc::mergeSearchUrl($url, $search);
		
		$data = array(
    	    'list'       => $list,
    	    'multipage'  => CMyfunc::pagination($totalNums, $pageSize, $page, $url),
            'totalNums'  => $totalNums,
            'returnUrl'  => $url . '/page/' . $page,
    	);
    	
    	return $data;
	}
	
 	/**
     * 删除用户
     * @param array or int $ids
     */
    public function deleteData($ids)
    {
    	if (!$ids) return false;
		
		if (is_array($ids)) $ids = implode(',', $ids);
		
		$result = AdminUser::model()->deleteAll('admin_user_id IN(:admin_user_id)', array(':admin_user_id' => $ids));
		
		if ($result)
		{
			// 删除详情
			//AdminUserInfo::model()->deleteAll('admin_user_id IN(:admin_user_id)', array(':admin_user_id' => $ids));
			
			// 删除用户分配的角色
			AuthAssignment::model()->deleteAll('userid IN(:userid)', array(':userid' => $ids));
			
			return true;
		} else 
			return false;
    }
}