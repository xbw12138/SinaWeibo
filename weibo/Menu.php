<?php
	require_once 'db_config.php';
	setMenu();

	function setMenu(){
		global $access_token;
		$login_url='https://m.api.weibo.com/2/messages/menu/create.json?access_token='.$access_token;
		$data_string='{
			"button": [
				{
					"name": "VPN服务器",
					"sub_button": [
						{
							"type": "click",
							"name": "状态",
							"key": "status"
						},
						{
							"type": "click",
							"name": "启动",
							"key": "start"
						},
						{
							"type": "click",
							"name": "获取ss",
							"key": "result"
						}
					]
				},
				{
					"type": "click",
					"name": "说明",
					"key": "explain"
				},
				{
					"name": "我的",
					"sub_button": [
						{
							"type": "view",
							"name": "博客",
							"url": "http://blog.csdn.net/xbw12138"
						},
						{
							"type": "click",
							"name": "绑定账号",
							"key": "binding"
						},
						{
							"type": "click",
							"name": "激活码",
							"key": "active"
						}
					]
				}
			]
		}';
			$login=curl_init($login_url);
			curl_setopt($login,CURLOPT_HEADER,0);//1 将头文件的信息作为数据流输出 0则不输出头文件
			curl_setopt($login,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($login,CURLOPT_POST,1);
			curl_setopt($login,CURLOPT_POSTFIELDS,$data_string);     
			$content=curl_exec($login);
			//return $content;
			echo $content;
	}
?>