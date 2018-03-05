<?php 
	require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
 	$type=(integer)$_POST['type'];

 	switch ($type) {
 		case 0:
 			# user list(分页)
 			$sql = "select ManageUserName, WeChatAccount, createTime from YQ_ManageUser";
 			$replyStr = json_encode(runSelectSql($sql));
 			break;
 		case 1:
 			# code list(分页)
 			break;
 	}

 	echo $replyStr;
?>