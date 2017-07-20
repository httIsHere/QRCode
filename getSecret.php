<?php
$user = $_POST['user'];
require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
	$sql = "select Pwd from qydt_ManageUser where ManageUserName='$user'";
	if($result = runSelectSql($sql)){
		echo $result[0]['Pwd'];
	}
	else{
		echo 0;
	}
?>
