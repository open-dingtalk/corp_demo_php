<?php
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/util/Log.php");
require_once(__DIR__ . "/util/Cache.php");
require_once(__DIR__ . "/api/Auth.php");
require_once(__DIR__ . "/api/User.php");
require_once(__DIR__ . "/api/Message.php");

$auth = new Auth();
$user = new User();
$message = new Message();

$event = $_REQUEST["event"];
switch($event){
    case '':
        echo json_encode(array("error_code"=>"4000"));
        break;
    case 'getuserid':
        $accessToken = $auth->getAccessToken();
        $code = $_POST["code"];
        $userInfo = $user->getUserInfo($accessToken, $code);
        Log::i("[USERINFO-getuserid]".json_encode($userInfo));
        echo json_encode($userInfo, true);
        break;

    case 'get_userinfo':
        $accessToken = $auth->getAccessToken();
        $userid = $_POST["userid"];
        $userInfo = $user->get($accessToken, $userid);
        Log::i("[get_userinfo]".json_encode($userInfo));
        echo json_encode($userInfo, true);
        break;
    case 'jsapi-oauth':
        $href = $_GET["href"];
        $configs = $auth->getConfig($href);
        $configs['errcode'] = 0;
        echo json_encode($configs, JSON_UNESCAPED_SLASHES);
        break;
}
