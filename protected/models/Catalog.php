<?php
class Catalog extends ActiveRecord
{
	public $op;
// 	public $category;
	public $pid_name;
	public $nav;
	// 角色状态
	public $statusArray = array(
			'1'  => '公开',
			'0'  => '不公开',
	);
	// 显示方式
	public $displayArray = array(
			'1'  => '列表',
			'0'  => '单页',
	);
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{catalog}}';
	}

	public function rules()
	{
		return array(
				array('parent_id, catalog_name', 'required', 'message' => '{attribute}必须填写！'),
				array('status, display', 'required', 'message' => '{attribute}必须选择！'),
				array('parent_id, status, display', 'numerical', 'integerOnly' => true, 'message' => '{attribute}必须为数字！'),
				array('catalog_name', 'length', 'max' => 20, 'encoding' => 'UTF-8', 'tooLong' => '{attribute}长度不能超过10个字！'),
		);
	}

	public function relations()
	{
		return array();
	}

	public function attributeLabels()
	{
		return array(
				'id'   				=> 'ID',
				'parent_id'			=> '上级栏目',
				'catalog_name'      => '栏目名称',
				'status'       		=> '栏目状态',
				'sort'				=> '排序',
				'display'           => '显示方式',
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
	 * 获取导航
	 * @return array
	 */
	public function getNav()
	{
		$criteria = new CDbCriteria();
		$criteria->select = "t.id,t.catalog_name";
		$criteria->compare('t.parent_id', 0);
		$criteria->compare('t.status', 1);
		$criteria->order = 't.sort DESC';
		$model =$this->findAll($criteria);
		
		if (!$model) return array();
		
		$dataArray = array();
		foreach ($model as $val)
		{
			$dataArray[$val['id']] = $val['catalog_name'];
		}
        return $dataArray;
	}
}