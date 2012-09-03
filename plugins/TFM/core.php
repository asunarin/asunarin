<?php

require "config.php";
require "function.php";

if($_SESSION['login']!==true){
die();
}

$relativePath=$_POST['path'];

if($_GET['action']=="download"){
	$path=UTG($base.$_GET['path']);
	 if(file_exists($path)){
		$filename=getPathName($path);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . filesize($path));
		ob_clean();
		flush();
		readfile($path);
	}
	die();
}


if(strstr($relativePath,".."))die();

if(strrchr($relativePath,"/")!="/")
	$relativePath.="/";

date_default_timezone_set  ("Etc/GMT-8");
function GetTime($timestamp){
return date("Y-m-d G:i:s",$timestamp);
}

switch ($_POST['action']){
case "":
jsonDir("/");
break;
case "get":
jsonDir($relativePath);
break;
case "unzip":
	include "fasisun_zip_class.php";
//       C:/PHP/htdocs/_project/tfm/zhong/images.zip
	$Path=$base.$_POST['path'];
	$path_parts = pathinfo($Path);

	$z = new Zip;
	$z->Extract(UTG($Path),UTG($path_parts['dirname']));	
	echo 1;
break;
case "newDir":
	$path=$base.$_POST['path'];
	mkdir(UTG($path));
break;
case "rmDir":
	$path=$base.$_POST['path'];
	advancedRmdir(UTG($path));
break;
case "renameDir":
	$PathFrom=$base.$_POST['pathFrom'];
	$PathTo=$base.$_POST['pathTo'];
	rename(UTG($PathFrom),UTG($PathTo));
break;
case "copy":
	$PathFrom=$base.$_POST['pathFrom'];
	$PathTo=$base.$_POST['pathTo'];
	if(is_file($PathFrom))
		copy(UTG($PathFrom),UTG($PathTo));
	else
		swCopy(UTG($PathFrom."/"), UTG($PathTo."/"));
break;
case "cut":
	$PathFrom=$base.$_POST['pathFrom'];
	$PathTo=$base.$_POST['pathTo'];
	if(is_file($PathFrom)){
		copy(UTG($PathFrom),UTG($PathTo));
		unlink(UTG($PathFrom));
	}else{
		swCopy(UTG($PathFrom."/"), UTG($PathTo."/"));
		advancedRmdir(UTG($PathFrom));
	}
break;
case "property":
	$path=UTG($base.$_POST['path']);
	
	if(is_file($path)){
		$r['size']=round(filesize($path)/1024,2)."KB";
		$r['atime']=GetTime(fileatime($path));
		$r['mtime']=GetTime(filemtime($path));
		$r['ctime']=GetTime(filectime($path));
	}
	if(is_dir($path)){
		$r['size']=round(getDirSize($path)/1024,2)."KB";
	}
	echo json_encode($r);
break;
/*
file relevant
*/
case "newFile":
	$path=$base.$_POST['path'];
	fopen(UTG($path),"a");
break;
case "rmFile":
	$path=$base.$_POST['path'];
	unlink(UTG($path));
break;
case "renameFile":
	$pathFrom=$base.$_POST['PathFrom'];
	$pathTo=$base.$_POST['PathTo'];
	rename(UTG($pathFrom),UTG($pathTo));
break;
case "edit":

	$path=UTG($base.$_POST['path']);
	$content=file_get_contents($path);
	$r['encoding']=mb_detect_encoding($content,"auto,GBK",true);
	$r['content']=mb_convert_encoding($content,"UTF-8","auto,GBK");
	echo json_encode($r);
break;
case "saveEdit":
	$path=UTG($base.$_POST['path']);
	$content=$_POST['content'];
	$encoding=$_POST['encoding'];
	
	@$content=mb_convert_encoding($content,$encoding,"UTF-8");
	file_put_contents($path,$content);	
break;
/*
FAV
*/
case "getFav":

	$jsonFav=json_decode(file_get_contents("fav.php"));
	echo json_encode($jsonFav);
break;
case "addFav":
	$name=getPathName($_POST['path']);
	$path=$_POST['path'];
	$jsonFav=json_decode(file_get_contents("fav.php"));
	if($jsonFav)
		array_unshift($jsonFav, array("name"=>$name,"path"=>$path));
	else
		$jsonFav[]=array("name"=>$name,"path"=>$path);
	file_put_contents("fav.php", json_encode($jsonFav));
	echo json_encode($jsonFav);
break;
case "renameFav":
	$jsonFav=json_decode(file_get_contents("fav.php"));
	$oldname=$_POST['oldname'];
	$newname=$_POST['newname'];
	
	foreach($jsonFav as $k=>$v){
		if($v->name==$oldname){
			$v->name=$newname;
			break;
		}
	}

	file_put_contents("fav.php", json_encode($jsonFav));
	echo json_encode($jsonFav);	
break;
case "rmFav":
	$jsonFav=json_decode(file_get_contents("fav.php"));
	$name=$_POST['name'];
	
		foreach($jsonFav as $k=>$v){
			if($v->name!=$name)
			$jsonFav2[]=array("name"=>$v->name,"path"=>$v->path);
		}
	
	file_put_contents("fav.php", json_encode($jsonFav2));	
	echo json_encode($jsonFav2);	
break;
case "setpass":
$oldpwd=$_POST['oldpwd'];
$newpwd=$_POST['newpwd'];
$pwd=file_get_contents("pwd.php");

}




function jsonDir($path){
echo json_encode( listDirectory($path));
}
function listDirectory($relative){

global $base;
global $root;
$list['currDir']=$relative;
//$path=$base.$relative;
$path=UTG($base.$relative);

	$folder=opendir($path);
	while($one=readdir($folder)){
		$currPath=$path.$one;
		if(is_dir($currPath) && $one!="." && $one!=".."){
			$list['dirs'][]=GTU($one);
		}elseif(is_file($currPath)){
			$list['files'][]=array("name"=>GTU($one),
								  			 "size"=>round(filesize($currPath)/1024,2),
											 "link"=>str_replace($root,"",GTU($currPath)));
		}
	}
	return $list;
}





function getPathName($path){
//   path

//   /images/
	$arrPath=explode("/",$path);
	$name=$arrPath[count($arrPath)-1];
	if($name=="")$name=$arrPath[count($arrPath)-2];
	return $name;
}


function swCopy($from, $to){
	@mkdir($to);
	if(is_file($from)){
		copy($from,$to);
		return;
	}

	$currDir=opendir($from);
	while($dir = readdir($currDir)){
		if($dir=="." || $dir =="..") continue;
		
		if(is_file($from.$dir))
			@copy($from.$dir, $to.$dir);
		else{
			@mkdir($to.$dir);
			swCopy($from.$dir."/",$to.$dir."/");
		}
	}
}


//from php.net
function advancedRmdir($path) {
    $origipath = $path;
    $handler = opendir($path);
    while (true) {
        $item = readdir($handler);
        if ($item == "." or $item == "..") {
            continue;
        } elseif (gettype($item) == "boolean") {
            closedir($handler);
            if (!@rmdir($path)) {
                return false;
            }
            if ($path == $origipath) {
                break;
            }
            $path = substr($path, 0, strrpos($path, "/"));
            $handler = opendir($path);
        } elseif (is_dir($path."/".$item)) {
            closedir($handler);
            $path = $path."/".$item;
            $handler = opendir($path);
        } else {
            unlink($path."/".$item);
        }
    }
    return true;
}





    // 获取文件夹大小

    function getDirSize($dir)
    { 
        $handle = @opendir($dir);
        while (false!==($FolderOrFile =@ readdir($handle)))
        { 
            if($FolderOrFile != "." && $FolderOrFile != "..") 
            { 
                if(is_dir("$dir/$FolderOrFile"))
                { 
                    $sizeResult += getDirSize("$dir/$FolderOrFile"); 
                }
                else
                { 
                    $sizeResult += filesize("$dir/$FolderOrFile"); 
                }
            }    
        }
        @closedir($handle);
        return $sizeResult;
    }
			  
	function UTG($str){
	return mb_convert_encoding($str,"GB2312","UTF-8");
	}
	function GTU($str){
	return mb_convert_encoding($str,"UTF-8","GB2312");
	}
?>