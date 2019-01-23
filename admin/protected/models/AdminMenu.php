<?php
/**
 * 后台菜单模型
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class AdminMenu extends ActiveRecord
{
	public $op;
	public $pid_menu;
	public $menuList;
    public $itemNames;
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return '{{admin_menu}}';
    }

    public function rules()
    {
        return array(
            array('menu_name, pid', 'required', 'message' => '{attribute}必须填写！'),
			array('pid', 'numerical', 'integerOnly' => true, 'message' => '{attribute}必须为数字！'),
			array('menu_name', 'length', 'max' => 100, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过100个字符！'),
			array('itemname', 'checkItem'),
		);
    }

    public function relations()
    {
        return array();
    }
    
    public function attributeLabels()
	{
		return array(
			'menu_id'   => 'ID',
			'menu_name' => '菜单名称',
			'itemname'  => '操作名称',
			'url'       => 'URL地址',
			'sort'      => '排序',
			'pid'       => '上级菜单',
			'op'        => '操作',
        );
	}

    // 验证操作名称
    public function checkItem($attribute, $params)
    {
        $itemName = trim($this->itemname);

        if ($itemName)
        {
            $itemInfo = Authitem::model()->find('name=:name AND type=0', array(':name' => $itemName));

            if (!$itemInfo)
                $this->addError('itemname', '操作名称不存在！');
            else
            {
                $this->itemname = $itemInfo->name;
                $this->url = $itemInfo->url;
            }
        } else {
            $this->itemname = '';
            $this->url = '';
        }

        return true;
    }

    // 保存数据
	public function saveData()
	{
		if (!$this->menu_id)
		{
			$this->is_sys = 0;
			$this->sort = 0;
			$this->is_show = 1;
		}
		
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
	public function getList($search = array(), $pageSize = 20, $page = 1, $url = '/menu/index')
	{
		// 当前页数
		$page = isset($_GET['page']) ? (int) $_GET['page'] : $page;
		$pageSize = isset($_GET['pageSize']) ? (int) $_GET['pageSize'] : $pageSize;
    	
		$criteria = new CDbCriteria();
		$criteria->alias = "t";
		$criteria->select = "t.menu_id, t.menu_name, t.url, t.sort, t.pid, t.is_sys, am.menu_name AS pid_menu";
		$criteria->join = " LEFT JOIN " . AdminMenu::model()->tableName() . " am ON t.pid=am.menu_id";

		$criteria->compare('t.menu_name', $search['menu_name'], true);

		$criteria->order = 't.pid, t.sort';
		
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
	 * 获取所有菜单
	 */
	public function getMenu()
	{
		$firstUrl = '';
		
		$sqlString = "SELECT m.menu_id, m.menu_name, m.url, m.pid, m.sort FROM " . AdminMenu::model()->tableName() . " m WHERE m.is_show=1 ORDER BY m.sort,m.pid,m.menu_id";

		$model = Yii::app()->db->createCommand($sqlString)->queryAll();
		if (!$model) return array();
		
		$list = $child = array();
		foreach ($model as $val)
		{
			if ($val['pid'] > 0)
			{
				// 上级菜单
				$list[$val['pid']] = AdminMenu::model()->getParentMenu($val['pid']);
				
				$child[$val['pid']][] = $val;
			} else {
				$list[$val['menu_id']] = $val;
			}
		}
		
		$arr = array();
		foreach ($list as $k => $v)
		{
			$v['child'] = isset($child[$v['menu_id']]) ? $child[$v['menu_id']] : array();
			
			$arr[$v['sort'] . '_' . $v['menu_id']] = $v;
		}
		
		ksort($arr);
		
		// 获取第一个菜单的URL
		$firstUrl = AdminMenu::model()->getFirstUrl($arr);
		
		$data = array(
			'menu'     => $arr,
			'firstUrl' => $firstUrl,
		);
		
		return $data;
	}
	
	/**
	 * 获得上级菜单的记录
	 * @param int  $pid  上级菜单ID
	 */
	public function getParentMenu($pid)
	{
		if (!$pid) return array();
		
		$sqlString = "SELECT m.menu_id, m.menu_name, m.url, m.pid, m.sort FROM " . AdminMenu::model()->tableName() . " m WHERE m.menu_id='$pid' AND m.is_show=1 Order by m.pid, m.sort, m.menu_id";
				   
		$model = Yii::app()->db->createCommand($sqlString)->queryRow();
		return $model ? $model : array();
	}
	
	/**
	 * 删除菜单
	 * @param int or array $ids  菜单ID
	 */
	public function deleteData($ids)
	{
		if (!$ids) return false;
		
		if (is_array($ids)) $ids = implode(',', $ids);
		
		$reslut = AdminMenu::model()->deleteAll('menu_id IN(:menu_id) AND is_sys=0', array(':menu_id' => $ids));
		
		return $reslut ? true : false;
	}
	
	/**
	 * 获取第一个菜单的URL
	 * @param array $data  菜单数据
	 */
	public function getFirstUrl($data)
	{
		if (!$data || !is_array($data)) return '';
		
		$firstUrl = '';
		
		foreach ($data as $val)
		{
			if ($val['url']) 
			{
				$firstUrl = $val['url'];
				break;
			}
			
			if ($val['child'] && is_array($val['child'])) 
			{
				$firstUrl = AdminMenu::model()->getFirstUrl($val['child']);
				
				if ($firstUrl) break;
			};
		}
		
		return $firstUrl;
	}

    /**
     * 获取当前模块下的所有一级菜单
     * @param int  $module_id  模块ID
     */
    public function getMenuOptions()
    {
        $c = new CDbCriteria();
        $c->select = 'menu_id, menu_name';
        $c->compare('pid', 0);
        $c->order = 'sort ASC, menu_id ASC';

        $model = $this->findAll($c);
        if (!$model) return array();

        $list = array();
        foreach ($model as $val)
        {
            $list[$val['menu_id']] = $val['menu_name'];
        }

        return $list;
    }
}