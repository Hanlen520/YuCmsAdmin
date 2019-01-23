<?php
/**
 * 用户登录验证
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-06-18
 */
class UserIdentity extends CUserIdentity
{
	public $userInfoModel;
	
	public function authenticate()
	{
		$userModel = new AdminUser();
        $userInfo = $userModel->safeLogin($this->username, $this->password);
        $this->userInfoModel = $userInfo;
        
		if(is_null($userInfo))
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		else if($userInfo === -1)      // 账号IP受限
			$this->errorCode = 10;  
		else if($userInfo === -2)      // 账号被锁定
			$this->errorCode = 20;   
		else{        
            $this->errorCode=self::ERROR_NONE;
        }
		return !$this->errorCode;
	}
	
    public function getId()
    {
        return $this->userInfoModel->admin_user_id;
    }
}