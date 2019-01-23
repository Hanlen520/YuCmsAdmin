<title>权限管理</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 管理员管理 <span class="c-gray en">&gt;</span> 权限管理 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
    <table class="table table-border table-bordered table-bg" id="table_list">
        <thead>
        <tr>
            <th scope="col" colspan="7">权限列表</th>
        </tr>
        <tr class="text-c">
            <th width="40">序号</th>
            <th width="200">控制器</th>
            <th width="100">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($list) :?>
        <?php $i=1;
        foreach ($list as $key => $val)
        {
        $className = $key % 2 == 0 ? '' : 'tr_bj';

        $arr = explode($this->delimeter, $val);
        if (count($arr) == 1)
        {
            $module     = '-';
            $controller = $arr[0];
        } else
        {
            $module     = $arr[0];
            $controller = $arr[1];
        }
        ?>
            <tr class="text-c">
                <td><?php echo $i;?></td>
                <td><?php echo $controller;?></td>
                <td>
                    <a title="授权" href="javascript:;" onclick="admin_permission_edit('新增权限','<?php echo $this->createUrl('item/add/name/' . $val);?>','1','','500')" class="ml-5" style="text-decoration:none"> <i class="Hui-iconfont">&#xe600;</i></a>
                    <a title="更新描述" href="javascript:;" onclick="admin_permission_edit('更新权限描述','<?php echo $this->createUrl('item/update/name/' . $val);?>','1','','500')" class="ml-5" style="text-decoration:none"> <i class="Hui-iconfont">&#xe6df;</i></a>
                    <a title="删除" href="javascript:;" onclick="admin_permission_del('删除权限','<? echo $this->createUrl('item/delete/name/' . $val);?>','1','','500')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
                </td>
            </tr>
        <?php $i++;}?>
        <?php else :?>
            <tr class="text-c">
                <td colspan="3" style="text-align:center">对不起，没有找到相应的控制器！</td>
            </tr>
        <?php endif;?>
        </tbody>
    </table>
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
    /*管理员-权限-添加*/
    function admin_permission_add(title,url,w,h){
        layer_show(title,url,w,h);
    }
    /*管理员-权限-编辑*/
    function admin_permission_edit(title,url,id,w,h){
        layer_show(title,url,w,h);
    }

    /*管理员-权限-删除*/
    function admin_permission_del(title,url,id,w,h){
        layer_show(title,url,w,h);
    }


    $('tbody tr').hover(function(){
        $(this).addClass('selected');
    },function(){
        $(this).removeClass('selected');
    });
</script>