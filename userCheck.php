<?php
require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
session_start();
include("page_switching.php");

$accessid = $_SESSION["accessID"];
$user=$_SESSION["username"];
$pwd = $_SESSION["userpwd"];
if($_SESSION["accessID"] == null){
	page_redirect(false,"new-signin.html","请重新登录");
}
$sql = "SELECT ManageUserName, AccessID FROM YQ_ManageUser WHERE ManageUserName = '$user'";
$result = runSelectSql($sql);
if($result){
	if($result[0]["AccessID"] != $accessid){
		echo "<script>alert('请重新登录');</script>";
		page_redirect(false,"new-signin.html","请重新登录");
	}
}
?>