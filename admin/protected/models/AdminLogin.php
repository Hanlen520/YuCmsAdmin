<?php
/**
 * 后台用户登录日志 模型
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class AdminLogin extends ActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return '{{admin_login}}';
    }

    public function rules()
    {
        return array(

		);
    }

    public function relations()
    {
        return array();
    }
    
    public function attributeLabels()
	{
		return array(

        );
	}
	
	/**
	 * 保存登录日志
	 * @param array  $data  登录日志数据
	 */
	public function saveData($data)
	{
		if (!$data) return false;
		
		$user_name = isset($data['user_name']) ? $data['user_name'] : '';
		$ip = isset($data['ip']) ? $data['ip'] : '';
		$status = isset($data['status']) ? $data['status'] : 0;
		
		if (!$user_name && !$ip && !$status) return false;
		
		$this->admin_user_name   = $user_name;
		$this->login_ip          = $ip;
		$this->login_time        = time();
		$this->status            = $status;
		
		$result = $this->save(false);
		
		// 验证当天是否有登录失败三次,如有则需要锁定账户1小时
		$total = AdminLogin::model()->getErrorLoginCount($user_name);
		if ($total == 3)
		{
			AdminUser::model()->lockUser($user_name, 3600);
		}
		
		return $result ? true : false;
	}
	
	/**
	 * 根据用户名获得登录失败次数(如果开始日期与结束日期都为空则为当天)
	 * @param string  $userName     用户名
	 * @param string  $startDate    开始日期
	 * @param string  $endDate      结束日期
	 */
	public function getErrorLoginCount($userName, $startDate = '', $endDate = '')
	{
		if (!$userName) return false;
		
		$sqlString = "SELECT COUNT(login_id) AS TOTAL FROM " . $this->tableName() . " WHERE admin_user_name='{$userName}' AND status=0";
		
		if (!$startDate && !$endDate)     // 当天
		{
			$startDate = strtotime(date('Y-m-d'));
			$endDate   = strtotime(date('Y-m-d 23:59:59'));
			$sqlString .= " AND login_time>='$startDate' AND login_time<='$endDate'";
		} else if ($startDate)
		{
			$startDate = strtotime($startDate);
			$sqlString .= " AND login_time>='$startDate'";
		} else if ($endDate)
		{
			$endDate   = strtotime($endDate . ' 23:59:59');
			$sqlString .= " AND login_time<='$endDate'";
		}
		
		$row = Yii::app()->db->createCommand($sqlString)->queryRow();
		
		return $row['TOTAL'];
	}
    
}