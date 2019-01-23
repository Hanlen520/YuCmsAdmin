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
		<?php echo $form->labelEx($model,'name',array('class'=>'form-label col-3')); ?>
		<div class="formControls col-8"><?php echo $model->name;?></div>
		<div class="col-8"> </div>
	</div>
	<div class="row cl">
		<?php echo $form->labelEx($model,'description',array('class'=>'form-label col-3')); ?>
		<div class="formControls col-8"><?php echo $model->description;?></div>
		<div class="col-8"> </div>
	</div>
	<div class="row cl">
		<?php echo $form->labelEx($modelAC,'purview',array('class'=>'form-label col-3')); ?>
		&nbsp;&nbsp;<input type="checkbox" title="全选" id="tg_checkAll" /> 全选
	</div>
	<?php if ($AllPurview) :?>
		<div class="row cl">
			<label class="form-label col-3"></label>
			<div class="formControls col-3" style="background-color:#edfbf2;border:1px solid #c6e9dd;width:500px">
				<ul class="ul-list" style="padding-top: 0px;margin-left: 10px">
					<table border="0" width="100%">
						<?php
                            $i=1;
                            foreach ($AllPurview as $k => $p){
							$str = '';

							//if ($k % 6 == 0 && $k != 0) $str .= "<br>";
							$str .= "<span style ='display:inline-block; width:140px;line-height:25px;'><input type = 'checkbox'  id = 'RolePurview_".$i."' name = 'RolePurview[]' value = '".$k . "'";
							if ($RolePurview && in_array($k, $RolePurview)) $str .= " checked";
							$str .= ">\t".$p."</span>";
                            $i++;
							echo $str;
						}
						?>
					</table>
				</ul>
			</div>
		</div>
	<?php endif;  ?>
		<div class="col-8"> </div>
	</div>
	<div class="row cl">
		<div class="col-8 col-offset-4">
			<input class="btn btn-primary radius" type="button" url="<?php echo $this->createUrl('role/Purview/name/'.$model->name)?>"value="&nbsp;&nbsp;保存&nbsp;&nbsp;">
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
							layer_close();
							window.location.href = r.data.url;

						});
					} else
					{
						layer.msg(r.msg,{icon:2,time:2000});
					}
				}
			});
		})

        $('#tg_checkAll').click(function(){
            $("[id^=RolePurview_]").prop('checked' ,this.checked);
            var $subBox = $("[id^=RolePurview_]");
            $subBox.click(function(){
                $("#tg_checkAll").prop("checked",$subBox.length == $("[id^=RolePurview_]:checked").length ? true : false);
            });
        });
	});
</script>
</body>
</html>