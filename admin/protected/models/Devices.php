<?php
/**
 * 设备模型
 * @author yuzhijie
 * @date 2019-1-25
 */
class Devices extends ActiveRecord
{
    public $op;
    public $path;
    public $statusArray = array (
        '0'  => '闲置',
        '1'  => '申请中',
        '2'  => '已租用'
    );

    public $platformArray = array (
        '1'  => 'Android',
        '2'  => 'iOS',
    );

    public $categoryArray = array (
        '1'  => '手机',
        '2'  => '平板',
    );

    public $availableArray = array (
        '0'  => '正常',
        '1'  => '报废',
        '2'  => '丢失',
        '3'  => '故障'
    );

    public $brandArray = array (
        '三星'=> '三星',
        '小米'=> '小米',
        '华为'=> '华为',
        '苹果'=> '苹果',
        'oppo'=> 'oppo',
        'vivo'=> 'vivo',
        '锤子'=> '锤子',
        '金立'=> '金立',
        '一加'=> '一加',
        '魅族'=> '魅族',
        '联想'=> '联想',
        '诺基亚'=> '诺基亚',
    );

    public $checkArray = array (
        '0'  => '未盘点',
        '1'  => '已盘点',
    );

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{devices}}';
    }

    public function rules()
    {
        return array(
            array('status,category,brand,model,borrower,platform,old_dev,check_dev', 'safe'),
            array('device_name,model,theNum,version', 'required', 'message' => '{attribute}必须填写！'),
            array('platform,brand,category', 'required', 'message' => '请选择{attribute}！'),
            array('device_name', 'length', 'max' => 10, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过10个字符！'),
            array('model', 'length', 'max' => 10, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过10个字符！'),
            array('theNum', 'length', 'max' => 30, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过30个字符！'),
            array('version', 'length', 'max' => 15, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过15个字符！'),
            array('owner', 'length', 'max' => 5, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过5个字符！'),
            array('other', 'length', 'max' => 30, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过30个字符！'),
            array('comments', 'length', 'max' => 60, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过60个字符！'),
        );
    }

    public function relations()
    {
        return array();
    }

    public function attributeLabels()
    {
        return array(
            'id'           => 'id',
            'device_name'  => '设备名',
            'model'        => '型号',
            'platform'     => '平台',
            'brand'        => '品牌',
            'category'     => '分类',
            'path'         => '封面',
            'theNum'       => '编号',
            'image'        => '封面',
            'status'       => '状态',
            'old_dev'      => '可用',
            'version'      => '系统版本',
            'owner'        => '所属人',
            'other'        => '其他信息',
            'comments'     => '备注',
            'check_dev'    => '盘点',
            'borrower'     => '借阅人',
            'op'           => '操作',
        );
    }

    /**
     * 列表
     * @param array   $search       搜索条件
     * @param int     $pageSize     每页记录数
     * @param int     $page         当前页数
     * @param string  $url          URL地址
     */
    public function getList($search = array(), $pageSize = 20, $page = 1, $url = '/devices/index')
    {
        // 当前页数
        $page = isset($_GET['page']) ? (int) $_GET['page'] : $page;
        $pageSize = isset($_GET['pageSize']) ? (int) $_GET['pageSize'] : $pageSize;

        $criteria = new CDbCriteria();
        $criteria->alias = "t";
        $criteria->select = "t.id, t.device_name, t.model, t.theNum, t.platform, t.borrow_time, t.borrower, t.owner, t.status, t.add_time, i.path,t.old_dev";
        $criteria->join = " LEFT JOIN " . DevicesImage::model()->tableName() . " i ON t.id=i.device_id";

        // 设备名称
        if (isset($search['device_name']) && trim($search['device_name']))
            $criteria->compare("t.device_name", trim($search['device_name']), true);

        // 型号,模糊查询
        if (isset($search['model']) && trim($search['model']))
            $criteria->compare("t.model", trim($search['model']), true);

        // 状态
        $status = isset($search['status']) ? $search['status'] : '';
        if ($status != '')
            $criteria->compare('t.status', $status);

        // 平台
        $platform = isset($search['platform']) ? $search['platform'] : '';
        if ($platform != '')
            $criteria->compare('t.platform', $platform);

        // 品牌
        $brand= isset($search['brand']) ? $search['brand'] : '';
        if ($brand != '')
            $criteria->compare('t.brand', $brand);

        // 分类
        $category= isset($search['category']) ? $search['category'] : '';
        if ($category != '')
            $criteria->compare('t.category', $category);

        // 是否可用
        $old_dev= isset($search['old_dev']) ? $search['old_dev'] : '';
        if ($old_dev != '')
            $criteria->compare('t.old_dev', $old_dev);

        // 是否盘点
        $check_dev= isset($search['check_dev']) ? $search['check_dev'] : '';
        if ($check_dev!= '')
            $criteria->compare('t.check_dev', $check_dev);

        //  系统版本
        $version= isset($search['version']) ? $search['version'] : '';
        if ($version != '')
            $criteria->compare('t.version', $version);

        // 借用人
        if (isset($search['borrower']) && trim($search['borrower']))
            $criteria->compare("t.borrower", trim($search['borrower']), true);

        $criteria->order = 't.id';

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
     * 申请/取消
     * @param int  $id  设备ID
     * @param int  $status  状态
     * @param string  $borrower  借用人
     */
    public function SetStatus($status, $borrower)
    {
        try{
            $this->status = $status;
            $this->borrower = $borrower;

            if(($status == '1' || $status == '2') && $borrower == '' )  throw new Exception('请填写申请人姓名！');
            if($status == '2') $this->borrow_time = date('Y-m-d H:i:s', time());
            if($status == '0') $this->borrow_time = NULL;

            $result = $this->save();
            if(!$result) throw new Exception('操作失败！');

            return true;
        }catch (Exception $e)
        {
            $this->addError('id', $e->getMessage());
            return false;
        }
    }

    /**
     * 删除设备
     */
    public function deleteData()
    {
        try{
            $devices_id = $this->id;
            $model = DevicesImage::model()->find('device_id=:devices_id', array(':devices_id' => $devices_id));
            $result = $model->delete();
            if(!$result) throw new Exception('删除失败！');

            $result = $this->delete();
            if(!$result) throw new Exception('删除失败！');
            return true;
        }catch (Exception $e)
        {
            $this->addError('id', $e->getMessage());
            return false;
        }
    }

    // 保存数据
    public function saveData()
    {
        try{
            $result = $this->save();
            if(!$result) throw new Exception('保存失败！');
            return true;
        }catch (Exception $e)
        {
            $this->addError('id', $e->getMessage());
            return false;
        }
    }

    // 保存数据
    public function handleData()
    {
        try{
            $status = $this->old_dev;
            if($status != '0'){
                $this->borrower = NULL;
                $this->borrow_time = NULL;
                $this->owner = NULL;
                $this->status = '0';
            }

            $this->check_dev = '1';
            $result = $this->save();
            if(!$result) throw new Exception('盘点失败！');
            return true;
        }catch (Exception $e)
        {
            $this->addError('id', $e->getMessage());
            return false;
        }
    }
}