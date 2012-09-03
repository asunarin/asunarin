<?php
session_start();
//http://cn.php.net/manual/en/security.magicquotes.php
if (get_magic_quotes_gpc()) {
		$in = array(&$_GET, &$_POST, &$_COOKIE);
		while (list($k,$v) = each($in)) {
				foreach ($v as $key => $val) {
						if (!is_array($val)) {
								$in[$k][$key] = stripslashes($val);
								continue;
						}
						$in[] =& $in[$k][$key];
				}
		}
		unset($in);
}

function chkLogin(){
global $password;
	if($_SESSION['login']!==true){
		if($_COOKIE['tfm_pwd']==$password){
			$_SESSION['login']=true;
		}else{
			header("location: login.php");
		}
	}
}
#返回是否登陆
function chkLogin2(){
global $password;
	if($_SESSION['login']!==true){
		if($_COOKIE['tfm_pwd']==$password){
			$_SESSION['login']=true;
			return true;
		}else{
		return false;
		}
	}
	return true;
}
?>