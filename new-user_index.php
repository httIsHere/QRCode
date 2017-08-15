<?php
require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
session_start();
include("page_switching.php");

$accessid = $_SESSION["accessID"];
$user=$_SESSION["username"];
$pwd = $_SESSION["userpwd"];
if($_SESSION["accessID"] == null){
	page_redirect(false,"new-signin.html","请重新登录");
}
$sql = "SELECT ManageUserName, AccessID FROM ManageUser WHERE ManageUserName = '$user'";
$result = runSelectSql($sql);
if($result){
	if($result[0]["AccessID"] != $accessid){
		echo "<script>alert('请重新登录');</script>";
       page_redirect(false,"new-signin.html","请重新登录");
	}
	}
?>
<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8">
		<title><?php echo $user;?>的主页</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
		<!--<link rel="stylesheet" href="style3.css">-->
		<!--<link rel="stylesheet" href="userIndexStyle.css">-->
		<link rel="stylesheet" type="text/css" href="DateTimePicker.css" />
		<link type="text/css" rel="stylesheet" href="fileinput.css" />
		<link rel="stylesheet" type="text/css" href="sweetalert.css">
		<link rel="stylesheet" type="text/css" href="table.css">
		<link rel="stylesheet" type="text/css" href="jquery.dataTables.min.css">
		<link rel="stylesheet" type="text/css" href="semantic.min.css">
		<link rel="stylesheet" type="text/css" href="swiper.min.css" />
		<link rel="stylesheet" href="new-index.css">

		<script type="text/javascript" src="jquery-3.2.1.min.js"></script>
		<script src="http://cdn.hcharts.cn/highcharts/highcharts.js"></script>
		<script type="text/javascript" src="sweetalert-dev.js"></script>
		<script type="text/javascript" src="fileinput.js"></script>
		<script type="text/javascript" src="zh.js"></script>
		<script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<!--图表引入-->
		<script src="//cdn.bootcss.com/Chart.js/2.5.0/Chart.bundle.js"></script>
		<script src="//cdn.bootcss.com/Chart.js/2.5.0/Chart.bundle.min.js"></script>
		<script type="text/javascript" src="DateTimePicker.js"></script>
		<script type="text/javascript" language="javascript" src="jquery.dataTables.min.js"></script>
		<script src="semantic.min.js"></script>
		<script src="swiper.min.js"></script>
	</head>

	<body>
		<div class="mainHead">
			<div class="ui secondary pointing menu">
				<a class="title" style="font-size: 20px;align-self: flex-end;color: #88c1bc;margin:0 12px 12px 12px;">QRCM</a>
				<a class="active item">数据统计</a>
				<a class="item">二维码管理</a>
				<a class="item">系统设置</a>
				<a class="item">操作说明</a>
				<div class="right menu">
					<a class="ui item">欢迎<span style="color: #88c1bc;"><?php echo $user;?></span></a>
					<a class="ui item" href="new-signin.html">退出</a>
				</div>
			</div>
		</div>
		<!--模块滚动-->
		<!-- 功能页 -->
		<div class="swiper-container">
			<div class="swiper-wrapper">
				<div class="swiper-slide">
					<!-- 数据统计 -->
					<div class="contentPage myStatisticIndex activeContentPage">
						<div class="new-row">
							<ul id="statisticTab" class="nav nav-tabs">
								<li class="active">
									<a href="#StatisticUserNum" data-toggle="tab">扫描图表</a>
								</li>
								<li>
									<a href="#StatisticUserInfo" data-toggle="tab">扫描用户列表</a>
								</li>
								<li>
									<a href="#StatisticNum" data-toggle="tab">扫描统计</a>
								</li>
								<li>
									<a href="#StatisticQRNum" data-toggle="tab">场景数量</a>
								</li>
							</ul>
							<div class="closeBtn text-right" style="padding-right: 0;">
								<img src="close.png" style="width: 15px;height: 15px;float: right;margin-bottom: 5px;" />
							</div>
						</div>
						<br />
						<div id="statisticTabContent" class="tab-content">
							<!--显示统计数据图表-->
							<div class="new-row showChart statisticShow tab-pane fade in active" id="StatisticUserNum">
								<!-- 统计图表页面的头 -->
								<!--<div class="col-md-12">-->
								<p>选择日期(默认到今天的近7天扫描记录)</p>
								<input type="text" class="timePickerStyle" data-field="date" data-format="yyyy-MM-dd" readonly id="dateInput">
								<input type="button" name="dateBtn" class="btn1" id="dateBtn" value="确定" onclick="getStatisticData()" />
								<div id="datepicker"></div>
								<!--</div>-->
								<!-- 统计图表 -->
								<div id="container" style="min-width:100%;min-height:400px;margin-bottom:50px;"></div>
							</div>
							<!--显示扫描用户信息-->
							<div class="new-row userInfo statisticShow tab-pane fade " id="StatisticUserInfo" >
								<table class="table table-bordered table-hover" width="95%" border="1" id="userInfoTab">
									<thead class="sceneNameText" id="listTitle">
										<th>头像</th>
										<th>用户名</th>
										<th>性别</th>
										<th>城市</th>
										<th>最近扫描时间</th>
									</thead>
									<tbody></tbody>
								</table>
							</div>
							<br />
							<!--显示所有数据-->
							<div class="new-row totalData statisticShow tab-pane fade" id="StatisticNum">
								总扫描用户：<span></span>
								<hr /> 扫描总次数：
								<span></span>
								<hr />
							</div>
							<!--显示所产生的二维码的数量-->
							<div class="new-row statisticShow tab-pane fade" id="StatisticQRNum">
								总二维码数：<span id="QRNum"></span>
								<hr />
							</div>
							<div class="new-row" style="justify-content: flex-end;">
								<input type="button" class="col-xs-2 btn1 btn-login pull-left" value="刷新" id="uploadBtn">
								<!--<input type="button" class="col-xs-2 btn1 btn-register pull-right closeBtnTwo" value="关闭">-->
							</div>
						</div>
					</div>
				</div>
				<div class="swiper-slide">
					<!-- 二维码生成 -->
					<div class="contentPage myQRCodeCreateIndex">
						<div class="new-row">
							<ul id="myQRCodeTab" class="nav nav-tabs">
								<li class="active">
									<a href="#QRCodeList" data-toggle="tab">已生成二维码</a>
								</li>
								<li>
									<a href="#CreateQRCode" data-toggle="tab">二维码生成</a>
								</li>
							</ul>
							<div class="closeBtn text-right" style="padding-right: 0;">
								<img src="close.png" style="width: 15px;height: 15px;float: right;margin-bottom: 5px;" />
							</div>
						</div>
						<div id="myQRCodeTabContent" class="tab-content">
							<div class="new-row QRlist tab-pane fade in active" id="QRCodeList">
								<div class="h3 text-info text-center">信息列表</div>
								<table class="table table-bordered table-hover" width="95%" border="1" id="resultList">
									<thead class="sceneNameText" id="listTitle">
										<th class="text-center">编号</th>
										<th class="text-center">标题</th>
										<th class="text-center">简介</th>
										<th class="text-center">图片</th>
										<th class="text-center">链接</th>
										<th class="text-center">二维码</th>
										<th class="text-center">操作</th>
									</thead>
									<tbody></tbody>
								</table>
							</div>
							<br />
							<div class="QRInput tab-pane fade" id="CreateQRCode">
								<ul>
									<li>
										<div>标题<span class="redText">(必填*)</span></div>
										<input type="text" id="unitName" class="form-control" />
									</li>
									<li>
										<div>简介（地址，联系人，联系电话）</div>
										<textarea id="desp" rows="4" class="form-control"></textarea>
									</li>
									<!-- <li> -->
									<form>
										<div><strong>场景图片</strong></div>
										<input type="file" id="sceneImg" name="sceneImg" style="margin-top: 10px;height: 32px;" />
										<p>支持jpg，png，gif格式图片,图片应小于2M</p>
										<br />
									</form>
									<!-- </li> -->
									<li>
										<div>图文链接</span>
										</div>
										<input type="text" id="sceneUrl" class="form-control" />
									</li>
								</ul>
								<div class="new-row" style="justify-content: flex-end;">
									<input type="button" class="btn1 btn-login" value="生成" onclick="checkInfo()" />
									<!--<input type="button" class="col-xs-2 btn1 btn-register pull-right closeBtnTwo" value="关闭">-->
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="swiper-slide">
					<!-- 系统设置 -->
					<div class="contentPage mySystemSettingIndex">
						<div class="new-row">
							<ul id="myTab" class="nav nav-tabs col-md-11 col-xs-11">
								<li class="active">
									<a href="#ChangeAccountP" data-toggle="tab">绑定/修改公众号</a>
								</li>
								<li>
									<a href="#ChangePwdP" data-toggle="tab">修改登录密码</a>
								</li>
							</ul>
							<div class="col-md-1 col-xs-1 closeBtn text-right" style="padding-right: 0;">
								<img src="close.png" style="width: 15px;height: 15px;float: right;margin-bottom: 5px;" />
							</div>
						</div>
						<br />
						<div id="myTabContent" class="tab-content">
							<!--有修改所操作的公众号和修改登录密码的修改-->
							<div class="new-row changeApp settingChange tab-pane fade in active" id="ChangeAccountP">
								<div class="form-group">
									<label for="account">微信公众号账号</label>
									<input type="text" id="Account" class="form-control">
								</div>
								<br />
								<div class="form-group">
									<label for="appID">AppID</label>
									<input type="text" id="AppId" class="form-control">
								</div>
								<br />
								<div class="form-group">
									<label for="appSecret">AppSecret</label>
									<input type="text" id="AppSecret" class="form-control">
								</div>
								<br />
								<div class="new-row">
									<input type="button" id="change" value="确认" class="btn1 btn-login pull-left">
									<input type="button" id="reset" value="恢复" class="btn1 btn-register pull-left cancelBtn">
									<!--<input type="button" class="btn1 btn-login pull-right closeBtnTwo" style="float: right;" value="关闭">-->
								</div>
							</div>
							<!--<hr />-->
							<div class="new-row changePwd settingChange tab-pane fade" id="ChangePwdP">
								<div class="form-group">
									<label for="appID">原密码</label>
									<input type="password" id="oldSecret" class="form-control">
								</div>
								<br />
								<div class="form-group">
									<label for="appSecret">新密码</label>
									<input type="password" id="newSecret" class="form-control">
								</div>
								<br />
								<div class="form-group">
									<label for="appSecret">确认新密码</label>
									<input type="password" id="checkNewSecret" class="form-control">
								</div>
								<br />
								<div class="new-row">
									<input type="button" id="changepwd" value="确认" class="btn1 btn-login pull-left">
									<input type="button" id="reset" value="恢复" class="btn1 btn-register pull-right">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="swiper-slide">
					<!--基本页介绍-->
					<div class="contentPage pageIntroduction">
						<div class="row closeBtn">
							<img src="close.png" style="width: 15px;height: 15px;float: right;margin-bottom: 5px;margin-right: 15px;" />
						</div>
						<div class="row content-padding">
							<p class="introTitle">系统说明</p>
							<p class="introContent">该系统主要用于公众号场景二维码的生成与管理，其中场景二维码即带不同场景信息的二维码，用户扫描后，可以在公众号中接收到二维码内带有的场景信息；</p>
							<p class="introContent">可以进行扫描的统计与查看，查看二维码被扫描的次数以及扫描用户的信息；</p>
							<p class="introContent">系统界面左侧是四个导航目录</p>
						</div>
						<div class="row content-padding">
							<div class="introContentTitle">统计数据</div>
							<div class="introContentBox">
								<p>用于扫描的统计与查看（一共有四个标签页）；</p>
								<div class="col-md-12 col-xs-12"><img src="op1.JPG" style="width: 50%;"></div>
								<p>点击第一个标签页可查看当前扫描的统计图表，统计图表以一周为一个周期，默认显示最近到该天一周时间内的扫描数量，包括扫描用户量和全部扫描次数，可点击时间选框选择需要查看的时间并点击确定即可查看统计数据；</p>
								<p>点击第二个标签页可查看全部扫描用户数量和全部扫描次数；</p>
								<p>点击第三个标签页可查看当前用户所生成的场景二维码的数量；</p>
								<p>点击第四个标签页可查看并搜索扫描用户信息。</p>
							</div>
						</div>
						<div class="row content-padding ">
							<div class="introContentTitle">二维码管理</div>
							<div class="introContentBox">
								<p>用于二维码生成以及二维码查看（注意：二维码生成一定要绑定公众号的AppID和 AppSecret，公众号的AppID和AppSecret在微信公众平台>>开发>>基本配置）;</p>
								<p>第一个标签页是二维码内所带信息的输入以及二维码的生成，其中场景名称是必填项目；</p>
								<p>第二个标签页是已生成的二维码的查看，可对二维码进行删除。</p>
							</div>
						</div>
						<div class="row content-padding">
							<div class="introContentTitle">系统设置</div>
							<div class="introContentBox">
								<p>主要用于用户所绑定公众号账户的操作以及登录密码的修改；</p>
								<p>第一个标签页是公众号账户的绑定与修改，在账号，AppID和AppSecret通过验证后只有修改账号才可以修改AppID和AppSecret，修改AppID和AppSecret后确认时会进行两者的验证只有验证通过才能正确修改；在确认修改之前点击取消按钮可恢复到修改之前的数据；</p>
								<p>第二个标签页是登录密码的修改，需要同时确认旧密码和新密码以及确认新密码，只有全部符合才能正确修改。</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- 模态框（Modal）二维码信息确认 -->
		<div class="modal fade" id="checkQRInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="color: #000000;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">信息确认</h4>
					</div>
					<div class="modal-body" id="nowQRInfo"></div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="button" class="btn btn-primary" id="createBtn" onclick="createQRCodes()">确定</button>
					</div>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal -->
		</div>
		<!-- /.modal -->
		<!-- 模态框（Modal）公众号修改 -->
		<div class="modal fade" id="checkChangeApp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="color: #000000;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">是否确认修改</h4>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" id="createBtn" onclick="realChangeApp()">确定</button>
					</div>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal -->
		</div>
		<!-- /.modal -->
		<!-- 模态框（Modal）二维码删除 -->
		<div class="modal fade" id="checkDeleteQRCode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="color: #000000;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">确定删除二维码？</h4>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" id="createBtn" onclick="deleteQRCode(codeIndex,codeTicket)">确定</button>
					</div>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal -->
		</div>
		<!-- /.modal -->
		<!--信息编辑-->
		<div class="modal fade" id="editSceneInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="color: #000000;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">修改信息</h4>
					</div>
					<div class="modal-body">
						<div align="left">标题<span class="redText">(必填*)</span></div>
						<input type="text" id="unitName" class="form-control" />
						<div align="left">简介（地址，联系人，联系电话）</div>
						<textarea id="desp" rows="4" class="form-control"></textarea>
						<div align="left">场景图片</span>
						</div>
						<img id="sceneImgShow" src="" style="width: 15vw; height: 10vw;" />
						<br>
						<input type="file" id="sceneImage" />
						<div align="left">图文链接</span>
						</div>
						<input type="text" id="sceneUrl" class="form-control" />
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" id="createBtn" onclick="editQRCodeInfo(codeTicket)">确定</button>
					</div>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal -->
		</div>
		<!-- 加载动画效果 -->
		<div class="spinner" style="display: none;">
			<div class="spinner-container container1">
				<div class="circle1"></div>
				<div class="circle2"></div>
				<div class="circle3"></div>
				<div class="circle4"></div>
			</div>
			<div class="spinner-container container2">
				<div class="circle1"></div>
				<div class="circle2"></div>
				<div class="circle3"></div>
				<div class="circle4"></div>
			</div>
			<div class="spinner-container container3">
				<div class="circle1"></div>
				<div class="circle2"></div>
				<div class="circle3"></div>
				<div class="circle4"></div>
			</div>
		</div>
	</body>
	<script type="text/javascript">
		//变量设置
		var userId = "<?php echo $user; ?>";
		localStorage['userId'] = userId;
		console.log(userId);
		var nowIndex = 0,
			activeItem = 1;
		var fun1, fun2, fun3, fun4; //是否已点击过功能块
		var res = 0;
		//用于重置
		var account, id, secret;
		//默认头像
		var head = "head.jpeg";
		//用于保存二维码信息
		var qrinfo;
		//删除code所需变量
		var codeIndex, codeImg, codeTicket;
		//生成二维码所需(上传到服务器的图片名)
		var sceneImg, sceneImage;
		//初始化
		init();
		//菜单切换&功能切换
		$('.menu .item').click(function() {
			$('.item').removeClass('active');
			$(this).addClass('active');
			var _index = $(this).index();
			//			console.log(_index);
			$('.swiper-slide').removeClass('swiper-slide-active');
			$('.swiper-slide').removeClass('swiper-slide-prev');
			$('.swiper-slide').removeClass('swiper-slide-next');
			//添加class
			$('.swiper-slide').eq(_index - 1).addClass('swiper-slide-active');
			if(_index > 1)
				$('.swiper-slide').eq(_index - 2).addClass('swiper-slide-prev');
			if(_index < 3)
				$('.swiper-slide').eq(_index).addClass('swiper-slide-next');
			//切换
			var _w = document.body.clientWidth;
			var _tl = 'translate3d(-' + _w * (_index - 1) + 'px, 0px, 0px)';
			$('.swiper-wrapper').css('transition', '0.5s');
			$('.swiper-wrapper').css('-webkit-transition', '0.5s');
			$('.swiper-wrapper').css('-moz-transition', '0.5s');
			$('.swiper-wrapper').css('-ms-transition', '0.5s');
			$('.swiper-wrapper').css('-o-transition', '0.5s');
			$('.swiper-wrapper').css('transform', _tl);
			$('.swiper-wrapper').css('-webkit-transform', _tl);
			$('.swiper-wrapper').css('-moz-transform', _tl);
			$('.swiper-wrapper').css('-ms-transform', _tl);
			$('.swiper-wrapper').css('-o-transform', _tl);
			//加载数据
			getInfomation(_index);
		})
		//模块切换
		var swiper = new Swiper('.swiper-container', {
			onTouchMove: function(swiper) {
				var _active = $('.swiper-slide').index($('.swiper-slide-active'));
				if(activeItem != _active) {
					$('.item').removeClass('active');
					$('.item').eq(_active).addClass('active');
					activeItem = _active;
				}
			}
		});
		//初始页面
		function init() {
			$('#datepicker').DateTimePicker();
			var date = new Date(new Date().getTime());
			var nowDate = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
			//对当前时间进行格式化
			$('#dateInput').val(nowDate);
			nowIndex = 0;
			$("#statisticTab li").attr("class", "");
			$("#statisticTab li").eq(0).addClass("active");
			$("#statisticTabContent .row").removeClass("active");
			$("#statisticTabContent .row").removeClass("in");
			$("#statisticTabContent .row").eq(0).addClass("in");
			$("#statisticTabContent .row").eq(0).addClass("active");
		}
		//----------------------输入框初始化----------------------
		function initInput() {
			$('input[type=text]').val('');
			$('input[type=password]').val('');
			$('textarea').val('');
			$("#sceneImg").trigger('fileclear');
			$("#sceneImage").trigger('fileclear');
			$("#sceneImg").trigger('filedelete');
			$("#sceneImage").trigger('filedelete');
			$("#sceneImg").trigger('filesuccessremove');
			$("#sceneImage").trigger('filesuccessremove');
			getStatisticData();
			getApp();
		}
		//----------------------文件上传输入框初始化----------------
		//初始化fileinput控件（第一次初始化）
		function initFileInput(ctrlName, uploadUrl) {
			var control = $('#' + ctrlName);
			control.fileinput({
				language: 'zh', //设置语言
				uploadUrl: uploadUrl, //上传的地址
				allowedFileExtensions: ['jpg', 'png', 'gif'], //接收的文件后缀
				showUpload: false, //是否显示上传按钮
				showCaption: true, //是否显示标题
				browseClass: "btn btn-green", //按钮样式
				dropZoneEnabled: false, //是否显示拖拽区域
				previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
			});
		}
		//初始化fileinput控件（第一次初始化）
		initFileInput("sceneImg", "");
		initFileInput("sceneImage", "");
		//------------------------信息提示--------------------
		//信息确认
		function alertSwal(title,text) {
			swal({
					title: title,
					text: text,
					showCancelButton: true,
					cancelButtonText: "取消",
					showConfirmButton: true,
					confirmButtonColor: "#88c1bc",
					confirmButtonText: "确定",
					closeOnConfirm: true,
					html: true
				},
				function() {
					//确认并生成二维码信息
					if(title == '信息确认'){
						createQRCodes();
					}
					//修改公众号信息
					else if(text == '是否确认修改'){
						realChangeApp();
					}
					//确定删除二维码
					else if(text == '确定删除二维码？'){
						deleteQRCode(codeIndex,codeTicket);
					}
				}
			);
		}
		//time tip
		function alertTimeSwal(title, text, type) {
			swal({
				title: title,
				text: text,
				type: type,
				timer: 2500,
				showConfirmButton: false
			});
		}
		//html框,修改二维码的时候
		function alertInputSwal() {
			swal({
				title: '修改信息',
				text: '<div align="left">标题<span class="redText">(必填*)</span></div><input type="text" id="unitName" class="form-control" /><div align="left">简介（地址，联系人，联系电话）</div><textarea id="desp" rows="4" class="form-control"></textarea><div align="left">场景图片</span></div><img id="sceneImgShow" src="" style="width: 15vw; height: 10vw;" /><br><input type="file" id="sceneImage" /><div align="left">图文链接</span></div><input type="text" id="sceneUrl" class="form-control" />',
				type: 'input',
				html: true,
				InputValue: false,
				showCancelButton: true,
				cancelButtonText: "取消",
				showConfirmButton: true,
				confirmButtonColor: "#88c1bc",
				confirmButtonText: "确定",
				closeOnConfirm: true,
				animation: 'slide-from-top'

			},
			function(){

			});
			initFileInput("sceneImage", "");
		}
		//---------------业务逻辑-------------------
		//加载相应数据（from session）
		function getInfomation(index) {
			//如果session内有数据则直接读取否则获取数据并存入session
			//根据index判断读取数据
			switch(index) {
				case 1:
					getStatisticData();
					getUserInfor();
					break;
				case 2:
					getInfos();
					break;
				case 3:
					//加载第四个功能块的数据
					getApp();
					changeApp();
					changePwd();
					break;
			}
		}
		//----------------------弹出警告框-----------------------
		function alertShow(s) {
			$('#alertTipTitle').html(s);
			$('#alertTip').modal('show');
			setTimeout(function() {
				$('#alertTip').modal('hide');
			}, 2000);
		}
		//----------------------数据统计-------------------------
		//显示扫描用户信息
		function showUserInfo(data) {
			var res = eval(data);
			var array = new Array();
			var s, h;
			if(res != null) {
				for(var i = 0; i < res.length; i++) {
					var info = new Array(6);
					h = res[i].headimgurl;
					if(res[i].headimgurl == "") {
						h = head;
					}
					info[0] = "<img src='" + h + "' style='width:50px; height:50px;'/>";
					// var l = "<tr><td><img src='" + h + "' style='width:50px; height:50px;'/></td>";
					if(res[i].nickname == "") {
						info[1] = "<div class='userInfoLine'>公众号用户</div>";
						// l += "<td><div class='userInfoLine'>公众号用户</div></td>";
					} else {
						info[1] = "<div class='userInfoLine'>" + res[i].nickname + "</div>";
						// l += "<td><div class='userInfoLine'>" + res[i].nickname +"</div></td>";
					}
					if(res[i].sex == '2')
						s = "女";
					else if(res[i].sex == '1') {
						s = "男";
					} else s = "不明";
					info[2] = "<div class='userInfoLine'>" + s + "</div>";
					info[3] = "<div class='userInfoLine'>" + res[i].city + "</div>";
					// l += "<td><div class='userInfoLine'>" + s +"</div></td>" + "<td><div class='userInfoLine'>" + res[i].city +"</div></td>";
					//将时间戳转换成日期
					var d = new Date(res[i]["max(CreateTime)"]);
					info[4] = "<div class='userInfoLine'>" + formatMyTime(parseInt(res[i].CreateTime)) + "</div>";
					array[i] = info;
				}
				console.log(array);
				$('#userInfoTab').dataTable().fnDestroy();
				$('#userInfoTab').DataTable({
					data: array,
					"sortable": false, //是否启用排序
					"bLengthChange": true, //改变每页显示数据数量
					"ordering": false,
					"sScrollXInner": "100%",
					"bAutoWidth": false,
					"bProcessing": true,
					"iDisplayLength": 10,
					"oLanguage": {
						"sLengthMenu": "每页显示 _MENU_ 个用户",
						// "sLengthMenu":"每页显示10个用户",
						"sZeroRecords": "抱歉， 没有找到相应的用户",
						"sInfo": "从 _START_ 到 _END_ /共 _TOTAL_ 个用户",
						"sInfoEmpty": "没有用户数据",
						"sInfoFiltered": "(从 _MAX_ 条数据中检索)",
						"sSearch": "搜索",
						"oPaginate": {
							"sFirst": "首页",
							"sPrevious": "前一页",
							"sNext": "后一页",
							"sLast": "尾页"
						},
						"sZeroRecords": "暂无扫描用户",
						"bStateSave": true //保存状态到cookie *************** 很重要
					}
				});
			}
		}
		//得到数据统计的数据
		function getStatisticData() {
			var time = $('#dateInput').val();
			// console.log(time);
			$.ajax({
				type: "post",
				url: "getStatisticData.php",
				dataType: "json",
				data: {
					appId: $("#Account").val(),
					user: userId,
					dateTime: time
				},
				success: function(data) {
					var res = eval(data);
					console.log(res);
					if(res != null) {
						getCharts(res['userNum'], res['msgNum']);
						$('.totalData').children().eq(0).text(res['totalUserNum']);
						$('.totalData').children().eq(2).text(res['totalMsgNum']);
						$("#QRNum").text(res['totalQRNum']);
					}
				}
			});
		}
		//计算前一天,使图表地x轴动态显示
		function preDay(now) {
			var l = new Array();
			if(now.indexOf("-") >= 0)
				var data = now.split("-");
			else var data = now.split("/");
			var year = data[0];
			var month = data[1];
			var day = data[2];
			var dd = new Date();
			var d = new Date(year, month - 1, day);
			// console.log(year+"-"+month+"-"+day);
			for(var i = 6; i >= 0; i--) {
				dd.setTime(d.getTime() - 24 * 60 * 60 * 1000 * i);
				var y = dd.getFullYear();
				var m = dd.getMonth() + 1;
				var d2 = dd.getDate();
				// console.log(dd+"~"+m);
				l.push(([y, m, d2].join('-')));
			}
			return l;
		}
		//生成图表
		function getCharts(user, msg) {
			var now = $('#dateInput').val();
			var label = preDay(now);
			// console.log(label);
			var chart = new Highcharts.Chart('container', { // 图表初始化函数，其中 container 为图表的容器
				chart: {
					type: 'column' //指定图表的类型，默认是折线图（line）
				},
				title: {
					text: '数据统计' //指定图表标题
				},
				xAxis: {
					categories: label //指定x轴分组
				},
				yAxis: {
					title: {
						text: '每日扫描用户量/扫描次数' //指定y轴的标题
					},
					allowDecimals: false, //是否支持小数
					min: 0,
					minRange: 1
				},
				series: [{ //指定数据列
					name: '扫描用户量', //数据列名
					color: '#ffa579',
					data: user //数据
				}, {
					name: '扫描次数',
					data: msg
				}],
				credits: {
					enabled: false // 禁用版权信息
				}
			});
		}
		//时间格式化
		function formatMyTime(str) {
			var userAgent = navigator.userAgent; //取得浏览器的userAgent字符串
			var isIE = userAgent.indexOf("compatible") > -1 && userAgent.indexOf("MSIE") > -1 && !isOpera; //判断是否IE浏览器
			var isEdge = userAgent.indexOf("Edge") > -1; //判断是否IE的Edge浏览器
			console.log("isIE:" + isIE + isEdge);
			var d = new Date(str * 1000);
			if(isIE || isEdge) {
				commonTime = d.toLocaleString();
			} else
				commonTime = d.toLocaleString('chinese', {
					hour12: false
				});
			return commonTime;
		}
		//得到扫描用户信息
		function getUserInfor() {
			var info = null;
			$(".userInforTable").html("<tr><td>头像</td><td>用户名</td><td>性别</td><td>城市</td><td>最近扫描时间</td></tr>");
			$.ajax({
				type: "post",
				url: "getUserInfor.php",
				dataType: "json",
				data: {
					account: $("#Account").val(),
					user: userId
				},
				success: function(data) {
					console.log(data);
					showUserInfo(data);
				}

			});
			//监听input
			$('#searchUser').bind('input propertychange', function() {
				//进行相关操作
				var v = $('#searchUser').val();
				var s;
				$(".userInforTable").html("<tr><td>头像</td><td>用户名</td><td>性别</td><td>城市</td></tr>");
				var l = "";
				for(var i = 0; i < info.length; i++) {
					if(info[i].nickname.indexOf(v) >= 0) {
						l = "<tr><td><img src='" + info[i].headimgurl + "' style='width:50px; height:50px;'/></td>";
						l += "<td><div class='userInfoLine'>" + info[i].nickname + "</div></td>";
						if(info[i].sex == '2')
							s = "女";
						else if(info[i].sex == '1') {
							s = "男";
						} else s = "不明";
						l += "<td><div class='userInfoLine'>" + s + "</div></td>" + "<td><div class='userInfoLine'>" + info[i].city + "</div></td><tr>";
						$(".userInforTable").append(l);
					}
				}
			});
		}
		//-----------------------二维码生成-----------------------
		//显示二维码信息表格
		function showQRCodeList(data) {
			var field = eval(data);
			qrinfo = field;
			var array = new Array();
			var k = 0;
			if(field.length > 0) {
				for(var i = 0; i < field.length; i++) {
					SceneName = field[i].SceneName;
					SceneDesp = field[i].SceneDescription;
					SceneImg = field[i].SceneImage;
					SceneUrl = field[i].SceneUrl;
					SceneID = field[i].SceneID;
					// SceneImg = "http://www.music"
					var dir = location.href.substring(0, location.href.lastIndexOf('/'));
					// console.log(dir);
					QRCodeImgFileName = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" + field[i].Ticket;
					if(SceneImg == "" || SceneImg == null) {
						SceneImg = "defaultImg.png";
					}
					if(SceneUrl == "" || SceneUrl == null) {
						SceneUrl = "http://mp.weixin.qq.com/s/asipaNiCoCs8tUj7dmyHPg";
					}
					var list = new Array(7);
					list[0] = SceneID;
					list[1] = SceneName;
					list[2] = '<div style="word-wrap:break-word;word-break:break-all;">' + SceneDesp + '</div>';
					list[3] = '<img src="' + SceneImg + '"style="height:8vw;width:8vw;margin:10px auto;" />';
					if(field[i].SceneUrl == "" || field[i].SceneUrl == null) {
						list[4] = '<div style="word-wrap:break-word;word-break:break-all;">（默认图文链接）<p><a href="' + SceneUrl + '"/>' + SceneUrl + '</p></div>';
					} else {
						list[4] = '<div class="showList" style="word-wrap:break-word;word-break:break-all;"><p><a href="' + SceneUrl + '"/>' + SceneUrl + '</p></div>';
					}
					list[5] = '<img src="' + QRCodeImgFileName + '"style="height:8vw;width:8vw;margin:10px auto;" />';
					list[6] = '<input type="button" name="delete" id="delete" class="btn btn-danger deleteBtn" value="删除" /></div><div><input type="button" name="edit" id="edit" class="btn btn-warning editBtn" value="编辑" />'
					array[i] = list;
				}
			}
			//给二维码列表填入数据
			$('#resultList').dataTable().fnDestroy();
			$('#resultList').DataTable({
				data: array,
				"sortable": false, //是否启用排序
				"bLengthChange": true, //改变每页显示数据数量
				"ordering": false,
				"sScrollXInner": "100%",
				"bAutoWidth": false,
				"bProcessing": true,
				"iDisplayLength": 3,
				"oLanguage": {
					// "sLengthMenu": "每页显示 _MENU_ 个二维码",
					"sLengthMenu": "每页显示3个二维码",
					"sZeroRecords": "抱歉， 没有找到相应的二维码",
					"sInfo": "从 _START_ 到 _END_ /共 _TOTAL_ 个二维码",
					"sInfoEmpty": "没有二维码数据",
					"sInfoFiltered": "(从 _MAX_ 条数据中检索)",
					"sSearch": "搜索",
					"oPaginate": {
						"sFirst": "首页",
						"sPrevious": "前一页",
						"sNext": "后一页",
						"sLast": "尾页"
					},
					"sZeroRecords": "暂无二维码",
					"bStateSave": true //保存状态到cookie *************** 很重要 ， 当搜索的时候页面一刷新会导致搜索的消失。使用这个属性就可避免了
				}
			});
			$('#resultList th').removeClass('sorting_asc');
			//删除的绑定事件
			$('#resultList tbody').on('click', 'input#delete', function() {
//				$('#checkDeleteQRCode').modal('show');
				alertShow('','确定删除二维码？');
				var codeIndex = $('#resultList').DataTable().row($(this).parents('tr')).index();
				codeTicket = qrinfo[codeIndex].Ticket;
				// console.log(codeIndex + "-" + qrinfo[codeIndex].Ticket);
			});
			//编辑的绑定事件
			$('#resultList tbody').on('click', 'input#edit', function() {
				var index = $('#resultList').DataTable().row($(this).parents('tr')).index();
				$('#editSceneInfo .modal-body #unitName').val(qrinfo[index].SceneName);
				$('#editSceneInfo .modal-body #desp').val(qrinfo[index].SceneDescription);
				var showimg;
				if(qrinfo[index].SceneImage == "" || qrinfo[index].SceneImage == null) {
					showimg = "defaultImg.png";
				} else showimg = qrinfo[index].SceneImage;
				if(qrinfo[index].SceneUrl == "" || qrinfo[index].SceneUrl == null) {
					var url = "http://mp.weixin.qq.com/s/asipaNiCoCs8tUj7dmyHPg";
				} else
					url = qrinfo[index].SceneUrl;
				$('#editSceneInfo .modal-body #sceneImgShow').attr("src", showimg);
				$('#editSceneInfo .modal-body #sceneUrl').val(url);
				$('#editSceneInfo').modal('show');
				alertInputSwal();
				codeTicket = qrinfo[index].Ticket;
			});

		}
		//获得图片对象并上传
		function getFileAndUpload(op, ticket) {
			if(op == 1)
				var img = $('#sceneImg').get(0).files[0];
			else var img = $('#sceneImage').get(0).files[0];
			// console.log(img);
			if(!img) {
				// console.log("img return");
				swal("失败", "图片不存在", "error");
				return;
			}
			console.log(img.type);
			if(!(img.type.indexOf('image') == 0 && img.type && /\.(?:jpg|png|gif|jpeg|JPG|PNG|GIF|JPEG)$/.test(img.name))) {
				// console.log(img.name);
				swal("失败", "图片格式不符合要求", "error");
				return;
			}
			// console.log("typeof:"+typeof(FileReader));
			if(typeof FileReader != "undefined") {
				var reader = new FileReader();
				reader.readAsDataURL(img);
				// console.log("support fileReader");
				reader.onload = function(e) { // reader onload start
					// ajax 上传图片
					$.ajax({
						url: "server.php",
						type: "POST",
						data: {
							img: e.target.result,
							user: userId
						},
						dataType: "json",
						beforeSend: function(data) {
							// console.log("before upload image");
							$(".spinner").show(3);
						},
						success: function(ret) {
							$(".spinner").hide(3);
							if(ret.img != '') {
								sceneImg = ret.img;
								sceneImage = ret.img;
								console.log(ret.img);
								//生成二维码
								if(op == 1) {
									checkInfoModal(ret.img);
								}
								//修改二维码
								else {
									var unitName = $('#editSceneInfo .modal-body #unitName').val();
									var desp = $('#editSceneInfo .modal-body #desp').val();
									var sceneUrl = $('#editSceneInfo .modal-body #sceneUrl').val();
									var account = $("#Account").val();
									var appId = $("#AppId").val();
									var appS = $("#AppSecret").val();
									$.ajax({
										type: "post",
										url: "QR_Mid2.php",
										dataType: "text",
										data: {
											ticket: ticket,
											type: 4,
											unitName: unitName,
											desp: desp,
											sceneImg: sceneImage,
											sceneUrl: sceneUrl,
											account: account,
											appId: appId,
											appS: appS
										},
										beforeSend: function(data) {
											//修改中
											$(".spinner").show(3);
										},
										success: function(data) {
											$(".spinner").hide(3);
											if(data == "二维码修改成功！") {
												swal("成功", data, "success");
												getInfos();
											} else {
												swal("失败", data, "error");
											}
										},
										error: function() {
											$(".spinner").hide(3);
											swal("失败", "二维码修改失败！", "error");
										}
									});

								}
							} else {
								alertTimeSwal('', "所选图片出现问题", 'warning');
							}
						}
					}); // reader onload end
				}
			} else {
				console.log("你的浏览器不兼容fileReader");
				//采用其他方式

			}
		}

		//信息确认框
		function checkInfoModal(img) {
			var unitName = $('#unitName').val();
			var desp = $('#desp').val();
			var sceneUrl = $('#sceneUrl').val();
			if(img && img != "")
				var l = "标题：<div>" + unitName + "</div>简介：<div style='word-wrap:break-word;'>" + desp + "</div><div>场景图片：<img src=" + img + " style='height:15vw;width:50%;margin:10px auto;' /></div>图文链接：<div style='word-wrap:break-word;'>" + sceneUrl + "</div>";
			else
				var l = "标题：<div>" + unitName + "</div>简介：<div style='word-wrap:break-word;'>" + desp + "</div>图文链接：<div style='word-wrap:break-word;'>" + sceneUrl + "</div>";
			$("#nowQRInfo").html(l);
			$("#checkQRInfo").modal('show');
		}

		function listshow(xmlDoc) {
			var l = '<tr class="text-center" id="listTitle"><th class="text-center">场景编号</th><th class="text-center">标题</th><th class="text-center">简介</th><th class="text-center">场景图片</th><th class="text-center">图文链接</th><th class="text-center">二维码</th><th class="text-center">操作</th></tr>';
			$("#resultList").html(l);
			var field = eval(xmlDoc);
			if(field.length > 0) {
				for(var i = 0; i < field.length; i++) {
					SceneName = field[i].SceneName;
					SceneDesp = field[i].SceneDescription;
					SceneImg = field[i].SceneImage;
					SceneUrl = field[i].SceneUrl;
					SceneID = field[i].SceneID;
					// SceneImg = "http://www.music"
					var dir = location.href.substring(0, location.href.lastIndexOf('/'));
					// console.log(dir);
					QRCodeImgFileName = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" + field[i].Ticket;
					if(SceneImg == "" || SceneImg == null) {
						SceneImg = "http://mmbiz.qpic.cn/mmbiz_jpg/eJAQYeyIQyCVRUWd8Xj46H7faibmYtWfU4kanRvFdqzzbOdzrGyo1TlaoxHkGgBEgiaQ6nIcjVe0mZpEmDsDOdbg/0?wx_fmt=jpeg";
					} else {
						// SceneImg = dir+"/"+SceneImg;
						console.log(SceneImg);
					}
					if(SceneUrl == "" || SceneUrl == null) {
						SceneUrl = "http://mp.weixin.qq.com/s/asipaNiCoCs8tUj7dmyHPg";
					}
					var newTr = resultList.insertRow();
					var newTd0 = newTr.insertCell(0);
					var newTd1 = newTr.insertCell(1);
					var newTd2 = newTr.insertCell(2);
					var newTd3 = newTr.insertCell(3);
					var newTd4 = newTr.insertCell(4);
					var newTd5 = newTr.insertCell(5);
					var newTd6 = newTr.insertCell(6);
					newTd0.innerHTML = '<div class="showList" style="word-wrap:break-word;word-break:break-all;">' + SceneID + '</div>';
					newTd1.innerHTML = '<div class="showList" style="word-wrap:break-word;word-break:break-all;">' + SceneName + '</div>';
					newTd2.innerHTML = '<div class="showList" style="word-wrap:break-word;word-break:break-all;">' + SceneDesp + '</div>';
					newTd3.innerHTML = '<img src="' + SceneImg + '"style="height:8vw;width:8vw;margin:10px auto;" />';
					if(field[i].SceneUrl == "" || field[i].SceneUrl == null) {
						newTd4.innerHTML = '<div class="showList" style="word-wrap:break-word;word-break:break-all;">（默认图文链接）<p><a href="' + SceneUrl + '"/>' + SceneUrl + '</p></div>';
					} else
						newTd4.innerHTML = '<div class="showList" style="word-wrap:break-word;word-break:break-all;"><p><a href="' + SceneUrl + '"/>' + SceneUrl + '</p></div>';
					newTd5.innerHTML = '<img src="' + QRCodeImgFileName + '"style="height:8vw;width:8vw;margin:10px auto;" />';
					newTd6.innerHTML = '<div class="showList" style="word-wrap:break-word;word-break:break-all;"><div><input type="button" name="delete" id="delete" class="btn btn-danger deleteBtn" value="删除" /></div><div><input type="button" name="edit" id="edit" class="btn btn-warning editBtn" value="编辑" /></div></div>';
				}
				//对于删除按钮有一个绑定动作
				$(".deleteBtn").bind("click", function() {
					$('#checkDeleteQRCode').modal('show');
					codeIndex = $(".deleteBtn").index(this);
					codeTicket = field[codeIndex].Ticket;
				});
				//对于编辑按钮的绑定
				$(".editBtn").bind("click", function() {
					var index = $(".editBtn").index(this);
					//跳出一个输入模态框吧 editSceneInfo
					$('#editSceneInfo .modal-body #unitName').val(field[index].SceneName);
					$('#editSceneInfo .modal-body #desp').val(field[index].SceneDescription);
					var showimg;
					if(field[index].SceneImage == "" || field[index].SceneImage == null) {
						showimg = "http://mmbiz.qpic.cn/mmbiz_jpg/eJAQYeyIQyCVRUWd8Xj46H7faibmYtWfU4kanRvFdqzzbOdzrGyo1TlaoxHkGgBEgiaQ6nIcjVe0mZpEmDsDOdbg/0?wx_fmt=jpeg";
					} else showimg = field[index].SceneImage;
					if(field[index].SceneUrl == "" || field[index].SceneUrl == null) {
						var url = "http://mp.weixin.qq.com/s/asipaNiCoCs8tUj7dmyHPg";
					} else
						url = field[index].SceneUrl;
					$('#editSceneInfo .modal-body #sceneImgShow').attr("src", field[index].SceneImage);
					$('#editSceneInfo .modal-body #sceneUrl').val(url);
					$('#editSceneInfo').modal('show');
					codeTicket = field[index].Ticket;
				});
			}
		}

		function getInfos() {
			$.ajax({
				type: "post",
				url: "QR_Mid2.php",
				dataType: "json",
				data: {
					type: 0,
					UserWebID: userId
				},
				success: function(data) {
					// listshow(data);
					showQRCodeList(data);
				},
				error: function() {
					alertTimeSwal('', "获取数据失败！", 'error');
				}
			});
		}
		// 保存信息
		function sendInfos() {
			var unitName = $('#unitName').val();
			var desp = $('#desp').val();
			var sceneImg = $('#sceneImg').val();
			var sceneUrl = $('#sceneUrl').val();
			// var tel = $('#tel').val();
			if(unitName == "" || sceneImg == "" || sceneUrl == "") {
				//				alertShow("输入信息不完全，请检查必填部分");
			} else {
				$.ajax({
					type: "post",
					url: "QR_Mid2.php",
					data: {
						type: 1,
						UserWebID: userId,
						unitName: unitName,
						desp: desp,
						sceneImg: sceneImg,
						sceneUrl: sceneUrl
					},
					success: function(data) {}
				});
			}
		}
		// 生成二维码
		function createQRCodes() {
			$("#checkQRInfo").modal('hide');
			var account = $("#Account").val();
			var appId = $("#AppId").val();
			var appS = $("#AppSecret").val();
			var unitName = $('#unitName').val();
			var desp = $('#desp').val();
			var sceneUrl = $('#sceneUrl').val();
			$.ajax({
				type: "post",
				url: "QR_Mid2.php",
				dataType: "text",
				data: {
					type: 2,
					UserWebID: userId,
					unitName: unitName,
					desp: desp,
					sceneImg: sceneImg,
					sceneImage: sceneImage,
					sceneUrl: sceneUrl,
					account: account,
					appId: appId,
					appS: appS
				},
				beforeSend: function(data) {
					//修改中
					$(".spinner").show(3);
				},
				success: function(data) {
					$(".spinner").hide(3);
					if(data == "二维码生成成功！") {
						swal("成功", data, "success");
						initInput();
						getInfos();
					} else {
						$(".spinner").hide(3);
						console.log(data);
						swal("失败", data, "error");
					}
				},
				error: function(data) {
					$(".spinner").hide(3);
					swal("失败", "二维码生成失败！", "error");
				}
			});
		}

		function deleteQRCode(index, ticket) {
			$('#checkDeleteQRCode').modal('hide');
			$.ajax({
				type: "post",
				url: "QR_Mid2.php",
				dataType: "text",
				data: {
					type: 3,
					ticket: ticket
				},
				success: function(data) {
					//删除表格行
					var i = index + 1;
					$("#resultList tr").eq(i).remove();
					swal("成功", data, "success");
					getInfos();
				},
				error: function(data) {
					$(".spinner").hide(3);
					swal("失败", "二维码删除失败！", "error");
				}
			});
		}
		//确认输入信息
		function checkInfo() {
			var unitName = $('#unitName').val();
			var desp = $('#desp').val();
			var sceneImg = $('#sceneImg').val();
			var sceneUrl = $('#sceneUrl').val();
			var checkLink = 1;
			if(unitName == "") {
				alertTimeSwal("", "输入信息不完全，请检查必填部分", "warning", false, '');
			} else {
				//有图片则进行图片上传
				if(sceneImg != "") {
					console.log("正在上传图片...");
					getFileAndUpload(1, "");
				} else {
					//直接进行生成二维码
					checkInfoModal("");
				}
			}
		}
		//修改二维码信息
		function editQRCodeInfo(ticket) {
			$('#editSceneInfo').modal('hide');
			var unitName = $('#editSceneInfo .modal-body #unitName').val();
			var desp = $('#editSceneInfo .modal-body #desp').val();
			var newImg = $('#editSceneInfo .modal-body #sceneImage').val();
			var sceneUrl = $('#editSceneInfo .modal-body #sceneUrl').val();
			var checkLink = 1;
			if(unitName == "") {
				alertTimeSwal('', "输入信息不完全，请检查必填部分", 'warning');
				return;
			}
			if(checkLink == 1) {
				//没有修改图片
				if(newImg == "") {
					$.ajax({
						type: "post",
						url: "QR_Mid2.php",
						dataType: "text",
						data: {
							ticket: ticket,
							type: 4,
							unitName: unitName,
							desp: desp,
							sceneImg: newImg,
							sceneUrl: sceneUrl
						},
						beforeSend: function(data) {
							//修改中
							$(".spinner").show(3);
						},
						success: function(data) {
							$(".spinner").hide(3);
							if(data == "二维码修改成功！") {
								swal("成功", data, "success");
								getInfos();
							} else {
								swal("失败", data, "error");
							}
						},
						error: function() {
							$(".spinner").hide(3);
							swal("失败", "二维码修改失败！", "error");
						}
					});
				}
				//有修改图片
				else {
					//上传图片得到图片名称
					getFileAndUpload(2, ticket);
				}
			}
		}
		//-----------------------系统设置------------------------
		//得到该用户的appId和app Secret，如果用户未绑定公众号则在使用其他功能时会有警告
		function getApp() {
			var r = 0;
			$.ajax({
				type: "post",
				url: "changeApp.php",
				dataType: "json",
				data: {
					user: userId,
					op: 1
				},
				success: function(data) {
					var result = eval(data);
					if(result != null) {
						account = result[0].WeChatAccount;
						id = result[0].AppId;
						secret = result[0].AppSecret;
						$("#change").eq(0).val("确认");
						$("#Account").val(result[0].WeChatAccount);
						$("#AppId").val(result[0].AppId);
						$("#AppSecret").val(result[0].AppSecret);
						$("#AppId").attr("disabled", true);
						$("#AppSecret").attr("disabled", true);
						r = 1;
						if($("#Account").val() == "") {
							alertTimeSwal('', "您尚未绑定公众号，若要实现这些功能需前往系统设置绑定公众号", 'warning');
						} else {}
					}
				}
			});
			res = r;
		}
		//---------------------------系统设置----------------------------
		//不能单独修改app，除非账号修改时
		function whileAccountChange() {
			//监听账号输入框
			$("#Account").bind('input propertychange', function() {
				$("#AppId").attr("disabled", false);
				$("#AppSecret").attr("disabled", false);
			});
		}
		//	修改公众号信息
		function changeApp() {
			$("#change").click(function() {
//				$('#checkChangeApp').modal('show');
				alertSwal('','是否确认修改');
			});
			$("#reset").eq(0).click(function() {
				getApp();
				$('#Account').val(account);
				$("#AppId").val(id);
				$("#AppSecret").val(secret);
			});
		}

		function realChangeApp() {
			var account = $("#Account").val();
			var appid = $("#AppId").val();
			var apps = $("#AppSecret").val();
			if(account != "" && appid != "" && apps != "") {
				//对app进行验证，验证其有效性0
				$.ajax({
					type: "post",
					url: "changeApp.php",
					data: {
						op: 3,
						account: account,
						appid: appid,
						apps: apps
					},
					dataType: "text",
					success: function(data) {
						// alert(data);
						if(data != 0) {
							//app有效
							updateApp(account, appid, apps);
						} else {
							$('#checkChangeApp').modal('hide');
							swal("修改失败", "appID或appSecret错误，请重新确认后修改!", "error");
							getApp();
						}
					}
				});
			} else {
				updateApp(account, appid, apps);
			}
		}

		function updateApp(account, appid, apps) {
			$('#checkChangeApp').modal('hide');
			$.ajax({
				type: "post",
				url: "changeApp.php",
				dataType: "text",
				data: {
					op: 2,
					user: userId,
					account: account,
					appid: appid,
					apps: apps
				},
				success: function(data) {
					swal("修改成功", "", "success");
					$("#AppId").attr("disabled", true);
					$("#AppSecret").attr("disabled", true);
					getStatisticData();
					getUserInfor();
					getInfos();
				}
			});
		}
		//	修改密码
		function changePwd() {
			$("#changepwd").eq(0).click(function() {
				//先确认新密码是否相同
				var newp = $("#newSecret").val();
				var cnewp = $("#checkNewSecret").val();
				var op = $("#oldSecret").val();
				if(newp != cnewp || newp == "" || cnewp == "") {
					alertTimeSwal('', "两次输入新密码不相同,请重新输入", 'warning');
				} else {
					//去获得当前的密码
					var p = 1;
					$.ajax({
						type: "post",
						url: "changePwd.php",
						dataType: "text",
						data: {
							user: userId,
							op: 1
						},
						success: function(data) {
							p = data;
							if(op == p) {
								//继续修改密码
								$.ajax({
									type: "post",
									url: "changePwd.php",
									dataType: "text",
									data: {
										op: 2,
										user: userId,
										pwd: newp
									},
									success: function(data) {
										if(data == "1") {
											swal("密码修改成功", "", "success");
											initInput();
										} else {
											swal("密码修改失败", "", "error");
										}
									}
								});
							} else {
								//重新输入原密码
								swal("失败", "原密码不正确，请重新输入密码", "error");
							}
						}
					});
				}
			});
			$("#reset").eq(1).click(function() {
				$('#oldSecret').val("");
				$('#newSecret').val("");
				$('#checkNewSecret').val("");
			});
		}
		//刷新清除session
		function removeSession() {
			$.ajax({
				type: "post",
				url: "unsetSession.php",
				success: function(data) {}
			});
		}

		function signOut() {
			//清除session并返回登录页面
			$.ajax({
				type: "post",
				url: "unsetSession.php",
				success: function() {
					window.location = 'signin.html';
				}
			});
		}
		//关闭功能页
		function closeContentPage() {
			var i = $('.closeBtn').index(this);
			init();
			$(".contentPage").eq(i).css('transform', 'scale(0.0)');
			$(".settingChange").eq(0).show();
			getInfomation(i);
		}
	</script>

</html>
