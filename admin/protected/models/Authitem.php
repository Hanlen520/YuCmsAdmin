<?php
/**
 * 权限操作模型
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class Authitem extends ActiveRecord
{
	public $module_name;
	public $itemList;
	
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
            array('name, description, url', 'required', 'message' => '{attribute}必须填写！'),
			array('name', 'length', 'max' => 64, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过64个字符！'),
			array('url', 'length', 'max' => 200, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过200个字符！'),
			array('name', 'checkItemName'),
		);
    }

    public function relations()
    {
        return array();
    }
    
    public function attributeLabels()
	{
		return array(
			'name'         => '权限名称',
			'description'  => '权限描述',
        );
	}
	
	// 验证权限
	public function checkItemName($attribute, $params)
	{
		$total = $this->count('name=:name', array(':name' => $this->name));
			
		if ($total)
		{
			$this->addError('name', '权限已存在！');
			return false;
		}
		
		return true;
	}
	
	// 验证模块
//	public function checkModuleName($attribute, $params)
//	{
//		$moduleInfo = AdminModule::model()->find('module_id=:module_id', array(':module_id' => $this->module_id));
//
//		if (!$moduleInfo)
//		{
//			$this->addError('module_id', '模块不存在！');
//			return false;
//		}
//
//		$this->module_name = $moduleInfo->module_name;
//
//		return true;
//	}
	
	// 批量验证
	public function validateData($model, $data)
	{
		if (!$model || !$data ||!isset($data['name']) || !$data['name'])
		{
            $this->addError('name', '请选择要授权的方法！');
            return false;
		}
		
		$this->itemList = array();
		
		$validate = true;		
		foreach ($data['name'] as $key => $val)
		{
			$arr = array(
				'name'         => trim($val),
				'description'  => isset($data['description'][$key]) ? $data['description'][$key] : trim($val),
				'type'         => 1,
				'data'         => 'N;',
				'url'          => isset($data['url'][$key]) ? $data['url'][$key] : '',
			);
			
			$model->attributes = $arr;
			
			if (!$model->validate())
			{
				$validate = false;
				break;
			}
			
			$this->itemList[$arr['name']] = $arr;
		}
		
		return $validate;
	}
	
	// 保存数据
	public function saveData()
	{
		if (!$this->itemList || !is_array($this->itemList)) return false;
		
		foreach ($this->itemList as $val)
		{
			$ai = new Authitem();
			$ai->attributes = $val;
			
			$result = $ai->save(false);
			
			if (!$result) return false;
		}
		
		return true;
	}
	
	/**
	 * 获取当前模块下的所有权限操作
	 * @param int  $module_id  模块ID
	 */
	public function getOperateOptions()
	{
		//if (!$module_id) return array();
		
		$c = new CDbCriteria();
		$c->select = 'name, url, description';
//		$c->compare('module_id', $module_id);
		$c->compare('type', 0);
		$c->order = 'name ASC';
		
		$model = $this->findAll($c);
		if (!$model) return array();
		
		$list = array();
		foreach ($model as $val)
		{
			$list[$val['name']] = $val['description'];
		}
		
		return $list;
	}
	
	/**
	 * 获得当前用户的所有权限
	 */
	public function getAllItem()
	{
		$userID = Yii::app()->user->getState('USER_ID');  // 用户ID
		
		// 获取角色等
		$a = new AuthAssignment();
		$iRows = $a->findAll('userid=:userid', array(':userid' => $userID));
		if (!$iRows) return array();
		
		$list = array();
		foreach ($iRows as $v)
		{
			$list[]  = $v['itemname'];
		}
		$itemName = "'" . implode("','", $list) . "'";
		
		$sqlString = "SELECT a.name FROM " . Authitem::model()->tableName() . " a INNER JOIN ".Authitemchild::model()->tableName()." ac ON a.name=ac.child WHERE ac.parent IN($itemName)";
		$model = Yii::app()->db->createCommand($sqlString)->queryAll();
		
		foreach ($model as $val)
		{
			$list[] = $val['name'];
			
			// 获得子项目
			$child = Authitem::model()->getMenuChild($val['name']);
			
			foreach ($child as $v)
			{
				$list[] = $v;
			}			
		}
		
		return $list;
	}
	
	/**
	 * 获得子项目
	 * @param string  $parent    上级
	 */
	public function getMenuChild($parent)
	{
		if (!$parent) return array();
		
		$sqlString = "SELECT a.name FROM " . Authitem::model()->tableName() . " a INNER JOIN ".Authitemchild::model()->tableName()." ac ON a.name=ac.child WHERE ac.parent='$parent'";
		$model = Yii::app()->db->createCommand($sqlString)->queryAll();
		if (!$model) return array();
		
		$list = array();
		foreach ($model as $val)
		{
			$list[] = $val['name'];
			
			// 获得子项目
			$child = Authitem::model()->getMenuChild($val['name']);
			
			foreach ($child as $v)
			{
				$list[] = $v;
			}
		}
		
		return $list;
	}
	
	/**
	 * 获得所有模块下的所有权限
	 */
	public function getAllPurview()
	{
		$c = new CDbCriteria();
		
		$c->alias = 't';
		$c->select = 't.name, t.description';
		$c->compare('t.type', 0);
		$c->order = 't.name ASC';
		
		$model = $this->findAll($c);
		if (!$model) return array();

        $list = array();
        foreach ($model as $val)
        {
            $list[$val['name']] = $val['description'];
        }

		return $list;
	}
	
	/**
	 * 获得角色列表
	 */
	public function getRoles()
	{
		$criteria = new CDbCriteria();
		$criteria->select = "name, description";
		$result = $this->findAllByAttributes(array("type" => 2), $criteria);
		
		return !empty($result) ? $result : false;
	}
	
	/**
	 * 当前角色下的所有操作权限
	 * @param string  $name   角色名称
	 */
	public function getAllPurviewByRole($name)
	{
		if (!$name) return array();
		
		$sqlString = "SELECT a.name, a.type FROM " . Authitem::model()->tableName() . " a INNER JOIN " . Authitemchild::model()->tableName() . " ac ON a.name=ac.child"
				   . " WHERE ac.parent='$name'";
				   
		$model = Yii::app()->db->createCommand($sqlString)->queryAll();
		if (!$model) return array();
		
		$list = array();
		foreach ($model as $val)
		{
			if ($val['type'] == 0) 
				$list[] = $val['name'];
			else 
			{
				$temp = Authitem::model()->getAllPurviewByRole($val['name']);
				
				$list = array_merge($list, $temp);
			}
		}
		
		return $list;
	}
	
	/**
	 * 获取操作权限的描述
	 * @param array  $name  操作权限
	 */
	public function getDescription($name)
	{
		if (!$name) return array();
		
		$name = "'" .  (is_array($name) ? implode("','", $name)  : $name) . "'";
		
		$sqlString = "SELECT name, description, module_id FROM " . Authitem::model()->tableName() . " WHERE name IN($name)";
		$model = Yii::app()->db->createCommand($sqlString)->queryAll();
		if (!$model) return array();
		
		$list = array();
		foreach ($model as $val)
		{
			$module_id = $val['module_id'];
			$list[] = $val;
		}
		
		$data = array(
			'description' => $list,
			'module_id'   => $module_id,
		);
		
		return $data;
	}
	
	/**
	 * 删除操作权限
	 * @param array  $name  操作权限
	 */
	public function deleteData($name)
	{
		if (!$name) return false;
		$name = "'" .  (is_array($name) ? implode("','", $name)  : $name) . "'";
		// 检查操作权限是否被应用
//		$isApply = $this->isApply($name);
//		if ($isApply)
//		{
//			$this->addError('name', '相关权限已被应用，不能删除！');
//			return false;
//		}
		
		$sqlString = "DELETE FROM " . Authitem::model()->tableName() . " WHERE name IN($name)";
		$result = Yii::app()->db->createCommand($sqlString)->query();
		
		return $result ? true : false;
	}
	
	/**
	 * 操作权限是否被应用
	 * @param array  $name  操作权限
	 */
	public function isApply($name)
	{
		if (!$name) return false;
		
		$sqlString1 = "SELECT COUNT(*) AS total FROM " . AdminMenu::model()->tableName() . " WHERE itemname IN($name)";
		$row1 = Yii::app()->db->createCommand($sqlString1)->queryRow();
		
		$sqlString2 = "SELECT COUNT(*) AS total FROM " . AuthAssignment::model()->tableName() . " WHERE itemname IN($name)";
		$row2 = Yii::app()->db->createCommand($sqlString2)->queryRow();
		
		$sqlString3 = "SELECT COUNT(*) AS total FROM " . Authitemchild::model()->tableName() . " WHERE parent IN($name) OR child IN($name)";
		$row3 = Yii::app()->db->createCommand($sqlString3)->queryRow();
		
		if ($row1['total'] > 0 || $row2['total'] > 0 || $row3['total'] > 0)
			return true;
		else 
			return false;
	}
	
	/**
	 * 根据操作权限获得模块名称
	 * @param string  $itemName  操作权限
	 */
	public function getModuleByItem($itemName)
	{
		if (!$itemName) return false;
		
		$sqlString = "SELECT m.module_name FROM " . Authitem::model()->tableName() . " t INNER JOIN " . AdminModule::model()->tableName() . " m ON t.module_id=m.module_id WHERE t.name='$itemName'";
		$row = Yii::app()->db->createCommand($sqlString)->queryRow();
		if (!$row) return false;
		
		return $row['module_name'];
	}
	
	/**
	 * 根据操作权限获得面包屑
	 * @param string  $module      模块
	 * @param string  $controller  控制器
	 * @param string  $itemName    操作权限
	 */	
	public function getBreadcrumbs($module, $controller, $itemName)
	{
		if (!$itemName || !$controller) return false;
		
		$links = $arr = array();
		
		if ($module) $arr[] = $module;
		if ($controller) $arr[] = strtolower($controller);
		$search = implode('/', $arr) . '/';
		
		$itemInfo = Authitem::model()->findByPk($itemName);
		if (!$itemInfo) return false;
		
		$sqlString = "SELECT pid, menu_name FROM " . AdminMenu::model()->tableName() . " WHERE itemname='$itemName'";
		$menu = Yii::app()->db->createCommand($sqlString)->queryRow();
		
		$menuName = '';
		if (!$menu)
		{
			$sqlString = "SELECT pid, menu_name FROM " . AdminMenu::model()->tableName() . " WHERE url LIKE '{$search}%'";
			$menu = Yii::app()->db->createCommand($sqlString)->queryRow();
		} else 
			$menuName = $menu['menu_name'];
		
		if ($menu)
		{
			if ($menu['pid'])
			{
				$tMenu = AdminMenu::model()->findByPk($menu['pid']);
				if ($tMenu) $links[] = $tMenu->menu_name;
			} else 
				$links[] = $menu['menu_name'];
		}

		$description = preg_replace("/\((.+?)\)/s", '', $itemInfo->description);
		$links[] = $menuName ? $menuName : $description;
		
		return $links;
	}
}