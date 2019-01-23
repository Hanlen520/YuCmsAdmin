<?php
/**
 * 微信接口类
 * @author qianxfu<qianxfu@gmail.com>
 * @date 2013-10-10
 */
class Weixin
{
	// 微信号ID
	public $account_id;
	
	// 微信号
	public $account_name;
	
	// 微信接口 Token
	public $Token = '';
	
	// 微信接口 Access_token
	public $ACCESS_TOKEN = '';
	
	// 微信接口APPID
	public $appID = '';
	
	// 微信接口APPSECRET
	public $appsecret = '';
	
	// 微信关注用户
	public $weinxinUsers = array();
	
	// 微信用户详情
	public $userInfo = array();
	
	// 实例化
	public function __construct($account = '')
	{
		if (!$account) die('error!');
		
		$this->account_name = $account;
		$res = WeixinAccount::model()->getAccountInfo($this->account_name);
		if (!$res) die('error!');
		
		$this->account_id = $res['account_id'];
		$this->Token      = $res['token'];
		$this->appID      = $res['appID'];
		$this->appsecret  = $res['appsecret'];
	}
	
	// 验证
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        if($this->checkSignature())
        {
        	echo $echoStr;
        	exit;
        }
    }
    
	// 验证信号
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = $this->Token;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1($tmpStr);
		
		if($tmpStr == $signature)
			return true;
		else
			return false;
	}
	
    /**
     * 返回微信回复内容的数据
     * @param object           $xml   对象
     * @param string or array  $data  数据
     * @param string           $type  类型
     */
    public function response($xml, $data, $type = 'text')
    {
    	if (!$xml || !$data) return false;
    	
		$xmltpl = array();
		$timestamp = time();
		
		// 文本格式
        $xmltpl['text']  = '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName>';
        $xmltpl['text'] .= '<CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content><FuncFlag>0</FuncFlag></xml>';
        
        // 图文格式
        $xmltpl['news']  = '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[news]]></MsgType>';
        $xmltpl['news'] .= '<ArticleCount>%s</ArticleCount><Articles>%s</Articles><FuncFlag>1</FuncFlag></xml>';
        // 图文格式-项
        $xmltpl['item']  = '<item><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description><PicUrl><![CDATA[%s]]></PicUrl><Url><![CDATA[%s]]></Url></item>';
        
        if ($type == 'text')      // 文本格式
            return sprintf($xmltpl['text'], $xml->FromUserName, $xml->ToUserName, $timestamp, $data);
        else if($type == 'news')  // 图文格式
        {
            if(is_array($data))
            {
                $items = '';
                if(count($data) > 1)         // 多个图片
                {
                    foreach($data as $e)
                    {
                        $items .= sprintf($xmltpl['item'], $e['title'], '', $e['images'], $e['link']);
                    }
                }elseif(count($data) == 1)   // 单个图片
                {
                    foreach($data as $e)
                    {
                        $items = sprintf($xmltpl['item'], $e['title'], $e['desc'], $e['images'], $e['link']);
                    }
                }
                
                return sprintf($xmltpl['news'], $xml->FromUserName, $xml->ToUserName, $timestamp, count($data), $items);
            } else
            	return false;
        } else
        	return false;
    }

	// 获取动态Access_token
	public function getAccessToken()
	{
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appID."&secret=".$this->appsecret;

		$json = file_get_contents($url);
		$result = json_decode($json, true);
		
		if (isset($result['access_token']) && $result['access_token'])
		{
			$this->ACCESS_TOKEN = $result['access_token'];
			return $result['access_token'];
		}
		else 
			return false;
	}
	
	// 清空微信菜单
	public function clearMenu()
	{
		$Access_token = $this->ACCESS_TOKEN ? $this->ACCESS_TOKEN : $this->getAccessToken();
		if (!$Access_token) return false;
		
		$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token={$Access_token}";
        $result = file_get_contents($url);

        $result = json_decode($result, true);

        return $result['errcode'] == 0 ? true : false;
	}
	
	/**
	 * 创建微信菜单(需要开启php_curl扩展)
	 * @param array   $data          菜单数据数组
	 */
	public function createMenu($data)
	{
		if (!$data || !is_array($data)) return false;
		
		// 清空原微信菜单
        $r = $this->clearMenu();
        if (!$r) return false;
        
		$Access_token = $this->ACCESS_TOKEN ? $this->ACCESS_TOKEN : $this->getAccessToken();
		if (!$Access_token) return false;
		
		// 组装菜单数据
		$data = $this->convertMenuData($data);
		if (!$data) return false;
		
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$Access_token}";
        $data = CMyfunc::converJson($data);  // 菜单数据

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($result, true);

        return $result['errcode'] == 0 ? true : false;
	}
	
	/**
	 * 组装菜单数据
	 * @param array  $data  菜单数据数组
	 */
	public function convertMenuData($data)
	{
		if (!$data || !is_array($data)) return false;
		
		$list = array();
		foreach ($data as $val)
		{
			$tmp = array();
			if (!$val['type']) continue;
			if ($val['type'] == 'view' && !$val['url']) continue;
			if ($val['type'] == 'click' && !$val['key']) continue;
			
			if ($val['child'])      // 有子菜单
			{
				$subMenu = array();
				foreach ($val['child'] as $k => $v)
				{
					$Vkey = $v['type'] == 'view' ? 'url' : 'key';
					
					$subMenu = array(
						"type"  => $v['type'],
						"name"  => $v['name'],
						"$Vkey" => $v[$Vkey],
					);
				}
				
				$tmp = array(
					'name'        => $val['name'],
					'sub_button'  => $subMenu,
				);
				
			} else        // 无子菜单
			{
				$Vkey = $val['type'] == 'view' ? 'url' : 'key';
				
				$tmp = array(
					"type"  => $val['type'],
					"name"  => $val['name'],
					"$Vkey" => $val[$Vkey],
				);
			}
			
			$list[] = $tmp;
		}
		
		$data = array();
		$data['button'] = $list;
		
		return $data;
	}
	
	/**
	 * 获取微信关注用户
	 * @param string  $nextOpenID  下一个用户的OPENID
	 */
	public function getWeinxinUsers($nextOpenID = '')
	{
		$Access_token = $this->ACCESS_TOKEN ? $this->ACCESS_TOKEN : $this->getAccessToken();
		if (!$Access_token) return false;

		// 调用接口
		$url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token={$Access_token}";
		if ($nextOpenID) $url .= "&next_openid={$nextOpenID}";
        $result = file_get_contents($url);
        
        $json = json_decode($result, true);
        if (isset($json['data']['openid']) && $json['data']['openid'])
        {
        	if ($json['total'] > $json['count'] && isset($json['next_openid']) && trim($json['next_openid']) != '')
        		$this->getWeinxinUsers($json['next_openid']);
        	else 
        	{
        		if ($this->weinxinUsers)
	        		$this->weinxinUsers = array_merge($this->weinxinUsers, $json['data']['openid']);
	        	else 
	        		$this->weinxinUsers = $json['data']['openid'];
        	}
        	
        	return true;
        } else 
        	return false;
	}
	
	/**
	 * 获取用户详情
	 * @param string $openID  用户openID
	 */
	public function getUserInfo($openID)
	{
		if (!$openID) return false;

		$Access_token = $this->ACCESS_TOKEN ? $this->ACCESS_TOKEN : $this->getAccessToken();
		if (!$Access_token) return false;

		// 调用接口
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$Access_token}&openid={$openID}&lang=zh_CN";
        $result = file_get_contents($url);
        
        $json = json_decode($result, true);
        if (!isset($json['errcode']))
        {
        	$this->userInfo = array(
        		'nickname'      => $json['nickname'],
        		'sex'           => $json['sex'],
        		'language'      => $json['language'],
        		'city'          => $json['city'],
        		'province'      => $json['province'],
        		'country'       => $json['country'],
        		'avatar'        => $json['headimgurl'],
        	);
        	
        	return true;
        } else 
        	return false;
	}
	
	/**
	 * 微信群发消息
	 * @param int     $mass_id    群发ID
	 * @param string  $type       类型
	 * @param string  $content    内容
	 */
	public function massMessage($mass_id, $type, $content)
	{
		if (!$mass_id || !$type || !$content) return false;
		
		// 获取关注用户
		$result = $this->getWeinxinUsers();
		if (!$result && !$this->weinxinUsers) return false;

		// 调用接口URL
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$this->ACCESS_TOKEN}";
		
		// 发送消息
		foreach ($this->weinxinUsers as $openID)
		{
			$data = array(
				"touser"  => $openID,
				"msgtype" => $type,
				"$type"   => $content,
			);
			$data = CMyfunc::converJson($data);  // 数据转换
			
			$ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        
	        $result = curl_exec($ch);
	        curl_close($ch);
	        
	        $result = json_decode($result, true);

	        // 发送结果
	        $sendStatus = isset($result['errcode']) && $result['errcode'] ? 0 : 1;
	        
	        // 添加发送日志
	        WeixinMassLog::model()->saveData($mass_id, $openID, $sendStatus);
		}
		
		return true;
	}
}