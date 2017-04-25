<?php
require_once 'db_config.php';
require 'arukas.php';
	//echo runSs();
	//var_dump(getArraySs());
	$arrayss=getArraySs();
	$str="";
	foreach($arrayss as &$data){
		$str.="【实例】：".$data["name"]."\n【状态】：".$data['status_text']."\n【ID】：".$data['id']."\n【SS】：{".$data['ss']."}\n\n【二维码】：http://ecfun.cc/sina/qrcode/qrcode.html?url=".$data['ss']."\n";
	}
	//$qrcode=getSs();
	$str_data=$str."\n输入{}内容到shadowsocks客户端\n或者手动配置\n如果翻墙失败，请查看服务器状态\n或者点击运行等待数秒\n点击链接查看配置二维码\n\n@DMT许博文 开发测试";
	echo $str_data;

?>