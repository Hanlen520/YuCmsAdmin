<?php
/**
 * 任务模型
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-13
 */
class AdminTask extends ActiveRecord
{
	public $op;
	const  TaskSuffix = 'TASK';     // 任务的后缀
	
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
			array('name', 'checkTaskName'),
		);
    }

    public function relations()
    {
        return array();
    }
    
    public function attributeLabels()
	{
		return array(
			'name'         => '任务代码',
			'description'  => '任务描述',
			'op'           => '操作',
        );
	}
	
	public function checkTaskName($attribute, $params)
	{
		$this->name = trim($this->name);
		$itemName = $this->name . self::TaskSuffix;
		
		$total = $this->count('name=:name', array(':name' => $itemName));
			
		if ($total)
		{
			$this->addError('name', '任务代码已存在！');
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
			$this->name .= self::TaskSuffix;    // 添加后缀
			$total = $this->count('name=:name', array(':name' => $this->name));
			
			if ($total)
			{
				$this->addError('name', '任务代码已存在！');
				return false;
			}
			
			$isNew = true;
		}
		
		$this->type = 1; // 任务
		
		$result = $this->save(false);
		
		return $result ? true : false;
	}
}