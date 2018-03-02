# QRCode <br>

功能优化：<br><br>

- 2018-03-02 <br>
1、用户（还需添加管理员，可手动删除警告不安全二维码，维护二维码和扫描用户的安全性）；??<br>

页面UI优化：<br><br>

- 2018-01-26 <br>
12、图表下载（图片，数据等）；<br>

- 2018-01-08 <br>
11、需要将时间测试生成的结果下载到本地（导入曲线拟合代码中，得出曲线参数）；<br>


- 2018-01-06 <br>
10、禁止拖动slide，防止拖动可拖动操作对按钮点击效果的影响；<br>
```
//old
var swiper = new Swiper('.swiper-container', {
	keyboardControl: true,
	onTouchMove: function(swiper) {
		var _active = $('.swiper-slide').index($('.swiper-slide-active'));
		if(activeItem != _active) {
			$('.item').removeClass('active');
			$('.item').eq(_active).addClass('active');
			activeItem = _active;
		}
	}
});
//new(>=4.0.0)(实际上keyboard控制效果也会消失)
var swiper = new Swiper('.swiper-container', {
	keyboard: true,
	allowTouchMove: false
});
```

- 2018-01-03 <br>
8、系统设置ui修改；<br>
9、添加忠实粉丝部分；<br>

- 2018-01-02 <br>
6、生成时间测试的逻辑修改（需要上传QR_Mid2.php）；<br>
7、修改系统说明（在html文件内，文案以及样式）；<br>
```
$qqDur = array();
for($i = 1000000000; $i < (1000000000+$num); $i++){
	$start3 = microtime(true);
	getQRCodeTest('http://mobile.qq.com/qrcode?url=', $i);
	$end3 = microtime(true);
	$qqDur[] = round(($end3 - $start3)*1000);
}
```

- 2017-12-30 <br>
5、需要发送邮件的两个地方：注册验证邮箱；忘记密码重置密码；<br>

- 2017-12-29 <br>
3、各项数据模块-排行榜的设计（全部，近一月，近一周，今日）；<br>
4、排行list数据的进度条；<br>

- 2017-12-27 <br>
1、登录、注册仿知乎（已完成）；<br>
2、主页数据统计模块-各项数据-三个数据已卡片的形式；<br>



