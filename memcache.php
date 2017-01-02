<?php
class lock_memcache extends lock_abs{

    protected $_handler;

    public static function isLocked($id, $key) {
        $fullKey = self::selfFullKey($id, $key);
        $handler = new lock_memcache_handler($fullKey);
        if ($handler->get($fullKey)) {
            return true;
        }
        return false;
    }

    protected static function selfFullKey($id, $key) {
        return $id. '|' . $key;
    }

    public function _init(){
        $i = 0;
        $this->_handler = new lock_memcache_handler($this->_fullKey);
        do{
            while ($i<$this->_tries){
                $locked = $this->_handler->get($this->_fullKey);
                if(!$locked){
                    break 2;
                }
                $i++;
                if ($i<$this->_tries) usleep($this->_sleepForTry);
            }
            throw new Exception('获取锁失败');
        }while(false);
        
        if (!$this->_handler->set($this->_fullKey, $this->_lockExpire)) {
            throw new Exception('设置锁失败');
        }
        $this->_locked = true;
    }

    protected function _genFullKey($id, $key) {
        return self::selfFullKey($id, $key);
    }

    public function unlock(){
        if($this->_locked){
            $this->_handler->delete($this->_fullKey);
        }
    }
}