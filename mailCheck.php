<?php 

include "mailFunc.php";

$code = $_POST['code'];
if($code) {
	$decode = authcode($code, 'DECODE', 'htt-qrcodeManagement');
	//修改数据库内的验证状态
	$json = array();
	$json['code'] = $decode;
	$json['success'] = true;
	echo json_encode($json);
}  

?>