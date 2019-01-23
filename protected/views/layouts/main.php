<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="<?php echo SITE_URL;?>assets/styles/base.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo SITE_URL;?>assets/styles/main.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo SITE_URL;?>common/scripts/sea-modules/jquery-1.11.3.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL;?>common/scripts/sea-modules/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL;?>common/scripts/sea-modules/jquery.metadata.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL;?>common/scripts/sea-modules/seajs/2.2.0/sea.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL;?>common/scripts/sea-modules/config.js"></script>
<title><?php echo CHtml::encode(Yii::app()->name); ?></title>
</head>
<body>
<div class="ui-layout">
	<div class="ui-cont ui-header">
		<div class="ui-subcont">
			<a href="#" class="ui-logo"></a>
			<div class="header-cont">
				<div class="header-tips">
					<i class="phone"></i>
					<i class="wx"></i>
					<IMG class="code" src="<?php echo SITE_URL?>/assets/images/qr_code.png" style="display:none;position:absolute;left:894px;top:45px;z-index:9999">
					<i class="wb"><a href="https://www.baidu.com/index.php"></a></i>
				</div>
<?php echo $content;?>

<div class="ui-cont ui-footer">
	<div class="ui-subcont">
		<p class="fn-right"><a href="#">系统项目</a>|<a href="#">实战讲堂</a>|<a href="#">师资力量</a>|<a href="#">关于我们</a></p>
		<p class="fn-left">©2015泰策企业管理有限公司&nbsp;&nbsp;&nbsp;浙ICP备15000000号&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;官方网址:www.chinataice.com </p>
	</div>
</div>

<script type="text/javascript">
seajs.use(['$', 'carousel','slide'], function ($, carousel, slide) {
    $(function () {
    	
    	var carousel01 = new carousel({
            element: '#fn_team_list',
            panels: '#fn_team_list .ui-team-list li',
            hasTriggers: true,
            easing: 'easeOutStrong',
            effect: 'scrollx',
            step: 4,
            viewSize: [1046],
            circular: true,
            autoplay: true
        }).render();

        var carousel_demo_1 = new carousel({
            element: '#evaluation_carousel',
            panels: '#evaluation_carousel .evaluation-content li',
            hasTriggers: true,
            effect: 'fade',
            circular: true,
            autoplay: true
        }).render();

         var scrollnews = new slide({
            element: '#js_scrollnews',
            panels: '#js_scrollnews .ui-switchable-content li',
            effect: 'scrolly',
            easing: 'easeOutStrong',
            interval: 3000
        }).render();

  		$('.wx').hover(function(){
			$('.code').show();
  	  	},function(){
  	  	  	$('.code').hide();
  	  	});

  	  	$('.wb').click(function(){
  	  	  	window.open('http://weibo.com') ;
  	  	});
    });
});
</script>
</body>
</html>
