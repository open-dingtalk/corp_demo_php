<?php
require_once(__DIR__ . "/../util/Http.php");

/**
 * 会话管理接口
 */
class Chat
{
    private $http;
    public function __construct() {
        $this->http = new Http();
    }

    public function createChat($accessToken, $chatOpt)
    {
        $response = $this->http->post("/chat/create",
            array("access_token" => $accessToken),
            json_encode($chatOpt));
        return $response;
    }

    public function bindChat($accessToken, $chatid,$agentid)
    {
        $response = $this->http->get("/chat/bind",
            array("access_token" => $accessToken,"chatid"=>$chatid,"agentid"=>$agentid));
        return $response;
    }

    public function sendmsg($accessToken, $opt)
    {
        $response = $this->http->post("/chat/send",
            array("access_token" => $accessToken),
            json_encode($opt));
        return $response;
    }

    public function callback($accessToken, $opt)
    {
        $response = $this->http->post("/call_back/register_call_back",
            array("access_token" => $accessToken),
            json_encode($opt));
        return $response;
    }
}