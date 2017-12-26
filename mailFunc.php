<?php 
header("Content-type: text/html; charset=utf-8");
include "class.phpmailer.php";  
include "class.smtp.php";  

//$code = $_POST['code'];

//发送邮件
function mailCheck($toMail, $toUrl) {
	$mail = new PHPMailer();  
	$mail->isSMTP();// 使用SMTP服务  
	$mail->CharSet = "utf-8";// 编码格式为utf8，不设置编码的话，中文会出现乱码  
	$mail->Host = "smtp.163.com";// 发送方的SMTP服务器地址  
	$mail->SMTPAuth = true;// 是否使用身份验证  
	$mail->Username = "18457730959@163.com";// 发送方的163邮箱用户名  
	$mail->Password = "19911116";// 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！  
	$mail->SMTPSecure = "ssl";// 使用ssl协议方式  
	$mail->Port = 994;// 163邮箱的ssl协议方式端口号是465/994  
	$mail->Form= "htt-qrcodeManagement";  
	$mail->Helo= "xxxx";  
	$mail->setFrom("18457730959@163.com","htt-qrcodeManagement");// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示  
	$toName = $toMail;
	$mail->addAddress($toMail,$toName);// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)  
	$mail->IsHTML(true);  
	$mail->Subject = '验证HTT二维码管理系统电子邮箱';// 邮件标题  
	$mail->Body = '<a href="http://www.musicren.com/itsmusic/new-signin.html">HTT二维码管理系统</a><br/><h3>请确认你的邮箱地址</h3><p>点击下方进行邮箱验证</p><a href="http://localhost/mailCheck/checkMailUrl.html?encode='.$toUrl.'">点击此处验证邮箱</a>';// 邮件正文  
	$status = $mail->send();

	return $status;
}

//加密邮箱，产生加密信息
/** 
 * @param string $string 原文或者密文 
 * @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE 
 * @param string $key 密钥 
 * @param int $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效 
 * @return string 处理后的 原文或者 经过 base64_encode 处理后的密文 
 * 
 * @example 
 * 
 * $a = authcode('abc', 'ENCODE', 'key'); 
 * $b = authcode($a, 'DECODE', 'key');  // $b(abc) 
 * 
 * $a = authcode('abc', 'ENCODE', 'key', 3600); 
 * $b = authcode('abc', 'DECODE', 'key'); // 在一个小时内，$b(abc)，否则 $b 为空 
 */  
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 3600) {  
      
    $ckey_length = 4;  
    // 随机密钥长度 取值 0-32;  
    // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。  
    // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方  
    // 当此值为 0 时，则不产生随机密钥  
      
  
    $key = md5 ( $key ? $key : 'key' ); //这里可以填写默认key值  
    $keya = md5 ( substr ( $key, 0, 16 ) );  
    $keyb = md5 ( substr ( $key, 16, 16 ) );  
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr ( $string, 0, $ckey_length ) : substr ( md5 ( microtime () ), - $ckey_length )) : '';  
      
    $cryptkey = $keya . md5 ( $keya . $keyc );  
    $key_length = strlen ( $cryptkey );  
      
    $string = $operation == 'DECODE' ? base64_decode ( substr ( $string, $ckey_length ) ) : sprintf ( '%010d', $expiry ? $expiry + time () : 0 ) . substr ( md5 ( $string . $keyb ), 0, 16 ) . $string;  
    $string_length = strlen ( $string );  
      
    $result = '';  
    $box = range ( 0, 255 );  
      
    $rndkey = array ();  
    for($i = 0; $i <= 255; $i ++) {  
        $rndkey [$i] = ord ( $cryptkey [$i % $key_length] );  
    }  
      
    for($j = $i = 0; $i < 256; $i ++) {  
        $j = ($j + $box [$i] + $rndkey [$i]) % 256;  
        $tmp = $box [$i];  
        $box [$i] = $box [$j];  
        $box [$j] = $tmp;  
    }  
      
    for($a = $j = $i = 0; $i < $string_length; $i ++) {  
        $a = ($a + 1) % 256;  
        $j = ($j + $box [$a]) % 256;  
        $tmp = $box [$a];  
        $box [$a] = $box [$j];  
        $box [$j] = $tmp;  
        $result .= chr ( ord ( $string [$i] ) ^ ($box [($box [$a] + $box [$j]) % 256]) );  
    }  
      
    if ($operation == 'DECODE') {  
        if ((substr ( $result, 0, 10 ) == 0 || substr ( $result, 0, 10 ) - time () > 0) && substr ( $result, 10, 16 ) == substr ( md5 ( substr ( $result, 26 ) . $keyb ), 0, 16 )) {  
            return substr ( $result, 26 );  
        } else {  
            return '';  
        }  
    } else {  
        return $keyc . str_replace ( '=', '', base64_encode ( $result ) );  
    }    
} 

$url = authcode('tingting.huang@cgtz.com', 'ENCODE', 'htt-qrcodeManagement'); 
// echo $a.'<br/>';
// $b = authcode($a, 'DECODE', 'htt-qrcodeManagement');  // $b(abc) 
// echo $b.'<br/>';
//echo mailCheck('tingting.huang@cgtz.com', $url);

?>