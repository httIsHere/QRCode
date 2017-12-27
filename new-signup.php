<?php
header('Access-Control-Allow-Origin:*');
header("Content-type: text/html; charset=utf-8");
require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
include("page_switching.php");
include("mailFunc.php");
$qyAccount = 'ItsMusic';
$qyAppId = 'wx3baa5a278d207f5e';
$qyAppsecret = '072b4d7cf04dd25660f577d0017c6f62';
$user=$_POST["email"];
$password=$_POST["password"];
$sql = "insert into YQ_ManageUser(ManageUserName, Pwd, WeChatAccount, AppID, AppSecret, status) value('$user', '$password','".$qyAccount."', '".$qyAppId."','".$qyAppsecret."', 1)";

logger('QRCode_signUp','log/','Log',"signup_insertSql=".$sql);
$ret=runInsertUpdateDeleteSql($sql);
if($ret){
	page_redirect(false,"new-signin.html","注册成功！");
	//page_redirect(true,"","验证信息已发送到邮箱，请至邮箱验证注册信息");
	//向邮箱发送验证url
	// $url = authcode($user, 'ENCODE', 'htt-qrcodeManagement'); 
	// mailCheck($user, $url);	
}
else{
	page_redirect(true,"","注册失败，该邮箱已被注册过，请重新注册！");
}
?>
