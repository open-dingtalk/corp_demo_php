<?php

class Cache
{
    public function setJsTicket($ticket)
    {
        $memcache = $this->getMemcache();
        $memcache->set("js_ticket", $ticket, time() + 7000); // js ticket有效期为7200秒，这里设置为7000秒
    }
    
    public function getJsTicket()
    {
        $memcache = $this->getMemcache();
        return $memcache->get("js_ticket");
    }
    
    public function setCorpAccessToken($accessToken)
    {
        $memcache = $this->getMemcache();
        $memcache->set("corp_access_token", $accessToken, time() + 7000); // corp access token有效期为7200秒，这里设置为7000秒
    }
    
    public function getCorpAccessToken()
    {
        $memcache = $this->getMemcache();
        return $memcache->get("corp_access_token");
    }

    
    
    private function getMemcache()
    {
        /*if (class_exists("Memcache"))
        {
            $memcache = new Memcache; 
            if ($memcache->connect('localhost', 11211))
            {
                return $memcache;   
            }
        }*/

        return new FileCache;
    }
    
    public function get($key)
    {
        return $this->getMemcache()->get($key);
    }
    
    public function set($key, $value)
    {
        $this->getMemcache()->set($key, $value);
    }
}

/**
 * fallbacks 
 */
class FileCache
{
	function set($key, $value, $expire_time = 0) {
        if($key&&$value){
            $data = json_decode($this->get_file(DIR_ROOT ."filecache.php"),true);
            $item = array();
            $item["$key"] = $value;

            $item['expire_time'] = $expire_time;
            $item['create_time'] = time();
            $data["$key"] = $item;
            $this->set_file("filecache.php",json_encode($data));
        }
	}

	function get($key) {
        if($key){
            $data = json_decode($this->get_file(DIR_ROOT ."filecache.php"),true);
            if($data&&array_key_exists($key,$data)){
                $item = $data["$key"];
                if(!$item){
                    return false;
                }
                if($item['expire_time']>0&&$item['expire_time'] < time()){
                    return false;
                }

                return $item["$key"];
            }else{
                return false;
            }

        }
	}

    function get_file($filename) {
        if (!file_exists($filename)) {
            $fp = fopen($filename, "w");
            fwrite($fp, "<?php exit();?>" . '');
            fclose($fp);
            return false;
        }else{
            $content = trim(substr(file_get_contents($filename), 15));
        }
        return $content;
    }

    function set_file($filename, $content) {
        $fp = fopen($filename, "w");
        fwrite($fp, "<?php exit();?>" . $content);
        fclose($fp);
    }
}