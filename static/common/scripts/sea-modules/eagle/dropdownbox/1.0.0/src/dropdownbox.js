/**
 * 下拉列表框
 * DropDownBox Plugins v1.0.0
 * @author ChenDeSheng
 * @date: 2014-02-14 10:00
 */
define(function(require, exports, module){
	var $ = require('$'),
		juicer = require('juicer'),
		mousewheel = require('mousewheel');

	// 常量
	var DDB_BOX_ZINDEX = 100,
		DDB_BOX_FOCUS_CLASSNAME = 'dropdownbox-focus',
		DDB_BOX_EXPAND_DOWN_CLASSNAME = 'dropdownbox-expand-down',
		DDB_BOX_EXPAND_UP_CLASSNAME = 'dropdownbox-expand-up',
		DDB_BOX_INDEX = 0,
		DDB_SELECT_INDEX_NAME = 'data-ddb-tabindex',
		DDB_CAPTION_VALUE_NAME = 'data-ddb-value',
		DDB_ITEM_HOVER_CLASSNAME = 'current';

	// 默认配置属性
	var defaults = {
		className: '',// 下拉列表框自定义样式类名
		width: 150,// 下拉列表框自定义宽度
		wheelScrolling: true, // 滚轮带动列表滚动
		autoUseSelecting: false, // 滚轮上下滚动 或 键盘上下键切换时是否自动使用选择的值
		dependOnSelecting: true, // 滚轮上下滚动 或 键盘上下键切换时取决于已选择的项，注意：[autoUseSelecting=true]
		updateLinkage: true // 从绑定的下拉框中自动更新联动数据
	};

	/**
	 * 模块化扩展接口，用于初始化匹配选择器的下拉列表框
	 */
	var dropDownBox = function(selector, options){
		var options = $.extend(true, {}, defaults, options || {}),
			$selects = $(selector).filter('select');

		// 判断目标选择器jQuery对象长度
		if ($selects.length == 0) {
			return null;
		}

		return $selects.map(function(){
			return new dropDownBox_init($(this), options);
		}).get();
	};

	/**
	 * 定义初始化select控件转换为下拉列表框
	 */
	var dropDownBox_init = function($select, options){
		this.options = $.extend(true, {}, options || {})
		this.dom = {// typeof dom === jQuery
			select: $select,
			box: this._convertToBox($select)// 转换为下拉列表框
		};
		// 两者相互关联
		this._correlative();
		// 自定义渲染
		this._drawing();
		// 模拟用户界面
		this._simulation();
		// 返回下拉列表框 jQuery 对象
		return this;
	};

	/**
	 * 下拉列表框扩展对象
	 */
	dropDownBox_init.prototype = {
		// select 控件转换为 dropdownbox 下拉列表框
		_convertToBox: function($select){
			var $options = $('option', $select),// select 控件选项集合
				$caption = $options.filter(':selected'),// 标头
				tabIndex = $select.prop('tabIndex') || $select.data(DDB_SELECT_INDEX_NAME) || (DDB_BOX_INDEX += 1),
				templates = dropDownBox_init._templates,// 得到下拉列表框模板
				json = {// 得到下拉列表框数据模型
					ddbValue : $.trim($caption.attr('value')) || '', // ddb value
					ddbText : $.trim($caption.text()) || '',
					tabIndex : tabIndex,
					width: Number(this.options.width || 0) || 0,
					className : this.options.className || '',// config class name
					list: $options.map(function(){
						return {
							value : $.trim($(this).attr('value')) || '',
							text : $.trim($(this).text()) || ''
						}
					}).get()
				},
				$box =  $(juicer(templates, json));// 通过模板数据转换为jQuery DropDownBox 对象

			// 隐藏 select 控件并插入或替换到DOM
			if (typeof this.dom !== 'undefined' && this.dom.box.length > 0) {
				this.dom.box.replaceWith($box);
			}
			else{
				$select.data(DDB_SELECT_INDEX_NAME, tabIndex);
				var nextDependon = $select.next('div[data-dependon="select"]');
				if (nextDependon.length > 0) {
					nextDependon.replaceWith($box);
				}
				else{
					$select.addClass('none').after($box);
				}
			}
			
			// 返回下拉列表框 jQuery 对象
			return $box;
		},
		// select 控件与 dropdownbox 下拉列表框相互关联值
		_correlative: function(){
			var that = this,
				dom = this.dom;
			// 移除.ddb事件
			dom.select.off('.ddb-oc');
			// 下拉框控件绑定更新事件
			dom.select
				.on('ddb-update.ddb-oc', function(e){
					// 更新联动数据
					if (that.options.updateLinkage) {
						dom.box = that._convertToBox($(this));
						// 两者相互关联
						that._correlative();
						// 自定义渲染
						that._drawing();
						// 模拟用户界面
						that._simulation();
						// 设置获取焦点和活动状态
						dom.box.focus();
						that._boxFocus();
					};
				});
		},
		// dropdownbox 下拉列表框实现自定义渲染
		_drawing: function(){
			var dom = this.dom,
				_dom = this._dom,
				width = Number(this.options.width || 0) || 0;
			if(!!width && width > 12){
				dom.box.css('width', width);
				_dom.getCaption.call(this).css('width', width - 12);
				_dom.getList.call(this).css('width', width - 2);
			}
		},
		// dropdownbox 下拉列表框实现模拟用户界面
		_simulation: function(){
			var that = this,
				dom = this.dom,
				$caption = that._dom.getCaption.call(this),
				$list = that._dom.getList.call(this);

			// 下拉列表项鼠标点击、进入、离开事件
			$list.undelegate('li', '.ddb-ds')
				.delegate('li', 'click.ddb-ds',function(e){
					that._selected($(this));
					that._pullUp();
					that._boxExtend();
					e.stopPropagation();
					e.cancelbubble = true;
				})
				.delegate('li', 'mouseenter.ddb-ds', function(){
					$(this).siblings('li').removeClass(DDB_ITEM_HOVER_CLASSNAME);
					$(this).addClass(DDB_ITEM_HOVER_CLASSNAME);
				})
				.delegate('li', 'mouseleave.ddb-ds', function(){
					$(this).removeClass(DDB_ITEM_HOVER_CLASSNAME);
				});

			// 下拉列表框获取焦点、点击事件
			dom.box.off('.ddb-os')
				.on('focus.ddb-os', function(){
					that._pullUp();
					that._boxFocus();
				})
				.on("click.ddb-os", function(e) {
					if ($list.is(':visible')) {
						that._pullUp();
						that._boxExtend();
						return false;
					} 
					else {
						var $window = $(window),
							_windowTopSpace = ($window.scrollTop() + document.documentElement.clientHeight) - $(this).offset().top,
							_listSpace = $list.outerHeight(true) + 1,
							_captionSpace = $caption.outerHeight(true),
							_windowBotSpace = $(this).offset().top - $window.scrollTop() - _listSpace,
							_listIsUpwards = _windowTopSpace < _listSpace && _windowBotSpace > 0,
							_listTop = _listIsUpwards ? - _listSpace : _captionSpace;
						
						$list.css('top', _listTop);
						that._pullDown();
						that._boxExpand(_listIsUpwards);
						$window.off('.ddb-win').on('scroll.ddb-win resize.ddb-win', function() {
							_windowTopSpace = ($window.scrollTop() + document.documentElement.clientHeight) - dom.box.offset().top;
							_listIsUpwards = _windowTopSpace < _listSpace;
							_listTop = _listIsUpwards ? - _listSpace : _captionSpace;

							$list.css('top', _listTop);
							that._boxExpand(_listIsUpwards);
						});
					};
					e.stopPropagation();
				})
				.on('mousewheel.ddb-os', function(e, delta) {
					if (!that.options.wheelScrolling) {
						e.preventDefault();
					};
					delta > 0 ? that._movePrev(null) : that._moveNext(null);
				})
				.on("dblclick.ddb-os", function() {
					that._pullUp();
					return false;
				})
				.on("blur.ddb-os", function() {
					that._pullUp();
					that._boxExtend();
					return false;
				})
				.on("selectstart.ddb-os", function() {
					return false;
				})
				.on('keydown.ddb-os', function(e){
					if (e.keyCode == 35 || e.keyCode == 36 || e.keyCode == 38 || e.keyCode == 40) {
						return false;
					}
				})
				.on("keydown.ddb-os", function(e) {
					var $activeItem = that._dom.getActiveItem.call(that);

					switch (e.keyCode) {
						case 9: // TAB
						case 13: // ENTER
							that._selected($activeItem);
							that._pullUp();
							that._boxExtend();
							break;
						case 27: // ESC
							that._pullUp();
							that._boxExtend();
							break;
						case 33: // Prior
						case 36: // Home
							that._movePrev(that._dom.getFirstItem.call(that));
							return false;
							break;
						case 34: // Next
						case 35: // End
							that._moveNext(that._dom.getLastItem.call(that));
							return false;
							break;
						case 38: // Up
							that._movePrev(null);
							return false;
							break;
						case 40: // Down
							that._moveNext(null);
							return false;
							break;
						default:
							e.preventDefault();
							break;
					};
				});
		},
		// 展开下拉框
		_pullDown: function(){
			this.dom.box.css('z-index', DDB_BOX_ZINDEX += 1);
			this._dom.getList.call(this).removeClass('none');
		},
		// 收缩下拉框
		_pullUp: function(){
			this._dom.getList.call(this).addClass('none');
		},
		// 下拉列表框呈现获取焦点状态
		_boxFocus: function(){
			this.dom.box.addClass(DDB_BOX_FOCUS_CLASSNAME);
		},
		// 下拉列表框呈现失去焦点状态
		_boxBlur: function(){
			this.dom.box.removeClass(DDB_BOX_FOCUS_CLASSNAME);
		},
		// 下拉列表框呈现展开列表状态
		_boxExpand: function(isUpwards){
			this.dom.box.addClass(DDB_BOX_FOCUS_CLASSNAME);
			if (isUpwards) {
				this.dom.box.removeClass(DDB_BOX_EXPAND_DOWN_CLASSNAME);
				this.dom.box.addClass(DDB_BOX_EXPAND_UP_CLASSNAME);
			}
			else{
				this.dom.box.removeClass(DDB_BOX_EXPAND_UP_CLASSNAME);
				this.dom.box.addClass(DDB_BOX_EXPAND_DOWN_CLASSNAME);
			}
		},
		// 下拉列表框呈现伸缩列表状态
		_boxExtend: function(){
			this.dom.box.removeClass(DDB_BOX_FOCUS_CLASSNAME);
			this.dom.box.removeClass(DDB_BOX_EXPAND_DOWN_CLASSNAME);
			this.dom.box.removeClass(DDB_BOX_EXPAND_UP_CLASSNAME);
		},
		// 选择一项下拉列表项
		_selected: function($item){
			var $select = this.dom.select,
				$caption = this._dom.getCaption.call(this),
				$items = this._dom.getItems.call(this),
				_ddbValue = $.trim($item.attr('value')),
				_ddbText = $.trim($item.text()),
				_index = $items.index($item);

			if ($select.length > 0) {
				$select[0].selectedIndex = _index;
				$select.change();
			};
			$caption.attr(DDB_CAPTION_VALUE_NAME, _ddbValue);
			$caption.text(_ddbText);
		},
		// 向指定的下拉列表项或当前已选择的下拉列表项向上移动一项
		_movePrev: function($item){
			var _dom = this._dom,
				_dependOnSelecting = this.options.dependOnSelecting,
				_autoUseSelecting = this.options.autoUseSelecting,
				$list = _dom.getList.call(this),
				$curItem = $item || (_dependOnSelecting && _autoUseSelecting ? _dom.getSelectingItem : _dom.getActiveItem).call(this),
				$prevItem = $curItem.prev('li'),
				isPeaked = $prevItem.length == 0,
				$selectItem = !isPeaked ? $prevItem : $curItem;
			if (!isPeaked) {
				$prevItem.siblings('li').removeClass(DDB_ITEM_HOVER_CLASSNAME);
				$prevItem.addClass(DDB_ITEM_HOVER_CLASSNAME);
			}
			// 判断是否自动选择滚轮滚动、键盘切换的项
			if (_autoUseSelecting) {
				this._selected($selectItem);
			};
		},
		// 向指定的下拉列表项或当前已选择的下拉列表项向下移动一项
		_moveNext: function($item){
			var _dom = this._dom,
				_dependOnSelecting = this.options.dependOnSelecting,
				_autoUseSelecting = this.options.autoUseSelecting,
				$list = _dom.getList.call(this),
				$curItem = $item || (_dependOnSelecting && _autoUseSelecting ? _dom.getSelectingItem : _dom.getActiveItem).call(this),
				$nextItem = $curItem.next('li'),
				isTail = $nextItem.length == 0,
				$selectItem = !isTail ? $nextItem : $curItem;
			if (!isTail) {
				$nextItem.siblings('li').removeClass(DDB_ITEM_HOVER_CLASSNAME);
				$nextItem.addClass(DDB_ITEM_HOVER_CLASSNAME);
			}
			// 判断是否自动选择滚轮滚动、键盘切换的项
			if (_autoUseSelecting) {
				this._selected($selectItem);
			};
		},
		// 查询 DOM jQuery 对象
		_dom: {
			// 获取标头
			getCaption: function(){
				return $('h4[data-ddb-type="caption"]', this.dom.box);
			},
			// 获取列表
			getList: function(){
				return $('ul[data-ddb-type="list"]', this.dom.box);
			},
			// 获取列表项集合
			getItems: function(){
				return $('li', this._dom.getList.call(this));
			},
			// 获取当前活动的下拉列表项
			getActiveItem: function(){
				var $list = this._dom.getList.call(this),
					$activeItem = $('li.' + DDB_ITEM_HOVER_CLASSNAME, $list),
					isExist = $activeItem.length > 0;
				if (!isExist) {
					$activeItem = this._dom.getSelectingItem.call(this);
				}
				// 活动下拉列表项优先等级 li.current > caption[selected] > li:first
				return $activeItem;
			},
			// 获取当前预选的下拉列表项
			getSelectingItem: function(){
				var $list = this._dom.getList.call(this),
					$caption = this._dom.getCaption.call(this),
					$SelectingItem = null,
					ddbValue = $.trim($caption.attr(DDB_CAPTION_VALUE_NAME)) || '';

				if (!!ddbValue) {
					$SelectingItem = $('li[value="'+ ddbValue +'"]', $list);
				}
				else{
					$SelectingItem = this._dom.getFirstItem.call(this);
				}
				// 预选下拉列表项优先等级 caption[selected] > li:first
				return $SelectingItem;
			},
			// 获取第一个下拉列表项
			getFirstItem: function(){
				return $('li:first', this._dom.getList.call(this));
			},
			// 获取最后一个下拉列表项
			getLastItem: function(){
				return $('li:last', this._dom.getList.call(this));
			}
		}
	};

	/**
	 * 下拉列表框HTML模板
	 */
	dropDownBox_init._templates = '<div tabIndex="${tabIndex}" class="dropdownbox ${className}">'+
					            '<h4 class="dropdowncaption" data-ddb-value="${ddbValue}" data-ddb-type="caption">${ddbText}</h4>'+
					            '<ul data-ddb-type="list" class="dropdownlist none">'+
					            	'{@each list as item}'+
					                '<li {@if item.value === ddbValue}class="current"{@/if} value="${item.value}">${item.text}</li>'+
					                '{@/each}'+
					            '</ul>'+
					        '</div>';

	/**
	 * jQuery下拉列表扩展函数
	 */
	;(function($){
		$.fn.extend({
			dropDownBox: function(){

			}
		});
	}(jQuery));

	/**
	 * jQuery 的 html、append、appendTo、prepend、prependTo方法重写
	 */
	;(function($){
		var fnExtends = {},
			methods = ['append', 'prepend'];// html=>append、appendTo=>append、prependTo=>prepend

		// 遍历方法集合重写 jQuery 上的扩展方法
		$.each(methods, function(i, method){
			fnExtends[method] = $.fn[method];
			(function(method){
				$.fn[method] = function(){
					fnExtends[method].apply(this, arguments);
					if($(this).is('select')){
						$(this).trigger('ddb-update');
					}
				};
			}(method));
		});
	}(jQuery));
	/**
	 * jQuery增加鼠标中间功能mousewheel
	 */
	;(function ($) {
	    var types = ['DOMMouseScroll', 'mousewheel'];
	    $.event.special.mousewheel = {
	        setup: function () {
	            if (this.addEventListener) {
	                for (var i = types.length; i;) {
	                    this.addEventListener(types[--i], handler, false);
	                }
	            } else {
	                this.onmousewheel = handler;
	            }
	        },
	        teardown: function () {
	            if (this.removeEventListener) {
	                for (var i = types.length; i;) {
	                    this.removeEventListener(types[--i], handler, false);
	                }
	            } else {
	                this.onmousewheel = null;
	            }
	        }
	    };
	    $.fn.extend({
	        mousewheel: function (fn) {
	            return fn ? this.bind("mousewheel", fn) : this.trigger("mousewheel");
	        },
	        unmousewheel: function (fn) {
	            return this.unbind("mousewheel", fn);
	        }
	    });

	    function handler(event) {
	        var orgEvent = event, args = [].slice.call(arguments, 1), delta = 0, returnValue = true, deltaX = 0, deltaY = 0;
	        event = $.event.fix(orgEvent);
	        event.type = "mousewheel";
	        // Old school scrollwheel delta
	        if (event.originalEvent.wheelDelta) { delta = event.originalEvent.wheelDelta / 120; }
	        if (event.originalEvent.detail) { delta = -event.originalEvent.detail / 3; }
	        // New school multidimensional scroll (touchpads) deltas
	        deltaY = delta;
	        // Gecko
	        if (orgEvent.axis !== undefined && orgEvent.axis === orgEvent.HORIZONTAL_AXIS) {
	            deltaY = 0;
	            deltaX = -1 * delta;
	        }
	        // Webkit
	        if (orgEvent.wheelDeltaY !== undefined) { deltaY = orgEvent.wheelDeltaY / 120; }
	        if (orgEvent.wheelDeltaX !== undefined) { deltaX = -1 * orgEvent.wheelDeltaX / 120; }
	        // Add event and delta to the front of the arguments
	        args.unshift(event, delta, deltaX, deltaY);
	        return $.event.dispatch.apply(this, args);
	    }
	})(jQuery);

	// 添加扩展接口
	module.exports = dropDownBox;
});