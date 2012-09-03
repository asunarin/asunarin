<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>无标题文档</title>
<style type="text/css">
body{
margin:0px;
padding:10px;
background-image:url(images/index_14.gif);
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:14px;
color:#0099CC;
}
</style>

</head>

<body>
<?php
require "config.php";
require "function.php";
chkLogin();

$filepath=$base.$_POST['path'].$_FILES['file']['name'];
move_uploaded_file($_FILES['file']['tmp_name'],
mb_convert_encoding($filepath,"GB2312","UTF-8"));
if($_POST['autounzip']=="yes"){
	if(getExtension($_FILES['file']['name'])=="zip"){
		include "fasisun_zip_class.php";
	//       C:/PHP/htdocs/_project/tfm/zhong/images.zip
		
		$path_parts = pathinfo($filepath);
		$z = new Zip;
		$z->Extract(UTG($filepath),UTG($path_parts['dirname']));	
	
	}
}

 function getExtension($str)
{
         $i = strrpos($str,".");
         if (!$i)
         {
            return "";
         }
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return strtolower($ext);
 }
?>
<script>
window.parent.doAction("Go");
location.replace("upfile.htm");
</script>
</body>
</html>
