<?php
/** 
* 基于CActiveRecord类的封装，实现多库和主从读写分离 
* 所有Model都必须继承些类. 
* @author: ljw
*/  
class ActiveRecord extends CActiveRecord  
{  
    // model配置  
    public $modelConfig = '';
    
    // 数据库配置  
    public $dbConfig = '';
    
    // 定义一个多数据库集合  
    static $dataBase = array();
    
    // 当前数据库名称  
    public $dbName = '';
    
    // 定义库类型（读或写）  
    public $dbType = 'read';
    
    /** 
    * @param string $scenario  Model的应用场景 
    * @param string $dbname    数据库名称 
    */  
    public function __construct($scenario = 'insert', $dbname = '')  
    {  
        if (!$dbname) $this->dbName = $dbname;
        
        parent::__construct($scenario);
    }
    
    /**
    * 根据SQL切换读写模式
    * @param string $sql  SQL语句
    */
    private function changeConnBySql($sql)
    {
        $query_type = preg_match("/^(\s*)select/i", $sql);
        
        if($query_type)
             $this->dbRead();
        else
            $this->dbWrite(); 
    }
    
    /**
    * 返回一个数据库执行对象
    * @param string $sql  SQL语句
    */
    public function createCommand($sql)
    {
        $this->changeConnBySql($sql);
        
        return $this->getDbConnection()->createCommand($sql);
    }
    
    /**
    * 返回一条记录
    * @param string $sql  SQL语句
    */
    public function queryRow($sql)
    {
        return $this->createCommand($sql)->queryRow();
    }
    
    /**
    * 执行SQL语句
    * @param string    $sql  SQL语句
    * @return          影响的行数
    */
    public function execute($sql)
    {
        return $this->createCommand($sql)->execute();
    }
    
    /**
    * 返回全部记录
    * @param string  $sql  SQL语句
    */
    public function queryAll($sql)
    {
        return $this->createCommand($sql)->queryAll();
    }
    
    /** 
    * 重写父类的getDbConnection方法 
    * 多库和主从都在这里切换 
    */  
    public function getDbConnection()  
    {  
        // 如果指定的数据库对象存在则直接返回  
        if (isset(self::$dataBase[$this->dbName]) && self::$dataBase[$this->dbName]!==null)  return self::$dataBase[$this->dbName];  
        
        if ($this->dbName == 'db') 
            self::$dataBase[$this->dbName] = Yii::app()->getDb();  
        else
            $this->changeConn($this->dbType);
        
        if(self::$dataBase[$this->dbName] instanceof CDbConnection)
        {  
            self::$dataBase[$this->dbName]->setActive(true);
            
            return self::$dataBase[$this->dbName];
        } else
        	throw new CDbException(Yii::t('yii','Model requires a db CDbConnection application component.'));  
    }  
  
    /** 
    * 获取配置文件 
    * @param string  $type  参数名称
    * @param string  $key   参数的键名
    */  
    private function getConfig($type = 'modelConfig', $key = '')
    {  
        $config = Yii::app()->params[$type];
        if($key) $config = $config[$key];
        
        return $config;  
    }  
  
    /** 
    * 获取数据库名称 
    */  
    private function getDbName()
    {  
        if($this->dbName) return $this->dbName;  
        
        $modelName = get_class($this->model());
        $this->modelConfig = $this->getConfig('modelConfig'); 
        
        // 获取model所对应的数据库名
        $dbName = 'db';
        if($this->modelConfig)
        {
            foreach($this->modelConfig as $key => $val)
            {  
                    if(in_array($modelName, $val))
                    {  
                        $dbName = $key;
                        break;  
                    }  
             }
        }
        
        return $dbName;
    }  
  
    /** 
    * 切换数据库连接
    * @param string  $dbtype  数据库类型: read or write
    */  
    protected function changeConn($dbtype = 'read')
    {  
        if($this->dbType == $dbtype && isset(self::$dataBase[$this->dbName]) && self::$dataBase[$this->dbName] !== null)  
        	return self::$dataBase[$this->dbName];  
          
        $this->dbName = $this->getDbName();
        if($this->dbName == 'db')
        {
            self::$dataBase[$this->dbName] = Yii::app()->getDb();
            return self::$dataBase[$this->dbName];
        }
        
        if(Yii::app()->getComponent($this->dbName.'_'.$dbtype) !== null)
        {  
            self::$dataBase[$this->dbName] = Yii::app()->getComponent($this->dbName.'_'.$dbtype);  
            return self::$dataBase[$this->dbName];
        }  
  
        $this->dbConfig = $this->getConfig('dbConfig', $this->dbName);  
  
        // 跟据类型取对应的配置（从库是随机值）  
        if($dbtype == 'write')  
            $config = $this->dbConfig[$dbtype];  
        else
        {  
            $slavekey = array_rand($this->dbConfig[$dbtype]);  
            $config = $this->dbConfig[$dbtype][$slavekey];  
        }
  
        // 将数据库配置加到component中  
        $dbComponent = Yii::createComponent($config);
        if($dbComponent)
        {  
             Yii::app()->setComponent($this->dbName.'_'.$dbtype, $dbComponent);
             self::$dataBase[$this->dbName] = Yii::app()->getComponent($this->dbName.'_'.$dbtype);
             $this->dbType = $dbtype;
             
             return self::$dataBase[$this->dbName];
             
        } else  
            throw new CDbException(Yii::t('yii','Model requires a changeConn CDbConnection application component.'));  
   }
   
    /** 
    * 保存数据前选择 主 数据库 
    */  
    protected function beforeSave()
    {  
        parent::beforeSave();
        $this->changeConn('write');
        
        return true;  
    }
    
    /** 
    * 删除数据前选择 主 数据库 
    */  
    protected function beforeDelete()
    {  
        parent::beforeDelete();  
        $this->changeConn('write');
        
        return true;  
    }
    
    /** 
    * 读取数据选择 从 数据库 
    */  
    protected function beforeFind()
    {  
        parent::beforeFind();
        $this->changeConn('read');
          
        return true;  
    }
    
    /** 
    * 获取主库对象 
    */  
    public function dbWrite()
    {  
        return $this->changeConn('write');  
    }
    
    /** 
    * 获取从库对象 
    */  
    public function dbRead()
    {  
        return $this->changeConn('read');  
    }  
}