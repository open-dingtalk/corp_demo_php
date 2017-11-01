<?php
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/util/Log.php");
require_once(__DIR__ . "/util/Cache.php");
require_once(__DIR__ . "/crypto/DingtalkCrypt.php");

$signature = $_GET["signature"];
$timeStamp = $_GET["timestamp"];
$nonce = $_GET["nonce"];
$postdata = file_get_contents("php://input");
$postList = json_decode($postdata,true);
$encrypt = $postList['encrypt'];
$msg = "";

/**
 * TOKEN, ENCODING_AES_KEY, CORPID配置在config文件中
 */
try {
    $crypt = new DingtalkCrypt(TOKEN, ENCODING_AES_KEY, CORPID);
    $errCode = $crypt->DecryptMsg($signature, $timeStamp, $nonce, $encrypt, $msg);
} catch (Exception $e) {
    Log::e("DecryptMsg Exception".$e->getMessage());
    print $e->getMessage();
    exit();
}

$eventMsg = json_decode($msg);
$eventType = $eventMsg->EventType;

switch ($eventType){
    case "user_add_org":
        //通讯录用户增加 do something
        Log::i("【callback】:user_add_org_action");
        break;
    case "user_modify_org":
        //通讯录用户更改 do something
        Log::i("【callback】:user_modify_org_action");
        break;
    case "user_leave_org":
        //通讯录用户离职  do something
        Log::i("【callback】:user_leave_org_action");
        break;
    case "org_admin_add":
        //通讯录用户被设为管理员 do something
        Log::i("【callback】:org_admin_add_action");
        break;
    case "org_admin_remove":
        //通讯录用户被取消设置管理员 do something
        Log::i("【callback】:org_admin_remove_action");
        break;
    case "org_dept_create":
        //通讯录企业部门创建 do something
        Log::i("【callback】:org_dept_create_action");
        break;
    case "org_dept_modify":
        //通讯录企业部门修改 do something
        Log::i("【callback】:org_dept_modify_action");
        break;
    case "org_dept_remove":
        //通讯录企业部门删除 do something
        Log::i("【callback】:org_dept_remove_action");
        break;
    case "org_remove":
        //企业被解散 do something
        Log::i("【callback】:org_remove_action");
        break;


    case "check_url"://do something
    default : //do something
        break;
}

/**对返回信息进行加密**/
$res = "success";
$encryptMsg = "";
$errCode = $crypt->EncryptMsg($res, $timeStamp, $nonce, $encryptMsg);
if ($errCode == 0)
{
    echo $encryptMsg;
    Log::i("【callback】:RESPONSE: " . $encryptMsg);
}
else
{
    Log::e("RESPONSE ERR: " . $errCode);
}
