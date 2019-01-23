<?php
/**
 * 用户授权模型
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class AuthAssignment extends ActiveRecord
{
	//角色数组
	public $role;
	
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'authassignment';
    }

    public function rules()
    {
        return array(
			array('userid', 'safe'),
			array('role','checkEmpty'),
		);
    }

    public function relations()
    {
        return array();
    }
    
    public function attributeLabels()
	{
		return array(
			'role' => '角色',
        );
	}
	
	/**
	 * 角色是否被用户授权
	 * @param string  $itemName  角色代码
	 */
	public function isAssign($itemName)
	{
		if (!$itemName) return false;
		
		$aa = new AuthAssignment();
		
		$total = $aa->count('itemname=:itemname', array(':itemname' => $itemName));
		
		return $total > 0 ? true : false;
	}
	
	/**
	 * 获取每个用户当前的角色
	 * @param int $id
	 */
	public function getRole($id)
	{
		$criteria = new CDbCriteria();
		$criteria->select = "itemname";
		$data = $this->findByAttributes(array("userid" => $id), $criteria);
		return !empty($data) ? $data['itemname'] : false;
	}
	
	/**
	 * 保存记录
	 */
	public function saveData()
	{
		if (isset($this->userid))
			$this->deleteAllByAttributes(array('userid' => $this->userid));

        $authAssignment = new AuthAssignment();
        $authAssignment->userid = $this->userid;
        $authAssignment->itemname = $this->role;
        $result = $authAssignment->save(false);
        if(!$result)
        {
            return false;
        }

		return true;
	}
	
	/**
	 * 验证是否选择角色
	 */
	public function checkEmpty($attribute, $params)
	{
		if (empty($this->role))
		{
			$this->addError('role', '没有选择角色！');
			return false;
		}
		
		return true;
	}
}