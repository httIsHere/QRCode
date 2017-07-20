<?php
header('Access-Control-Allow-Origin:*');
header("Content-type: text/html; charset=utf-8");
require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
include("page_switching.php");
$user=$_POST["email"];
$password=$_POST["password"];
$sql = "insert into qydt_ManageUser(ManageUserName, Pwd, WeChatAccount, AppID, AppSecret) value('$user', '$password','".qyAccount."', '".qyAppId."','".qyAppsecret."')";

logger('QRCode_signUp','log/','Log',"signup_insertSql=".$sql);
$ret=runInsertUpdateDeleteSql($sql);
if($ret)
page_redirect(false,"signin.html","注册成功，可进行登录！");
else
page_redirect(true,"","注册失败，该邮箱已被注册过，请重新注册！");
?>
