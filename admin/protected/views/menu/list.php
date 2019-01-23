<title>菜单列表</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 菜单管理 <span class="c-gray en">&gt;</span> 菜单列表 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
    <div class="cl pd-5 bg-1 bk-gray mt-20"><span class="l"><a href="javascript:;" onclick="admin_menu_update()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe68f;</i>更新排序</a>
    <a href="javascript:;" onclick="admin_menu_add('新增菜单','<?php echo $this->createUrl('menu/add');?>','','510')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;
     </i>新增菜单</a></span><span class="r">共有数据：<strong><?php echo $totalNums?></strong> 条</span> </div>
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'                   => 'edit-form',
        'enableAjaxValidation' => false,
        'htmlOptions'          => array('class'=>'form form-horizontal')
    )); ?>
    <table class="table table-border table-bordered table-bg" id="table_list">
        <thead>
        <tr>
            <th scope="col" colspan="5">菜单列表</th>
        </tr>
        <tr class="text-c">
            <th width="200">菜单名称</th>
            <th width="100">上级菜单</th>
            <th width="300">链接地址</th>
            <th width="50">排序</th>
            <th width="200">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($list) :?>
        <?php foreach ($list as $key => $val):?>
                <tr class="text-c">
                    <td><?php echo $val['menu_name'];?></td>
                    <td><?php echo $val['pid_menu'] ? $val['pid_menu']: '顶级菜单' ?></td>
                    <td><?php echo $val['url']? $val['url']: '-'?></td>
                    <td><input  style="text-align:center" type="text" name="Menu[<?php echo $val['menu_id'];?>][sort]" value="<?php echo $val['sort'];?>" class="input-text"></td>
                    <td>
                        <a title="编辑" href="javascript:;" onclick="admin_menu_edit('编辑菜单','<?php echo $this->createUrl('menu/edit/id/' . $val['menu_id']);?>','1','','500')" class="ml-5" style="text-decoration:none"> <i class="Hui-iconfont">&#xe6df;</i></a>
                        <a title="删除" href="javascript:;" onclick="admin_menu_del(this,<?php echo $val['menu_id'] ?>)" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
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
    /*菜单-添加*/
    function admin_menu_add(title,url,w,h){
        layer_show(title,url,w,h);
    }
    /*菜单-编辑*/
    function admin_menu_edit(title,url,id,w,h){
        layer_show(title,url,w,h);
    }
    /*菜单-删除*/
    function admin_menu_del(obj,id){
        layer.confirm('确认要删除吗？',function(index){
            $.ajax({
                type:'GET',
                url: "<?php echo $this->createUrl('delete')?>",
                dataType:'json',
                data:{id:id},
                success:function(r){
                    if (r.status == 1)
                    {
                        layer.msg(r.msg,{icon:1,time:2000},function(){
                            $(obj).parents("tr").remove();
                            //window.location.href = r.data.url;
                        });
                    } else
                    {
                        layer.msg(r.msg,{icon:2,time:2000});
                    }
                }
            });
        });
    }
    /*菜单-删除*/
    function admin_menu_update(){
        layer.confirm('确认要更新排序吗？',function(){
            $.ajax({
                type:'POST',
                url: "<?php echo $this->createUrl('update')?>",
                dataType:'json',
                data:$('#edit-form').serialize(),
                success:function(r){
                    if (r.status == 1)
                    {
                        layer.msg(r.msg,{icon:1,time:2000},function(){
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