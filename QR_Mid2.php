<?php
require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
// include("getQRCode.php");
include("checkLink.php");
include("uploadImgToWX.php");
//$fromOpenID=$_POST['fromOpenID'];
//$fromOpenID = "123";
 $type=(integer)$_POST['type'];
	//获得当前的user
	$webuser=$_POST['UserWebID'];
	switch($type)
	{
		case 0:
				// $qrCodeInfo=runSelectSql("select SceneID,SceneName,SceneDescription,SceneImg, SceneUrl,QRCodeImgFileName,Ticket,SceneImage from qydt_QRCode where ManageUserName='$webuser' order by SceneID desc");
				$qrCodeInfo=runSelectSql("select SceneID,SceneName,SceneDescription,SceneImg, Ticket,SceneImage,SceneUrl,QRCodeImgFileName from YQ_QRCode where ManageUserName='$webuser' and Ticket is not null and Ticket <> '' order by SceneID desc");
				$replyStr=json_encode($qrCodeInfo);
				break;
		case 2:
				$unitName=$_POST['unitName'];
				$desp=$_POST['desp'];
				$sceneImg=$_POST['sceneImg'];
				$sceneUrl=$_POST['sceneUrl'];
				$sceneImage = $_POST['sceneImage'];
				$account = $_POST['account']; $appId = $_POST['appId']; $appS = $_POST['appS'];
				//如果链接不为空对链接进行判断
				$urlCheck = 1;
				if($sceneUrl != ""){
					//无效链接
					if(checkTheLink($sceneUrl) != 1){
						$urlCheck = 0;
						$replyStr="图文链接为无效链接,请确认链接";
						break;
					}
				}
				else{
					$sceneUrl = "http://mp.weixin.qq.com/s/asipaNiCoCs8tUj7dmyHPg";
				}
				//如果有图片上传图片至微信服务器
				if($sceneImg != ""){
				$Img = dirname(__FILE__)."/".$sceneImg;
				logger('iM_QRUser_Mid','log/','Log',"sImg=".$Img);
				//获得token
				// $token = getAccess_Token(qyAccount, qyAppId, qyAppsecret);
				$token = getAccess_Token($account, $appId, $appS);
				logger('iM_QRUser_Mid','log/','Log',"token=".$token);
				if($token != null || $token != ""){
					$url = uploadImg($token,$Img);
				}				
				logger('iM_QRUser_Mid','log/','Log',"sceneImg=".$url);
				if($url == null || $url == ""){
					$replyStr="图片上传失败！";
					// $replyStr=$token;
					break;
				}
				}

				if($urlCheck == 1){
				$sql = "INSERT INTO YQ_QRCode(SceneName , SceneDescription,SceneImg, SceneImage,SceneUrl,ManageUserName ) VALUE ('$unitName','$desp','$url','$sceneImg','$sceneUrl','$webuser')";
				logger('iM_QRUser_Mid','log/','Log',"save sql = "+$sql);
				$ret=runInsertUpdateDeleteSql($sql);
				$replyStr="信息已经保存";
				$isNewUser=runSelectSql("select SceneID,QRCodeImgFileName from YQ_QRCode where (ManageUserName='$webuser') and (QRCodeImgFileName IS NULL  OR QRCodeImgFileName ='')");

				$qrNumber=count($isNewUser);
				if($qrNumber==0)
					$replyStr="无新的信息，二维码已全部显示！";
				else{
					for($i=0;$i<$qrNumber;$i++){
						$QRCodeImgFileName=$isNewUser[$i]['QRCodeImgFileName'];
						if(!empty($QRCodeImgFileName))
							$replyStr="二维码全部产生，刷新页面查看！";
						else
						{
							$sceneID=$isNewUser[$i]['SceneID'];logger('iM_QRUser_Mid','log/','Log',"$sceneID");
							// $qrCodeInfo=getTicketOfQrcode(qyAccount,qyAppId,qyAppsecret,$sceneID);
							$start1 = time();
							$start = microtime(true);
							$qrCodeInfo=getTicketOfQrcode($account,$appId,$appS,$sceneID);
							$end1 = time();
							$end = microtime(true);
							$ticket=$qrCodeInfo['ticket'];
							// echo $ticket;
							if($ticket != null && $ticket != ""){
								$filename= getRandStr('qrcode_'.round(($end-$start)*1000).'_',36,'.jpg');
								$QRCodeImgFileName=getQRCodeUrlFromTicket($ticket,$filename);
								logger('iM_QRUser_Mid','log/','Log',"ticket = '$ticket'");
								$ret=runInsertUpdateDeleteSql("update YQ_QRCode set Ticket='$ticket',QRCodeImgFileName='$QRCodeImgFileName' where (ManageUserName='$webuser') and (SceneID='$sceneID')");
								$replyStr="二维码生成成功！";
              					//推送图文信息给该生成用户
              					//参数：公众号3件套，fromUserName, toUserName, newsNumber,title,desp,url,picurl
              					if($url == null || $url ==""){
                					$url = "http://mmbiz.qpic.cn/mmbiz_jpg/eJAQYeyIQyCVRUWd8Xj46H7faibmYtWfU4kanRvFdqzzbOdzrGyo1TlaoxHkGgBEgiaQ6nIcjVe0mZpEmDsDOdbg/0?wx_fmt=jpeg";
              					}
              					if($sceneUrl == null || $sceneUrl == ""){
                					$sceneUrl = "http://mp.weixin.qq.com/s/asipaNiCoCs8tUj7dmyHPg";
              					}
              					$newsNumber = 1;
              					$titleArray[0] = $unitName;
              					$despArray[0] = $desp;
              					$urlArray[0] = $sceneUrl;
              					$imgArray[0] = $url;
              					sendNewsMsg($account, $appId, $appS, 'gh_21c7cdfd4e9d', $webuser, $newsNumber, $titleArray, $despArray, $urlArray, $imgArray);
							}else{
								$replyStr = "无法获取二维码，请确认公众号信息";
							}
						}
					}
					break;
				}
				}
				break;
		case 3:
				$ticket = $_POST['ticket'];
				$sql = "delete from YQ_QRCode where Ticket = '$ticket'";
				$ret = runInsertUpdateDeleteSql($sql);
				$replyStr = "场景二维码已删除";
				break;
		case 4://编辑二维码信息
				$ticket = $_POST['ticket'];
				$account = $_POST['account']; $appId = $_POST['appId']; $appS = $_POST['appS'];
				$unitName=$_POST['unitName']; $desp=$_POST['desp'];$sceneUrl=$_POST['sceneUrl'];
				$sceneImg = $_POST['sceneImg'];
				//如果链接不为空对链接进行判断
				$urlCheck = 1;
				if($sceneUrl != ""){
					//无效链接
//					echo checkTheLink($sceneUrl);
					if(checkTheLink($sceneUrl) != 1){
						$urlCheck = 0;
						$replyStr="图文连接为无效链接，场景二维码修改失败！";
						break;
					}
				}
				//如果有图片上传图片至微信服务器
				if($sceneImg != null  && $sceneImg != ""){
				$Img = dirname(__FILE__)."/".$sceneImg;
				logger('iM_QRUser_Mid','log/','Log',"sImg=".$Img);
				//获得token
				// $token = getAccess_Token(qyAccount, qyAppId, qyAppsecret);
				$token = getAccess_Token($account, $appId, $appS);
				logger('iM_QRUser_Mid','log/','Log',"token=".$token);
				if($token != null || $token != "")
				$url = uploadImg($token,$Img);
				logger('iM_QRUser_Mid','log/','Log',"sceneImg=".$url);
				if($url == null || $url == ""){
					$replyStr="图片上传失败！";
					break;
				}
				}
				if($url != null && $url != ""){
				// if(checkTheLink($sceneUrl) == 1){
					$sql = "update YQ_QRCode set SceneName='$unitName', SceneDescription='$desp', SceneUrl='$sceneUrl',SceneImage = '$sceneImg',SceneImg = '$url' where Ticket='$ticket'";
				}
				else{
					$sql = "update YQ_QRCode set SceneName='$unitName', SceneDescription='$desp', SceneUrl='$sceneUrl' where Ticket='$ticket'";
				}
				logger('iM_QRUser_Mid','log/','Log',"update sql = "+$sql);
				$ret = runInsertUpdateDeleteSql($sql);
				$replyStr = "二维码修改成功！";
				break;
		case 5://生成测试
				$account = $_POST['account']; $appId = $_POST['appId']; $appS = $_POST['appS'];
				$num = $_POST['num'];
				# 需要将时间测试生成的结果下载到本地（导入曲线拟合代码中，得出曲线参数）
				$text = $num."\n";
				//微信
				if($num <= 200){
					$wechatDur = array();
					$wechatDur[] = 0;
					for($i = 1000000000; $i < (1000000000+$num); $i++){
						$start2 = microtime(true);
						$qrCodeInfo=getTicketOfQrcode($account,$appId,$appS,$i);
						$end2 = microtime(true);
						$wechatDur[] = round(($end2 - $start2)*1000);
					}
				}
				else {
					$wechatDur = -1;
				}
				$text = $text.json_encode($wechatDur)."\n";
				//腾讯(不支持https)
				//http://mobile.qq.com/qrcode?url=
				$qqDur = array();
				$qqDur[] = 0;
				for($i = 1000000000; $i < (1000000000+$num); $i++){
					$start3 = microtime(true);
					getQRCodeTest('http://mobile.qq.com/qrcode?url=', $i);
					$end3 = microtime(true);
					$qqDur[] = round(($end3 - $start3)*1000);
				}
				$text = $text.json_encode($qqDur)."\n";

				//联图
				$liantuDur = array();
				$liantuDur[] = 0;
				for($i = 1000000000; $i < (1000000000+$num); $i++){
					$start4 = microtime(true);
					getQRCodeTest('http://qr.liantu.com/api.php?&w=200&text=', $i);
					$end4 = microtime(true);
					$liantuDur[] = round(($end4 - $start4)*1000);
				}				
				$text = $text.json_encode($liantuDur);
				$filename = 'timeDur.txt';
				$myfile = fopen($filename, "w");
				fwrite($myfile, $text);
				fclose($myfile);

				$res = array();
				$res['wechat'] = $wechatDur;
				$res['qq'] = $qqDur;
				$res['liantu'] = $liantuDur;
				$replyStr = json_encode($res);
				break;
		default:
				$replyStr="switch default";
				break;
	}
	function getQRCodeTest($req, $url){
		//$url = urlencode($url);
		$req = $req.$url;
		$ch = curl_init($req);
		curl_setopt($ch, CURLOPT_HEADER, 0);    
    	curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$res = curl_exec( $ch );
		curl_close( $ch );
	}
	logger('iM_QRUser_Mid','log/','Log',"replyStr=".$replyStr);
	echo $replyStr;
?>
