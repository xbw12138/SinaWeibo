<?php
$response = array();
require("db_config.php");
$conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_password) or die("error connecting") ;
mysql_query("set names 'utf8'"); 
mysql_select_db($mysql_database);
if (isset($_GET['user_phone'])) {
$user_phone = $_GET['user_phone'];
$sql="select 1 from user where uid ='$user_phone' limit 1";
$result = mysql_query($sql);
$data = mysql_num_rows($result);
echo $data;
}
?>