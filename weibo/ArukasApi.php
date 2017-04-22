<?php 
/**
* Arukas API 
*
* By Xbw<xbw@ecfun.cc>
* Version 1.0
* Copyright 2017 ,Xbw
*/

header('Content-type: application/json; charset=UTF-8');
//登陆并获取cookie
function login($email,$password){
	$login_url='https://app.arukas.io/api/login';
	$postdata = array('email' =>$email ,'password'=>$password);
	//$cookie_file = dirname(__FILE__).'/cookie.txt';
	$login=curl_init($login_url);
	curl_setopt($login,CURLOPT_HEADER,1);//1 将头文件的信息作为数据流输出 0则不输出头文件
	curl_setopt($login,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($login,CURLOPT_POST,1);
	curl_setopt($login,CURLOPT_POSTFIELDS,$postdata);
	curl_setopt($login,CURLOPT_SSL_VERIFYPEER,false); //忽略SSL证书
	//curl_setopt($login,CURLOPT_COOKIEJAR,$cookie_file);
    $content=curl_exec($login);
    $httpCode = curl_getinfo($login,CURLINFO_HTTP_CODE); 
    if($httpCode==200){
        preg_match('/Set-Cookie:(.*);/iU',$content,$str); //正则匹配
        $cookie = $str[1]; //获得COOKIE（SESSIONID）
        $array = array( 
                'status'=>200, 
                'message'=>$cookie,
                ); 
    }else if($httpCode==422){
        $array = array( 
        'status'=>422, 
        'message'=>'Failed to login',
        ); 
    }else{
        $array = array( 
        'status'=>444, 
        'message'=>'An unknown error',
        ); 
    }
    curl_close($login);
    return json_encode($array);
}

//利用cookie获取信息
function getMyinfo($cookie){
    $url='https://app.arukas.io/api/me';
    //$cookie_file = dirname(__FILE__).'/cookie.txt';
    $curl=curl_init($url);
    curl_setopt($curl,CURLOPT_HEADER, 0);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($curl,CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($curl,CURLOPT_COOKIE,$cookie);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
    $api=curl_exec($curl);
    $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE); 
    if($httpCode==200){
        return $api;
    }else if($httpCode==401){
        $array = array( 
        'status'=>401, 
        'message'=>'Not authorized',
        ); 
        return json_encode($array);
    }else{
        $array = array( 
        'status'=>444, 
        'message'=>'An unknown error',
        ); 
        return json_encode($array);
    }
    curl_close($curl);
}

function getApps($cookie){
    $url='https://app.arukas.io/api/apps';
    //$cookie_file = dirname(__FILE__).'/cookie.txt';
    $curl=curl_init($url);
    curl_setopt($curl,CURLOPT_HEADER, 0);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($curl,CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($curl,CURLOPT_COOKIE,$cookie);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
    $api=curl_exec($curl);
    $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE); 
    if($httpCode==200){
        return $api;
    }else if($httpCode==401){
        $array = array( 
        'status'=>401, 
        'message'=>'Not authorized',
        ); 
        return json_encode($array);
    }else{
        $array = array( 
        'status'=>444, 
        'message'=>'An unknown error',
        ); 
        return json_encode($array);
    }
    curl_close($curl);
}

function getContainer($containerID,$cookie){
    if (!isset($containerID)) {
    	$url='https://app.arukas.io/api/containers/';
    }else
    {
    	$url='https://app.arukas.io/api/containers/'.$containerID;
    }
    //$cookie_file = dirname(__FILE__).'/cookie.txt';
    $curl=curl_init($url);
    curl_setopt($curl,CURLOPT_HEADER, 0);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($curl,CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($curl,CURLOPT_COOKIE,$cookie);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
    $api=curl_exec($curl);
    $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE); 
    if($httpCode==200){
        return $api;
    }elseif($httpCode==404){
        $array = array( 
        'status'=>404, 
        'message'=>'Not found',
        ); 
        return json_encode($array);
    }else if($httpCode==401){
        $array = array( 
        'status'=>401, 
        'message'=>'Not authorized',
        ); 
        return json_encode($array);
    }else{
        $array = array( 
        'status'=>444, 
        'message'=>'An unknown error',
        ); 
        return json_encode($array);
    }
    curl_close($curl);
}
function setAPP($containerID,$cookie){
    	$login_url='https://app.arukas.io/api/containers/'.$containerID;
    	$postdata = array('image_name' =>'nginx:latest' ,'instances'=>1,'mem'=>256,'cmd'=>'nginx -g \"daemon off;\"','arukas_domain'=>'endpoint-sample');
    $data_string='{"data":{"type": "containers","attributes": {"image_name": "xbw12138/centos-shadowoscks","instances": 1,"mem":512,"cmd": "","envs": [],"ports": [],"arukas_domain": ""}}}';
    	$login=curl_init($login_url);
    	curl_setopt($login,CURLOPT_HEADER,1);//1 将头文件的信息作为数据流输出 0则不输出头文件
    	curl_setopt($login,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($login,CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($login,CURLOPT_COOKIE,$cookie);
    	curl_setopt($login,CURLOPT_POSTFIELDS,$data_string);
    	curl_setopt($login,CURLOPT_SSL_VERIFYPEER,false); //忽略SSL证书
    	//curl_setopt($login,CURLOPT_COOKIEJAR,$cookie_file);
    curl_setopt($login, CURLOPT_HTTPHEADER, array(                                                                            
        'Content-Type: application/json',                                                                                  
        'Content-Length: ' . strlen($data_string))                                                                         
    );                 
        $content=curl_exec($login);
        return $content;
        
}
function startContainer($containerID,$cookie){
    $url='https://app.arukas.io/api/containers/'.$containerID.'/power';
    $curl=curl_init($url);
    curl_setopt($curl,CURLOPT_HEADER, 0);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl,CURLOPT_COOKIE,$cookie);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
    $api=curl_exec($curl);
    $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE); 
    if($httpCode==202){
        return "服务器启动中，请等待……";
    }elseif($httpCode==404){
        $array = array( 
        'status'=>404, 
        'message'=>'Not found',
        ); 
        return json_encode($array);
    }else if($httpCode==401){
        $array = array( 
        'status'=>401, 
        'message'=>'Not authorized',
        ); 
        return json_encode($array);
    }else{
        $array = array( 
        'status'=>444, 
        'message'=>'The app is not bootable.',
        ); 
        return json_encode($array);
    }
    curl_close($curl);
}
function getSSH($host,$port){
    $url='http://182.254.146.68/ssh/ssh.php?host='.$host.'&port='.$port;
    $curl=curl_init($url);
    curl_setopt($curl,CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_TIMEOUT, 1);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($curl);
    $array = array( 'status'=>200, 'message'=>'Starting Shadowsocks-go success'); 
    return json_encode($array);
    curl_close($curl);
}


?>