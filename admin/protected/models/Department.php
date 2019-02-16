<?php
/**
 * 部门模型
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class Department extends ActiveRecord
{
	public $op;

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return '{{department}}';
    }

    public function rules()
    {
        return array(
        	array('depart_name', 'required', 'message' => '{attribute}必须填写！'),
			array('depart_name', 'length', 'max' => 100, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过100个字符！'),
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
			'depart_id'    => '部门ID',
			'depart_name'  => '部门名称',
			'sort'         => '排序',
			'status'       => '状态',
			'op'           => '操作',
        );
	}
	
	// 获取有效部门数据
	public function getDepartment()
	{
		$sqlString = "SELECT depart_id, depart_name FROM " . $this->tableName() . " WHERE status=1 ORDER BY sort, depart_id";
		$model = Yii::app()->db->createCommand($sqlString)->queryAll();
		
		if (!$model) return array();
		
		$list = array();
		foreach ($model as $val)
		{
			$list[$val['depart_id']] = $val['depart_name'];
		}
		
		return $list;
	}
	
	// 保存数据
	public function saveData()
	{
		$this->depart_name = trim($this->depart_name);
		
		if (!$this->depart_id)
		{
			$this->status = 1;
			$this->sort = 0;
			
			$total = $this->count('depart_name=:depart_name', array(':depart_name' => $this->depart_name));
		} else 
			$total = $this->count('depart_name=:depart_name AND depart_id!=:depart_id', array(':depart_name' => $this->depart_name, ':depart_id' => $this->depart_id));
			
		if ($total)
		{
			$this->addError('depart_name', '部门名称已存在！');
			return false;
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
	public function getList($search = array(), $pageSize = 20, $page = 1, $url = '/department/index')
	{
		// 当前页数
		$page = isset($_GET['page']) ? (int) $_GET['page'] : $page;
		$pageSize = isset($_GET['pageSize']) ? (int) $_GET['pageSize'] : $pageSize;
    	
		$criteria = new CDbCriteria();
		$criteria->alias = "t";
		$criteria->select = "t.depart_id, t.depart_name, t.sort, t.status";
		
		$criteria->compare('t.depart_name', $search['depart_name'], true);
		$criteria->compare('t.status', $search['status']);
		
		$criteria->order = 't.sort ASC, t.depart_id ASC';
		
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
     * 禁用/启用
     * @param int  $depart_id  部门ID
     * @param int  $status     状态
     */
    public function SetStatus($depart_id, $status)
    {
    	if (!$depart_id) return false;
    	
    	$sqlString = "UPDATE " . $this->tableName() . " SET status='$status' WHERE depart_id='$depart_id'";
    	$result = Yii::app()->db->createCommand($sqlString)->query();
    	
    	return $result ? true :false;
    }
    
	/**
     * 删除部门
     * @param int $depart_id  部门ID
     */
    public function deleteData($depart_id)
    {
    	if (!$depart_id) return false;
		
    	// 检查该部门下是否存在用户
    	$hasUser = Department::model()->hasUserByDepartID($depart_id);
    	if ($hasUser)
    	{
    		$this->addError('depart_id', '该部门下已有用户，不能删除！');
    		return false;
    	}
    	
		$reslut = Department::model()->deleteByPk($depart_id);
		
		return $reslut ? true : false;
    }
    
    /**
     * 该部门下是否存在用户
     * @param int  $depart_id  部门ID
     */
    public function hasUserByDepartID($depart_id)
    {
    	if (!$depart_id) return false;
    	
    	$total = AdminUser::model()->count('depart_id=:depart_id', array(':depart_id' => $depart_id));
    	
    	return $total > 0 ? true : false; 
    }
}