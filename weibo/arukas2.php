<?php
	require 'ArukasApi.php';
	$login=login($email,$password);
	$result=json_decode($login,true);
	$cookie=$result['message'];
	
	$app=getApps($cookie);
	$result1=json_decode($app,true);
	//适用多容器
	foreach ($result1["data"] as &$data) {
		$relationships=$data["relationships"];
		$attributesss=$data["attributes"];
		$name=$attributesss["name"];
		$container=$relationships["container"];
		$date=$container["data"];
		$id=$date["id"];
		
		$a1=getContainer($id,$cookie);
		$result2=json_decode($a1,true);
		$data1=$result2['data'];
		$attributes=$data1['attributes'];
		$status=$attributes['is_running'];
		$status_text=$attributes['status_text'];
		$port_mappings=$attributes['port_mappings'];

		$SsResult[]=getSs($port_mappings);
		$array=array('name' => $name, 'id' => $id, 'status_text' =>$status_text);
		$StatusResult[]=$array;
	}
	//返回全部容器ss
	function getArraySs(){
		global $SsResult;
		return $SsResult;
	}
	//返回全部容器状态
	function getArrayStatus(){
		global $StatusResult;
		return $StatusResult;
	}
	//获取服务器信息
	function getInfo(){
		global $result2;
		return $result2;
	}	
	//获取服务器状态
	function getStatus() {
		global $status_text;
		return $status_text;
	}
	//启动容器
	function startApp($id) {
		global $status,$cookie;
		if(!$status){
			return startContainer($id,$cookie);
		}else{
			return "服务器已经启动，请不要重复启动服务器";
		}
	}
	//该方法废弃，
	function runSs() {
		global $port_mappings;
		$port1=$port_mappings[0][0];
		$port2=$port_mappings[0][1];
		$port3=$port_mappings[0][2];
		if($port1['container_port']==22){
			$isMatched = preg_match_all('/[1-9]\d*/', $port1['host'], $matches);
			$host=$matches[0][0].".".$matches[0][1].".".$matches[0][2].".".$matches[0][3];
			$ports=$port1['service_port'];
			$resultss=json_decode(getSSH($host,$ports),true);
			return $resultss['message'];
			//var_dump(ssh_exec($host, $ports, $ssh_username, $ssh_password, $command));
		}else if($port2['container_port']==22){
			$isMatched = preg_match_all('/[1-9]\d*/', $port2['host'], $matches);
			$host=$matches[0][0].".".$matches[0][1].".".$matches[0][2].".".$matches[0][3];
			$ports=$port2['service_port'];	
			$resultss=json_decode(getSSH($host,$ports),true);
			return $resultss['message'];
			//var_dump(ssh_exec($host, $ports, $ssh_username, $ssh_password, $command));
		}else if($port3['container_port']==22){
			$isMatched = preg_match_all('/[1-9]\d*/', $port3['host'], $matches);
			$host=$matches[0][0].".".$matches[0][1].".".$matches[0][2].".".$matches[0][3];
			$ports=$por3['service_port'];
			$resultss=json_decode(getSSH($host,$ports),true);
			return $resultss['message'];
			//var_dump(ssh_exec($host, $ports, $ssh_username, $ssh_password, $command));
		}else{
			return "服务器启动失败";
		}
	}
	function getSs($port_mappings) {
		//global $port_mappings;
		$port1=$port_mappings[0][0];
		$port2=$port_mappings[0][1];
		$port3=$port_mappings[0][2];
		if($port1['container_port']==8989){
			$isMatched = preg_match_all('/[1-9]\d*/', $port1['host'], $matches);
			return "ss://aes-256-cfb:xbw12138@".$matches[0][0].".".$matches[0][1].".".$matches[0][2].".".$matches[0][3].":".$port1['service_port'];
		}else if($port2['container_port']==8989){
			$isMatched = preg_match_all('/[1-9]\d*/', $port2['host'], $matches);
			return "ss://aes-256-cfb:xbw12138@".$matches[0][0].".".$matches[0][1].".".$matches[0][2].".".$matches[0][3].":".$port2['service_port'];
		}else if($port3['container_port']==8989){
			$isMatched = preg_match_all('/[1-9]\d*/', $port3['host'], $matches);
			return "ss://aes-256-cfb:xbw12138@".$matches[0][0].".".$matches[0][1].".".$matches[0][2].".".$matches[0][3].":".$port3['service_port'];
		}else{
			return "服务器启动失败";
		}

	}
?>