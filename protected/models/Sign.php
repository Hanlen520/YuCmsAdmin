<?php
class Sign extends ActiveRecord
{
	public $op;
	public $deal;
	//状态
	public $statusArray = array(
			'0' =>'待处理',
			'1' =>'电话已确认', 
			'2' =>'电话不通', 
			'3' =>'作废', 
			'4' =>'其他原因', 
			'5' =>'已处理'
	);
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{Sign}}';
	}

	public function rules()
	{
		return array(
				array('name, mobile,company', 'required', 'message' => '{attribute}必须填写！'),
				array('gender,status,course_name', 'required', 'message' => '{attribute}必须选择！'),
				array('mobile', 'numerical', 'integerOnly' => true, 'message' => '{attribute}必须为数字！'),
				array('name', 'length', 'max' => 20, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过10个字！'),
				array('require', 'length', 'max' => 1000, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过500个字！'),
				array('department', 'length', 'max' => 20, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过10个字！'),
				array('mobile', 'length', 'max' => 11, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过11位！'),
				array('email', 'length', 'max' => 50, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过25个字！'),
				array('weixin', 'length', 'max' => 50, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过25位！'),
				array('intro', 'length', 'max' => 400, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过200个字！'),
		);
	}

	public function relations()
	{
		return array();
	}

	//公告类型下广告必填
	public function attributeLabels()
	{
		return array(
				'id'   				=> '报名号',
				'name'				=> '姓名',
				'gender'            => '性别',
				'weixin'     		=> '微信',
				'email'       		=> '邮箱',
				'company'       	=> '公司名称',
				'department'        => '需求部门',
				'require'           => '需要改善的问题',
				'mobile'   			=> '手机',
				'status'            => '状态',
				'course_name'		=> '报名课程',
				'create_time'       => '报名时间',
  				'update_user_name'  => '更新人员',
  				'update_user_id'    => '更新人员ID',
  				'update_time' 		=> '更新时间',
				'intro'             => '备注',
				'op'        		=> '操作',
				'deal'              => '处理'
		);
	}

	/**
	 * 过滤操作更改项
	 * @param array  $data  已更改的数据项及值
	 */
	public function filterOperateLog($data)
	{
		if (isset($data['url'])) unset($data['url']);

		return $data;
	}

	/**
	 * 设置日志相应的值
	 * @param string  $key    键
	 * @param string  $value  值
	 */
	public function setOperateValue($key, $value)
	{
		if (!$key) return false;

		switch ($key)
		{
			// 上级菜单
			case 'pid':
				$menu = AdminMenu::model()->findByPk($value);
				$value = $menu ? $menu['menu_name'] : $value;
				break;

				// 所属模块
			case 'module_id':
				$module = AdminModule::model()->findByPk($value);
				$value = $module ? $module['module_name'] : $value;
				break;

				// 操作名称
			case 'itemname':
				$item = Authitem::model()->findByPk($value);
				$value = $item ? $item['description'] : $value;
				break;
		}

		return $value;
	}
	
	// 保存数据
	public function saveData()
	{
		$this->update_user_name = Yii::app()->user->getState('USER_NAME');//更新者
		$this->update_user_id = Yii::app()->user->getState('USER_ID');//更新ID
		$this->update_time = date('Y-m-d H:i:s');                       //最后更新时间
		if($this->isNewRecord){
				$this->create_time = date('Y-m-d H:i:s'); //创建时间
		}
		$result = $this->save(false);

		return $result ? true : false;
	}
}