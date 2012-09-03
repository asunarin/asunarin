<?php
/* +---------------------------------------------------------------------------------------------------+
 * | Author: thirteen
 * | Email: thirteensky@126.com
 * | Url: http://thirteen.com.cn
 * |
 * | 有时电脑不在手边,突然想起要改点什么,或是查看某个不能直接下载数据文件,
 * | 所以自己弄了个WWW/WAP双栖的WEB文件管理器.
 * | 用两个不同的登录密码区分管理界面的显示.
 * |
 * | 安装:
 * | 改好密码就可以了.
 * | 默认根目录为安装目录的上一级.可以在类定义部分修改.
 * |
 * |
 * | 使用:
 * | WWW/WAP版,都是点击目录名进入,点文件名下载.
 * | 为手机屏幕及内存考虑,WAP版目录里不显示文件信息.
 * | 有"+"的为文件夹,"-"为文件.
 * | 点击"+","-"进入属性查看及管理页.
 * |
 * | 功能:
 * | 列出目录下的文件夹和文件
 * | 进入二级或N级目录
 * | 删除文件及文件夹
 * | 移动文件,如果指定文件夹不存在,将会被创建
 * | 复制文件/文件夹,如果指定文件夹不存在,将会被创建
 * | 可以新建文件夹
 * | 可以上传文件
 * | 解压ZIP文件
 * | 若干目录/文件打包成ZIP文件
 * | 白定义列表打包成ZIP文件
 * |
 * | 不准备解决的问题:
 * | 没有文件编辑功能.对于手机来说,下载后编辑再上传更现实一些.
 * | 移动或复制文件,要自己指定目标文件夹.同样理由,文件夹可能过多在手机上翻拣不方便.
 * |
 * +-----------------------------------------------------------------------------------------------------+
*/

//默认密码:第一个进WWW版,第二个进WAP版
$pswa = 'logintowww';
$pswb = 'logintowap';


//释放参数
@extract($_GET, EXTR_SKIP);
@extract($_POST, EXTR_SKIP);


//登录验证
if ( ($_COOKIE['cookie:tsnote'] != md5($pswa)) && ($_COOKIE['cookie:tsnote'] != md5($pswb)) ) :

	if ($login) {
		setcookie('cookie:tsnote', md5($login), time()+60*60*24*30 );
		die( '<meta http-equiv="refresh" content="1;URL='.$_SERVER['PHP_SELF'].'">' );
	} else {
		die('<form action="" method="post"><input type="text" size="16" name="login" value="" /><input type="submit" value="Login" /></form>');
	}

//正文开始
else:
$info = ($_COOKIE['cookie:tsnote'] == md5($pswa)) ? 1 : 0;
$path = $dir ? str_replace('//','/',$dir) : '/';
$tf = new tsfile;
$_SESSION['zip'] =  new Zip;


//退出
if ($logout) {
	setcookie('cookie:tsnote', null );
	die( '<meta http-equiv="refresh" content="0;URL='.$_SERVER['PHP_SELF'].'">' );
}


//下载
if ($down) { $tf->down($down); die(); }


?><html>
<head><title>TS File Manage - thirteen.com.cn</title>
<meta http-equiv=Content-Type content="text/html; charset=gb2312">
<style>
<!--
*,html {font:9pt tahoma;color:#353638;}
a{text-decoration:none;color:#567567;}
div {border:1px solid;padding: 2px;margin:1px;}
form{margin:2px;}
table,td{border:0;border-collapse:collapse;margin:0;}
input{border:1px solid;}
.main{width:98%;padding:0;}
.bottom{clear:both;}
.submit{border:1px solid;margin-left:2px;}
.check{border:0;margin:0;font-size:7px;}
.t2{background:#defdef;}
.t1{background:#edcedc;}
-->
</style>
</head>
<body>
<div class="main">
<?php


//单个文件属性
if ($attr) :
?><div>Attrib : <?php echo urldecode($attr); ?></div><div>
CreatDate : <?php echo @date("Y-m-d H:i:s",@filectime($tf->basedir.$attr)+8*3600); ?><br />
ModifyDate : <?php echo @date("Y-m-d H:i:s",@filemtime($tf->basedir.$attr)+8*3600); ?><br />
<?php if(!is_dir($tf->basedir.$attr)){ ?>
Size : <?php echo @number_format((filesize($tf->basedir.$attr)/1024),3); ?>KB<br />
<?php } ?>
Attrib : <a href="?fileperm=<?php echo $attr; ?>"><?php echo @$attrperm=substr(base_convert(@fileperms($tf->basedir.$attr),10,8),-4); ?></a><br />
<a href="?copy=<?php echo $attr; ?>">Copy</a>
<a href="?move=<?php echo $attr; ?>">Move</a>
<a href="?del=<?php echo $attr; ?>">Del</a>
<a href="?rename=<?php echo $attr; ?>">Rename</a>
<?php if ((substr($attr,-4,4)=='.zip')||(substr($attr,-3,3)=='.gz')) { ?><a href="?unpack=<?php echo $attr; ?>">Unpack</a><?php } ?>
<br /><a href="?dir=<?php echo $tf->dirname($attr); ?>">Return</a></div>
<?php


//复制到
elseif ($copy) :
$path = $tf->abspath($name,$copy);
if ($name) {
	echo '<div>',$tf->copy($tf->basedir.$copy, $tf->basedir.$path.'/'.basename($copy)) ? 'Success!' : 'False.';
	?><br /><a href="?<?php if ($info) { ?>dir=<?php echo $tf->dirname($copy).'/'; } else { ?>attr=<?php echo $copy;} ?>">Return</a></div><?php
	die('<meta http-equiv="refresh" content="1;URL=?dir='.$tf->dirname($copy).'/">');
} else {
	?><div>Copyto : <?php echo $copy; ?></div><div><form action="?copy=<?php
		echo $copy; ?>" method="post"><input type="text" size="30" name="name" value="" /> <input type="submit" class="submit" value="Copyto" /></form><br /><a href="?<?php
		if ($info) { ?>dir=<?php echo $tf->dirname($copy).'/'; } else { ?>attr=<?php echo $copy;} ?>">Return</a></div>
<?php }


//删除
elseif ($del) :
$path = $tf->dirname($del);
if ($answer=='y') {
	echo '<div>', $tf->del($tf->basedir.$del) ? 'Success!' : 'False.';
	?><br /><a href="?<?php if ($info) { ?>dir=<?php echo $tf->dirname($del).'/'; } else { ?>attr=<?php echo $del;} ?>">Return</a></div><?php
	die('<meta http-equiv="refresh" content="1;URL=?dir='.$path.'/">');
} else {
	?><div>Del : <?php echo $del; ?></div><div>文件删除将不可恢复,确认要删除吗?<br /><a href="?del=<?php
		echo $del; ?>&answer=y">Yes</a><br /><a href="?<?php
		if ($info) { ?>dir=<?php echo $tf->dirname($del).'/'; } else { ?>attr=<?php echo $del;} ?>">Return</a></div>
<?php }


//修改Attrib表单
elseif ($fileperm) :
if ($perm) {
	echo '<div>', @chmod($tf->basedir.$fileperm, base_convert($perm,8,10)) ? 'Success!' : 'False.';
	?><br /><a href="?attr=<?php echo $file; ?>">Return</a></div><?php
	if ($info) die('<meta http-equiv="refresh" content="1;URL=?dir='.$tf->dirname($fileperm).'/">');
	else die ('<meta http-equiv="refresh" content="1;URL=?attr='.$fileperm.'">');
} else {
	?><div>FilePert : <?php echo urldecode($fileperm); ?></div><div><form action="?fileperm=<?php
		echo $fileperm; ?>" method="post"><input type="text" size="6" name="perm" value="<?php
		echo @$fileperm=substr(base_convert(@fileperms($tf->basedir.$fileperm),10,8),-4); ?>" /> <input type="submit" class="submit" value="Modify" /></form><br /><?php
	?><a href="?<?php if ($info) { ?>dir=<?php echo $tf->dirname($fileperm).'/'; } else { ?>attr=<?php echo $fileperm;} ?>">Return</a></div>
<?php }


//建新目录
elseif ($md) :
if ($name)  echo '<div>', @mkdir($tf->basedir.$md.'/'.$name) ? 'Success!' : 'False. Path is exists!.';
?><br /><a href="?dir=<?php echo $md; ?>">Return</a></div><?php
die('<meta http-equiv="refresh" content="1;URL=?dir='.$md.'/">');


//移动到
elseif ($move) :
$path = $tf->abspath($name,$move);
if ($name) {
	if(!is_dir($tf->basedir.$path)) mkdir($tf->basedir.$path);
	echo '<div>', @rename($tf->basedir.$move, $tf->basedir.$path.'/'.basename($move)) ? 'Success!' : 'False.';
	?><br /><a href="?<?php if ($info) { ?>dir=<?php echo $tf->dirname($move).'/'; } else { ?>attr=<?php echo $move;} ?>">Return</a></div><?php
	die('<meta http-equiv="refresh" content="1;URL=?dir='.$tf->dirname($move).'/">');
} else {
	?><div>Moveto : <?php echo $move; ?></div><div><form action="?move=<?php
		echo $move; ?>" method="post"><input type="text" size="30" name="name" value="" /> <input type="submit" class="submit" value="Moveto" /></form><br /><a href="?<?php
		if ($info) { ?>dir=<?php echo $tf->dirname($move).'/'; } else { ?>attr=<?php echo $move;} ?>">Return</a></div>
<?php }


//改名
elseif ($rename) :
if ($name) {
	echo '<div>', @rename($tf->basedir.$rename, $tf->basedir.dirname($rename).'/'.$name) ? 'Success!' : 'False.', '</div>';
	die ('<meta http-equiv="refresh" content="0;URL=?dir='.$tf->dirname($rename).'/">');
} else {
	?><div>Rename : <?php echo $rename; ?></div><div><form action="?rename=<?php
	echo $rename; ?>" method="post"><input type="text" size="30" name="name" value="<?php
	echo basename($rename); ?>" /> <input type="submit" class="submit" value="Rename" /></form><br /><a href="?<?php
	if ($info) { ?>attr=<?php echo $rename; } else { ?>dir=<?php echo $tf->dirname($rename);} ?>">Return</a></div>
<?php }


//上传
elseif ($upload) :
$n = ($n > 0) ? $n : 1;
?><div>Upload : <?php echo $upload; ?></div><div><?php
for ($i=1; $i <= $n; $i++) {
	$fn = 'file'.$i;
	if ($_FILES[$fn]['error'] > 0) { ?><p>Error: <?php echo $_FILES[$fn]['error']; ?></p><?php
	} else {
		move_uploaded_file($_FILES[$fn]['tmp_name'],$tf->basedir.$upload.$_FILES[$fn]['name']); ?><p>Upload: <?php
		echo $_FILES[$fn]['name']; ?><br />Type: <?php
		echo $_FILES[$fn]['type']; ?><br />Size: <?php
		echo $_FILES[$fn]['size'] / 1024; ?>Kb<br />Stored in: <?php
		echo $_FILES[$fn]['name']; ?></p><?php
	}
}
?><br /><a href="?dir=<?php echo $upload; ?>">Return</a></div><?php
die ('<meta http-equiv="refresh" content="2;URL=?dir='.$upload.'">');


//上传表单
elseif ($uploadform) :
$n = ($n > 0) ? $n : 1;
?><div>Upload to : <?php echo $uploadform; ?></div><div>
<form action="?upload=<?php echo $uploadform?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="n" value="<?php echo $n?>" /><?php
for ($i=1; $i <= $n; $i++) { ?><input type="file" name="file<?php echo $i; ?>" size="40" /><br /><?php } ?>
<input type="submit" class="submit" value="Upload" /></form>
<br /><a href="?dir=<?php echo $uploadform; ?>">Return</a></div>
<?php


//解压
elseif ($unpack) :
?><div>Unpack : <?php echo $unpack; ?></div><div><?php
$_SESSION['zip']->Extract($tf->basedir.$unpack, $tf->basedir.$tf->dirname($unpack));
?><br /><a href="?dir=<?php echo $tf->dirname($unpack).'/'; ?>">Return</a></div><?php
die ('<meta http-equiv="refresh" content="1;URL=?dir='.$tf->dirname($unpack).'/">');


//压缩
elseif ($zippack) :
?><div>Zippack : <?php echo $zippack; ?></div><div><?php
if (!isset($z)) {
	?>No selected file!<?php
	die ('<meta http-equiv="refresh" content="1;URL=?dir='.$zippack.'/">');
} elseif ($zippack_add) {
	$packdir = explode("\n",str_replace("\r","",$z));
	$packfile = $tf->basedir. $zippack. ((urldecode($zippack)!='/') ? basename($zippack) : basename($packdir[0])).'.zip';
	foreach ($packdir as $key => $value) { if ($value) $tf->packfile($tf->basedir.$value, $tf->basedir.$zippack); }
	$f = fopen ($packfile, "wb");
	fwrite ($f, $_SESSION['zip']-> getZippedfile());
	fclose ($f);
	?><br /><a href="?dir=<?php echo $zippack; ?>">Return</a></div><?php
	die ('<meta http-equiv="refresh" content="1;URL=?dir='.$zippack.'/">');
} elseif ($zippack_edit) {
	$ze = explode("\n",str_replace("\r",'',$z));
	?><form action="?zippack=<?php echo $zippack; ?>" method="post"><textarea name="z" rows="8" cols="80"><?php foreach ($ze as $value) { if ($value) $tf->tree($tf->basedir.$value); } ?></textarea><br /><input type="submit" class="submit" name="zippack_add" value="AddtoZip" /></form><?php
} else {
	?><form action="?zippack=<?php echo $zippack; ?>" method="post"><textarea name="z" rows="8" cols="80"><?php foreach ($z as $key=>$value) { echo urldecode($key), "\n"; } ?></textarea><br /><input type="submit" class="submit" name="zippack_add" value="AddtoZip" /><input type="submit" class="submit" name="zippack_edit" value="EditfileList" /></form><?php
}
?><br /><a href="?dir=<?php echo $zippack; ?>">Return</a></div><?php


//默认显示根目录
else :

?><div><span><a href="?logout=true" style="float:right">EXIT</a></span>
<?php if ($info) { ?><table width="95%"><tr><td><?php } ?>Host: <?php echo $_SERVER['SERVER_NAME'],'(',@gethostbyname($_SERVER['SERVER_NAME']),')'; ?>
<?php if ($info) { ?></td><td>Time: <?php echo date('Y-m-d H:i:s',time()+8*3600); ?></td><td>IP: <?php echo $_SERVER['REMOTE_ADDR']; ?></td></tr></table><?php } ?>
</div><div>Path : <?php echo $path; ?></div><div>
<?php if ($info) { ?><script language="javascript"><!--
var CheckAll=new Function("form","for (var i=0;i<form.elements.length;i++) {var e = form.elements[i];if (e.name != 'chkall') e.checked = form.chkall.checked;}");
//--></script>
<form action="?zippack=<?php echo $path; ?>" method="post" name="form"><table width="98%">
<colgroup width="21%"></colgroup>
<colgroup width="18%" align="center"></colgroup>
<colgroup width="18%" align="center"></colgroup>
<colgroup width="9%" align="center"></colgroup>
<colgroup width="9%" align="center"></colgroup>
<colgroup width="25%" align="center"></colgroup>
<th>Name</th><th>CreatDate</th><th>ModifyDate</th><th>Size</th><th>Attrib</th><th>Action</th>
<?php }
$lists = $tf->listdir($path);
$uppath = (dirname($path)!="\\") ? urlencode(dirname($path)).'/' : '/';
for ( $i=1; $lists[$i]; $i++) {
	$f = explode("\t",$lists[$i]);
	if ($f[1]=='..') {
		if ($info) { ?><tr class="<?php $tc=($tc=='t1')?'t2':'t1'; echo $tc;?>"><td colspan="6"><?php }?><a href="?dir=<?php echo $uppath; ?>">..</a><?php  if ($info) { ?></td></tr><?php } else { ?><br /><?php }
	} elseif ($f[0]=='d') { if ($info) { ?><tr class="<?php $tc=($tc=='t1')?'t2':'t1'; echo $tc;?>">
		<td><?php } if ($info==0) { ?><a href="?attr=<?php echo urlencode($path.$f[1]); ?>">+</a> <?php } else { ?><input type="checkbox" class="check" value="1" name="z[<?php echo urlencode($path.$f[1]); ?>]" /><?php } ?><a href="?dir=<?php echo urlencode($path.$f[1]); ?>/"><?php echo $f[1]; ?></a><?php if ($info) { ?></td>
		<td><?php echo @date("Y-m-d H:i:s",@filectime($tf->basedir.$path.$f[1])+8*3600); ?></td>
		<td><?php echo @date("Y-m-d H:i:s",@filemtime($tf->basedir.$path.$f[1])+8*3600); ?></td>
		<td>&lt;dir&gt;</td><td><a href="?fileperm=<?php echo $path.$f[1]; ?>"><?php echo @$fileperm=substr(base_convert(@fileperms($tf->basedir.$path.$f[1]),10,8),-4); ?></a></td>
		<td><a href="?copy=<?php echo $path.$f[1]; ?>">Copy</a>
		<a href="?move=<?php echo $path.$f[1]; ?>">Move</a>
		<a href="?del=<?php echo $path.$f[1]; ?>">Del</a>
		<a href="?rename=<?php echo $path.$f[1]; ?>">Rename</a></td></tr><?php } else { ?><br /><?php }
	} elseif ($f[0]=='f') {
		if ($info) { ?><tr class="<?php $tc=($tc=='t1')?'t2':'t1'; echo $tc;?>"><td><?php }
		if ($info==0) { ?><a href="?attr=<?php echo urlencode($path.$f[1]); ?>">-</a> <?php }
		else { ?><input type="checkbox" class="check" value="1" name="z[<?php echo urlencode($path.$f[1]); ?>]" /><?php } ?><a href="?down=<?php echo urlencode($path.$f[1]); ?>"><?php echo $f[1]; ?></a><?php if ($info) { ?></td>
		<td><?php echo @date("Y-m-d H:i:s",@filectime($tf->basedir.$path.$f[1])+8*3600); ?></td>
		<td><?php echo @date("Y-m-d H:i:s",@filemtime($tf->basedir.$path.$f[1])+8*3600); ?></td>
		<td><?php echo @number_format((filesize($tf->basedir.$path.$f[1])/1024),3); ?>KB</td>
		<td><a href="?fileperm=<?php echo $path.$f[1]; ?>"><?php echo @$fileperm=substr(base_convert(@fileperms($tf->basedir.$path.$f[1]),10,8),-4); ?></a></td>
		<td><a href="?copy=<?php echo $path.$f[1]; ?>">Copy</a>
		<a href="?move=<?php echo $path.$f[1]; ?>">Move</a>
		<a href="?del=<?php echo $path.$f[1]; ?>">Del</a>
		<a href="?rename=<?php echo $path.$f[1]; ?>">Rename</a><?php
		if ((substr($f[1],-3,3)=='zip')||(substr($f[1],-2,2)=='gz')) { ?><a href="?unpack=<?php echo $path.$f[1]; ?>">Unpack</a><?php } ?></td></tr><?php } else { ?><br /><?php }
	}
}
if ($info) { ?><tr><td colspan="6"><input name="chkall" class="check" value="on" type="checkbox" onclick="CheckAll(this.form)"><input type="submit" value="Add Selected to Zip" /></form></td></tr></table><?php } ?>
</div><div class="bottom"><table width="98%">
<tr><td width="50%"><form action="?upload=<?php echo $path?>" method="post" enctype="multipart/form-data"><input type="file" name="file1" size="40" /><input type="submit" class="submit" value="Upload" /></form></td><?php if ($info==0) { ?></tr>
<tr><?php } ?><td width="25%"><form action="?uploadform=<?php echo $path; ?>" method="post">Or add (1-15).<input type="text" size="2" name="n" value="1" /><input type="submit" class="submit" value="add" /></form></td><?php if ($info==0) { ?></tr>
<tr><?php } ?><td width="35%"><form action="?md=<?php echo $path?>" method="post"><input type="text" size="10" name="name" value="" /><input type="submit" class="submit" value="CreatDir" /></form></td></tr>
</table></div>
<?php
endif;
?><div align="center"><a href="http://thirteen.com.cn">thirteen.com.cn</a></div></div></body></html>
<?php
endif;


//函数
class tsfile {
	var $basedir;

	function tsfile() {
		$this->basedir = '../';
	}


	function abspath($path,$path1='/') {
		if ($path=='/') return $path = '';
		if (substr($path,0,1)!='/') $path = $this->dirname($path1).'/'.$path;
		if (substr($path,-1,1)=='/') $path = rtrim($path);
		return $path;
	}


	function copy($from, $to) {
		if ($this->abspath($to)=="/") $to=$this->basedir;
		if ($this->dirname($from) == $this->dirname($to)) $to = $this->dirname($to).'/复件'.basename($from);
		if (!is_dir($from)) {
			return @copy($from, $to);
		} else {
			if (!is_dir($to)) @mkdir($to);
			$path = opendir($from);
			while( $file = readdir( $path ) ) {
				if (($file=='.')||($file=='..')) continue;
				if (is_dir($from.'/'.$file)) $this->copy($from.'/'.$file, $to.'/'.$file);
				else echo basename($file), copy($from.'/'.$file, $to.'/'.$file) ? ' Success!' : ' False.', '<br />';
			}
			return true;
		}
	}


	function del($name) {
		if (!is_dir($name)) {
			return @unlink($name);
		} else {
			$dir = opendir($name);
			while( $file = readdir( $dir ) ) {
				if (($file=='.')||($file=='..')) continue;
				if (is_dir($name.'/'.$file)) $this->del($name.'/'.$file);
				else echo basename($file), @unlink($name.'/'.$file) ? ' Success!' : ' False.', '<br />';
			}
		closedir($dir);
		}
		return @rmdir($name);
	}


	function dirname($str) {
		return $str = ((@dirname($str)=="\\")||(@dirname($str)==".")) ? '/' : @dirname($str);
	}


	function down( $file ) { /*文件下载*/
		$filetype = array(
			'chm' => 'application/octet-stream',
			'ppt' => 'application/vnd.ms-powerpoint',
			'xls' => 'application/vnd.ms-excel',
			'doc' => 'application/msword',
			'exe' => 'application/octet-stream',
			'rar' => 'application/octet-stream',
			'js' => 'javascrīpt/js',
			'css' => 'text/css',
			'hqx' => 'application/mac-binhex40',
			'bin' => 'application/octet-stream',
			'oda' => 'application/oda',
			'pdf' => 'application/pdf',
			'ai' => 'application/postsrcipt',
			'eps' => 'application/postsrcipt',
			'es' => 'application/postsrcipt',
			'rtf' => 'application/rtf',
			'mif' => 'application/x-mif',
			'csh' => 'application/x-csh',
			'dvi' => 'application/x-dvi',
			'hdf' => 'application/x-hdf',
			'nc' => 'application/x-netcdf',
			'cdf' => 'application/x-netcdf',
			'latex' => 'application/x-latex',
			'ts' => 'application/x-troll-ts',
			'src' => 'application/x-wais-source',
			'zip' => 'application/zip',
			'bcpio' => 'application/x-bcpio',
			'cpio' => 'application/x-cpio',
			'gtar' => 'application/x-gtar',
			'shar' => 'application/x-shar',
			'sv4cpio' => 'application/x-sv4cpio',
			'sv4crc' => 'application/x-sv4crc',
			'tar' => 'application/x-tar',
			'ustar' => 'application/x-ustar',
			'man' => 'application/x-troff-man',
			'sh' => 'application/x-sh',
			'tcl' => 'application/x-tcl',
			'tex' => 'application/x-tex',
			'texi' => 'application/x-texinfo',
			'texinfo' => 'application/x-texinfo',
			't' => 'application/x-troff',
			'tr' => 'application/x-troff',
			'roff' => 'application/x-troff',
			'shar' => 'application/x-shar',
			'me' => 'application/x-troll-me',
			'ts' => 'application/x-troll-ts',
			'gif' => 'image/gif',
			'jpeg' => 'image/pjpeg',
			'jpg' => 'image/pjpeg',
			'jpe' => 'image/pjpeg',
			'ras' => 'image/x-cmu-raster',
			'pbm' => 'image/x-portable-bitmap',
			'ppm' => 'image/x-portable-pixmap',
			'xbm' => 'image/x-xbitmap',
			'xwd' => 'image/x-xwindowdump',
			'ief' => 'image/ief',
			'tif' => 'image/tiff',
			'tiff' => 'image/tiff',
			'pnm' => 'image/x-portable-anymap',
			'pgm' => 'image/x-portable-graymap',
			'rgb' => 'image/x-rgb',
			'xpm' => 'image/x-xpixmap',
			'txt' => 'text/plain',
			'c' => 'text/plain',
			'cc' => 'text/plain',
			'h' => 'text/plain',
			'html' => 'text/html',
			'htm' => 'text/html',
			'htl' => 'text/html',
			'rtx' => 'text/richtext',
			'etx' => 'text/x-setext',
			'tsv' => 'text/tab-separated-values',
			'mpeg' => 'video/mpeg',
			'mpg' => 'video/mpeg',
			'mpe' => 'video/mpeg',
			'avi' => 'video/x-msvideo',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',
			'moov' => 'video/quicktime',
			'movie' => 'video/x-sgi-movie',
			'au' => 'audio/basic',
			'snd' => 'audio/basic',
			'wav' => 'audio/x-wav',
			'aif' => 'audio/x-aiff',
			'aiff' => 'audio/x-aiff',
			'aifc' => 'audio/x-aiff',
			'swf' => 'application/x-shockwave-flash',
			'myz' => 'application/myz' );
			$filename = basename($file);
			$ext = preg_replace("/.*\./", '', $file);
			header('Content-type: application/force-download');
			header('Content-type: ' . $filetype[$ext]);
			header("Content-Disposition: attachment; filename={$filename}");
			header("Content-length: ".filesize($this->basedir.$file));
			readfile($this->basedir.$file);
	}


	function listdir($path='/') {
		$dir = opendir($this->basedir.$path);
		while( $file = readdir( $dir ) ) {
			if (($file=='.')||($file=='..')) $lists[] = "d\t".$file;
			elseif ( is_dir($this->basedir.$path.'/'.$file)) $lists[] = "d\t".$file;
			else $lists[] = "f\t".$file;
		}
		closedir($dir);
		sort($lists);
		return $lists;
	}


	function quickdir($file) {
		?><form action="" method="get"><select name="dir"><?php
		for ($i=0; $file!='\\'; $i++) {
			$file = $tf->dirname($file);
			if ($file=='\\') break;
			echo '<option value="',$file,'/">',$file,'</option>';
		}
		?><option value="/">/</option></select> <input type="submit" class="submit" value="goto" /><br /><a href="?dir=<?php echo $file; ?>">Return</a></form><?php
	}


	function tree($path) {
		if (is_dir($path)) {
			foreach (glob($path.'/*') as $value) { $this->tree($value); }
		} else echo str_replace($this->basedir,'', $path), "\n";
	}


	function packfile($path,$basedir) {
		if (is_dir($path)) {
			foreach (glob($path.'/*') as $value) { $this->packfile($value,$basedir); }
		} else {
			$currPath = str_replace($basedir,'',$path);
			$fileContents = file_get_contents($path);
			$_SESSION['zip'] -> addFile($fileContents,$currPath);
			echo $currPath,'<br />';
		}
	}

// end class
}

class zip {
	public $total_files = 0;
	public $total_folders = 0;

	public function Extract ($zn, $to) {
		$ok = 0;
		$zip = @fopen($zn,'rb');
		if (!$zip) return(-1);
		$cdir = $this->ReadCentralDir($zip,$zn);
		$pos_entry = $cdir['offset'];

		for ($i=0; $i < $cdir['entries']; $i++) {
			@fseek($zip, $pos_entry);
			$header = $this->ReadCentralFileHeaders($zip);
			$header['index'] = $i;
			$pos_entry = ftell($zip);
			@rewind($zip);
			fseek($zip, $header['offset']);
			$this->ExtractFile($header, $to, $zip);
		}
		fclose($zip);
		return $stat;
	}

	public function ReadFileHeader($zip) {
		$binary_data = fread($zip, 30);
		$data = unpack('vchk/vid/vversion/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len', $binary_data);
		$header['filename'] = fread($zip, $data['filename_len']);
		if ($data['extra_len'] != 0) {
			$header['extra'] = fread($zip, $data['extra_len']);
		} else { $header['extra'] = ''; }
		$header['compression'] = $data['compression'];$header['size'] = $data['size'];
		$header['compressed_size'] = $data['compressed_size'];
		$header['crc'] = $data['crc']; $header['flag'] = $data['flag'];
		$header['mdate'] = $data['mdate'];$header['mtime'] = $data['mtime'];

		if ($header['mdate'] && $header['mtime']) {
			$hour=($header['mtime']&0xF800)>>11;$minute=($header['mtime']&0x07E0)>>5;
			$seconde=($header['mtime']&0x001F)*2;$year=(($header['mdate']&0xFE00)>>9)+1980;
			$month=($header['mdate']&0x01E0)>>5;$day=$header['mdate']&0x001F;
			$header['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year);
		} else {$header['mtime'] = time();}

		$header['stored_filename'] = $header['filename'];
		$header['status'] = "ok";
		return $header;
	}

	public function ReadCentralFileHeaders($zip) {
		$binary_data = fread($zip, 46);
		$header = unpack('vchkid/vid/vversion/vversion_extracted/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len/vcomment_len/vdisk/vinternal/Vexternal/Voffset', $binary_data);

		if ($header['filename_len'] != 0) $header['filename'] = fread($zip,$header['filename_len']);
		else $header['filename'] = '';

		if ($header['extra_len'] != 0) $header['extra'] = fread($zip, $header['extra_len']);
		else $header['extra'] = '';

		if ($header['comment_len'] != 0) $header['comment'] = fread($zip, $header['comment_len']);
		else $header['comment'] = '';

		if ($header['mdate'] && $header['mtime']) {
			$hour = ($header['mtime'] & 0xF800) >> 11;
			$minute = ($header['mtime'] & 0x07E0) >> 5;
			$seconde = ($header['mtime'] & 0x001F)*2;
			$year = (($header['mdate'] & 0xFE00) >> 9) + 1980;
			$month = ($header['mdate'] & 0x01E0) >> 5;
			$day = $header['mdate'] & 0x001F;
			$header['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year);
		} else {
			$header['mtime'] = time();
		}
		$header['stored_filename'] = $header['filename'];
		$header['status'] = 'ok';
		if (substr($header['filename'], -1) == '/')
			$header['external'] = 0x41FF0010;
		return $header;
 	}

	public function ReadCentralDir($zip,$zip_name) {
		$size = filesize($zip_name);
		if ($size < 277) $maximum_size = $size;
		else $maximum_size=277;
		@fseek($zip, $size-$maximum_size);
		$pos = ftell($zip); $bytes = 0x00000000;

		while ($pos < $size) {
			$byte = @fread($zip, 1); $bytes=($bytes << 8) | ord($byte);
			if ($bytes == 0x504b0506 or $bytes == 0x2e706870504b0506) { $pos++; break;}
			$pos++;
		}

		$fdata=fread($zip,18);
		$data=@unpack('vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_size',$fdata);

		if ($data['comment_size'] != 0) $centd['comment'] = fread($zip, $data['comment_size']);
		else $centd['comment'] = ''; $centd['entries'] = $data['entries'];

		$centd['disk_entries'] = $data['disk_entries'];
		$centd['offset'] = $data['offset'];
		$centd['disk_start'] = $data['disk_start'];
		$centd['size'] = $data['size'];
		$centd['disk'] = $data['disk'];
		return $centd;
	}


	public function ExtractFile($header,$to,$zip) {
		$header = $this->readfileheader($zip);

		if (substr($to,-1)!="/") $to.="/";
		if ($to=='./') $to = '';
		$pth = explode("/",$to.$header['filename']);
		$mydir = '';
		for($i=0;$i<count($pth)-1;$i++) {
			if (!$pth[$i]) continue;
			$mydir .= $pth[$i]."/";
			if ((!is_dir($mydir) && @mkdir($mydir,0777)) || (($mydir==$to.$header['filename'] || ($mydir==$to && $this->total_folders==0)) && is_dir($mydir)) ) {
				@chmod($mydir,0777);
				$this->total_folders ++;
				echo 'Extract : ',$mydir,'<br>';
			}
		}

		if (strrchr($header['filename'],'/')=='/') return;
		if (!($header['external']==0x41FF0010)&&!($header['external']==16)) {
			if ($header['compression']==0) {
				$fp = @fopen($to.$header['filename'], 'wb');
				if (!$fp) return(-1);
				$size = $header['compressed_size'];
				while ($size != 0) {
					$read_size = ($size < 2048 ? $size : 2048);
					$buffer = fread($zip, $read_size);
					$binary_data = pack('a'.$read_size, $buffer);
					@fwrite($fp, $binary_data, $read_size);
					$size -= $read_size;
				}
				fclose($fp);
				touch($to.$header['filename'], $header['mtime']);
			} else {
				$fp = @fopen($to.$header['filename'].'.gz','wb');
				if (!$fp) return(-1);
				$binary_data = pack('va1a1Va1a1', 0x8b1f, Chr($header['compression']),
				Chr(0x00), time(), Chr(0x00), Chr(3));

				fwrite($fp, $binary_data, 10);
				$size = $header['compressed_size'];

				while ($size != 0) {
					$read_size = ($size < 1024 ? $size : 1024);
					$buffer = fread($zip, $read_size);
					$binary_data = pack('a'.$read_size, $buffer);
					@fwrite($fp, $binary_data, $read_size);
					$size -= $read_size;
				}

				$binary_data = pack('VV', $header['crc'], $header['size']);
				fwrite($fp, $binary_data,8); fclose($fp);

				$gzp = @gzopen($to.$header['filename'].'.gz','rb') or die("Cette archive est compress閑");
				if (!$gzp) return(-2);
				$fp = @fopen($to.$header['filename'],'wb');
				if (!$fp) return(-1);
				$size = $header['size'];

				while ($size != 0) {
					$read_size = ($size < 2048 ? $size : 2048);
					$buffer = gzread($gzp, $read_size);
					$binary_data = pack('a'.$read_size, $buffer);
					@fwrite($fp, $binary_data, $read_size);
					$size -= $read_size;
				}
				fclose($fp); gzclose($gzp);

				touch($to.$header['filename'], $header['mtime']);
				@unlink($to.$header['filename'].'.gz');
			}
		}
		$this->total_files ++;
		echo 'Extract : ',$header[filename],'<br>';
		return true;
	}

//createZip:
	public $compressedData = array();
	public $centralDirectory = array(); // central directory
	public $endOfCentralDirectory = "\x50\x4b\x05\x06\x00\x00\x00\x00"; //end of Central directory record
	public $oldOffset = 0;

	/**
	* Function to create the directory where the file(s) will be unzipped
	*
	* @param $directoryName string
	*
	*/

	public function addDirectory($directoryName) {
		$directoryName = str_replace("\\", "/", $directoryName);

		$feedArrayRow = "\x50\x4b\x03\x04";
		$feedArrayRow .= "\x0a\x00";
		$feedArrayRow .= "\x00\x00";
		$feedArrayRow .= "\x00\x00";
		$feedArrayRow .= "\x00\x00\x00\x00";

		$feedArrayRow .= pack("V",0);
		$feedArrayRow .= pack("V",0);
		$feedArrayRow .= pack("V",0);
		$feedArrayRow .= pack("v", strlen($directoryName) );
		$feedArrayRow .= pack("v", 0 );
		$feedArrayRow .= $directoryName;

		$feedArrayRow .= pack("V",0);
		$feedArrayRow .= pack("V",0);
		$feedArrayRow .= pack("V",0);

		$this -> compressedData[] = $feedArrayRow;

		$newOffset = strlen(implode("", $this->compressedData));

		$addCentralRecord = "\x50\x4b\x01\x02";
		$addCentralRecord .="\x00\x00";
		$addCentralRecord .="\x0a\x00";
		$addCentralRecord .="\x00\x00";
		$addCentralRecord .="\x00\x00";
		$addCentralRecord .="\x00\x00\x00\x00";
		$addCentralRecord .= pack("V",0);
		$addCentralRecord .= pack("V",0);
		$addCentralRecord .= pack("V",0);
		$addCentralRecord .= pack("v", strlen($directoryName) );
		$addCentralRecord .= pack("v", 0 );
		$addCentralRecord .= pack("v", 0 );
		$addCentralRecord .= pack("v", 0 );
		$addCentralRecord .= pack("v", 0 );
		$ext = "\x00\x00\x10\x00";
		$ext = "\xff\xff\xff\xff";
		$addCentralRecord .= pack("V", 16 );

		$addCentralRecord .= pack("V", $this -> oldOffset );
		$this -> oldOffset = $newOffset;

		$addCentralRecord .= $directoryName;

		$this -> centralDirectory[] = $addCentralRecord;
	}

/**
* Function to add file(s) to the specified directory in the archive
*
* @param $directoryName string
*
*/

	public function addFile($data, $directoryName) {

		$directoryName = str_replace("\\", "/", $directoryName);

		$feedArrayRow = "\x50\x4b\x03\x04";
		$feedArrayRow .= "\x14\x00";
		$feedArrayRow .= "\x00\x00";
		$feedArrayRow .= "\x08\x00";
		$feedArrayRow .= "\x00\x00\x00\x00";

		$uncompressedLength = strlen($data);
		$compression = crc32($data);
		$gzCompressedData = gzcompress($data);
		$gzCompressedData = substr( substr($gzCompressedData, 0, strlen($gzCompressedData) - 4), 2);
		$compressedLength = strlen($gzCompressedData);
		$feedArrayRow .= pack("V",$compression);
		$feedArrayRow .= pack("V",$compressedLength);
		$feedArrayRow .= pack("V",$uncompressedLength);
		$feedArrayRow .= pack("v", strlen($directoryName) );
		$feedArrayRow .= pack("v", 0 );
		$feedArrayRow .= $directoryName;

		$feedArrayRow .= $gzCompressedData;

		$feedArrayRow .= pack("V",$compression);
		$feedArrayRow .= pack("V",$compressedLength);
		$feedArrayRow .= pack("V",$uncompressedLength);

		$this -> compressedData[] = $feedArrayRow;

		$newOffset = strlen(implode("", $this->compressedData));

		$addCentralRecord = "\x50\x4b\x01\x02";
		$addCentralRecord .="\x00\x00";
		$addCentralRecord .="\x14\x00";
		$addCentralRecord .="\x00\x00";
		$addCentralRecord .="\x08\x00";
		$addCentralRecord .="\x00\x00\x00\x00";
		$addCentralRecord .= pack("V",$compression);
		$addCentralRecord .= pack("V",$compressedLength);
		$addCentralRecord .= pack("V",$uncompressedLength);
		$addCentralRecord .= pack("v", strlen($directoryName) );
		$addCentralRecord .= pack("v", 0 );
		$addCentralRecord .= pack("v", 0 );
		$addCentralRecord .= pack("v", 0 );
		$addCentralRecord .= pack("v", 0 );
		$addCentralRecord .= pack("V", 32 );

		$addCentralRecord .= pack("V", $this -> oldOffset );
		$this -> oldOffset = $newOffset;

		$addCentralRecord .= $directoryName;

		$this -> centralDirectory[] = $addCentralRecord;
	}

/**
* Fucntion to return the zip file
*
* @return zipfile (archive)
*/

	public function getZippedfile() {

		$data = implode("", $this -> compressedData);
		$controlDirectory = implode("", $this -> centralDirectory);

		return
		$data.
		$controlDirectory.
		$this -> endOfCentralDirectory.
		pack("v", sizeof($this -> centralDirectory)).
		pack("v", sizeof($this -> centralDirectory)).
		pack("V", strlen($controlDirectory)).
		pack("V", strlen($data)).
		"\x00\x00";
	}

/**
*
* Function to force the download of the archive as soon as it is created
*
* @param archiveName string - name of the created archive file
*/

	public function forceDownload($archiveName) {
		$headerInfo = '';

		if(ini_get('zlib.output_compression')) {
			ini_set('zlib.output_compression', 'Off');
		}

		// Security checks
		if( $archiveName == "" ) {
			echo "ERROR: The download file was NOT SPECIFIED.";
			exit;
		} elseif ( ! file_exists( $archiveName ) ) {
			echo "ERROR: File not found.";
			exit;
		}

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=".basename($archiveName).";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($archiveName));
		readfile("$archiveName");

	}
//end class
}
?>
