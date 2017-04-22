<?php
	require 'ArukasApi.php';
	$login=login($email,$password);
	$result=json_decode($login,true);
	$cookie=$result['message'];
	
	$app=getApps($cookie);
	$result1=json_decode($app,true);
	$data=$result1["data"][0];
	$relationships=$data["relationships"];
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
	
	function getStatus() {
		global $status_text;
		return $status_text;
	}
	function startApp() {
		global $status,$id,$cookie;
		if(!$status){
			return startContainer($id,$cookie);
		}else{
			return "服务器已经启动，请不要重复启动服务器";
		}
	}
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
		}
	}
	function getSs() {
		global $port_mappings;
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
		}

	}
?>