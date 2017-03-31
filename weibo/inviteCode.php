<?php
require_once 'db_config2.php';
$conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_password) or die("error connecting") ;
mysql_query("set names 'utf8'"); 
mysql_select_db($mysql_database);


$x = rand(10, 1000);
$z = rand(10, 1000);
$x = md5($x).md5($z);
$x = base64_encode($x);
$sub = "id";
$code = $sub.substr($x, rand(1, 13), 24);
$sql="INSERT INTO invite_code(code)VALUES('$code')";
$result = mysql_query($sql);
if($result){
    echo "{\"code\":\"$code\"}";
}else{
    echo "{\"code\":\"no\"}";
}