<?php
require_once(__DIR__ . "/../config.php");

Class Http
{
    /**
     * GET 请求
     * @param string $url
     * @param string $params
     */
    public function get($url, $params){
        $oCurl = curl_init();
        $url = $this->joinParams($url, $params);
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_VERBOSE, 1);
        curl_setopt($oCurl, CURLOPT_HEADER, false);
        curl_setopt($oCurl, CURLINFO_HEADER_OUT, false);
        $sContent = $this->execCURL($oCurl);
        return $sContent;
    }
    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @param boolean $post_file 是否文件上传
     * @return string content
     */
    public function post($url, $params, $data,$post_file=false){
        $oCurl = curl_init();
        $url = $this->joinParams($url, $params);
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if(PHP_VERSION_ID >= 50500 && class_exists('\CURLFile')){
            $is_curlFile = true;
        }else {
            $is_curlFile = false;
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($oCurl, CURLOPT_SAFE_UPLOAD, false);
            }
        }

        if($post_file) {
            if($is_curlFile) {
                foreach ($data as $key => $val) {
                    if(isset($val["tmp_name"])){
                        $data[$key] = new \CURLFile(realpath($val["tmp_name"]),$val["type"],$val["name"]);
                    }else if(substr($val, 0, 1) == '@'){
                        $data[$key] = new \CURLFile(realpath(substr($val,1)));
                    }
                }
            }
        }

        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($oCurl, CURLOPT_VERBOSE, 1);
        curl_setopt($oCurl, CURLOPT_HEADER, false);
        curl_setopt($oCurl, CURLINFO_HEADER_OUT, false);
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)));

        $sContent = $this->execCURL($oCurl);
        curl_close($oCurl);
        return $sContent;
    }

    /**
     * 执行CURL请求，并封装返回对象
     */
    private function execCURL($ch){
        $response = curl_exec($ch);
        error_log($response);
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200') {
            return json_decode($response, false);
        }

        return null;
    }

    private function joinParams($path, $params)
    {
        $url = OAPI_HOST . $path;
        if (count($params) > 0)
        {
            $url = $url . "?";
            foreach ($params as $key => $value)
            {
                $url = $url . $key . "=" . $value . "&";
            }
            $length = count($url);
            if ($url[$length - 1] == '&')
            {
                $url = substr($url, 0, $length - 1);
            }
        }
        return $url;
    }
}