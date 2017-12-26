<?php
//require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
$account = 'ItsMusic';
$appId = 'wx3baa5a278d207f5e';
$appS = '072b4d7cf04dd25660f577d0017c6f62';
echo $account;
//$num = (integer)$_POST['num'];
$num = 1;
$start = microtime(true);
//for($i = 1000000000; $i < (1000000000+$num); $i++){
//	$qrCodeInfo=getTicketOfQrcode($account,$appId,$appS,$i);
//}
$end = microtime(true);
echo (($end - $start)*1000).'ms';
?>