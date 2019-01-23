<title>部门列表</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 部门管理 <span class="c-gray en">&gt;</span> 部门列表 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
    <div class="cl pd-5 bg-1 bk-gray mt-20"><span class="l"><a href="javascript:;" onclick="admin_depart_update()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe68f;</i>更新排序</a>
    <a href="javascript:;" onclick="admin_depart_add('新增部门','<?php echo $this->createUrl('department/add');?>','500','200')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;
        </i>新增部门</a></span><span class="r">共有数据：<strong><?php echo $totalNums?></strong> 条</span> </div>
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'                   => 'edit-form',
        'enableAjaxValidation' => false,
        'htmlOptions'          => array('class'=>'form form-horizontal')
    )); ?>
    <table class="table table-border table-bordered table-bg" id="table_list">
        <thead>
        <tr>
            <th scope="col" colspan="5">部门列表</th>
        </tr>
        <tr class="text-c">
            <th width="200">部门名称</th>
            <th width="200">状态</th>
            <th width="100">排序</th>
            <th width="200">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($list) :?>
            <?php foreach ($list as $key => $val):?>
                <tr class="text-c">
                    <td><?php echo $val['depart_name'];?></td>
                    <td><?php echo isset($this->_config['common_status'][$val['status']]) ? $this->_config['common_status'][$val['status']] : '';?></td>
                    <td><input  style="text-align:center" type="text" name="Depart[<?php echo $val['depart_id'];?>][sort]" value="<?php echo $val['sort'];?>" class="input-text"></td>
                    <td>
                        <?php if($val['status'] == '1'):?>
                            <a title="停用" href="javascript:;" onclick="admin_depart_set(this,<?php echo $val['depart_id'] ?>,'0')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe631;</i></a>
                        <?php else: ?>
                            <a title="启用" href="javascript:;" onclick="admin_depart_set(this,<?php echo $val['depart_id'] ?>,'1')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe615;</i></a>
                        <?php endif;?>
                        <a title="编辑" href="javascript:;" onclick="admin_depart_edit('编辑部门','<?php echo $this->createUrl('department/edit/id/' . $val['depart_id']);?>','1','500','200')" class="ml-5" style="text-decoration:none"> <i class="Hui-iconfont">&#xe6df;</i></a>
                        <a title="删除" href="javascript:;" onclick="admin_depart_del(this,<?php echo $val['depart_id'] ?>)" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
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
    /*部门-添加*/
    function admin_depart_add(title,url,w,h){
        layer_show(title,url,w,h);
    }
    /*部门-编辑*/
    function admin_depart_edit(title,url,id,w,h){
        layer_show(title,url,w,h);
    }
    /*部门-删除*/
    function admin_depart_del(obj,id){
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
    /*部门-排序*/
    function admin_depart_update(){
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

    /*部门-启用禁用*/
    function admin_depart_set(obj,id,status){
        msg = status == 1 ? '启用': '停用';
        layer.confirm('确认要'+msg+'该部门吗？',function(index){
            $.ajax({
                type:'GET',
                url: "<?php echo $this->createUrl('setstate')?>",
                dataType:'json',
                data:{id:id,status:status},
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