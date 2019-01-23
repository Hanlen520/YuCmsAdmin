<?php
class Ad extends ActiveRecord
{
	public $op;
	public $ad;
	public $notice;
	//广告状态
	public $statusArray = array(
			'1'  => '公开',
			'0'  => '不公开',
	);
	
	public $uploadImage;
	//广告类型
	public $typeArray = array(
			'1'  => '广告',
			'2'  => '公告',
			'3'  => '动态',
	);
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{ad}}';
	}

	public function rules()
	{
		return array(
				array('title, link', 'required', 'message' => '{attribute}必须填写！'),
				array('status, type', 'required', 'message' => '{attribute}必须选择！'),
				array('image','required','message'=>'{attribute}必须上传！'),
				array('status, type, sort', 'numerical', 'integerOnly' => true, 'message' => '{attribute}必须为数字！'),
				array('title', 'length', 'max' => 25, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过20个字！'),
				array('link', 'length', 'max' => 25, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过100个字！'),
				array('intro', 'length', 'max' => 25, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过100个字！'),
				array('link', 'url', 'message'=>'{attribute}格式不正确'),
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
				'id'   				=> 'ID',
				'title'				=> '名称',
				'link'     			=> '链接地址',
				'image'       		=> '图片',
				'intro'   			=> '描述',
				'status'            => '状态',
				'type'              => '类型',
				'sort'				=> '排序',
				'add_user_name' 	=> '操作人',
  				'add_user_id'  		=> '操作人ID',
				'add_time'          => '添加时间',
  				'update_user_name'  => '更新人员',
  				'update_user_id'    => '更新人员ID',
  				'update_time' 		=> '更新时间',
				'op'        		=> '操作',
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
	
	/**
	 * 获取导航
	 * @return array
	 */
	public function getAd($type = '1',$limit = 3)
	{
		if(!$type) return array();
		
		$criteria = new CDbCriteria();
		$criteria->select = "t.id,t.title,t.link,t.image,t.intro";
		$criteria->compare('t.status', 1);
		$criteria->compare('t.type', $type);
		$criteria->order = 't.sort DESC,t.update_time DESC';
		$criteria->limit = $limit;
		
		$model =$this->findAll($criteria);
	
		if (!$model) return array();
	
		$dataArray = array();
		foreach ($model as $val)
		{
			$dataArray[$val['id']]['title'] = $val['title'];
			$dataArray[$val['id']]['link']  = $val['link'];
			$dataArray[$val['id']]['image'] = $val['image'];
		}
		return $dataArray;
	}
}