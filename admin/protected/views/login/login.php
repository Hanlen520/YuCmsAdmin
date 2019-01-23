<input type="hidden" id="TenantId" name="TenantId" value="" />
<div class="header"></div>
<div class="loginWraper">
  <div id="loginform" class="loginBox">
    <form class="form form-horizontal" id="loginform" method="post" action="<?php echo $this->createUrl('login/login');?>" name="login">
      <div class="row cl">
        <label class="form-label col-3"><i class="Hui-iconfont">&#xe60d;</i></label>
        <div class="formControls col-8">
          <input name="Login[user_name]" id="username" type="text" placeholder="账户" class="input-text size-L">
        </div>
      </div>
      <div class="row cl">
        <label class="form-label col-3"><i class="Hui-iconfont">&#xe60e;</i></label>
        <div class="formControls col-8">
          <input name="Login[password]" id="password" type="password" placeholder="密码" class="input-text size-L">
        </div>
      </div>
      <div class="row cl">
        <div class="formControls col-8 col-offset-3">
          <input name="Login[scode]" id="scode" maxlength=4 class="input-text size-L" type="text" placeholder="验证码" onblur="if(this.value==''){this.value='验证码:'}" onclick="if(this.value=='验证码:'){this.value='';}" value="验证码:" style="width:150px;">
      	  <?php $this->widget('CCaptcha', array('showRefreshButton' => false, 'clickableImage' => true, 'imageOptions' => array('alt' => '点击换图', 'title' => '点击换图', 'style' => 'cursor:pointer'))); ?>
      </div>
      <div class="row">
        <div class="formControls col-8 col-offset-3">
          <label for="online">
            <input type="checkbox" name="online" id="online" value="">
            使我保持登录状态</label>
        </div>
      </div>
      <div class="row">
        <div class="formControls col-8 col-offset-3">
          <input name="" type="submit" class="btn btn-success radius size-L" value="&nbsp;登&nbsp;&nbsp;&nbsp;&nbsp;录&nbsp;">
          <input name="" type="reset" class="btn btn-default radius size-L" value="&nbsp;取&nbsp;&nbsp;&nbsp;&nbsp;消&nbsp;">
        </div>
      </div>
    </form>
  </div>
</div>
