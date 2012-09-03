<?php
require "function.php";

switch($_GET['action']){
case "login":
	$safecode=$_POST['safecode'];
	if($safecode!=$_SESSION["verifyCode"])break;

	require "config.php";
	$pwd=sha1($_POST['password']);
	$remember=$_POST['remember'];
	
	if($pwd!=$password)break;
	
	$_SESSION['login']=true;
	if($remember=="remember"){
		setcookie("tfm_pwd",$pwd,time()+3600*24*30);
	}else{
		setcookie("tfm_pwd",$pwd);
	}
echo 1;
break;
case "logout":
	unset($_SESSION['login']);
	setcookie("tfm_pwd","",time()-1);
	die("清除Cookie，退出成功。");
break;
case "":
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<title>Twinklous File Manager</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<style type="text/css">
body{
margin-top:20px;
background-color:#313131;
color:#555555;
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:14px;
}
.layouttable{
margin:0 auto;
}
.main{
background-color:#dfdfdf;
}
.text{
padding:5px;
border:1px solid #cccccc;
}
.button{
padding:6px;
border:1px solid #999999;
border-left:8px solid #999999;
border-top:2px solid #999999;
background-color:#eeeeee;
}
a:link,a:visited{
color:#aaaaaa;
text-decoration:none;
}
a:hover{
color:#cccccc;
}
center{
margin-top:20px;
}
</style>
<script src="jquery.js"></script>
<script>
function login(){
	$.post("login.php?action=login",{
		password:$("#password").val(),
		remember:$("#rememberMe:checked").val(),
		safecode:$("#safecode").val()
	  },function(rsps){
			if(rsps==1)
				location.replace("index.php");
			else
				alert("登陆验证没有通过，请检查重试。");
		  },"text");
}
</script>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table class="layouttable " width="725" height="504" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="3">
			<img src="images/login_01.gif" width="725" height="232" alt=""></td>
	</tr>
	<tr>
		<td rowspan="2">
			<img src="images/login_02.gif" width="99" height="272" alt=""></td>
		<td width="524" height="239" class="main">
		
			<table width="230" align="center" cellspacing="5">
				<tr>
					<td width="70"><strong>密码：</strong></td>
				</tr>
				<tr>
					<td><input id="password" type="password" class="text" /></td>
				</tr>
				<tr>
					<td><strong>验证码：</strong></td>
				</tr>
				<tr>
				<td><img src="safe.php" /></td>
				</tr>
				<tr>
					<td><input id="safecode" type="text" class="text" /></td>
				</tr>
				<tr>
					<td><label for="rememberMe"><input id="rememberMe" type="checkbox" value="remember" />记住我</label></td>
				</tr>
				<tr>
					<td><input class="button" type="button" onClick="login()" value="确定" /></td>
				</tr>
			</table>
		</td>
		<td rowspan="2">
			<img src="images/login_04.gif" width="102" height="272" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="images/login_05.gif" width="524" height="33" alt=""></td>
	</tr>
</table>
<center>Powered By <a href="http://twinklous.net/?c=tfm" target="_blank">Twinklous File Manager PV1.5</a><br>
	Designed by <a target="_blank" href="http://strongwillow.org.cn">Strongwillow</a>
</center>
</body>
</html>
<?php
}
?>