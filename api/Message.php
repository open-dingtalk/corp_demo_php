<?php
require_once(__DIR__ . "/../util/Http.php");

class Message
{
    private $http;

    public function __construct() {
        $this->http = new Http();
    }

    public function sendToConversation($accessToken, $opt)
    {
        $response = $this->http->post("/message/send_to_conversation",
            array("access_token" => $accessToken),
            json_encode($opt));
        return $response;
    }

    public function send($accessToken, $opt)
    {
        $response = $this->http->post("/message/send",
            array("access_token" => $accessToken),json_encode($opt));
        return $response;
    }
}