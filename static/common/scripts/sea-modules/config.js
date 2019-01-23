/**
 * SeaJS Config
 */

seajs.config({
	alias : {
		// gallery
		'cookie': 'gallery/cookie/1.0.2/cookie'
		,'juicer': 'gallery/juicer/0.6.4/juicer'
		,'json': 'gallery/json/1.0.3/json'
		,'jsuri': 'gallery/jsuri/1.2.2/jsuri'
		,'swfobject': 'gallery/swfobject/2.2.0/swfobject'
		,'SWFUpload': 'gallery/swfupload/2.2.0/swfupload'
		,'moment': 'gallery/moment/2.0.0/moment'
		,'math': 'gallery/mathjs/0.9.0/math'
		,'DD_belatedPNG': 'gallery/DD_belatedPNG/0.0.8a/DD_belatedPNG'
		,'placeholders': 'gallery/placeholders/3.0.1/placeholders.js'

		// Arale(Alipay)
		,'carousel': 'arale/switchable/1.0.1/carousel'
		,'tabs': 'arale/switchable/1.0.1/tabs'
		,'slide': 'arale/switchable/1.0.1/slide'
		,'sticky': 'arale/sticky/1.3.1/sticky'
		,'widget': 'arale/widget/1.1.1/widget'
		,'validator': 'arale/validator/0.9.7/validator'
		,'tip':'arale/tip/1.2.2/tip.js'

		// jquery(jQuery & jQuery.plugin)
		,'$': 'jquery/jquery/1.10.1/jquery'
		,'$-debug':'jquery/jquery/1.10.1/jquery-debug'
		,'artDialog': 'jquery/artDialog/5.0.2/artDialog'
		,'mousewheel': 'jquery/mousewheel/3.1.3/mousewheel'
		,'ztreeCore': 'jquery/ztree/3.5.14/core'
		,'ztreeExcheck': 'jquery/ztree/3.5.14/excheck'
		,'iCheck': 'jquery/iCheck/1.0.2/icheck'

		// eagle(JavaScript by us)
		// 字数统计
		,'charcount': 'eagle/charcount/1.0.0/charcount'
		// 'imgReady': 图片头数据加载就绪事件 - 更快获取图片尺寸
		,'imgReady': 'eagle/imgReady/1.0.0/imgReady'
		// 星星评分
		,'tstar': 'eagle/tstar/1.0.0/tstar'
		// 表情插入
		,'smiley': 'eagle/smiley/2.0.0/smiley'
		// loading加载插件
		,'loading': 'eagle/loading/1.0.0/loading'
		// 下拉列表框
		,'dropdownbox': 'eagle/dropdownbox/1.0.0/dropdownbox'
		// iCheck
	},

	// base:'/Git_Josh/item_0729/static/common/scripts/sea-modules/',

	paths:{
		'js': './assets/scripts'//配置调用本地的js 的路径
	},

	vars:{'locale':'zh-cn'},

	charset:'utf-8'
});