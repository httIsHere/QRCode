<?php
//开启session
header("Content-type: text/html; charset=utf-8");
session_start();
require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
include("page_switching.php");

$user=$_POST["useremail"];
$password=$_POST["userpwd"];

$sql = "SELECT Pwd, ManageUserName,WeChatAccount, status, isAdmin FROM YQ_ManageUser WHERE ManageUserName = '$user'";

    //查询记录
	// $result = mysqli_query($conn,$sql);
$result = runSelectSql($sql);
$pwd=$result[0]["Pwd"];
$_SESSION['WeChatAccount'] = $result[0]['WeChatAccount'];
$status = $result[0]['status'];
$isAdmin = $result[0]['isAdmin'];
	// echo $pwd;
    //获取当前行--一定是唯一的？
	// $rows = mysqli_fetch_array($result,MYSQLI_ASSOC);

if ($result){
	//邮箱是否验证
	if($status == '1'){
		page_redirect(true,"","该账户已被停用！有问题请联系系统管理员");
		return;
	}
	if ($pwd == $password){
		//对该用户嵌入accessID
		$_SESSION["username"] = $result[0]["ManageUserName"];
		$name = $_SESSION["username"];
    	//随机生成accessID
		$accessid = rand();

		$_SESSION["accessID"] = $accessid;

		$_SESSION["userpwd"] = $pwd;
		$sql = "UPDATE YQ_ManageUser SET AccessID = '$accessid'  WHERE ManageUserName = '$user'";
		$result = runSelectSql($sql);
		//echo $isAdmin;
		if($isAdmin && $isAdmin == '1'){
			page_redirect(false, 'new-admin.html');
		} else {
			page_redirect(false,"new-user_index.php","");
		}
	} else{
		page_redirect(true,"","用户或密码错误!");
	}
} else{
	page_redirect(true,"","用户或密码错误!");
}

?>
