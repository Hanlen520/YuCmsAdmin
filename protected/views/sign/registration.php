<ul class="nav">
				<?php foreach($nav as $key=>$val):?>
					<li><a  href="<?php  echo $key ==46 || $key ==47 ? $this->createUrl('index/about',array('cid'=>$key )): $this->createUrl('index/info',array('cid'=>$key ));?>"><?php echo $val;?></a></li>
				<?php endforeach;?>
				<li><a href="<?php  echo $this->createUrl('index/index')?>" class="current">泰策首页</a></li>
			</ul>
		</div>
	</div>
</div>
	<!-- // hearder -->
	
	<div class="ui-cont">
		<p class="subbanner"></p>
	</div>

	<div class="ui-cont">
		<div class="ui-subcont sublayout">
			<div style="  width:1005px; height:116px; ">
				<h3 style="border-bottom:#cccccc 1px solid;height:58px;"></h3>
				<h3 style=" text-align:center; font-size:26px; margin:0 auto; margin-top:-30px; background:#ffffff;  padding:5px;width:160px; font-weight:normal;"><?php echo $title;?></h3>
			</div>
		
			<h4 class="r-tips">特别说明：客户通过网站填写需求，费用享受9.5折优惠。</h4>
			<div class="r-cont">
				<img src="<?php echo SITE_URL; ?>assets/images/tip_registration.png">
				<form class="form-horizontal r-horizontal"  id="info_form">
		            <ul>
		            	<li class="dl-horizontal">
			              	<label class="control-label"><span style="color:red">*</span>企业名称</label>
			              	<div class="controls">
			                	<input type="text" class="ui-input-text"  name="company" maxlength="20">
			              	</div>
		            	</li>
		            	<li class="dl-horizontal">
			              	<label for="inputPassword" class="control-label">需求信息</label>
			             	 <div class="controls">
			                	<select id="fn_select" style="display:none" name="course_name">
			                		<?php foreach($course as $key=>$val):?>
									<option value="<?php echo $val['title'];?>"><?php echo $val['title'];?></option>
									<?php endforeach;?>
			                	</select>
			              	</div>
		            	</li>
		            	<li class="dl-horizontal">
			              	<label for="inputPassword2" class="control-label">需要改善问题</label>
			              	<div class="controls">
			                	<textarea rows="6" name="require" maxlength="10"></textarea>
			              	</div>
		            	</li>
		            	<li class="dl-horizontal">
			              	<label class="control-label">部门</label>
			              	<div class="controls">
			                	<input type="text" class="ui-input-text input-large" name="department" maxlength="10">
			              	</div>
		            	</li>
		            	<li class="dl-horizontal">
			              	<label  class="control-label"><span style="color:red">*</span>姓名</label>
			              	<div class="controls">
			                	<input type="text" class="ui-input-text input-large"  name="name" maxlength="6">
			              	</div>
		            	</li>
		            	<li class="dl-horizontal">
			              	<label for="inputPassword2" class="control-label"><span style="color:red">*</span>性别</label>
			              	<div class="controls">
			                	<label class="radio inline">
								  <input type="radio" class="ui-input-radio-checkbox input-cursor" name="gender"  value=1 checked="checked">男
								</label>
								<label class="radio inline">
								  <input type="radio" class="ui-input-radio-checkbox input-cursor" name="gender" value=2>女
								</label>
			              	</div>
		            	</li>
		            	<li class="dl-horizontal">
				            <label  class="control-label"><span style="color:red">*</span>手机号码</label>
			              	<div class="controls">
			                	<input type="text" class="ui-input-text input-large" name="mobile" maxlength="11">
			              	</div>
		            	</li>
		            	<li class="dl-horizontal">
				            <label class="control-label">微信号</label>
			                <div class="controls">
			                	<input type="text" class="ui-input-text input-large" name="weixin" maxlength="25">
			                </div>
		            	</li>
		            	<li class="dl-horizontal">
			              	<label class="control-label">邮箱</label>
			              	<div class="controls">
			                	<input type="text" class="ui-input-text input-large" name="email" maxlength="25">
			              	</div>
		            	</li>
		            	<li>
			              	<div class="controls">
			              		<!-- <a href="#" class="r-submit">提交</a> --> 
			              		<input class="r-submit" type="submit" value="提交" />
			              	</div>
		            	</li>
		            </ul>
		        </form>
			</div>
		</div>
	</div>

	<div class="push"><!-- not put anything here --></div>
</div>

<script type="text/javascript">
seajs.use(['dropdownbox', 'artDialog'], function (Dropdownbox,artDialog) {
	$(function () {
    	Dropdownbox('#fn_select', {
			className: '',
			width:200,
			wheelScrolling: true,
			autoUseSelecting: true,
			dependOnSelecting: true,
			updateLinkage: true
		});

    	var validate = $('#info_form').validate({
            debug: true, //调试模式取消submit的默认提交功能   
            focusInvalid: true, //当为false时，验证无效时，没有焦点响应  
            onfocusout: function(element) { $(element).valid(); },
            onkeyup: false,
            onsubmit: true,  
            submitHandler: function(form){   //表单提交句柄,为一回调函数，带一个参数：form   
            	var param = $("#info_form").serialize();  
                $.ajax({  
		           url : "<?php echo  $this->createUrl('deal')?>",  
		           type : "post",  
		           dataType : "json",  
		           data: param,  
		           success : function(result) {  
			           if(result) {
			        	   artDialog.tips({
								'type': 2,
								'content': '我们已经收到您的报名申请，将尽快与您联系！',
								'time': 9999,
							});
			        	    window.location.href='<?php echo  $this->createUrl('index/index')?>';  
			           } else {  
			                  var jsonObj = eval('('+result+')');  
			           }  
           		    }
                })
            },   
            rules:{
            	company:{
                    required:true,
                },
                name:{
               	 	required:true,
                },
                gender:{
                	required:true,
                },
                mobile:{
                	required:true,
                },
                email:{
                	email:true,
                }
            },
            messages:{
            	company:{
                    required:"请填写公司名称！",
                },
                name:{
               	 	required:"请填写姓名！",
                },
                gender:{
                	required:"请选择性别！",
                },
                mobile:{
                	required:"请填写手机号码！",
                },
                email:{
                	email:"邮箱格式不合法！",
                }
            }
        });    
    });
});
</script>
</body>
</html>
