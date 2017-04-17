<?php//access_token=2.00ZV7SrFT
//获取用户信息 get https://api.weibo.com/2/eps/user/info.json 
//?access_token=2.00ZV7SrFT&uid=6195369467
//接口要求返回的字符串需要是utf8编码。
header( 'Content-type: text/html; charset=utf-8' );
//加载SDK
require_once 'CallbackSDK.php';
require_once 'db_config.php';
$conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_password) or die("error connecting") ;
mysql_query("set names 'utf8'"); 
mysql_select_db($mysql_database);




//设置app_key对应的app_secret
define("APP_SECRET", "fb142de330addb6d2b5b4aaef01b702d");
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
$str_return = '';
$str_data='';
if (!empty($post_msg_str)) {
    //sender_id为发送回复消息的uid，即蓝v自己
    $sender_id = $post_msg_str['receiver_id'];
    //receiver_id为接收回复消息的uid，即蓝v的粉丝
    $receiver_id = $post_msg_str['sender_id'];    
    $function="0";
    if($post_msg_str['type']=="event"){
        $menu=$post_msg_str['data'];
        if($menu['key']=="memeda"){
            $function="1";
        }
    }else if($post_msg_str['type']=="text"){
        $message=substr($post_msg_str['text'], 0, 1 );
        if($post_msg_str['text']=="账号绑定"||$post_msg_str['text']=="激活码"){
            $function="1";//激活码
        }else if($message=="*"){
            $function="2";//屏蔽自动回复
        }else{
            $function="3";//自动回复
        }
    }
    if($function=="2"){
        
    }else{
        if($function=="1"){
            $sql="select 1 from user where uid =$receiver_id limit 1";
            $result = mysql_query($sql);
            $data = mysql_num_rows($result);
            $name=getNickname($receiver_id);
            if($data){
                $str_data="欢迎回来-亲爱的@".$name."\n"
                ."您的账号已经绑定"."\n"
                ."您已经领取过激活码"."\n"
                ."不要太贪心哦"."\n"
                ."@DMT许博文 开发测试";
            }else{
                $code = getInviteCode();
                $sql="INSERT INTO user(uid,nickname,code)VALUES('$receiver_id', '$name','$code')";
                $result = mysql_query($sql);
                if($result){
                    $str_data="用户绑定成功--@".$name."\n"
                    ."您的激活码:"."\n"
                    .$code."\n"
                    ."请前往http://v.ecfun.cc注册激活"."\n"
                    ."感谢您的支持"."\n"
                    ."@DMT许博文 开发测试";
                }else{
                    $str_data="用户绑定失败";
                }
            }
        }else if($function=="3"){
            $name=getNickname($receiver_id);
            $str_data="欢迎回来-亲爱的@".$name."\n"
            ."快捷功能(回复)"."\n"
            ."--------------------"."\n"
            ."激活码"."\n"
            ."--------------------"."\n"
            ."暂时只有一个功能，嘿嘿"."\n"
            ."说出你想要的功能吧"."\n"
            ."如果你不想看到自动回复-"."\n"
            ."那请在消息前加 * "."\n"
            ."@DMT许博文 开发测试";
        }
        //回复text类型的消息示例。
        $data_type = "text";
        $data = $call_back_SDK->textData($str_data);
        
        $str_return = $call_back_SDK->buildReplyMsg($receiver_id, $sender_id, $data, $data_type);
        echo json_encode($str_return);
    }
    
}function getNickname($receiver_id){
    //获取用户名
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
