<?php 
require 'mailFunc.php';
$url = authcode('tingting.huang@cgtz.com', 'ENCODE', 'htt-qrcodeManagement');
echo mailCheck('tingting.huang@cgtz.com',$url);
?>