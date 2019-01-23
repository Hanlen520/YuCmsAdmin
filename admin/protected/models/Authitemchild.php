<?php
/**
 * 角色/任务/权限关系模型
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class Authitemchild extends ActiveRecord
{
	public $roleName;
	public $taskName;
	public $purview;
		
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'authitemchild';
    }

    public function rules()
    {
        return array(
			array('roleName', 'checkRoleName'),
			array('purview', 'checkPurview'),
		);
    }

    public function relations()
    {
        return array();
    }
    
    public function attributeLabels()
	{
		return array(
			'purview'      => '分配权限',
        );
	}
	
	// 验证角色
	public function checkRoleName($attribute, $params)
	{
		$roleName = trim($this->roleName);
		$role = AdminRole::model()->find("name=:name AND type=2", array(':name' => $roleName));
		
		if (!$role)
		{
			$this->addError('roleName', '角色不存在！');
			return false;
		}
		
		$sqlString = "SELECT a.name FROM " . Authitemchild::model()->tableName() . " ac INNER JOIN " . Authitem::model()->tableName() . " a ON ac.child=a.name AND ac.parent='$roleName'"
					. " WHERE a.type=1";
		
		$task = Yii::app()->db->createCommand($sqlString)->queryRow();
		if (!$task)
		{
			$this->addError('taskName', '该角色下不存在任务！');
			return false;
		}
		
		$this->taskName = $task['name'];
		
		return true;
	}
	
	// 验证权限
	public function checkPurview($attribute, $params)
	{
		$data = array();
		
		$purview = $this->purview;
		if (!$purview)
		{
			$this->addError('purview', '没有分配权限！');
			return false;
		}
		
		foreach ($purview as $v)
		{
			$v = trim($v);
			
			$total = Authitem::model()->count('name=:name AND type=0', array(':name' => $v));
			if (!$total)
			{
				$this->addError('purview', '操作权限不存在！');
				return false;
			}
			
			$data[] = $v;
		}
		
		$this->purview = $data;
		
		return true;
	}
	
	/**
	 * 保存数据
	 * @param string  $parent  父级
	 * @param string  $child   子级
	 */
	public function saveData($parent, $child)
	{
		if (!$parent || !$child) return false;
		
		$this->parent = $parent;
		$this->child = $child;
		
		$result = $this->save(false);
		
		return $result ? true : false;
	}
	
	/**
	 * 设置权限关系
	 */
	public function setPurview()
	{
		$transaction = Yii::app()->db->beginTransaction();
		try
		{
			// 删除当前任务下的所有旧的权限操作
			Authitemchild::model()->deleteAll("parent=:parent", array(':parent' => $this->taskName));
			
			foreach ($this->purview as $val)
			{
				$a = new Authitemchild();
				
				$a->parent = $this->taskName;
				$a->child  = $val;
				
				$result = $a->save(false);
				if (!$result) throw new Exception("ERROR");
			}
			
			$transaction->commit();
			
			return true;
		} catch (Exception $e)
		{
			$transaction->rollBack();
			return false;
		}
	}
}