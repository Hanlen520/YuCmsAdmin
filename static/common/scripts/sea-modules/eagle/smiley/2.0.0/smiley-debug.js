/** @name: smiley - v2.0.0 
 *  @description: 表情加载 
 *  @author: ChenYuKun 
 *  @date: 2014-04-08 
 */
/**
 * 表情
 * @author: chenyukun
 * @date 2014/2/18
 */
define("eagle/smiley/2.0.0/smiley-debug", [ "$-debug" ], function(require, exports, module) {
    var $ = require("$-debug");
    //默认配置
    var defaultSettings = {
        //文本输入框
        textCont: "",
        //添加表情按钮
        smilies: "",
        //表情显示区域
        emotionBox: "",
        //表情json路径
        emotionSrc: "",
        //表情显示框,自定义宽、高、边框颜色
        smiliesBox: {
            Width: "",
            Height: "",
            Border_color: ""
        },
        //表情显示框left值
        smiliesBox_left: "",
        //表情显示框top值
        smiliesBox_top: "",
        //表情显示框是否手动关闭，默认插入后关闭，失去焦点关闭
        smiliesBox_close: true,
        // callbacks 回调函数集合
        callbacks: {
            // 点击插入表情执行的回调函数
            intervene: function() {},
            // 插入表情以后执行回调函数
            insertfinish: function() {},
            //ajax加载json失败执行回调函数
            ajaxError: function() {},
            //ajax加载json成功执行回调函数
            ajaxSucceed: function() {}
        }
    };
    // 模块化扩展接口，用于表情插入
    var smiley_init = function(options) {
        var options = $.extend(true, {}, defaultSettings, options || {}), /*
            *定义属性
            */
        $textCont = $(options.textCont).eq(0), //文本输入框
        $smilies = $(options.smilies).eq(0), //添加表情按钮
        $emotionBox = $(options.emotionBox).eq(0), //表情显示区域
        $emotionCont = "", /* 定义位置属性 */
        _width = options.smiliesBox.Width, //表情框 width
        _height = options.smiliesBox.Height, //表情框 height
        _border_color = options.smiliesBox.Border_color, //表情框 边框颜色
        _smiliesBox_left = options.smiliesBox_left, //表情框left值，前提 css定义position:relative
        _smiliesBox_top = options.smiliesBox_top, //表情框top值，前提 css定义position:relative
        /* 定义回调函数 */
        eventintervene = options.callbacks.intervene, // 点击插入表情执行的回调函数
        eventinsertfinish = options.callbacks.insertfinish, // 插入表情以后执行回调函数
        eventajaxError = options.callbacks.ajaxError, //ajax加载json失败执行回调函数
        eventajaxSucceed = options.callbacks.ajaxSucceed, //ajax加载json成功执行回调函数
        emotion = true, show = true;
        /*
		 * 判断内容元素和提示元素是否存在
		*/
        if (!$textCont.length || !$smilies.length || $textCont.length != $smilies.length) {
            return null;
        }
        // 获取内容元素的data-smiley，如果已初始化，则直接返回
        var data_smiley = $textCont.data("data-smiley");
        if (typeof data_smiley == "object") {
            return data_smiley;
        }
        // 返回操作对象
        var smiley = {
            options: options,
            hand_close: function() {
                $emotionCont.hide();
            },
            hand_open: function() {
                $emotionCont.show();
            }
        };
        //添加显示文本框
        function _add_emotionBox() {
            var $emotion_html = $('<div class="emotion-box"><span class="arrow-up relative"><i class="arrow-up-innr"></i></span></div>');
            $emotion_html.css({
                width: _width,
                height: _height,
                border: "1px solid " + _border_color + "",
                left: _smiliesBox_left,
                top: _smiliesBox_top
            });
            $emotionBox.append($emotion_html);
            $emotionCont = $emotionBox.find(".emotion-box");
        }
        //加载表情 ajax
        function _add_ajax() {
            $.ajax({
                url: options.emotionSrc,
                type: "GET",
                dataType: "json",
                timeout: 1e3,
                cache: false,
                error: function(data) {
                    eventajaxError(this, $textCont);
                },
                success: function(data) {
                    var drops = [];
                    $.each(data, function(i, k) {
                        var name = k["title"], links = k["link"];
                        var _html = [ '<img data-text="[' + name + ']" alt="[' + name + ']" title="' + name + '" src="' + links + '" />' ].join("\n");
                        drops.push(_html);
                    });
                    $emotionBox.find(".emotion-login").hide();
                    $emotionCont.append(drops.join("\n"));
                    eventajaxSucceed.call(this, $textCont);
                }
            });
        }
        /**
         *  textarea
         *  表情插入(公用函数)
         *  @argument [@Object] elemEmo 表情的jQuery对象
         *  @argument [@Object] elemText 对应输入框的jQuery对象
         */
        function insertEmoTextarea(elemEmo, elemText) {
            var _$elemEmo = elemEmo;
            _$elemText = elemText, _elemText = _$elemText[0];
            var _code = $.trim(_$elemEmo.attr("data-text"));
            var _sel = "";
            if (document.selection) {
                _$elemText.focus();
                _sel = document.selection.createRange();
                _sel.text = _code;
                _$elemText.focus();
            } else if (_elemText.selectionStart || _elemText.selectionStart == "0") {
                var _star = _elemText.selectionStart, _end = _elemText.selectionEnd, _cursor = _end;
                _elemText.value = _elemText.value.substring(0, _star) + _code + _elemText.value.substring(_end, _elemText.value.length);
                _cursor += _code.length;
                _elemText.focus();
                _elemText.selectionStart = _cursor;
                _elemText.selectionEnd = _cursor;
            } else {
                _elemText.value += _code;
                _$elemText.focus();
            }
        }
        //表情选择按钮
        $smilies.off(".add").on("click.add", function(e) {
            if (show) {
                _add_emotionBox();
                show = false;
            } else {
                $emotionCont.show();
            }
            e.stopPropagation();
            // 表情添加按钮(防止冒泡)
            if (emotion) {
                $emotionCont.append('<div class="emotion-login"></div>');
                _add_ajax();
                emotion = false;
            } else {
                return;
            }
        });
        //添加表情
        $emotionBox.off(".addimg").on("click.addimg", "img", function(e) {
            eventintervene.call(this, $textCont);
            insertEmoTextarea($(this), $textCont);
            eventinsertfinish.call(this, $textCont);
            if (options.smiliesBox_close) {
                smiley.hand_close();
            }
        });
        // 表情选择区域(防止冒泡)
        $emotionBox.off(".stop").on("click.stop", ".emotion-box", function(e) {
            e.stopPropagation();
        });
        // 绑定document的操作(控制表情选择区域)
        if (options.smiliesBox_close) {
            $(document).on("click", function(e) {
                if (!$emotionCont === null || $emotionCont.length) {
                    smiley.hand_close();
                }
            });
        }
        $textCont.data("data-smiley", smiley);
        return smiley;
    };
    // 模块化扩展接口，用于初始化多个表情插件
    var smiley_initMulti = function(options) {
        var $textConts = $(options.textCont), //文本输入框
        $smiliess = $(options.smilies), //添加表情按钮
        $emotionBoxs = $(options.emotionBox);
        //表情显示区域
        // 判断编辑框和提示框元素个数是否一致
        if (!$textConts.length || !$smiliess.length || $textConts.length != $smiliess.length) {
            return [];
        }
        // 遍历编辑框、提示框初始化字数统计插件
        return $(options.textCont).map(function(i) {
            var partOptions = {
                textCont: $(this),
                smilies: $smiliess.eq(i),
                emotionBox: $emotionBoxs.eq(i)
            };
            var settings = $.extend({}, options, partOptions);
            // 初始化，并返回扩展接口
            return smiley_init(settings);
        }).get();
    };
    // 添加jQuery辅助接口
    $.fn.extend({
        getsmiley: function() {
            return $(this).data("data-smiley") || null;
        }
    });
    exports.render = smiley_init;
    exports.initMulti = smiley_initMulti;
});
