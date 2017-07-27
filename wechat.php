<?php
//********获取微信用户基本信息基本代码********
// 示例1:scope为snsapi_userinfo  用户不需要关注公众号，需要用户手动授权
//*Index.php*
$appid = '';
$redirect_uri = '';//('http://第三方平台域名/getInfo.php')
$url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
header("Location:".$url);
//*getInfo.php*
$appid = '';
$secret = '';
$code = $_GET('code');
//定义getJson模拟提交方法（curl get）
function getJson($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output,true);
}
//第一步：获取openid
$oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
$oauth2 = getJson($oauth2Url);
//第二步：根据全局access_token和openid查询用户信息
$access_token = $oauth2['access_token'];
$openid = $oauth2['openid'];
$get_user_info_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
$userinfo = getJson($get_user_info_url);
print_r($userinfo);//打印用户信息
//示例2：scope为snsapi_base 用户需要关注公众号
//*Index.php*
$appid = '';
$redirect_uri = '';//('http://第三方平台域名/getInfo.php')
$url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
header("Location:".$url);
//*getInfo.php*
$appid = '';
$secret = '';
$code = $_GET['code'];
//定义getJson模拟提交方法（curl get）
function getJson($url){
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output, true);
}
//第一步：取得全局access_token
$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
$token = getJson($url);
//第二步：获取openid
$oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
$oauth2 = getJson($oauth2Url);
//第三步：根据openid和全局access_token查询用户信息
$access_token = $token["access_token"];
$openid = $oauth2['openid'];
$get_user_info_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
$userinfo = getJson($get_user_info_url);
print_r($userinfo);//打印用户信息
