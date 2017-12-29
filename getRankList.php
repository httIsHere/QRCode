<?php 
header("Content-type: text/html; charset=utf-8");
require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
$user = $_POST['user'];
$account = $_POST['weChatAccount'];
$type = $_POST['type'] || 1;
// $account = "ItsMusic";
// $type = 1;
$data = array();
switch ($type) {
	case 1:
		# all
		$sql = "select WeChatAccount, YQ_ReceiveMsg.Ticket, CreateTime,count(YQ_ReceiveMsg.Ticket),SceneName,SceneDescription, SceneUrl from YQ_ReceiveMsg join YQ_QRCode on YQ_QRCode.Ticket = YQ_ReceiveMsg.Ticket where WeChatAccount='$account' and YQ_ReceiveMsg.Ticket is not null group by YQ_ReceiveMsg.Ticket order by count(YQ_ReceiveMsg.Ticket) desc limit 10
";
		$result = runSelectSql($sql);
		// $msg = array();
		// foreach ($result as $key => $value) {
		// 	$t = $value['Ticket'];
		// 	$sql = "select SceneName, SceneDescription, SceneUrl from YQ_QRCode where Ticket='".$t."'";
		// 	$res = runSelectSql($sql);
		// 	if($res) {
		// 		array_push($value, $res);
		// 		echo json_encode($value);
		// 	}
		// }
		$data['rankList'] = $result;
		break;	
	default:
		# code...
		break;
}
echo json_encode($data);
?>