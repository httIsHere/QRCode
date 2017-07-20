<?php
header('content-type:application/json;charset=utf-8');
require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
function uploadImg($token, $filepath){
$url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=".$token."&type=image";
$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     //发送 POST 请求
        curl_setopt($ch, CURLOPT_POST, true);
        //全部数据使用HTTP协议中的 "POST" 操作来发送。
        $data = array('media'=>'@'.$filepath);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$res = curl_exec( $ch );
		curl_close( $ch );
		if($res){
			$res = json_decode($res,true);
			return $res['url'];
		}
		else return null;
}
?>
