<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <?php if (SITE_URL) :?>
        <base href="<?php echo SITE_URL;?>" />
    <?php endif;?>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="lib/html5.js"></script>
    <script type="text/javascript" src="lib/respond.min.js"></script>
    <script type="text/javascript" src="lib/PIE_IE678.js"></script>
    <![endif]-->
    <link href="css/H-ui.min.css" rel="stylesheet" type="text/css" />
    <link href="css/H-ui.admin.css" rel="stylesheet" type="text/css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <!--[if IE 6]>
    <script type="text/javascript" src="http://lib.h-ui.net/DD_belatedPNG_0.0.8a-min.js" ></script>
    <script>DD_belatedPNG.fix('*');</script>
    <![endif]-->
</head>
<body>
<div class="pd-20">
    <!-- 	<form action="/" method="post" class="form form-horizontal" id="form-change-password"> -->
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'                   => 'edit-form',
        'enableAjaxValidation' => false,
        'htmlOptions'          => array('class'=>'form form-horizontal')
    )); ?>
    <div class="row cl">
        <?php echo $form->labelEx($model,'title',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-3"><?php echo $model->title?></div>
        <div class="col-3"> </div>
    </div>
    <div class="row cl">
        <?php echo $form->labelEx($model,'content',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-8"><?php echo $model->content?></div>
        <div class="col-3"> </div>
    </div>
    <div class="row cl">
        <?php echo $form->labelEx($model,'controller_name',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-3"><?php echo $model->controller_name?></div>
        <div class="col-3"> </div>
    </div>
    <div class="row cl">
        <?php echo $form->labelEx($model,'action_name',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-3"><?php echo $model->action_name?></div>
        <div class="col-3"> </div>
    </div>
    <div class="row cl">
        <?php echo $form->labelEx($model,'url',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-3"><?php echo $model->url?></div>
        <div class="col-3"> </div>
    </div>
    <div class="row cl">
        <?php echo $form->labelEx($model,'ip',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-3"><?php echo $model->ip?></div>
        <div class="col-3"> </div>
    </div>
    <div class="row cl">
        <?php echo $form->labelEx($model,'admin_user_name',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-3"><?php echo $model->admin_user_name?></div>
        <div class="col-3"> </div>
    </div>
    <div class="row cl">
        <?php echo $form->labelEx($model,'add_time',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-3"><?php echo date("Y-m-d H:i:s", $model->add_time);?></div>
        <div class="col-3"> </div>
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
        $('.btn').click(function(){
            $.ajax({
                type:'POST',
                url: $(this).attr('url'),
                dataType:'json',
                data:$('#edit-form').serialize(),
                success:function(r){
                    if (r.status == 1)
                    {
                        layer.msg(r.msg,{icon:1,time:2000},function(){
                            //var index = parent.layer.getFrameIndex(window.name);
                            //parent.layer.close(index);
                            window.location.href = r.data.url;
                            //layer_close();
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