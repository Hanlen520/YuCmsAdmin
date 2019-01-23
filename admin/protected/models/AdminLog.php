<?php
/**
 * 后台用户操作日志模型
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class AdminLog extends ActiveRecord
{
	public $startDate;   // 开始时间
	public $endDate;	 // 结束时间
	public $op;
	
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return '{{admin_log}}';
    }

    public function rules()
    {
        return array(
			array('title, admin_user_name, ip, startDate, endDate', 'safe'),
		);
    }

    public function relations()
    {
        return array();
    }
    
    public function attributeLabels()
	{
		return array(
			'title'            => '标题',
			'content'          => '内容',
		    'module_name'      => '模块',
			'controller_name'  => '控制器',
			'action_name'      => '方法',
			'startDate'        => '开始时间',
			'endDate'          => '结束时间',
			'admin_user_name'  => '操作用户',
			'add_time'         => '操作时间',
			'ip'               => 'IP地址',
			'url'              => 'URL地址',
			'op'               => '操作',
        );
	}
	
	/**
     * 保存数据
     * @param string $title              标题
     * @param string or array $content   内容
     */
    public function saveData($title, $content = '')
    {
    	if (!$title) return false;
    	
    	if ($content && is_array($content)) $content = implode('、', $content);
    	$content = $content ? $content : $title;
    	
    	$this->admin_user_id     = Yii::app()->user->getState('USER_ID');
    	$this->admin_user_name   = Yii::app()->user->getState('USER_NAME');
    	$this->module_name       = isset(Yii::app()->controller->module->id) ? Yii::app()->controller->module->id : '';
        $this->controller_name   = Yii::app()->controller->id;
        $this->action_name       = Yii::app()->controller->action->id;
        $this->ip            = Yii::app()->user->getState('IP');
        $this->url           = ltrim(Yii::app()->request->url, '/'); 
        $this->title         = $title;
        $this->content       = $content;
        $this->add_time      = time();

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
	public function getList($search = array(), $pageSize = 20, $page = 1, $url = '/log/index')
	{
		// 当前页数
		$page = isset($_GET['page']) ? (int) $_GET['page'] : $page;
		$pageSize = isset($_GET['pageSize']) ? (int) $_GET['pageSize'] : $pageSize;
    	
		$criteria = new CDbCriteria();
		$criteria->alias = "t";
		$criteria->select = "t.log_id, t.url, t.ip, t.admin_user_name, t.add_time, t.title, t.controller_name, t.action_name";
		
		$criteria->compare('t.title', $search['title'], true);
		$criteria->compare('t.admin_user_name', $search['admin_user_name']);
		$criteria->compare('t.add_time', '>=' .strtotime($search['startDate']));
		$criteria->compare('t.add_time', '<=' . (strtotime($search['endDate']) ? strtotime($search['endDate'] . ' 23:59:59') : ''));
		$criteria->compare('t.ip', $search['ip']);
		
		$criteria->order = 't.log_id DESC';
		
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
     * 删除日志
     * @param int or array $ids   日志ID
     */
    public function deleteLog($ids)
    {
    	if (!$ids) return false;
		
		if (is_array($ids)) {
			$ids = implode(",", $ids);
		}
		
		$sqlString = "DELETE FROM " . $this->tableName() . " WHERE log_id IN($ids)";
		$result = Yii::app()->db->createCommand($sqlString)->query();
		
		return $result ? true : false;
    }
    
}