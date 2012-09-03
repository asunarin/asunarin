<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Twinklous File Manager PV 1.5快速设置</title>
<style type="text/css">
body{
line-height:30px;
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:14px;
}
.button{
padding:5px;
}
.desc{
margin-left:20px;
font-size:12px;
color:#777777;
}
</style>
</head>

<body>
<?php
require "function.php";
require "config.php";

switch($_GET['action']){
case "setup":
if($_POST['pwd']=="")die("不能没有密码。");
	$PWD=sha1($_POST['pwd']);
	$ROOT=$_POST['root'];
	$BASE=$_POST['base'];

	$model='
<?php
$password="'.$PWD.'";
$root="'.$ROOT.'";
$base="'.$BASE.'";
?>';
file_put_contents("config.php",$model);
echo "设置成功！<br /><a href='login.php'>登陆</a>";
die();
break;
case "":
if(chkLogin2()){
	showSetup();
}else{
	if($password==""){
	showSetup();
	}else{
	header("location: login.php");
	}
}
}
function showSetup(){
global $root;
global $base;
?>
<form action="?action=setup" method="post">
<h2>Twinklous File Manger PV 1.5快速设置</h2>
本文件地址：<?php echo str_replace("\\","/",__FILE__);?><br />
密码：<input name="pwd" type="password" />
<div class="desc">可以使用任何长度的任何字符做为密码。</div>
基本地址：<input name="base" type="text" size="40" value="<?php echo $base; ?>" /><br />
<div class="desc">基本地址是当相对地址为/时显示的目录，也就是进入程序时打开的目录。 TFM只能管理该目录以下的文件（夹）。<br>
这个地址最后一个字符不能是/，示例：C:/www/yourname/web/mysite</div>
Web根目录：<input name="root" type="text" size="40" value="<?php echo $root; ?>" />
<div class="desc">这是当浏览器地址栏为http://yourdomain.com/时在服务器上的目录。<br>
这个地址最后一个字符不能是/，示例：C:/www/yourname/web或C:/www/yourname/wwwroot等,您必须根据站点情况判断。</div>
<input class="button" type="submit" value="确定" />
</form>
<?php
}
?>
</body>
</html>
