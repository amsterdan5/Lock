<?php
class lock_redis_handler{

    private static $mcApi = null;

    function __construct($fullKey) {
        $this->connect();
    }

    private function connect()
    {
        if(!isset(self::$mcApi)){
            $config = config::get('config');
            $cache_config['port'] = $config->cache->port;
            $cache_config['host'] = $config->cache->host;
            $redis = new Redis($cache_config);
            if($config->cache->passwd) {
                $redis->auth($config->cache->passwd);
            }
            self::$mcApi = $redis;
        }
    }

    public function get($fullKey) {
        return self::$mcApi->get($fullKey);
    }

    public function set($fullKey, $expire) {
        return self::$mcApi->multi(Redis::PIPELINE)->set($fullKey, 1)->expire($fullKey, $expire)->exec();
    }

    public function delete($fullKey) {
        return self::$mcApi->remove($fullKey);
    }
}