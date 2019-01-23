<?php
/**
* 面包屑
* @author qianxfu<qianxfu@gmail.com>
* @date 2013-08-07
*/
Yii::import("zii.widgets.CBreadcrumbs");

class Breadcrumbs extends CBreadcrumbs
{
	public $rootName = '您现在所在的位置：';
	public $htmlOptions = array('class' => '');
	public $separator = " → ";
	
	public function run()
	{
		if(empty($this->links))	return;
		
		echo $this->rootName;		
		    
		$links = array();
		foreach($this->links as $label => $url)
		{
			if(is_string($label) || is_array($url))
			    $links[] = CHtml::link($this->encodeLabel ? CHtml::encode($label) : $label, $url, $this->htmlOptions);
			else
			    $links[] = '<span>'.($this->encodeLabel ? CHtml::encode($url) : $url).'</span>';
		}

		echo implode($this->separator, $links);
	}
}
