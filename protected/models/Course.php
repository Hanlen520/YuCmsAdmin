<?php
class Course extends ActiveRecord
{
	public $op;
	//广告状态
	public $statusArray = array(
			'1'  => '公开',
			'0'  => '不公开',
	);
	
	public $uploadImage;
	
	public  $catalog_name;//栏目名称
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{course}}';
	}

	public function rules()
	{
		return array(
				array('title, content', 'required', 'message' => '{attribute}必须填写！'),
 				array('status, catalog_id', 'required', 'message' => '{attribute}必须选择！'),
				array('status, catalog_id, sort', 'numerical', 'integerOnly' => true, 'message' => '{attribute}必须为数字！'),
 				array('title', 'length', 'max' => 40, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过20个字！'),
 				array('content', 'length', 'max' => 10000, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过500个字！'),
				array('intro', 'length', 'max' => 200, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过100个字！'),
				array('image_list,cover','safe'),
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
				'title'				=> '课程名称',
				'intro'   			=> '课程概述',
				'catalog_id'     	=> '所属栏目',
				'image_list'       	=> '图片',
				'cover'             => '封面',
				'content'   		=> '课程内容',
				'status'            => '状态',
				'sort'				=> '排序',
				'add_user_name' 	=> '操作人',
  				'add_user_id'  		=> '操作人ID',
				'add_time'          => '添加时间',
  				'update_user_name'  => '更新人员',
  				'update_user_id'    => '更新人员ID',
  				'update_time' 		=> '更新时间',
				'catalog_name' 		=> '所属栏目',
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
	 * 获取内容
	 * @return array
	 */
	public function getNew($ids, $limit = 1)
	{
		if(!is_array($ids) || !$ids) return array();
		
		$criteria = new CDbCriteria();
		$criteria->select = "t.id,t.title,t.content,t.intro,t.cover,t.image_list";
		$criteria->compare('t.status', 1);
		$criteria->addInCondition('t.catalog_id', $ids);
		$criteria->order = 't.update_time DESC';
		$criteria->limit = $limit;
		
		$model =$this->findAll($criteria);
		if (!$model) return array();
		
		$dataArray = array();
		foreach ($model as $val)
		{
			$dataArray[$val['id']]['title'] = $val['title'];
			$dataArray[$val['id']]['content']  = $val['content'];
			$dataArray[$val['id']]['cover'] = $val['cover'];
			$dataArray[$val['id']]['intro'] = substr($val['intro'], 0,135).'......';
		}
		return $dataArray;
	}
	
	/**
	 * 获取 内容列表
	 * @return array
	 */
	public function getTitle($id, $limit = 5)
	{
		if(!$id) return array();
	
		$criteria = new CDbCriteria();
		$criteria->select = "t.id,t.title,t.content,t.cover,t.intro";
		$criteria->compare('t.status', 1);
		$criteria->compare('t.catalog_id', $id);
		$criteria->order = 't.add_time';
		$criteria->limit = $limit;
	
		$model =$this->findAll($criteria);
	
		if (!$model) return array();
	
		$dataArray = array();
		foreach ($model as $val)
		{
			$dataArray[$val['id']]['title'] = $val['title'];
			$dataArray[$val['id']]['cover']  = $val['cover'];
			$dataArray[$val['id']]['content']  = $val['content'];
			$dataArray[$val['id']]['catalog_id']  = $id;
			$dataArray[$val['id']]['id']   = $val['id'];
			$dataArray[$val['id']]['intro']   = $val['intro'];
		}
		return $dataArray;
	}
	
	/**
	 * 获取  课程
	 * @return array
	 */
	public function getCourse()
	{
		$criteria = new CDbCriteria();
		$criteria->select = "t.id,t.title";
		$criteria->compare('t.status', 1);
		$criteria->addInCondition('t.catalog_id', array(43,44,45));
		$criteria->order = 't.add_time';
	
		$model =$this->findAll($criteria);
	
		if (!$model) return array();
	
		$dataArray = array();
		foreach ($model as $val)
		{
			$dataArray[$val['id']]['title'] = $val['title'];
		}
		return $dataArray;
	}
	
	/**
	 * 获取  实战课程 、师资力量
	 * @return array
	 */
	public function getAbout($id)
	{
		$criteria = new CDbCriteria();
		$criteria->select = "t.id,t.title,t.cover";
		$criteria->compare('t.status', 1);
		$criteria->compare('t.catalog_id', $id);
		$criteria->addCondition('t.cover IS NOT NULL');
		$criteria->order = 't.add_time DESC';
	
		$model =$this->findAll($criteria);
	
		if (!$model) return array();
	
		$dataArray = array();
		foreach ($model as $val)
		{
			$dataArray[$val['id']]['title'] = $val['title'];
			$dataArray[$val['id']]['cover'] = $val['cover'];
		}
		return $dataArray;
	}
}

