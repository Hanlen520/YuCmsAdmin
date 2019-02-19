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
    <link href="lib/Hui-iconfont/1.0.8/iconfont.css" rel="stylesheet" type="text/css" />
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
        <?php echo $form->labelEx($model,'device_name',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-6"><?php echo $form->textField($model,'device_name', array('class' => 'input-text'));?></div>
    </div>
    <div class="row cl">
        <?php echo $form->labelEx($model,'model',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-6"><?php echo $form->textField($model,'model', array('class' => 'input-text'));?></div>
    </div>
    <div class="row cl">
        <?php echo $form->labelEx($model,'theNum',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-6"><?php echo $form->textField($model,'theNum', array('class' => 'input-text'));?></div>
    </div>
    <div class="row cl">
        <?php echo $form->labelEx($model,'platform',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-4"><?php echo $form->DropDownList($model,'platform', $model->platformArray, array('empty' => array('' => '请选择'),'class'=>'text-l select-box')); ?></div>
    </div>
    <div class="row cl">
        <?php echo $form->labelEx($model,'brand',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-4"><?php echo $form->DropDownList($model,'brand', $model->brandArray, array('empty' => array('' => '请选择'),'class'=>'text-l select-box')); ?></div>
    </div>
    <div class="row cl">
        <?php echo $form->labelEx($model,'category',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-4"><?php echo $form->DropDownList($model,'category', $model->categoryArray, array('empty' => array('' => '请选择'),'class'=>'text-l select-box')); ?></div>
    </div>
    <div class="row cl">
        <?php echo $form->labelEx($model,'version',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-6"><?php echo $form->textField($model,'version', array('class' => 'input-text'));?></div>
    </div>
    <div class="row cl">
        <?php echo $form->labelEx($model,'owner',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-6"><?php echo $form->textField($model,'owner', array('class' => 'input-text'));?></div>
    </div>
    <div class="row cl">
        <?php echo $form->labelEx($model,'other',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-6"><?php echo $form->textField($model,'other', array('class' => 'input-text'));?></div>
    </div>
    <div class="row cl">
        <?php echo $form->labelEx($model,'comments',array('class'=>'form-label col-3')); ?>
        <div class="formControls col-8"><?php echo $form->textarea($model,'comments', array('class' => 'input-text', 'rows'=>'3','cols'=>'6', 'maxlength'=>60));?></div>
    </div>
    <div class="row cl">
        <div class="col-offset-4">
            <input class="btn btn-primary radius" type="button" url="<?php echo $model->id ? $this->createUrl('devices/edit/id/'.$model->id) : $this->createUrl('add')?>" value="&nbsp;&nbsp;保存&nbsp;&nbsp;">
        </div>
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
        $('.btn-primary').click(function(){
            $.ajax({
                type:'POST',
                url: $(this).attr('url'),
                dataType:'json',
                data:$('#edit-form').serialize(),
                success:function(r){
                    if (r.status == 1)
                    {
                        layer.msg(r.msg,{icon:1,time:2000},function(){
                            parent.location.reload();
                            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                            parent.layer.close(index);

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