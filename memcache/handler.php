<?php
class lock_memcache_handler{

    private static $mcApi = null;

    function __construct($fullKey) {
        $this->connect();
    }

    private function connect()
    {
        if(!isset(self::$mcApi)){
            $config = config::get('config');
            $hosts = (array)$config->memcahed->hosts;
            if(!empty($hosts)){
                self::$mcApi = new Memcached;
                foreach($hosts AS $row){
                    $row = trim($row);
                    $tmp = explode(':', $row);
                    $content = self::$mcApi->addServer($tmp[0], $tmp[1]);
                }
            }else{
                throw new Exception('缺少锁的配置');
            }
        }
    }

    public function get($fullKey) {
        return self::$mcApi->get($fullKey);
    }

    public function set($fullKey, $expire) {
        return self::$mcApi->set($fullKey, 1, $expire);
    }

    public function delete($fullKey) {
        return self::$mcApi->delete($fullKey);
    }
}