<input type="hidden" id="TenantId" name="TenantId" value="" />
<div class="header"></div>
<div class="loginWraper">
  <div id="loginform" class="loginBox">
    <form autocomplete="off" class="form-horizontal" id="loginform" method="post" action="<?php echo $this->createUrl('login/login');?>" name="login">
      <div class="row cl">
        <label class="form-label col-3"><i class="Hui-iconfont">&#xe60d;</i></label>
        <div class="formControls col-8">
          <input name="Login[user_name]" id="username" type="text" placeholder="用户名:" class="input-text size-L" autocomplete="off">
        </div>
      </div>
      <div class="row cl">
        <label class="form-label col-3"><i class="Hui-iconfont">&#xe60e;</i></label>
        <div class="formControls col-8">
            <input name="Login[password]" id="password" type="password" placeholder="密码:"  class="input-text size-L" autocomplete="off">
        </div>
      </div>
      <div class="row cl">
          <label class="form-label col-3"><i class="Hui-iconfont">&#xe72d;</i></label>
          <div class="formControls col-8">
              <input name="Login[scode]" id="scode" maxlength=4 class="input-text size-L mr-20" type="text" placeholder="验证码:"  style="width:160px;">
              <?php $this->widget('CCaptcha', array('showRefreshButton' => false, 'clickableImage' => true, 'imageOptions' => array('alt' => '点击换图', 'title' => '点击换图', 'style' => 'cursor:pointer'))); ?>
          </div>
      </div>
      <div class="row cl">
        <div class="formControls col-8 col-offset-3">
          <button  type="submit" class="btn btn-success radius size-L mr-50" href="javascript:void(0)">登&nbsp;&nbsp;&nbsp;&nbsp;录</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript" src="lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="lib/Validform/5.3.2/Validform.min.js"></script>
<script type="text/javascript" src="lib/layer/1.9.3/layer.js"></script>
<script type="text/javascript" src="js/H-ui.js"></script>
<script type="text/javascript" src="js/H-ui.admin.js"></script>
<script>
    //判断浏览器是否支持 placeholder属性
    function isPlaceholder(){
        var input = document.createElement('input');
        return 'placeholder' in input;
    }

    if (!isPlaceholder()) {//不支持placeholder 用jquery来完成
        $(document).ready(function() {
            if(!isPlaceholder()){
                $("input").not("input[type='password']").each(//把input绑定事件 排除password框
                    function(){
                        if($(this).val()=="" && $(this).attr("placeholder")!=""){
                            $(this).val($(this).attr("placeholder"));
                            $(this).focus(function(){
                                if($(this).val()==$(this).attr("placeholder")) $(this).val("");
                            });
                            $(this).blur(function(){
                                if($(this).val()=="") $(this).val($(this).attr("placeholder"));
                            });
                        }
                    });
                //对password框的特殊处理1.创建一个text框 2获取焦点和失去焦点的时候切换
                var pwdField    = $("input[type=password]");
                var pwdVal      = pwdField.attr('placeholder');
                pwdField.after('<input id="pwdPlaceholder" name="Login[password]"  type="text" class="input-text size-L" value='+pwdVal+' autocomplete="off" />');
                var pwdPlaceholder = $('#pwdPlaceholder');
                pwdPlaceholder.show();
                pwdField.hide();

                pwdPlaceholder.focus(function(){
                    pwdPlaceholder.hide();
                    pwdField.show();
                    pwdField.focus();
                });

                pwdField.blur(function(){
                    if(pwdField.val() == '') {
                        pwdPlaceholder.show();
                        pwdField.hide();
                    }
                });

            }
        });

    }
</script>
