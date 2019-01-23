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
		return true;
	}
	
    public function getId()
    {
        return $this->userInfoModel->admin_user_id;
    }
}