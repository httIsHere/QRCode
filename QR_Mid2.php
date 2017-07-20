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
				$qrCodeInfo=runSelectSql("select SceneID,SceneName,SceneDescription,SceneImg, Ticket,SceneImage,SceneUrl from qydt_QRCode where ManageUserName='$webuser' and Ticket is not null and Ticket <> '' order by SceneID desc");
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
				if($token != null || $token != "")
				$url = uploadImg($token,$Img);
				logger('iM_QRUser_Mid','log/','Log',"sceneImg=".$url);
				if($url == null || $url == ""){
					$replyStr="图片上传失败！";
					// $replyStr=$token;
					break;
				}
				}

				if($urlCheck == 1){
				$sql = "INSERT INTO qydt_QRCode(SceneName , SceneDescription,SceneImg, SceneImage,SceneUrl,ManageUserName ) VALUE ('$unitName','$desp','$url','$sceneImg','$sceneUrl','$webuser')";
				logger('iM_QRUser_Mid','log/','Log',"save sql = "+$sql);
				$ret=runInsertUpdateDeleteSql($sql);
				$replyStr="信息已经保存";
				$isNewUser=runSelectSql("select SceneID,QRCodeImgFileName from qydt_QRCode where (ManageUserName='$webuser') and (QRCodeImgFileName IS NULL  OR QRCodeImgFileName ='')");

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
							$qrCodeInfo=getTicketOfQrcode($account,$appId,$appS,$sceneID);
							$ticket=$qrCodeInfo['ticket'];
							// echo $ticket;
							if($ticket != null && $ticket != ""){
							$filename= getRandStr('qrcode_',36,'.jpg');
							$QRCodeImgFileName=getQRCodeUrlFromTicket($ticket,$filename);
							logger('iM_QRUser_Mid','log/','Log',"ticket = '$ticket'");
							$ret=runInsertUpdateDeleteSql("update qydt_QRCode set Ticket='$ticket',QRCodeImgFileName='$QRCodeImgFileName' where (ManageUserName='$webuser') and (SceneID='$sceneID')");
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
				$sql = "delete from qydt_QRCode where Ticket = '$ticket'";
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
					$sql = "update qydt_QRCode set SceneName='$unitName', SceneDescription='$desp', SceneUrl='$sceneUrl',SceneImage = '$sceneImg',SceneImg = '$url' where Ticket='$ticket'";
				}
				else{
					$sql = "update qydt_QRCode set SceneName='$unitName', SceneDescription='$desp', SceneUrl='$sceneUrl' where Ticket='$ticket'";
				}
				logger('iM_QRUser_Mid','log/','Log',"update sql = "+$sql);
				$ret = runInsertUpdateDeleteSql($sql);
				$replyStr = "二维码修改成功！";
				break;
		default:
				$replyStr="switch default";
				break;
	}
	logger('iM_QRUser_Mid','log/','Log',"replyStr=".$replyStr);
	echo $replyStr;
?>
