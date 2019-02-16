<div class="pd-20" style="padding-top:20px;">
  <p class="f-20 text-success">欢迎使用<?php echo CHtml::encode(Yii::app()->name); ?>!</p>
  <p>登录次数：<?php echo $server['count']?> </p>
  <p>上次登录IP：<?php echo Yii::app()->user->getState('lastIP');?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;上次登录时间：<?php echo date('Y-m-d H:i:s',Yii::app()->user->getState('lastTime'));?></p>
  <table class="table table-border table-bordered table-bg mt-20">
    <thead>
      <tr>
        <th colspan="2" scope="col">服务器信息</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>服务器IP地址</td>
        <td><?php echo $server['ip'];?></td>
      </tr>
      <tr>
        <td>服务器域名</td>
        <td><?php echo $server['servename']?></td>
      </tr>
      <tr>
        <td>服务器端口 </td>
        <td><?php echo $server['port']?></td>
      </tr>
      <tr>
        <td>本文件所在文件夹 </td>
         <td><?php echo $server['document_root']?></td>
      </tr>
      <tr>
        <td>服务器操作系统 </td>
        <td><?php echo $server['os']?></td>
      </tr>
      <tr>
        <td>服务器时间 </td>
        <td><?php echo $server['time']?></td>
      </tr>
      <tr>
        <td>服务器解译引擎</td>
        <td><?php echo $server['software']?></td>
      </tr>
      <tr>
        <td>PHP版本</td>
        <td><?php echo $server['phpver']?></td>
      </tr>
      <tr>
        <td>MySQL版本</td>
        <td><?php echo $server['mysqlver']?></td>
      </tr>
      <tr>
        <td>文件上传</td>
        <td><?php echo $server['upfile']?></td>
      </tr>
      <tr>
        <td>内存占用</td>
        <td><?php echo $server['memory']?></td>
      </tr>
    </tbody>
  </table>
</div>