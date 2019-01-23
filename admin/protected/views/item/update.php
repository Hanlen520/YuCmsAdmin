<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <?php if (SITE_URL) :?>
        <base href="<?php echo SITE_URL;?>"></base>
    <?php endif;?>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="lib/html5.js"></script>
    <script type="text/javascript" src="lib/respond.min.js"></script>
    <script type="text/javascript" src="lib/PIE_IE678.js"></script>
    <![endif]-->
    <link href="css/H-ui.min.css" rel="stylesheet" type="text/css" />
    <link href="css/H-ui.admin.css" rel="stylesheet" type="text/css" />
    <!--[if IE 6]>
    <script type="text/javascript" src="http://lib.h-ui.net/DD_belatedPNG_0.0.8a-min.js" ></script>
    <script>DD_belatedPNG.fix('*');</script>
    <![endif]-->
</head>
<body>
<div class="pd-20">
    <!-- 	<form action="/" method="post" class="form form-horizontal" id="form-change-password"> -->
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'                   => 'form-change-password',
        'enableAjaxValidation' => false,
        'htmlOptions'          => array('class'=>'form form-horizontal')
    )); ?>
    <div class="row cl">
        <label class="form-label col-3"><span class="c-red">*</span>控制器：</label>
        <div class="formControls col-3"><span><?php echo $data['controller'];?></span></div>
        <div class="col-3"> </div>
    </div>
    <div class="row cl">
        <label class="form-label col-3"><span class="c-red">*</span>方法：</label>
        <div class="formControls col-3">
            <?php echo !$Description ? '[未授权]' : '请更改以下描述';?>
        </div>
        <div class="col-3"> </div>
    </div>
    <?php if ($Description) :?>
        <div class="row cl">
            <label class="form-label col-3"></label>
            <div class="formControls col-3" style="background-color:#edfbf2;border:1px solid #c6e9dd;width:500px">
                <ul class="ul-list" style="padding-top: 0px;margin-left: 10px">
                    <table border="0" width="100%">
                        <tr height="35">
                            <td width="35%" style="font-size: 14px"><b>方法</b></td>
                            <td style="font-size: 14px"><b>描述</b></td>
                        </tr>
                        <?php foreach ($Description as $k => $v) :?>
                            <tr height="35">
                                <td><?php echo $v['name'];?></td>
                                <td><input type="text" class="input-text" name="description[<?php echo $v['name'];?>]" value="<?php echo $v['description'];?>"></td>
                            </tr>
                        <?php endforeach;?>
                    </table>
                </ul>
            </div>
        </div>
    <?php endif;  ?>
    <div class="row cl">
        <?php if ($Description) :?>
            <div class="col-8 col-offset-3">
                <input class="btn btn-primary radius" type="button" value="&nbsp;&nbsp;更新&nbsp;&nbsp;">
            </div>
        <?php endif;  ?>
    </div>
    <?php $this->endWidget(); ?>
</div>
<script type="text/javascript" src="lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="lib/Validform/5.3.2/Validform.min.js"></script>
<script type="text/javascript" src="lib/layer/1.9.3/layer.js"></script>
<script type="text/javascript" src="js/H-ui.js"></script>
<script type="text/javascript" src="js/H-ui.admin.js"></script>
<script>
    $(function(){
        var objAllChecked = $(".selectAllCheck"),
            objChecked    = $(".selectCheck");
        objAllChecked.click(function()
        {
            var $obj = $(this),
                checked  = $obj.prop("checked");
            if (!checked)
                objChecked.attr("checked", false);
            else
                objChecked.attr("checked", true);
        });

        objChecked.click(function()
        {
            var $obj   = $(this),
                checked = $obj.prop("checked"),
                isAllChecked = true;

            if (!checked)
                objAllChecked.attr("checked", false);
            else
            {
                objChecked.each(function(){
                    var $obj = $(this);
                    if (!$obj.prop("checked"))
                        isAllChecked = false;
                });

                if (isAllChecked)
                    objAllChecked.attr("checked", true);
            }
        });
        $('.btn').click(function(){
            $.ajax({
                type:'POST',
                url: "<?php echo $this->createUrl('update', array('name' => $data['name']))?>",
                dataType:'json',
                data:$('#form-change-password').serialize(),
                success:function(r){
                    if (r.status == 1)
                    {
                        layer.msg(r.msg,{icon:1,time:2000},function(){
                            layer_close();
                        });
                    } else
                    {
                        layer.msg(r.msg,{icon:2,time:2000});
                    }
                }
            });
        })
    });
</script>
</body>
</html>