<?php
	header("Content-type: text/html; charset=utf-8");
	require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
	session_start();
//if($_SESSION['userInfor'] == null){
//	include("sql_connection.php");
	$user = $_POST['user'];
//	$account = 'ItsMusic';
	$account = $_POST['account'];
//	$account = "bemusic";
	$data = array();
	//先搜索出OpenID，再根据OpenID来查找nickname之类的？
//	 $sql = "select OpenID, max(CreateTime) from qydt_ReceiveMsg where WeChatAccount='ItsMusic' group by OpenID order by CreateTime desc";

	// $sql = "select distinct qydt_WXUser.OpenID,nickname,sex,city,headimgurl from (qydt_WXUser join qydt_ReceiveMsg) where (qydt_WXUser.OpenID = qydt_ReceiveMsg.OpenID) and qydt_WXUser.WeChatAccount = '$account'";
	// $sql = "select * from (select qydt_ReceiveMsg.OpenID,nickname,sex,city,headimgurl,max(CreateTime) as CreateTime from (qydt_WXUser join qydt_ReceiveMsg) where (qydt_WXUser.OpenID = qydt_ReceiveMsg.OpenID) and qydt_WXUser.WeChatAccount = '$account' group by qydt_ReceiveMsg.OpenID) as info order by CreateTime desc";

	##之前用的sql语句 by 2017-12-29
	// $sql = "select * from (select qydt_ReceiveMsg.OpenID,nickname,sex,city,headimgurl,max(CreateTime) as CreateTime from (qydt_WXUser join qydt_ReceiveMsg join qydt_QRCode on qydt_QRCode.Ticket = qydt_ReceiveMsg.Ticket) where (qydt_WXUser.OpenID = qydt_ReceiveMsg.OpenID) and qydt_WXUser.WeChatAccount = '$account' and qydt_QRCode.ManageUserName = '$user' group by qydt_ReceiveMsg.OpenID) as info order by CreateTime desc";

	#扫描用户信息
	$sql = "select * from (select YQ_ReceiveMsg.OpenID,nickname,sex,city,headimgurl,max(CreateTime) as CreateTime from (YQ_WXUser join YQ_ReceiveMsg join YQ_QRCode on YQ_QRCode.Ticket = YQ_ReceiveMsg.Ticket) where (YQ_WXUser.OpenID = YQ_ReceiveMsg.OpenID) and YQ_WXUser.WeChatAccount = '$account' and YQ_QRCode.ManageUserName = '$user' group by YQ_ReceiveMsg.OpenID) as info order by CreateTime desc";
	$link=openDB();
	$recodeList=array();
	if($link){	
		$res=mysql_query($sql);
		if(!$res) {
			logger('CommonFunction','log/','Error',"runSelectSql Mysql-query error:".$sql.mysql_error());}
		else{	
			$sqlNum =  mysql_num_rows($res);
			for($i=0;$i<$sqlNum;$i++){	
				$row=mysql_fetch_array($res);
				if($row) {
					$recodeList[$i]=$row;
				}
			}
		}
		$replyStr=json_encode($recodeList);
		mysql_close($link);
	}

	$data['userInfor'] = $recodeList;

	#扫描用户排行榜
	$sql = "select * from (select YQ_ReceiveMsg.OpenID,nickname,sex,city,headimgurl,count(YQ_ReceiveMsg.OpenID) as count from (YQ_WXUser join YQ_ReceiveMsg join YQ_QRCode on YQ_QRCode.Ticket = YQ_ReceiveMsg.Ticket) where (YQ_WXUser.OpenID = YQ_ReceiveMsg.OpenID) and YQ_WXUser.WeChatAccount = '$account' and YQ_QRCode.ManageUserName = '$user' group by YQ_ReceiveMsg.OpenID) as info order by count desc limit 10";
	$link=openDB();
	$rank=array();
	if($link){	
		$res=mysql_query($sql);
		if(!$res) {
			logger('CommonFunction','log/','Error',"runSelectSql Mysql-query error:".$sql.mysql_error());}
		else{	
			$sqlNum =  mysql_num_rows($res);
			for($i=0;$i<$sqlNum;$i++){	
				$row=mysql_fetch_array($res);
				if($row) {
					$rank[$i]=$row;
				}
			}
		}
		$replyStr=json_encode($rank);
		mysql_close($link);
	}

	$data['userRank'] = $rank;

	echo json_encode($data);
//	echo $data;
	$_SESSION['userInfor'] = json_encode($data);
//	echo $data[1]['headimgurl'];
//}
//else{
//	echo $_SESSION['userInfor'];
//}
?>
