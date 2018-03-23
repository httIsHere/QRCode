<?php 
require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
$type=(integer)$_POST['type'];
switch ($type) {
	case 0:
		$data = array();
		$totalUser = count(runSelectSql("select ManageUserName from YQ_ManageUser"));
		$totalCode = count(runSelectSql("select SceneID from YQ_QRCode"));
		$totalScan = count(runSelectSql("select OpenID from YQ_ReceiveMsg"));
		$totalScanUser = count(runSelectSql("select distinct OpenID from YQ_ReceiveMsg"));
		$data['totalUser'] = $totalUser;
		$data['totalCode'] = $totalCode;
		$data['totalScan'] = $totalScan;
		$data['totalScanUser'] = $totalScanUser;
		$m = date("m");
		$d = date("d");
		$y = date("Y");
		$time = time();
		$start = mktime(0,0,0,$m,$d,$y);
		# 新用户数，增长率
		$newUsers = count(runSelectSql("select ManageUserName from YQ_ManageUser where createTime - '$start' <= 24*60*60 and createTime - '$start' > 0"));
		$data['newUsers'] = $newUsers;
		$data['userRate'] = $newUsers / ($totalUser - $newUsers);
		# 新增扫描事件
		$newScan = count(runSelectSql("select OpenID from YQ_ReceiveMsg where CreateTime - '$start' <= 24*60*60 and CreateTime - '$start' > 0"));
		$data['newScan'] = $newScan;
		$data['scanRate'] = $newScan / ($totalScan - $newScan);

		# 用户列表
		$sql = "select ManageUserName, WeChatAccount, createTime from YQ_ManageUser";
		$result = runSelectSql($sql);
		$data['userList'] = $result;

		$replyStr = json_encode($data);
		break;
	
	case 1:
		#账户操作
		$tp=(integer)$_POST['tp'];
		$opAccount = $_POST['account'];
		if($tp == 1){
			# stop
			$sql = "update YQ_ManageUser set status=1 where ManageUserName='$opAccount'";
		} else if($tp == 0){
			# recover
		} else if($tp == 2){
			# reset password
		}
		break;
}
echo $replyStr;
?>