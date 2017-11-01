<?php
require_once(__DIR__ . "/../util/Http.php");

class Department
{
    private $http;
    public function __construct() {
        $this->http = new Http();
    }

    public static function createDept($accessToken, $dept)
    {
        $response = Http::post("/department/create", 
            array("access_token" => $accessToken), 
            json_encode($dept));
        return $response;
    }
    
    
    public static function listDept($accessToken)
    {
        $response = Http::get("/department/list", 
            array("access_token" => $accessToken));
        return $response;
    }
    
    
    public static function deleteDept($accessToken, $id)
    {
        $response = Http::get("/department/delete", 
            array("access_token" => $accessToken, "id" => $id));
        return $response;
    }
}