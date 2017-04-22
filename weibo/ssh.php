<?php
	$host=$_GET['host'];
	$ports=$_GET['port'];
	$ssh_username="root";
	$ssh_password="password";
	$command="/home/ss.sh";
	var_dump(ssh_exec($host, $ports, $ssh_username, $ssh_password, $command));
	function ssh_exec($host, $port, $ssh_username, $ssh_password, $command) {  
		$con = ssh2_connect($host, $port);  
		$auth_methods = ssh2_auth_none($con, $ssh_username);  
		if (in_array('password', $auth_methods)) {//是否允许使用密码登陆  
			$auth_methods = ssh2_auth_password($con, $ssh_username, $ssh_password);  
		}  
		$stdout_stream = ssh2_exec($con, $command);  
		$err_stream = ssh2_fetch_stream($stdout_stream, SSH2_STREAM_STDERR);  
	  
		stream_set_blocking($stdout_stream, true); //阻塞执行  
		stream_set_blocking($err_stream, true);  
	  
		$result_stdout = stream_get_contents($stdout_stream);  
		$result_error = stream_get_contents($err_stream);  
	  
		fclose($stdout_stream);  
		fclose($err_stream);  
		return array('result' => $result_stdout, 'error' => $result_error);  
		
	} 
?>