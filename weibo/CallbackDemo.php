<?php
	//接口要求返回的字符串需要是utf8编码。
	header( 'Content-type: text/html; charset=utf-8' );
	//加载SDK
	require_once 'CallbackSDK.php';
	require_once 'db_config.php';
	require 'arukas.php';
	$conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_password) or die("error connecting") ;
	mysql_query("set names 'utf8'"); 
	mysql_select_db($mysql_database);
	
	//设置app_key对应的app_secret
	define("APP_SECRET", $secret);
	
	//初始化SDK
	$call_back_SDK = new CallbackSDK();
	$call_back_SDK->setAppSecret(APP_SECRET);
	
	//签名验证
	$signature = $_GET["signature"];
	$timestamp = $_GET["timestamp"];
	$nonce = $_GET["nonce"];
	if (!$call_back_SDK->checkSignature($signature, $timestamp, $nonce)) {
		die("check signature error");
	}
	//首次验证url时会有'echostr'参数，后续推送消息时不再有'echostr'字段
	//若存在'echostr'说明是首次验证,则返回'echostr'的内容。
	if (isset($_GET["echostr"])) {
		die($_GET["echostr"]);
	}
	//处理开放平台推送来的消息,首先获取推送来的数据.
	$post_msg_str = $call_back_SDK->getPostMsgStr();
	/**
	 * 设置接口默认返回值为空字符串。
	 * 请注意数据编码类型。接口要求返回的字符串需要是utf8编码
	 * 需要说明的是开放平台判断推送成功的标志是接口返回的http状态码,
	 * 只要应用的接口返回的状态为200就会认为消息推送成功，如果http状态码不为200则会重试，共重试3次。
	 */
	
	if (!empty($post_msg_str)) {
		$str_data="";
		$null="null";
		$functions=getFunction();
		$funcstart=substr($functions, 0, 1 );
		$funcs="0";
		if($funcstart=="&"){
			$funcs="1";
		}
		$name=getNickname();
		if(userExist()){
			if($functions=="active"){
				if(getActive()){
					$str_data="欢迎回来-亲爱的\n@".$name."\n"
					."您已经领取过激活码"."\n"
					."不要太贪心哦"."\n"
					."@DMT许博文 开发测试";
				}else{
					$code=setCode();
					if($code!="null"){
						$str_data="您的激活码:"."\n"
						.$code."\n"
						."感谢您的支持"."\n"
						."@DMT许博文 开发测试";
					}else{
						$str_data="获取激活码失败";
					}
				}
			}else if($functions=="status"){
				$array=getArrayStatus();
				$str="";
				foreach($array as &$data){
					$str.="【实例】:".$data["name"]."\n【状态】:".$data['status_text']."\n【ID】:".$data['id']."\n\n";
				}
				$str_data=$str."\n如果实例没有运行\n回复 &start&ID 开启\n@DMT许博文 开发测试";
			}else if($functions=="start"){
				//$str_data=startApp()."\n"."@DMT许博文 开发测试";
			}else if($functions=="run"){
				//$str_data=runSs()."\n"."@DMT许博文 开发测试";
			}else if($functions=="result"){
				$arrayss=getArraySs();
				$str="";
				foreach($arrayss as &$data){
					$str.="【实例】:".$data["name"]."\n【二维码】：http://ecfun.cc/sina/qrcode/qrcode.html?url=".$data['ss']."\n------------------\n";
				}
				$str_data=$str."点击查看详细使用说明\nhttp://ecfun.cc/sina/weibo/explain.html\n如果翻墙失败，请查看服务器状态\n或者回复 &start&ID 等待数秒\n@DMT许博文 开发测试";
		    }else if($functions=="binding"){
				$str_data="欢迎回来-亲爱的\n@".$name."\n"
				."您的账号已经绑定"."\n"
				."@DMT许博文 开发测试";
			}else if($funcs=="1"){
				$a=explode('&',$functions);
				$str_data=startApp($a[1])."\n"."@DMT许博文 开发测试";
			}
		}else{
			$str_data="欢迎回来-亲爱的\n@".$name."\n请先绑定账号再使用此功能";
			if($functions=="binding"){
				if(userInsert($name)){
					$str_data="用户绑定成功";
				}else{
					$str_data="用户绑定失败";
				}
			}
		}
		if($functions=="explain"){
			$str_data="---使用说明---\n亲爱的\n@".$name."\n绑定账号后\n通过菜单指令以及关键字回复\n完成以下功能\n"
			."-------------\n1.账号绑定【绑定账号】\n2.获取激活码【激活码】\n3.vpn状态查看【状态】\n4.vpn ss获取【获取】\n-------------\n点击连接查看详细使用说明\n-------------\n\nhttp://ecfun.cc/sina/weibo/explain.html\n\n@DMT许博文 开发测试";
		}
		if($functions!="!null"){
			//回复
			sendMessage($str_data,"text");
			//操作记录
			recordInsert($name,$functions);
		}
	}
	function getFunction(){
		global $post_msg_str;
		if($post_msg_str['type']=="event"){
			$menu=$post_msg_str['data'];
			if($menu['key']=="binding"){
				return "binding";
			}else if($menu['key']=="active"){
				return "active";
			}else if($menu['key']=="status"){
				return "status";
			}else if($menu['key']=="start"){
				return "start";
			}else if($menu['key']=="run"){
				return "run";
			}else if($menu['key']=="result"){
				return "result";
			}else if($menu['key']=="explain"){
				return "explain";
			}
		}else if($post_msg_str['type']=="text"){
			
			$message=substr($post_msg_str['text'], 0, 6 );
			if($message=="&start"){
				$a=explode('&',$post_msg_str['text']);
				return "&".$a[2];
			}
			if($post_msg_str['text']=="激活码"){
				return "active";//激活码
			}else if($post_msg_str['text']=="状态"){
				return "status";
			}else if($post_msg_str['text']=="运行"){
				return "run";
			}else if($post_msg_str['text']=="启动"){
				return "start";
			}else if($post_msg_str['text']=="获取"){
				return "result";
			}else if($post_msg_str['text']=="绑定账号"){
				return "binding";
			}else if($post_msg_str['text']=="说明"){
				return "explain";
			}
			else{
				return "!null";
			}
		}
	}
	function setCode(){
		$code = getInviteCode();
		global $post_msg_str;
		$receiver_id = $post_msg_str['sender_id'];  
		$sql="update user set code='$code' where uid=$receiver_id";
		$result = mysql_query($sql);
		if($result){
			return $code;
		}else{
			return "null";
		}
		
	}
	function getActive(){
		global $post_msg_str;
		$receiver_id = $post_msg_str['sender_id'];  
		$sql="select code from user where uid=$receiver_id";
		$result = mysql_query($sql);
		if($result){
			$row=mysql_fetch_assoc($result);
			if($row['code']=="1"){
				return false;
			}else{
				return true;
			}
		}else{
			return false;
		}
		
	}
	function recordInsert($name,$type){
		//用户记录
		global $post_msg_str;
		$receiver_id = $post_msg_str['sender_id']; 
		$sql="INSERT INTO record(uid,name,type)VALUES('$receiver_id','$name','$type')";
		mysql_query($sql);
	}
	function userInsert($name){
		//绑定用户
		global $post_msg_str;
		$receiver_id = $post_msg_str['sender_id'];  
		$code="1";
		$sql="INSERT INTO user(uid,nickname,code)VALUES('$receiver_id','$name','$code')";
		$result=mysql_query($sql);
		if($result){
			return true;
		}else{
			return false;
		}
	}
	function userExist(){
		//查看用户是否存在
		global $post_msg_str;
		$receiver_id = $post_msg_str['sender_id'];  
		$sql="select 1 from user where uid =$receiver_id limit 1";
		$result = mysql_query($sql);
		$data = mysql_num_rows($result);
		if($data){
			return true;
		}else{
			return false;
		}
	}
	function sendMessage($str_data,$data_type){
		//发送消息给用户
		global $call_back_SDK;
		global $post_msg_str;
		$data = $call_back_SDK->textData($str_data);
		$sender_id = $post_msg_str['receiver_id'];
		//receiver_id为接收回复消息的uid，即蓝v的粉丝
		$receiver_id = $post_msg_str['sender_id'];  
		$str_return = $call_back_SDK->buildReplyMsg($receiver_id, $sender_id, $data, $data_type);
		echo json_encode($str_return);
	}
	function getNickname(){
		//获取用户名
		global $post_msg_str;
		$receiver_id = $post_msg_str['sender_id'];  
		global $access_token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.weibo.com/2/eps/user/info.json?access_token=".$access_token."&uid=".$receiver_id);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$output = curl_exec($ch);
		curl_close($ch);
		$nickname=json_decode($output,true);
		return $nickname['nickname'];
	}
	function getInviteCode(){
		//获取邀请码
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://ecfun.cc/sina/weibo/invitecode.php");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$output = curl_exec($ch);
		curl_close($ch);
		$code=json_decode($output,true);
		return $code['code'];
	}
	
	
?>