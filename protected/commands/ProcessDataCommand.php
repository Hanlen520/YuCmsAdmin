<?php
/**
 * 历史数据处理程序
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-08-20
 */
class ProcessDataCommand extends CConsoleCommand 
{
	public function run($args)
	{	
		$type = '';
		if(isset($args) && count($args)){
			$type = trim($args[0]);
		}
		else{
			echo "input args is null!\r\n";
			exit;
		}
		
		$DataArr = array();
		
		if (!in_array($type, $DataArr))
		{
			echo "input args is error!\r\n";
			exit;
		}
		
		$fun = 'Process' . $type;
		
		$this->$fun();

	}
	
	public function Process()
	{
		
	}
}

