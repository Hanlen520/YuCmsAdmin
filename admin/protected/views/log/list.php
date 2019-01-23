<title>日志列表</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 菜单管理 <span class="c-gray en">&gt;</span> 菜单列表 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
    <div class="cl pd-5 bg-1 bk-gray mt-20"><span class="l">
    <a href="javascript:;" onclick="admin_log_del()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;
        </i>批量删除</a></span><span class="r">共有数据：<strong><?php echo $totalNums;?></strong> 条</span> </div>
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'                   => 'list-form',
        'enableAjaxValidation' => false,
        'htmlOptions'          => array('class'=>'form form-horizontal')
    )); ?>
    <table class="table table-border table-bordered table-bg" id="table_list">
        <thead>
        <tr>
            <th scope="col" colspan="7">日志列表</th>
        </tr>
        <tr class="text-c">
            <th width="2%"><input type="checkbox" title="全选" id="tg_checkAll" onclick="selectAll(this)" /></th>
            <th width="150">操作标题</th>
            <th width="200">URL地址</th>
            <th width="100">操作用户</th>
            <th width="100">IP地址</th>
            <th width="150">操作时间</th>
            <th width="50">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($list) :?>
        <?php foreach ($list as $key => $val):?>
                <tr class="text-c">
                    <td><input name="ids[]" class="chk-all" value="<?php echo $val['log_id'];?>" type="checkbox"></td>
                    <td><?php echo $val['title'];?></td>
                    <td><?php echo $val['url']?></td>
                    <td><?php echo $val['admin_user_name']?></td>
                    <td><?php echo $val['ip']?></td>
                    <td><?php echo date("Y-m-d H:i:s", $val['add_time'])?></td>
                    <td>
                        <a title="查看" href="javascript:;" onclick="admin_log_view('查看日志','<?php echo $this->createUrl('log/view/id/' . $val['log_id']);?>','1','','500')" class="ml-5" style="text-decoration:none"> <i class="Hui-iconfont">&#xe665;</i></a>
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
    /*日志-查看*/
    function admin_log_view(title,url,id,w,h){
        layer_show(title,url,w,h);
    }
    /*日志-删除*/
    function admin_log_del(){
        layer.confirm('确认要批量删除吗？',function(index){
            $.ajax({
                type:'POST',
                url: "<?php echo $this->createUrl('delete')?>",
                dataType:'json',
                data:$('#list-form').serialize(),
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