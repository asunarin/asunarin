<?php
require "config.php";
require "function.php";
chkLogin();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<title>Twinklous File Manger</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css">
<script src="jquery.js"></script>
<script src="jquery.tkey.js"></script>
<script src="core.js"></script>
</head>
<body oncontextmenu="return false;" onmouseup="hideMenu(event);">

<table class="container" align="center" style="margin:0 auto; "  width="901" height="799" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="7">
			<img src="images/index_01.gif" width="900" height="80" alt=""></td>
		<td>
			<img src="images/space.gif" width="1" height="80" alt=""></td>
	</tr>
	<tr>
		<td colspan="2" rowspan="2">
			<img src="images/index_02.gif" width="119" height="105" alt=""></td>
		<td colspan="3" background="images/index_03.gif"  width="529" height="34" >
			<input id="iptAddr" style="width:510px;height:20px;border:0px;
			background-image:url(images/index_03.gif);" type="text" /></td>
		<td colspan="2" rowspan="2">
			<img src="images/index_04.gif" alt="" width="252" height="105" border="0" usemap="#Map"></td>
		<td>
			<img src="images/space.gif" width="1" height="34" alt=""></td>
	</tr>
	<tr>
		<td colspan="3">
			<img src="images/index_05.gif" width="529" height="71" alt=""></td>
		<td>
			<img src="images/space.gif" width="1" height="71" alt=""></td>
	</tr>
	<tr>
		<td rowspan="7">
			<img src="images/index_06.gif" width="32" height="613" alt=""></td>
		<td colspan="2" width="272" height="246" style="background-image:url(images/index_07.gif); ">
		<div id="divDir" style="width:272px;height:246px;overflow:auto;"></div>
	  </td>
		<td rowspan="7">
			<img src="images/index_08.gif" width="46" height="613" alt=""></td>
		<td colspan="2" width="508" height="458" rowspan="3" style="background-image:url(images/index_09.gif); ">
				<div id="divFile" style="width:508;height:458px;overflow:auto;"></div>
</td>
		<td rowspan="7">
			<img src="images/index_10.gif" width="42" height="613" alt=""></td>
		<td>
			<img src="images/space.gif" width="1" height="246" alt=""></td>
	</tr>
	<tr>
		<td colspan="2">
			<img src="images/index_11.gif" width="272" height="86" alt=""></td>
		<td>
			<img src="images/space.gif" width="1" height="86" alt=""></td>
	</tr>
	<tr>
		<td colspan="2" width="272" height="250" rowspan="3" style="background-image:url(images/index_12.gif);">
				<div id="divFav"></div>
</td>
		<td>
			<img src="images/space.gif" width="1" height="126" alt=""></td>
	</tr>
	<tr>
		<td colspan="2">
			<img src="images/index_13.gif" width="508" height="79" alt=""></td>
		<td>
			<img src="images/space.gif" width="1" height="79" alt=""></td>
	</tr>
	<tr>
		<td colspan="2" width="508" height="47" rowspan="2">
	<iframe src="upfile.htm" width="508" height="47" scrolling="no"
	  frameborder="0"></iframe>
</td>
		<td>
			<img src="images/space.gif" width="1" height="45" alt=""></td>
	</tr>
	<tr>
		<td colspan="2" rowspan="2">
			<img src="images/index_15.gif" width="272" height="31" alt=""></td>
		<td>
			<img src="images/space.gif" width="1" height="2" alt=""></td>
	</tr>
	<tr>
		<td colspan="2">
			<img src="images/index_16.gif" width="508" height="29" alt=""></td>
		<td>
			<img src="images/space.gif" width="1" height="29" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="images/space.gif" width="32" height="1" alt=""></td>
		<td>
			<img src="images/space.gif" width="87" height="1" alt=""></td>
		<td>
			<img src="images/space.gif" width="185" height="1" alt=""></td>
		<td>
			<img src="images/space.gif" width="46" height="1" alt=""></td>
		<td>
			<img src="images/space.gif" width="298" height="1" alt=""></td>
		<td>
			<img src="images/space.gif" width="210" height="1" alt=""></td>
		<td>
			<img src="images/space.gif" width="42" height="1" alt=""></td>
		<td></td>
	</tr>
</table>

<div class="bottom">
	Powered By <a href="http://twinklous.net/?c=tfm" target="_blank">Twinklous File Manager PV 1.5</a><br />
	Designed by <a target="_blank" href="http://strongwillow.org.cn">Strongwillow</a>
</div>
<map name="Map">
  <area shape="rect" coords="55,-2,108,34" target="_blank" href="setup.php">
  <area id='btnMenu' shape="rect" coords="114,-5,166,33" href="javascript:void(0);">
  <area shape="rect" coords="8,-15,45,32" href="javascript:doAction('Go');">
</map>
<div style="display:none;">
<img src="images/fp_01.gif" />
<img src="images/fp_02.gif" />
<img src="images/fp_03.gif" />
<img src="images/fp_04.gif" />
<img src="images/fp_05.gif" />
</div>
</body>
</html>