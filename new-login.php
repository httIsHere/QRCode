<?php
//开启session
header("Content-type: text/html; charset=utf-8");
session_start();
	require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
	include("page_switching.php");

    $user=$_POST["useremail"];
	$password=$_POST["userpwd"];

		 	$sql = "SELECT Pwd, ManageUserName,OurWeChatAccount FROM ManageUser WHERE ManageUserName = '$user'";
//		 }

    //查询记录
	// $result = mysqli_query($conn,$sql);
	$result = runSelectSql($sql);
	$pwd=$result[0]["Pwd"];
	$_SESSION['OurWeChatAccount'] = $result[0]['OurWeChatAccount'];
	// echo $pwd;
    //获取当前行--一定是唯一的？
	// $rows = mysqli_fetch_array($result,MYSQLI_ASSOC);

	if ($result)
		 {
		 	if ($pwd == $password)
		 	{

					// echo "登录成功！";
					//对该用户嵌入accessID
					$_SESSION["username"] = $result[0]["ManageUserName"];
					$name = $_SESSION["username"];
//					$_SESSION["username"] = $rows["ManageUserName"];
    				//随机生成accessID
    				$accessid = rand();

    				$_SESSION["accessID"] = $accessid;
//    				$_SESSION["useremail"] = $user;
   	 				$_SESSION["userpwd"] = $pwd;
					$sql = "UPDATE ManageUser SET AccessID = '$accessid'  WHERE ManageUserName = '$user'";
					$result = runSelectSql($sql);
					page_redirect(false,"new-user_index.php","");
		 	}
		 	else
		 	{
		 		page_redirect(true,"","用户或密码错误!");
		 	}
		 }
		 else
		 {
			   page_redirect(true,"","用户或密码错误!");
		 }

?>
