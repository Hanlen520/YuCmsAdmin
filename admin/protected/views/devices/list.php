<title>设备列表</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 设备管理 <span class="c-gray en">&gt;</span> 设备申请 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'                   => 'edit-form',
        'enableAjaxValidation' => false,
        'action'               => $this->createUrl('index'),
    )); ?>
    <div class="clearfix  text-l">
        <span class="text-l f-14 inline ml-50 mr-10"><?php echo $model->getAttributeLabel('platform');?>:</span>
        <?php echo $form->DropDownList($model, 'platform', $model->platformArray, array('class' => 'text-l select-box inline ', 'empty' => array('' => '全部'))); ?>

        <span class="text-l f-14 inline ml-50 mr-10"><?php echo $model->getAttributeLabel('category');?>:</span>
        <?php echo $form->DropDownList($model, 'category', $model->categoryArray, array('class' => 'text-l select-box inline', 'empty' => array('' => '全部'))); ?>

        <span class="text-l f-14 inline ml-50 mr-10"><?php echo $model->getAttributeLabel('brand');?>:</span>
        <?php echo $form->DropDownList($model, 'brand', $model->brandArray, array('class' => 'text-l select-box inline', 'empty' => array('' => '全部'))); ?>

        <span class="text-l f-14 inline ml-50 mr-10"><?php echo $model->getAttributeLabel('status');?>:</span>
        <?php echo $form->DropDownList($model, 'status', $model->statusArray, array('class' => 'text-l select-box inline', 'empty' => array('' => '全部'))); ?>
    </div>
    <div class="clearfix  text-l mt-20">
        <span class="text-l f-14 inline ml-50 mr-10"><?php echo $model->getAttributeLabel('model');?>:</span>
        <?php echo $form->textField($model,'model', array('class' => 'input-text inline'));?>
        <span class="text-l f-14 inline ml-50 mr-10"><?php echo $model->getAttributeLabel('borrower');?>:</span>
        <?php echo $form->textField($model,'borrower', array('class' => 'input-text inline'));?>
        <button  type="submit" class="btn btn-default ml-50 mr-10" id="search_button" href="javascript:void(0)"><i class="Hui-iconfont"></i>查询</button>
    </div>
    <div class="clearfix  text-l mt-20">

    </div>
    <?php $this->endWidget();?>

    <div class="cl pd-5 bg-1 bk-gray mt-20">
        <span class="l">共有数据：<strong><?php echo $totalNums?></strong> 条</span>
    </div>
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'                   => 'edit-form',
        'enableAjaxValidation' => false,
        'htmlOptions'          => array('class'=>'form form-horizontal')
    )); ?>
    <table class="table table-border table-bordered table-bg" id="table_list">
        <thead>
        <tr>
            <th scope="col" colspan="9">设备列表</th>
        </tr>
        <tr class="text-c">
            <th width="5">id</th>
            <th width="25">型号</th>
            <th width="25">设备名称</th>
            <th width="50">IMEI</th>
            <th width="25">状态</th>
            <th width="25">借用人</th>
            <th width="50">借出时间</th>
            <th width="25">所属人</th>
            <th width="50">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($list) :?>
            <?php foreach ($list as $key => $val):?>
                <tr class="text-c">
                    <td><?php echo $val['id'];?></td>
                    <td><?php echo $val['model'];?></td>
                    <td><?php echo $val['device_name'];?></td>
                    <td><?php echo $val['theNum'];?></td>
                    <td>
                        <?php if($val['status'] == '0'):?>
                            <font color="#00FF00" class="Hui-iconfont"><?php echo $model->statusArray[$val['status']] ;?></font>
                        <?php elseif($val['status'] == '1'):?>
                            <font color="#FF0000" class="Hui-iconfont"><?php echo $model->statusArray[$val['status']] ;?></font>
                        <?php else:?>
                            <font color="#585858" class="Hui-iconfont"><?php echo $model->statusArray[$val['status']] ;?></font>
                        <?php endif;?>
                    </td>
                    <td>
                        <input class="input-text" style="text-align:center" value="<?php echo $val['borrower'];?>" data-id="<?php echo $val['id'];?>"  <?php echo $val['status'] == '0' ? '' : "disabled ='disabled'";?>/></td>
                    </td>
                    <td><?php echo $val['borrow_time'] ? $val['borrow_time'] : '--';?></td>
                    <td><?php echo $val['owner'];?></td>
                    <td>
                        <?php if($val['status'] == '0'):?>
                            <a title="申请" href="javascript:;" onclick="admin_devices_set(this,<?php echo $val['id'] ?>, 1)" class="btn btn-primary radius"><i class="Hui-iconfont">申请</i></a>
                        <?php elseif($val['status'] == '1'):?>
                            <a title="取消" href="javascript:;" onclick="admin_devices_set(this,<?php echo $val['id'] ?>, 0)" class="btn btn-danger radius"><i class="Hui-iconfont">取消</i></a>
                        <?php else:?>
                            <i class="Hui-iconfont">--</i>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach;?>
        <?php else :?>
            <tr class="text-c">
                <td colspan="5" style="text-align:center">对不起，没有找到相应的记录！</td>
            </tr>
        <?php endif;?>
        </tbody>
    </table>
    <?php $this->endWidget(); ?>
</div>
<script type="text/javascript" src="lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="lib/layer/1.9.3/layer.js"></script>
<script type="text/javascript" src="lib/laypage/1.2/laypage.js"></script>
<script type="text/javascript" src="lib/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="js/H-ui.js"></script>
<script type="text/javascript" src="js/H-ui.admin.js"></script>
<script type="text/javascript">
    /*
     参数解释：
     title	标题
     url		请求的url
     id		需要操作的数据id
     w		弹出层宽度（缺省调默认值）
     h		弹出层高度（缺省调默认值）
     */
    /*设备-添加*/
    function admin_devices_add(title,url,w,h){
        layer_show(title,url,w,h);
    }

    /*设备-编辑*/
    function admin_devices_edit(title,url,id,w,h){
        layer_show(title,url,w,h);
    }

    /*设备-申请、取消*/
    function admin_devices_set(obj,id,status){
        msg = status == 0 ? '取消': '';
        name = $(obj).closest('tr').children().eq(5).find('.input-text').val()
        layer.confirm('确认要'+msg+'申请该设备吗？',function(index){
            $.ajax({
                type:'GET',
                url: "<?php echo $this->createUrl('setstate')?>",
                dataType:'json',
                data:{id:id,status:status,borrower:name},
                success:function(r){
                    if (r.status == 1)
                    {
                        layer.msg(r.msg,{icon:1,time:2000},function(){
                            //$(obj).parents("tr").remove();
                            window.location.href = r.data.url;
                        });
                    } else
                    {
                        layer.msg(r.msg,{icon:2,time:2000});
                    }
                }
            });
        });
    }

    $('tbody tr').hover(function(){
        $(this).addClass('selected');
    },function(){
        $(this).removeClass('selected');
    });
</script>