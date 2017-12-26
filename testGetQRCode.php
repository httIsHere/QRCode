<?php
define("yqAccount","ItsMusic");
define("yqAppId","wx3baa5a278d207f5e");
define("yqAppsecret","072b4d7cf04dd25660f577d0017c6f62");
echo getTicketOfQrcode(yqAccount,yqAppId,yqAppsecret,1000000000);
function getTicketOfQrcode($WXAccount,$appid,$appsecret,$sceneName)
	{	
		$access_token =getAccess_Token($WXAccount,$appid,$appsecret); 
		$url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
		$data='{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "'.$sceneName.'"}}}';
		$output =wxHttpsRequest($url,$data);
		$jsoninfo = json_decode($output, true);		
		return 	$jsoninfo;		
	}
function getAccess_Token($WXAccount,$appid,$appsecret){
						$thisFunction="The function is getAccess_Token";$postStr=$WXAccount.",".$appid.",".$appsecret;
						
		$link=openDB();
		if($link)
		{   $nowtime=time();
			$sql = "SELECT * FROM `AccessToken` WHERE WXAccount='$WXAccount' order by getTime desc limit 1";
			$res = mysql_query($sql);
			if($res)
			 {	
				$row=mysql_fetch_array($res);
				$Token=$row['Token'];			
				$gettime=$row['getTime'];		
				$expire=$row['expire'];				
			 }
			 else
			 {  $Token="";
													logger($WXAccount.'_getAccess_Token','log/','error'," Mysql-query error:".$sql.mysql_error());
			 }					   			 
			mysql_close($link);	
			$expiretime=$gettime+$expire-600;
			if($expiretime<$nowtime) 
			{ $Token=""; 	
			}				
		  }
		  else {  $Token=""; }

		if($Token=="")
		{   $nowtime=time();
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			$jsoninfo = json_decode($output, true);	
			$Token = $jsoninfo["access_token"];
			$expire= $jsoninfo["expires_in"];
			$link=openDB();
			if($link)
			{	$nowtime=time();
				$sql = "insert into AccessToken(WXAccount,Token,getTime,expire) values('$WXAccount','$Token','$nowtime','$expire')";			
									
				$res = mysql_query($sql);
				if($res)
				 {									$a=0;//logger($WXAccount.'_getAccess_Token','log/','Debug'," insert:OK!");
				 }
				 else
				 { 									logger($WXAccount.'_getAccess_Token','log/','error',"Mysql-query error:".$sql.mysql_error());
				 }
				 mysql_close($link);	
			}
		}
		$mytime=time();
 		$replyStr=$Token; // json_encode($array);
						logger('CommonFunction','log/','Log',$thisFunction." postStr=".$postStr);
						logger('CommonFunction','log/','Log',$thisFunction." replyStr=".$replyStr);

		return  $Token;
	}
	function wxHttpsRequest($url,$data=null){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
			if (!empty($data)){
					curl_setopt($curl, CURLOPT_POST, 1);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			}
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($curl);
			curl_close($curl);
			return $output;
	}
?>