<?php
/**
 * 角色模型
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class AdminRole extends ActiveRecord
{
	public $op;
	const  RoleSuffix = 'ROLE';     // 角色代码的后缀
	
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'authitem';
    }

    public function rules()
    {
        return array(
			array('name, description', 'required', 'message' => '{attribute}必须填写！'),
			array('name', 'length', 'max' => 50, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过50个字符！'),
			array('name', 'match', 'pattern' => '/^[a-zA-Z]+$/', 'message' => '{attribute}必须为字母！'),
			array('name', 'checkRoleName'),
		);
    }

    public function relations()
    {
        return array();
    }
    
    public function attributeLabels()
	{
		return array(
			'name'         => '角色代码',
			'description'  => '角色描述',
			'op'           => '操作',
        );
	}
	
	public function checkRoleName($attribute, $params)
	{
		$this->name = trim($this->name);
		$itemName = $this->name . self::RoleSuffix;
		
		$total = $this->count('name=:name', array(':name' => $itemName));
			
		if ($total)
		{
			$this->addError('name', '角色代码已存在！');
			return false;
		}
		
		return true;
	}
	
	// 保存数据
	public function saveData()
	{
		$isNew = false;
		
		if ($this->isNewRecord)
		{
			$this->name .= self::RoleSuffix;    // 添加后缀
			$total = $this->count('name=:name', array(':name' => $this->name));
			
			if ($total)
			{
				$this->addError('name', '角色代码已存在！');
				return false;
			}
			
			$isNew = true;
		}
		
		$this->type = 2; // 角色
		
		$result = $this->save(false);
		
		return $result ? true : false;
	}
	
	/**
     * 列表
     * @param array   $search       搜索条件
     * @param int     $pageSize     每页记录数
     * @param int     $page         当前页数
     * @param string  $url          URL地址
     */
	public function getList($search = array(), $pageSize = 20, $page = 1, $url = '/role/index')
	{
		// 当前页数
		$page = isset($_GET['page']) ? (int) $_GET['page'] : $page;
		$pageSize = isset($_GET['pageSize']) ? (int) $_GET['pageSize'] : $pageSize;
    	
		$criteria = new CDbCriteria();
		$criteria->alias = "t";
		$criteria->select = "t.name, t.description";
		$criteria->compare('t.type', 2);
		$criteria->compare('t.name', $search['name']);
		$criteria->compare('t.description', $search['description'], true);
		$criteria->order = 't.name';
		
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
	 * 删除角色
	 * @param string  $name  角色代码
	 */
	public function deleteData($name)
	{
		if (!$name) return false;
		
		// 该角色是否已授权
		$isAssign = AuthAssignment::model()->isAssign($name);
		if ($isAssign)
		{
			$this->addError('name', '该角色已被授权，不能被删除！');
			return false;
		}
		
		// 批处理
		$transaction = Yii::app()->db->beginTransaction();
		try
		{
			$result = AdminRole::model()->deleteChild($name);
			if (!$result) throw new Exception("ERROR");
			
			$transaction->commit();
			
			return true;
		} catch (Exception $e)
		{
			$transaction->rollBack();
			return false;
		}
	}
	
	/**
	 * 逐级删除角色、任务及关系等
	 * @param string  $name  代码
	 */
	public function deleteChild($name)
	{
		if (!$name) return false;
		
		$sqlString = "SELECT parent, child FROM " . Authitemchild::model()->tableName() . " WHERE parent='$name'";
		$rows = Yii::app()->db->createCommand($sqlString)->queryAll();
		if (!$rows) return true;
		
		foreach ($rows as $val)
		{
			// 删除子项目
			$sqlString = "DELETE FROM " . Authitem::model()->tableName() . " WHERE name='{$val['child']}' AND type!=0";
			$result = Yii::app()->db->createCommand($sqlString)->query();
			if (!$result) return false;
			
			// 删除子级
			AdminRole::model()->deleteChild($val['child']);
			
		}
		
		// 删除项目
		$sqlString = "DELETE FROM " . Authitem::model()->tableName() . " WHERE name='{$val['parent']}' AND type!=0";
		$result = Yii::app()->db->createCommand($sqlString)->query();
		if (!$result) return false;
		
		return true;
	}
}