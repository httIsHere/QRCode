<?php 
require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
include "mailFunc.php";

$code = $_POST['code'];
if($code) {
	$decode = authcode($code, 'DECODE', 'htt-qrcodeManagement');
	//修改数据库内的验证状态
	$sql = "update YQ_ManageUser set status=1  where ManageUserName=".$decode;
	logger('QRCode_signUp','log/','Log',"signup_checkMailSql=".$sql);
	$ret=runInsertUpdateDeleteSql($sql);
	$json = array();
	$json['code'] = $decode;
	$json['success'] = true;
	echo json_encode($json);
}  

?>