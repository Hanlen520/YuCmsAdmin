<?php
/**
 * 计划任务程序
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-08-20
 */
class CronCommand extends CConsoleCommand 
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
		
		$DataArr = array('Sms');
		
		if (!in_array($type, $DataArr))
		{
			echo "input args is error!\r\n";
			exit;
		}
		
		$fun = 'Process' . $type;
		
		$this->$fun();

	}
	
	// 自动发送短信
	public function ProcessSms()
	{
		do 
		{
			echo "Starting send the sms...\r\n";
			$result = Sms::model()->AutoSendSms();
			
			if ($result)
				$str = "Sms is the successful Sended!\r\n";
			else
				$str = "No Sms!\r\n";
			
			echo $str;
			
			sleep(5);
			
		} while (1);
	}
}

