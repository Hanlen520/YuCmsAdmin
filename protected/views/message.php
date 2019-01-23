<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php if (SITE_URL) :?>
<base href="<?php echo SITE_URL;?>"></base>
<?php endif;?>
<title>页面提示</title>
<link href="css/admin.css" rel="stylesheet" type="text/css">
<script language="javascript">
	setTimeout(refresh, <?php echo $delay?> * 1000);
	function refresh()
	{
		$('#message-form').submit();
	}
</script>
</head>
<body>

<div id="body">
    <center>
    <table cellspacing="0" cellpadding="0" align="center" style="border: 1px solid #CCCCCC;background:#FFF;width:600px;height:150px;margin:50px 0">
        <tr><td align="center"><center><?php echo $message?> <br /><br /><a href="javascript:refresh();">如果您的浏览器没有自动跳转，请点击这里。</a></center></td></tr>
    </table>
    </center>
</div>
<form id="message-form" name="message-form" action="<?php echo $url?>" method="post">
	<input type="hidden" name="returnUrl" value="<?php echo $returnUrl?>">
</form>
</body>
</html>