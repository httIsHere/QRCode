<?php 
header("Content-type: text/html; charset=utf-8");
require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
$user = $_POST['user'];
$account = $_POST['weChatAccount'];
$type = $_POST['type'] || 1;
// $account = "ItsMusic";
// $type = 1;
$data = array();
$sql = "select OpenID from YQ_ReceiveMsg join YQ_QRCode on YQ_QRCode.Ticket = YQ_ReceiveMsg.Ticket where YQ_QRCode.ManageUserName = '$user' and WeChatAccount= '$account'";
$result = runSelectSql($sql);
$num = count($result);
$data['totalMsgNum'] = $num;

switch ($type) {
	case 1:
		# all
		$sql = "select WeChatAccount, YQ_ReceiveMsg.Ticket as ticket, CreateTime,count(YQ_ReceiveMsg.Ticket) as count,SceneName,SceneDescription, SceneUrl from YQ_ReceiveMsg join YQ_QRCode on YQ_QRCode.Ticket = YQ_ReceiveMsg.Ticket where WeChatAccount='$account' and YQ_ReceiveMsg.Ticket is not null and YQ_QRCode.ManageUserName = '$user' group by YQ_ReceiveMsg.Ticket order by count(YQ_ReceiveMsg.Ticket) desc limit 10";
		break;	
	default: 
		# today
		$y = date("Y");
		$m = date("m");
		$d = date("d");
		//目标日0点
		if($type = 2){
			$dd = 0;
		} else {
			$dd = ($type == 3) ? 7 : 30;
		}
		$start = mktime(-8,0,0,$m-$dd,$d,$y);
		$sql = "select WeChatAccount, YQ_ReceiveMsg.Ticket as ticket, CreateTime,count(YQ_ReceiveMsg.Ticket) as count,SceneName,SceneDescription, SceneUrl from YQ_ReceiveMsg join YQ_QRCode on YQ_QRCode.Ticket = YQ_ReceiveMsg.Ticket where CreateTime - '$start' <= 24*60*60 and CreateTime - '$start' > 0 andWeChatAccount='$account' and YQ_ReceiveMsg.Ticket is not null and YQ_QRCode.ManageUserName = '$user' group by YQ_ReceiveMsg.Ticket order by count(YQ_ReceiveMsg.Ticket) desc limit 10";
		break;
}
$link=openDB();
$recodeList=array();
if($link){	
	$res=mysql_query($sql);
	if(!$res) {
		logger('CommonFunction','log/','Error',"runSelectSql Mysql-query error:".$sql.mysql_error());
	}
	else{	
		$sqlNum =  mysql_num_rows($res);
		for($i=0;$i<$sqlNum;$i++){	
			$row=mysql_fetch_array($res);
			if($row) {
				$recodeList[]=$row;
			}
		}
	}
	//$replyStr=json_encode($recodeList);
	mysql_close($link);
}

$data['rankList'] = $recodeList;
echo json_encode($data);
?>